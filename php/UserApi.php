<?php
function MakePrivateKey(){return bin2hex(random_bytes(16));}
function updateUserChatOrGroup($userUuid, $targetUuid, $isGroup) {
    global $CipherDB;
    $column = $isGroup ? 'Groups' : 'Chats';
    $query = "SELECT `$column` FROM users WHERE Uuid = '$userUuid'";
    $res = $CipherDB->query($query);
    $row = $res->fetch_assoc();

    if ($row) {
        $list = json_decode($row[$column], true);
        if (!is_array($list)) $list = [];

        if (!in_array($targetUuid, $list)) {
            $list[] = $targetUuid;
            $updated = json_encode($list);
            $CipherDB->query("UPDATE users SET `$column` = '$updated' WHERE Uuid = '$userUuid'");
        }
    }
}

function GetUserChats(){
    global $CipherDB;
    $UserUuid = $_COOKIE["Uuid"];
    $QueryResult = $CipherDB->query("SELECT Chats, `Groups` FROM users WHERE Uuid = '$UserUuid'");
    $result = $QueryResult->fetch_assoc();

    $Chats = json_decode($result['Chats'], true);
    $Groups = json_decode($result['Groups'], true);

    $AllChats = [];

    if (!empty($Chats)) {
        foreach ($Chats as $ChatUuid) {
            $stmtUser = $CipherDB->query("SELECT * FROM users WHERE Uuid = '$ChatUuid'");
            $chatData = $stmtUser->fetch_assoc();
            $chatData['type'] = 'user';
            $AllChats[] = $chatData;}}

   if (!empty($Groups)) {
        foreach ($Groups as $GroupUuid) {
            $stmtGroup = $CipherDB->query("SELECT * FROM `groups` WHERE GroupUuid = '$GroupUuid'");
            $groupData  = $stmtGroup->fetch_assoc();
            $groupData ['type'] = 'group';
            $AllChats[] = $groupData ;}}

    header('Content-Type: application/json');
    echo json_encode($AllChats);}
function SendVerifyCode(){
    global $CipherDB;
    $Number = $_POST["number"];
    $VerifyCode = rand(10000, 99999);
    echo $VerifyCode;
    $Time = GetJaliliDate("H:M:S");
    $SendREQ = SendPatternMessaage(161, $Number, array("code" => strval($VerifyCode)));
    //$SendREQ = true;
    if($SendREQ){
        $CipherDB->query("INSERT INTO otp_codes (Number, `OTP Code`, CreateTime) VALUES ('$Number', $VerifyCode, '$Time')");
        http_response_code(200);
    }else{
        http_response_code(response_code: 503);}}
function CheckVerifyCode(){
    global $CipherDB;
    $Number = $_POST["number"];
    $VerifiyCode = $_POST["code"];
    $Result = $CipherDB->query("SELECT * FROM otp_codes WHERE Number = '$Number' AND `OTP Code` = '$VerifiyCode';");
    if($Result->num_rows == 1){
        $CipherDB-> query("DELETE FROM otp_codes WHERE Number = '$Number' AND `OTP Code` = '$VerifiyCode';");
        Login();
    }else{echo "fff";http_response_code(401);}}
function Login(){
    global $CipherDB;
    $Number = $_POST["number"];
    $result = $CipherDB->query("select * from users WHERE Number = $Number");
    if($result->num_rows == 1){
    $result = $result->fetch_assoc();
    setcookie("Uuid",$result["Uuid"],path:BaseUrl);
    setcookie("PrivateKey",$result["PrivateKey"],path:BaseUrl);
    http_response_code(204);
    echo "slam";
    }else{SignUP();}}
function SignUP(){
    global $CipherDB;
    $Number = $_POST["number"];
    $PrivateKey = MakePrivateKey();
    echo(strlen($PrivateKey));
    $CipherDB->query("INSERT INTO users (Uuid,Number, PrivateKey) VALUES (uuid(),'$Number', '$PrivateKey')");
    Login();}

function SearchUser(){
    global $CipherDB;
    $Search = $_POST["searchQury"];
    $UserUuid = $_COOKIE["Uuid"];
    $QueryResult = $CipherDB->query("SELECT * FROM users WHERE Uuid != '$UserUuid' AND (Name LIKE '%$Search%' OR Username LIKE '%$Search%')");
    $result = $QueryResult->fetch_all(MYSQLI_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($result);}