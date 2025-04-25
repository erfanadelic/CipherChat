// Search Functions
document.getElementById("SearchInput").addEventListener("change", function(event) {
    SearchBox = document.getElementById("SearchBox")
    SearchBox.innerHTML = ""
    
    if (event.target.value.trim() === "") {
        SearchBox.style.display = "none";
    } else {
        SearchBox.style.display = "block";
        SearchUserApi()}});

/**
 * Handle chat item click
 * @param {string} id Chat UUID to activate
 */
function OnClickChat(id) {
    console.log(window.location.hash);
    
    if (window.location.hash == "#" + id) {
        window.location.hash = "";
    } else {
        window.location.hash =  id;
    }
}

function OnclickSettingItem(id) {document.getElementById(id).classList.toggle("open") }

// Clear the messages area
function ClearChat() {document.getElementById("Messages").innerHTML = "";}

function openProfileSelect() {document.getElementById('ProfilePicture').click();}
/**
 * Handle search result click
 * @param {string} Uuid User UUID to chat with
 * @param {string} Name User's name
 * @param {string} ProfilePicture User's profile picture
 */
function OnClickSearchChat(Uuid, Name, ProfilePicture) {
    // Hide search results
    const SearchBox = document.getElementById("SearchBox");
    SearchBox.style.display = "none";
    document.getElementById("SearchInput").value = "";
    SearchBox.innerHTML = "";
    
    // Check if chat already exists
    const existingChat = document.getElementById(Uuid);
    if (existingChat) {
        console.log("Chat already exists");
        window.location.hash = Uuid;
        return;
    }
    // Create new chat and navigate to it
    AppendChat(Uuid, Name, ProfilePicture);
    window.location.hash = Uuid}
function OpenSettings() {
    // Toggle settings sidebar visibility
    document.getElementById("SettingsSideBar").classList.toggle('open');
    // Get user info using Api
    var userUuid = getCookie("Uuid");
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "php/api.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            var userData = JSON.parse(xhr.responseText);
            // Update profile picture in settings sidebar
            var profileImg = document.querySelector(".ProfilePicture img");
            if (userData.ProfilePicture && userData.ProfilePicture !== 'DefualtProfile.png') {
                profileImg.src = "Files/ProfileIMG/" + userData.ProfilePicture;
            } else {
                profileImg.src = "Files/ProfileIMG/DefualtProfile.png";}}};
    xhr.send("ApiMode=GetUserInfo&Uuid=" + userUuid);}
function ToggleSideBar(MobileMode = false) {
    var SideBar = document.getElementById("SideBar");
    if (SideBar.style.display === "none") {
        SideBar.style.display = "block";
    } else {
        SideBar.style.display = "none";
    }
    if (MobileMode) {
        SideBar.style.display = "none";
    }
}
function loadUserInfo() {
    // Get user UUID from cookie
    var userUuid = getCookie("Uuid");
    if (userUuid) {
        // Set the hidden UUID field
        document.getElementById("Uuid").value = userUuid;
        // Get user information from server
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "php/api.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                var userData = JSON.parse(xhr.responseText);
                // Fill form with user data
                document.getElementById("Name").value = userData.Name || "";
                document.getElementById("Username").value = userData.Username || "";
                document.getElementById("Bio").value = userData.Bio || "";
                // Display current profile picture
                if (userData.ProfilePicture && userData.ProfilePicture !== 'DefualtProfile.png') {
                    var profilePictureElement = document.getElementById('ProfilePictureSelect');
                    // Hide folder icon if it exists
                    // var folderIcon = profilePictureElement.querySelector('i');
                    // if (folderIcon) {
                    //     folderIcon.style.display = 'none';}
                    // Set background image and styling
                    profilePictureElement.style.backgroundImage = 'url(Files/ProfileIMG/' + userData.ProfilePicture + ')';
                    profilePictureElement.style.backgroundSize = 'cover';
                    profilePictureElement.style.backgroundPosition = 'center';}}};
        xhr.send("ApiMode=GetUserInfo&Uuid=" + userUuid);}}
/**
 * Update the active chat based on URL hash
 */
function UpdateActiveChat() {
    ClearChat();
    try {
        let ChatUuid = window.location.hash.replace("#", "");
        var ChatBox = document.getElementById(ChatUuid);
        ChatBox.classList.add("ActiveChat")
        
        // Remove active class from previous chat
        if (lastid) {
            var ChatBox = document.getElementById(lastid);
            ChatBox.classList.remove("ActiveChat")
        }
        
        // Clear messages and load new ones
        ClearChat();
        GetMesaages();
        PusherConfig();
        UiConfig();
        TopContentConfig();
        if (window.innerWidth < 768) {
            ToggleSideBar(true);
            
        }
        lastid = ChatUuid
    } catch(e) {}}
function TopContentConfig() {
    var UserUuid = window.location.hash.replace("#", "");
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "php/api.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            var userData = JSON.parse(xhr.responseText);
            document.querySelector("#TopContent img").src = "Files/ProfileIMG/" + userData.ProfilePicture;
            document.querySelector("#TopContent span").textContent = userData.Name;
        }   
    }
    xhr.send("ApiMode=GetUserInfo&Uuid=" + UserUuid);}
function UiConfig() {
    TopContent = document.getElementById("TopContent");
    Inputs = document.getElementById("Inputs");
    TopContent.style.display = "flex";
    Inputs.style.display = "flex";
}
function showImage() {
    var file = document.getElementById('ProfilePicture').files[0];
    var reader = new FileReader();

    reader.onload = function(e) {
        var imageUrl = e.target.result;
        var profilePictureElement = document.getElementById('ProfilePictureSelect');
        // Hide folder icon if it exists
        var folderIcon = profilePictureElement.querySelector('i');
        if (folderIcon) {folderIcon.style.display = 'none';}
        // Set background image and styling
        profilePictureElement.style.backgroundImage = 'url(' + imageUrl + ')';
        profilePictureElement.style.backgroundSize = 'cover';
        profilePictureElement.style.backgroundPosition = 'center';}
    // Read the file as data URL if a file was selected
    if (file) {reader.readAsDataURL(file);}}
/**
 * Append a message to the chat
 * @param {string} senderUuid Sender's UUID
 * @param {string} message Message content
 * @param {string} profileImgSrc Profile image source
 * @param {string} senderName Sender's name
 */
function appendMessage(senderUuid, message, profileImgSrc = "default.jpg", senderName = "") {
    const myUuid = getCookie("Uuid");
  
    // Create main message row
    const msgRow = document.createElement("div");
    msgRow.classList.add("MessageRow");
  
    // Add appropriate class based on sender
    if (senderUuid === myUuid) {
        msgRow.classList.add("sent");
    } else {
        msgRow.classList.add("received");}
  
    // Create profile image
    const img = document.createElement("img");
    img.src = "Files/ProfileIMG/" + profileImgSrc;
    img.alt = "Profile";
    img.classList.add("ProfileImage");
  
    // Create message content container
    const contentBox = document.createElement("div");
    contentBox.classList.add("MessageContent");
  
    // Add sender name if provided
    if (senderName) {
        const nameTag = document.createElement("span");
        nameTag.textContent = senderName;
        contentBox.appendChild(nameTag);
        contentBox.appendChild(document.createElement("br"));}
    // Add message text
    const messageText = document.createTextNode(message);
    contentBox.appendChild(messageText);
  
    // Add elements to message row
    msgRow.appendChild(img);
    msgRow.appendChild(contentBox);
  
    // Add to messages container
    document.getElementById("Messages").appendChild(document.createElement("br"));
    document.getElementById("Messages").appendChild(msgRow);}

/**
 * Create a new chat in the sidebar
 * @param {string} Uuid User UUID
 * @param {string} name User name
 * @param {string} profile Profile picture
 */
function AppendChat(Uuid, name, profile) {
    // Check if chat already exists
    const existingChat = document.getElementById(Uuid);
    if (existingChat) {return;}
    // Create new chat element
    CUser = document.createElement("div")
    CUser.classList.add("ChatBOX")
    CUser.id = Uuid;
    CUser.addEventListener('click', function() {OnClickChat(this.id)})
    CUser.innerHTML = "<span>" + name + "</span><img src='Files/ProfileIMG/" + profile + "'>"
    document.getElementById("Chats").appendChild(CUser)}

/**
 * Add a search result item to the search box
 * @param {string} Uuid User UUID
 * @param {string} Name User name
 * @param {string} ProfilePicture Profile picture
 */
function AppendSearchItem(Uuid, Name, ProfilePicture) {
    const div = document.createElement("div");
    div.classList.add("SearchItem");
    const span = document.createElement("span");
    span.textContent = Name;
    const img = document.createElement("img");
    img.src = "Files/ProfileIMG/" + ProfilePicture
    div.appendChild(span);
    div.appendChild(img);
    div.id = Uuid;
    
    // Add click handler to start chat with this user
    div.addEventListener("click", function() {
        OnClickSearchChat(Uuid, Name, ProfilePicture);
    });
    
    const container = document.getElementById("SearchBox");
    container.appendChild(div);}