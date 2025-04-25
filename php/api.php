<?php
include("UserApi.php");
include("MessageManager.php");
include("MessageApi.php");
include("config.php");
$ApiMode = $_POST["ApiMode"];
switch ($ApiMode) {
    case "SendVerifyCode":
        SendVerifyCode();
        break;
    case "CheckVerifyCode":
        CheckVerifyCode();
        break;   
    case "SendMessage":
        SendMessage();
        break;
    case "GetMessage":
        GetMessages();
        break;
    case "GetUserChats":
        GetUserChats();
        break;
    case "SearchUser":
        SearchUser();
        break;
    case "GetUserInfo":
        GetUserInfo();
        break;
    case "UpdateUserInfo":
        UpdateUserInfo();
        break;
}