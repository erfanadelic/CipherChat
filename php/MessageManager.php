<?php
/**
 * Message Manager Class
 * Handles all operations related to messages including sending and retrieving
 */
class MessageManager {
    private $DB;
    
    /**
     * Constructor
     * @param mysqli $DBConnection Database connection
     */
    public function __construct($DBConnection) {
        $this->DB = $DBConnection;
    }

    /**
     * Send a new message
     * @param array $data Message data
     * @return bool True if message was sent successfully
     */
    public function sendMessage($data) {
        global $Pusher;
    
        // Prepare SQL statement for inserting new message
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
    
        // Bind parameters to SQL statement
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
    
        // Check if this is the first message between users
        $FirsgtMessageQuery = "
            SELECT COUNT(*) as count FROM messages WHERE 
                (SenderUuid = '$SenderUuid' AND ReceiverUuid = '$receiverUuid') 
                OR (SenderUuid = '$receiverUuid' AND ReceiverUuid = '$SenderUuid')";
    
        $ResultRow = $this->DB->query($FirsgtMessageQuery)->fetch_assoc();
        $isFirstMessage = $ResultRow['count'] == 0;
    
        // If first message, update chat lists for both users
        if ($isFirstMessage) {
            updateUserChatOrGroup($SenderUuid, $receiverUuid, $data['IsGroup']);
            updateUserChatOrGroup($receiverUuid, $SenderUuid, $data['IsGroup']);
        }
    
        // Get sender's profile picture and name
        $userQuery = $this->DB->prepare("SELECT Name, ProfilePicture FROM users WHERE Uuid = ?");
        $userQuery->bind_param("s", $SenderUuid);
        $userQuery->execute();
        $userResult = $userQuery->get_result()->fetch_assoc();
    
        if ($userResult) {
            $data['SenderName'] = $userResult['Name'];
            $data['ProfilePicture'] = $userResult['ProfilePicture'];
        } else {
            $data['SenderName'] = 'No Name';
            $data['ProfilePicture'] = 'default.jpg';
        }
    
        // Sort UUIDs for consistent channel naming
        $uuids = [$data['SenderUuid'], $data['ReceiverUuid']];
        sort($uuids);
        $channelName = "chat-" . implode("-", $uuids);
    
        // Send real-time notification via Pusher
        $Pusher->trigger($channelName, "NewMessage", $data);
    
        // Execute SQL statement and return result
        return $stmt->execute();
    }
    
    /**
     * Get messages between users or for a group
     * @param string $user1 First user UUID or group UUID
     * @param string $user2 Second user UUID (null for group)
     * @param bool $isGroup Whether this is a group chat
     * @return array Array of messages
     */
    public function getMessages($user1, $user2 = null, $isGroup = false) {
        if ($isGroup) {
            // Query for group messages
            $stmt = $this->DB->prepare("
                SELECT m.*, u.Name AS SenderName, u.ProfilePicture
                FROM messages m
                JOIN users u ON m.SenderUuid = u.Uuid
                WHERE m.ReceiverUuid = ? AND m.IsGroup = 1
                ORDER BY m.SendTime ASC
            ");
            $stmt->bind_param("s", $user1); // user1 = groupUuid
        } else {
            // Query for direct messages between two users
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
    
        // Execute query and fetch results
        $stmt->execute();
        $result = $stmt->get_result();
        $messages = [];
    
        // Process result rows
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    
        return $messages;
    }    
}
?>
