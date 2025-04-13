<?php
$mysqli = new mysqli("localhost","root","12121212ssss","ochat");

$apiMode = $_POST["APImode"];
if ($apiMode == "login_number"){
    $number = $_POST["number"];
    setcookie("number",$number);
    if (FindUser($number)) {
        $FU = FindUser($number);
        setcookie("username",$FU[0]);
        setcookie("name",$FU[4]);
        header("Location: /ochat");
    }else{header(header: "Location: /ochat/setinfo.html");}}
if ($apiMode == "set_info") {
    $username = $_POST["username"];
    $name = $_POST["name"];
    $number = $_COOKIE["number"];
    $mysqli->execute_query("INSERT INTO users (username,number,name,userchat,profilepic) VALUES ('$username',$number,'$name','[]','DefualtProfile.png')");
    setcookie("username",$username);
    setcookie("name",$name);
    header("Location: /ochat");}
if($apiMode == "SearchUser"){
$user = $_POST["user"];
echo json_encode(SearchUser($user));}
if ($apiMode == "GetUserChats") {
    $username = $_POST["username"];
    $ChatsJson = [];
    $user = FindUser(username:$username);
    foreach (json_decode($user[8]) as $CUser){
        $UChat = $mysqli->execute_query("SELECT * FROM users where username = '$CUser'")->fetch_all(MYSQLI_ASSOC);
        $ChatsJson = array_merge($ChatsJson,$UChat);
    }
    echo json_encode($ChatsJson);
    
}
function SearchUser($user){
    global $mysqli;
    $qurey = "SELECT * FROM users WHERE (name LIKE '$user' OR username LIKE  '$user');";
    $result = $mysqli->execute_query($qurey);
    return $result->fetch_all(MYSQLI_ASSOC);}
function FindUser($number = "",$username = ""){
    global $mysqli;
    if ($username != ""){
        $qurey = "SELECT * FROM users WHERE username = '$username';";
        $result = $mysqli->execute_query($qurey);
        return $result->fetch_array();
    }elseif($number != ""){
        $qurey = "SELECT * FROM users WHERE number = $number;";
        $result = $mysqli->execute_query($qurey);
        return $result->fetch_array();}}