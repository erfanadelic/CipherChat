<?php

function sendMessage() {
    global $CipherDB;
    $MessageManager = new MessageManager($CipherDB);
    $MessageUuid = Uuid::uuid4()->toString();
    $data = [
        'MessageUuid' => $MessageUuid,
        'SenderUuid' => $_COOKIE['Uuid'],
        'ReceiverUuid' => $_POST['ReceiverUuid'],
        'IsGroup' => $_POST['IsGroup'],
        'Content' => $_POST['Content'],
        'MessageType' => $_POST['MessageType'],
        'SendTime' => $_POST['SendTime'],
        'SeenTime' => null,
        'Status' => "send",
        'ReplyTo' => $_POST['ReplyTo'],
        'EditTime' => null];
    $MessageManager->sendMessage($data);}

function getMessages() {
    global $CipherDB;
    $MessageManager = new MessageManager($CipherDB);
    $Messages = $MessageManager->getMessages($_COOKIE['Uuid'], $_POST['ReceiverUuid'], $_POST['IsGroup']);
    echo json_encode($Messages);}

