// Send a message to the current chat
function SendMessage() {
    MessageText = document.getElementById("InputText")
    if (MessageText.value) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST',"/cipher/php/api.php");
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.send("ApiMode=SendMessage&Content=" + 
            encodeURIComponent(MessageText.value.replaceAll("\n","<br>")) + 
            "&IsGroup=0&MessageType=text&ReceiverUuid=" + 
            window.location.hash.replace("#", ""));
        MessageText.value = "";}}

// Get messages for the current chat
function GetMesaages() {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', "/cipher/php/api.php", true);
    xhr.onload = function() {
        var mesages = JSON.parse(xhr.responseText);
        mesages.forEach(function(message) {
            appendMessage(
                message.SenderUuid, 
                message.Content, 
                message.ProfilePicture, 
                message.SenderName
            );})}
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send("ApiMode=GetMessage&ReceiverUuid=" + window.location.hash.replace("#", "") + "&IsGroup=0")}
// Get all user chats
function GetChats() {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', "/cipher/php/api.php", true);
    xhr.onload = function() {
        var chats = JSON.parse(xhr.responseText);
        chats.forEach(function(chat) {
            AppendChat(chat.Uuid, chat.Name, chat.ProfilePicture);})}
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send("ApiMode=GetUserChats")}
function updateUserInfo() {
    var formData = new FormData();
    // Get form field values
    var uuid = document.getElementById("Uuid").value; // User UUID
    var name = document.getElementById("Name").value; // User's name
    var username = document.getElementById("Username").value; // Username
    var profilePicture = document.getElementById("ProfilePicture").files[0]; // Profile picture file
    var bio = document.getElementById("Bio").value; // User bio
    // Add data to FormData object
    formData.append("ApiMode", "UpdateUserInfo");
    formData.append("Uuid", uuid);
    if (name) formData.append("Name", name);
    if (username) formData.append("Username", username);
    if (profilePicture) formData.append("ProfilePicture", profilePicture);
    if (bio) formData.append("Bio", bio);
    // Send data to server using XMLHttpRequest
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "php/api.php", true);
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            var response = JSON.parse(xhr.responseText);
            if (response.error) {
            } else {
                ShowNotification("Success",response.message);
                // Update profile picture display if a new image was uploaded
                if (profilePicture) {showImage();}}
        } else {
            ShowNotification("Danger","مشکل در ارسال اطلاعات")}};
    xhr.send(formData);}