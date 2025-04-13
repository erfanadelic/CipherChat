<?php
class MessageManager {
    private $DB;
    
    public function __construct($DBConnection) {
        $this->DB = $DBConnection;
    }

    public function sendMessage($data) {
        global $Pusher;
    
        $stmt = $this->DB->prepare("
            INSERT INTO messages (
                MessageUuid,
                SenderUuid,
                ReceiverUuid,
                IsGroup,
                Content,
                MessageType,
                SendTime,
                SeenTime,
                Status,
                ReplyTo,
                EditTime
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
    
        $stmt->bind_param(
            "sssssssssss",
            $data['MessageUuid'],
            $data['SenderUuid'],
            $data['ReceiverUuid'],
            $data['IsGroup'],
            $data['Content'],
            $data['MessageType'],
            $data['SendTime'],
            $data['SeenTime'],
            $data['Status'],
            $data['ReplyTo'],
            $data['EditTime']
        );
    
        $SenderUuid = $data['SenderUuid'];
        $receiverUuid = $data['ReceiverUuid'];
    
        // بررسی اولین پیام بین دو کاربر
        $FirsgtMessageQuery = "
            SELECT COUNT(*) as count FROM messages WHERE 
                (SenderUuid = '$SenderUuid' AND ReceiverUuid = '$receiverUuid') 
                OR (SenderUuid = '$receiverUuid' AND ReceiverUuid = '$SenderUuid')";
    
        $ResultRow = $this->DB->query($FirsgtMessageQuery)->fetch_assoc();
        $isFirstMessage = $ResultRow['count'] == 0;
    
        if ($isFirstMessage) {
            updateUserChatOrGroup($SenderUuid, $receiverUuid, $data['IsGroup']);
            updateUserChatOrGroup($receiverUuid, $SenderUuid, $data['IsGroup']);
        }
    
        // گرفتن عکس پروفایل فرستنده
        $userQuery = $this->DB->prepare("SELECT Name, ProfilePicture FROM users WHERE Uuid = ?");
        $userQuery->bind_param("s", $SenderUuid);
        $userQuery->execute();
        $userResult = $userQuery->get_result()->fetch_assoc();
    
        if ($userResult) {
            $data['SenderName'] = $userResult['Name'];
            $data['ProfilePicture'] = $userResult['ProfilePicture'];
        } else {
            $data['SenderName'] = 'بدون نام';
            $data['ProfilePicture'] = 'default.jpg';
        }
    
        // مرتب‌سازی UUIDها برای ثبات کانال
        $uuids = [$data['SenderUuid'], $data['ReceiverUuid']];
        sort($uuids);
        $channelName = "chat-" . implode("-", $uuids);
    
        $Pusher->trigger($channelName, "NewMessage", $data);
    
        return $stmt->execute();
    }
    

    public function getMessages($user1, $user2 = null, $isGroup = false) {
        if ($isGroup) {
            $stmt = $this->DB->prepare("
                SELECT m.*, u.Name AS SenderName, u.ProfilePicture
                FROM messages m
                JOIN users u ON m.SenderUuid = u.Uuid
                WHERE m.ReceiverUuid = ? AND m.IsGroup = 1
                ORDER BY m.SendTime ASC
            ");
            $stmt->bind_param("s", $user1); // user1 = groupUuid
        } else {
            $stmt = $this->DB->prepare("
                SELECT m.*, u.Name AS SenderName, u.ProfilePicture
                FROM messages m
                JOIN users u ON m.SenderUuid = u.Uuid
                WHERE m.IsGroup = 0 
                AND (
                    (m.SenderUuid = ? AND m.ReceiverUuid = ?) OR 
                    (m.SenderUuid = ? AND m.ReceiverUuid = ?)
                )
                ORDER BY m.SendTime ASC
            ");
            $stmt->bind_param("ssss", $user1, $user2, $user2, $user1);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
        $messages = [];
    
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    
        return $messages;
    }    
}
?>
