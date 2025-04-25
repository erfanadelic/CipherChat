<?php
/**
 * Generate a random private key for user authentication
 */
function MakePrivateKey(){
    return bin2hex(random_bytes(16));
}

/**
 * Update user's chats or groups list
 * 
 * @param string $userUuid User UUID
 * @param string $targetUuid Target UUID (chat or group)
 * @param bool $isGroup Whether the target is a group
 */
function updateUserChatOrGroup($userUuid, $targetUuid, $isGroup) {
    global $CipherDB;
    
    // Determine the column to update (Groups or Chats)
    $column = $isGroup ? 'Groups' : 'Chats';
    $query = "SELECT `$column` FROM users WHERE Uuid = '$userUuid'";
    $res = $CipherDB->query($query);
    $row = $res->fetch_assoc();

    if ($row) {
        // Get the current list and ensure it's an array
        $list = json_decode($row[$column], true);
        if (!is_array($list)) $list = [];

        // Add the new target if it doesn't already exist
        if (!in_array($targetUuid, $list)) {
            $list[] = $targetUuid;
            $updated = json_encode($list);
            $CipherDB->query("UPDATE users SET `$column` = '$updated' WHERE Uuid = '$userUuid'");
        }
    }
}

/**
 * Get all user chats and groups
 */
function GetUserChats(){
    global $CipherDB;
    $UserUuid = $_COOKIE["Uuid"];
    
    // Get user's chats and groups lists
    $QueryResult = $CipherDB->query("SELECT Chats, `Groups` FROM users WHERE Uuid = '$UserUuid'");
    $result = $QueryResult->fetch_assoc();

    $Chats = json_decode($result['Chats'], true);
    $Groups = json_decode($result['Groups'], true);

    $AllChats = [];

    // Get user info for each chat contact
    if (!empty($Chats)) {
        foreach ($Chats as $ChatUuid) {
            $stmtUser = $CipherDB->query("SELECT * FROM users WHERE Uuid = '$ChatUuid'");
            $chatData = $stmtUser->fetch_assoc();
            $chatData['type'] = 'user';
            $AllChats[] = $chatData;
        }
    }

    // Get info for each group
    if (!empty($Groups)) {
        foreach ($Groups as $GroupUuid) {
            $stmtGroup = $CipherDB->query("SELECT * FROM `groups` WHERE GroupUuid = '$GroupUuid'");
            $groupData = $stmtGroup->fetch_assoc();
            $groupData['type'] = 'group';
            $AllChats[] = $groupData;
        }
    }

    // Return all chats and groups as JSON
    header('Content-Type: application/json');
    echo json_encode($AllChats);
}

/**
 * Send verification code to user's phone number
 */
function SendVerifyCode(){
    global $CipherDB;
    $Number = $_POST["number"];
    
    // For development use static code 0000 instead of random code
    $VerifyCode = rand(10000, 99999);
    //$VerifyCode = 0000;
    
    $Time = convertPersianTimeToEnglish(GetJaliliDate("H:M:S"));
    
    // Uncomment for production to actually send SMS
    $SendREQ = SendPatternMessaage(161, $Number, array("code" => strval($VerifyCode)));
    //$SendREQ = true;
    
    if($SendREQ){
        // Store the OTP code in database
        $CipherDB->query("INSERT INTO otp_codes (Number, `OTP Code`, CreateTime) VALUES ('$Number', $VerifyCode, '$Time')");
        http_response_code(200);
    } else {
        http_response_code(503);
    }
}

/**
 * Verify the OTP code entered by user
 */
function CheckVerifyCode(){
    global $CipherDB;
    $Number = $_POST["number"];
    $VerifyCode = $_POST["code"];

    // Check if OTP code is valid for the given number
    $query = "SELECT * FROM otp_codes WHERE Number = '$Number' AND `OTP Code` = '$VerifyCode'";

    // Execute and validate query
    try {
        $Result = $CipherDB->query($query);
        if($Result->num_rows == 1){
            // Delete used OTP code and proceed to login
            $CipherDB->query("DELETE FROM otp_codes WHERE Number = '$Number' AND `OTP Code` = '$VerifyCode';");
            Login();
        } else {
            echo "Invalid code or number.";
            http_response_code(401);
        }
    } catch (mysqli_sql_exception $e) {
        echo "Error in SQL query: " . $e->getMessage();
        http_response_code(500);  // Internal Server Error
    }
}

/**
 * Login user with phone number
 */
function Login(){
    global $CipherDB;
    $Number = $_POST["number"];
    
    // Check if user exists
    $result = $CipherDB->query("select * from users WHERE Number = $Number");
    if($result->num_rows == 1){
        // Set authentication cookies
        $result = $result->fetch_assoc();
        setcookie("Uuid", $result["Uuid"], path:BaseUrl);
        setcookie("PrivateKey", $result["PrivateKey"], path:BaseUrl);
        http_response_code(204);
        echo "slam";
    } else {
        // Register new user if not found
        SignUP();
    }
}

/**
 * Register new user
 */
function SignUP(){
    global $CipherDB;
    $Number = $_POST["number"];
    
    // Generate private key for authentication
    $PrivateKey = MakePrivateKey();
    echo(strlen($PrivateKey));
    
    // Insert new user into database
    $CipherDB->query("INSERT INTO users (Uuid,Number, PrivateKey,Name) VALUES (uuid(),'$Number', '$PrivateKey','$Number')");
    
    // Log user in after registration
    Login();
}

/**
 * Search for users by name or username
 */
function SearchUser(){
    global $CipherDB;
    $Search = $_POST["searchQury"];
    $UserUuid = $_COOKIE["Uuid"];
    
    // Search for users excluding the current user
    $QueryResult = $CipherDB->query("SELECT * FROM users WHERE Uuid != '$UserUuid' AND (Name LIKE '%$Search%' OR Username LIKE '%$Search%')");
    $result = $QueryResult->fetch_all(MYSQLI_ASSOC);
    
    // Return results as JSON
    header('Content-Type: application/json');
    echo json_encode($result);
}

function GetUserInfo(){
    global $CipherDB;
    
    // Use UUID from POST data or fallback to cookie if not provided
    $UserUuid = isset($_POST["Uuid"]) ? $_POST["Uuid"] : $_COOKIE["Uuid"];
    
    // Query the database for user information
    $QueryResult = $CipherDB->query("SELECT * FROM users WHERE Uuid = '$UserUuid'");
    $result = $QueryResult->fetch_assoc();
    
    // Set content type for JSON response
    header('Content-Type: application/json');
    echo json_encode($result);
}

function UpdateUserInfo(){
    global $CipherDB;
    
    // Get user UUID from POST data
    $UserUuid = $_POST["Uuid"];
    
    // Data fields that might be updated
    $Name = isset($_POST["Name"]) ? $_POST["Name"] : null;
    $Username = isset($_POST["Username"]) ? $_POST["Username"] : null;
    $Bio = isset($_POST["Bio"]) ? $_POST["Bio"] : null;

    // Create arrays for building the query
    $updateFields = [];
    $params = [];

    // Add Name to update fields if provided
    if ($Name !== null) {
        $updateFields[] = "Name = ?";
        $params[] = $Name;
    }
    // Add Username to update fields if provided
    if ($Username !== null) {
        $updateFields[] = "Username = ?";
        $params[] = $Username;
    }
    // Add Bio to update fields if provided
    if ($Bio !== null) {
        $updateFields[] = "Bio = ?";
        $params[] = $Bio;
    }
    
    // Handle profile picture upload
    if(isset($_FILES['ProfilePicture']) && $_FILES['ProfilePicture']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../Files/ProfileIMG/";
        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Create a secure random filename
        $extension = strtolower(pathinfo($_FILES["ProfilePicture"]["name"], PATHINFO_EXTENSION));
        $randomName = bin2hex(random_bytes(16)); // Generate 32 random hex characters
        $fileName = $randomName . '.' . $extension;
        $targetFile = $targetDir . $fileName;
        
        // Check file type
        if($extension != "jpg" && $extension != "png" && $extension != "jpeg") {
            echo json_encode(["error" => "Only JPG, JPEG, and PNG files are allowed."]);
            return;
        }
        
        // Move uploaded file to target directory
        if(move_uploaded_file($_FILES["ProfilePicture"]["tmp_name"], $targetFile)) {
            $updateFields[] = "ProfilePicture = ?";
            $params[] = $fileName;
        } else {
            echo json_encode(["error" => "Error uploading image."]);
            return;
        }
    }

    // Execute the update if we have fields to update
    if (count($updateFields) > 0) {
        // Build the SQL query
        $query = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE Uuid = ?";
        $params[] = $UserUuid;

        // Prepare and execute the statement
        $stmt = $CipherDB->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        
        // Check if the update was successful
        if ($stmt->affected_rows > 0) {
            // Send success response
            echo json_encode(["message" => "User information updated successfully"]);
        } else {
            echo json_encode(["message" => "No changes made or user not found"]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(["message" => "No data provided for update"]);
    }
}
?>
    