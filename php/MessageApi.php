<?php

function sendMessage() {
    global $CipherDB;
    $MessageManager = new MessageManager($CipherDB);
    $MessageUuid = Ramsey\Uuid\Uuid::uuid4()->toString();
    $data = [
        'MessageUuid' => $MessageUuid,
        'SenderUuid' => $_COOKIE['Uuid'],
        'ReceiverUuid' => $_POST['ReceiverUuid'],
        'IsGroup' => $_POST['IsGroup'],
        'Content' => $_POST['Content'],
        'MessageType' => $_POST['MessageType'],
        'SendTime' => "2024-04-25 10:00:00",
        'SeenTime' => null,
        'Status' => "send",
        'ReplyTo' => null,
        'EditTime' => null];
    $MessageManager->sendMessage($data);}

function getMessages() {
    global $CipherDB;
    $MessageManager = new MessageManager($CipherDB);
    $Messages = $MessageManager->getMessages($_COOKIE['Uuid'], $_POST['ReceiverUuid'], $_POST['IsGroup']);
    echo json_encode($Messages);}

