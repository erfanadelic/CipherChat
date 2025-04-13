<?php
function SendMessage(){
    global  $CipherDB;
    $MessageManager = new MessageManager($CipherDB);
    $NewMessageData = [
        "MessageUuid" => GenrateUuid(), 
        "SenderUuid" => $_COOKIE["Uuid"],
        "ReceiverUuid" => $_POST["ReciverUuid"],
        "IsGroup" => $_POST["IsGroup"],
        "Content" => $_POST["Content"],
        "MessageType" => $_POST["MessageType"],
        "SendTime" => convertPersianTimeToEnglish(GetJaliliDate("YYYY-MM-DD HH:MM:SS")),
        "SeenTime" => null,
        "Status" => "sent",
        "ReplyTo" => null,
        "EditTime" => null];

    if ($MessageManager->sendMessage($NewMessageData)) {
       echo "پیام با موفقیت ارسال شد!"; 
    } else {
        echo "خطا در ارسال پیام!";
    }}
function GetMessages(){
    global  $CipherDB;
    $MessageManager = new MessageManager($CipherDB);
    $Messages = $MessageManager->getMessages($_COOKIE["Uuid"], $_POST["ReciverUuid"], $_POST["IsGroup"]);
    if ($Messages) {
        echo json_encode($Messages); 
    } else {
        echo "خطا در دریافت پیام ها!";
    }}