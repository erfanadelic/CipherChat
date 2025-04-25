// Reset hash on page load
window.location.hash = "";
// Last active chat ID
lastid = ""
// Update active chat when hash changes
window.addEventListener("hashchange", function() {UpdateActiveChat()})

// Call loadUserInfo when the page is loaded
window.addEventListener('DOMContentLoaded', loadUserInfo());

// change direction of textarea
const textarea = document.querySelector('#Inputs #box textarea');
textarea.addEventListener('input', () => {
    const value = textarea.value.trim();

    if (value === "") {
        // If empty - default to right-to-left
        textarea.style.direction = 'rtl';
        textarea.style.textAlign = 'right';
    } 
    else if (/[\u0600-\u06FF]/.test(value)) {
        // If Persian text - use right-to-left
        textarea.style.direction = 'rtl';
        textarea.style.textAlign = 'right';
    } 
    else {
        // If English text - use left-to-right
        textarea.style.direction = 'ltr';
        textarea.style.textAlign = 'left';
    }
});

// Initialize Pusher for real-time messaging
var pusher = new Pusher("25b90454242a66f7e06d", {cluster: "ap1"})

// Get consistent channel name for two UUIDs
function getPusherChannelName(Uuid1, Uuid2) {return "chat-" + [Uuid1, Uuid2].sort().join("-");}

// Configure Pusher for current chat
function PusherConfig() {
    const channelName = getPusherChannelName(
        getCookie("Uuid"), 
        window.location.hash.replace("#", "")
    );
    const channel = pusher.subscribe(channelName);
    
    channel.bind("NewMessage", function(data) {
        appendMessage(
            data.SenderUuid,
            data.Content,
            data.ProfilePicture,
            data.SenderName
        );
    })
}

// Load chats when page loads
document.addEventListener("DOMContentLoaded", function() {GetChats();});

function SearchUserApi() {
    // Get Result from API
    var xhr = new XMLHttpRequest();
    xhr.open('POST', "/cipher/php/api.php", true);
     xhr.onload = function () {
    var chats = JSON.parse(xhr.responseText);
        
    chats.forEach(function (chat) {
    
        AppendSearchItem(chat.Uuid,chat.Name,chat.ProfilePicture);
    })}
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send("ApiMode=SearchUser&searchQury=" + document.getElementById("SearchInput").value)}
    
function logout() {
    document.cookie = "PrivateKey=; path=/cipher; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
    document.cookie = "uuid=; path=/cipher; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
    window.location.reload();}
    
function GetUserInfo(Uuid,callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', "/cipher/php/api.php", true);
    xhr.onload = function () {
    var chats = JSON.parse(xhr.responseText);
    callback(chats);}
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send("ApiMode=GetUserInfo&Uuid=" + Uuid)}
// DropMenus Setup
document.addEventListener('DOMContentLoaded', function () {
    // Get of all menu 
    const menuButtons = document.querySelectorAll('[id^="menu-"][id$="-btn"]');

    menuButtons.forEach(function (button) {
        const menuId = button.id.replace('-btn', ''); 
        const menu = document.getElementById(menuId);
        button.addEventListener('click', function (event) {
            event.stopPropagation();
            const rect = button.getBoundingClientRect();
            menu.style.display = 'block';
            menu.style.top = `${rect.bottom }px`;
            menu.style.left = `${(rect.left - menu.offsetWidth) + rect.width}px`;});});
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.dropmenu') && !event.target.closest('[id^="menu-"][id$="-btn"]')) {
            document.querySelectorAll('.dropmenu').forEach(function (menu) {
                menu.style.display = 'none';});}});});