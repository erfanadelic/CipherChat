lastid = ""
function OnClickChat(id) {window.location.hash = id}
function OnClickSearchChat(Uuid, Name, ProfilePicture) {
  const SearchBox = document.getElementById("SearchBox");
  SearchBox.style.display = "none";
  document.getElementById("SearchInput").value = "";
  SearchBox.innerHTML = "";
  const existingChat = document.getElementById(Uuid);
  if (existingChat) {
    console.log("helidfd");
    
    window.location.hash = Uuid;
    return;
  }
  NewChat(Uuid, Name, ProfilePicture);
  window.location.hash = Uuid;
}


window.addEventListener("hashchange",function() {UpdateActiveChat()})
function UpdateActiveChat() {
  try{
    let ChatUuid = window.location.hash.replace("#", "");
    var ChatBox = document.getElementById(ChatUuid);
    ChatBox.classList.add("ActiveChat")
    if (lastid) {
    var ChatBox = document.getElementById(lastid);
    ChatBox.classList.remove("ActiveChat")}
    ClearChat();
    GetMesaages();
    PusherConfig();
    lastid = ChatUuid
  }catch(e){console.log(e);}}
function ClearChat() {document.getElementById("Messages").innerHTML = "";}
function Sendmsg() {
        MessageText = document.getElementById("InputText")
        if (MessageText.value) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST',"/cipher/php/api.php");
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.send("ApiMode=SendMessage&Content="+encodeURIComponent(MessageText.value.replaceAll("\n","<br>")
        )+"&IsGroup=0&MessageType=text&ReciverUuid="+ window.location.hash.replace("#", ""));MessageText.value=""}}
        function appendMsg(senderUuid, message, profileImgSrc = "default.jpg", senderName = "") {
          const myUuid = getCookie("Uuid");
      
          // ساختار اصلی
          const msgRow = document.createElement("div");
          msgRow.classList.add("MessageRow");
      
          if (senderUuid === myUuid) {
              msgRow.classList.add("sent");
          } else {
              msgRow.classList.add("received");
          }
      
          // عکس پروفایل
          const img = document.createElement("img");
          img.src = "Files/ProfileIMG/" + profileImgSrc;
          img.alt = "Profile";
          img.classList.add("ProfileImage");
      
          // محتوای پیام
          const contentBox = document.createElement("div");
          contentBox.classList.add("MessageContent");
      
          if (senderName) {
              const nameTag = document.createElement("span");
              nameTag.textContent = senderName;
              contentBox.appendChild(nameTag);
              contentBox.appendChild(document.createElement("br"));
          }
      
          const messageText = document.createTextNode(message);
          contentBox.appendChild(messageText);
      
          // اضافه کردن به ردیف پیام
          msgRow.appendChild(img);
          msgRow.appendChild(contentBox);
      
          // نمایش در صفحه
          document.getElementById("Messages").appendChild(document.createElement("br"));
          document.getElementById("Messages").appendChild(msgRow);
      }      
function NewChat(Uuid,name,profile) {
            const existingChat = document.getElementById(Uuid);
            if (existingChat) {return;}
            CUser = document.createElement("div")
            CUser.classList.add("ChatBOX")
            CUser.id = Uuid;
            CUser.addEventListener('click',function () {OnClickChat(this.id)})
            CUser.innerHTML = "<span>"+name+"</span><img src='Files/ProfileIMG/" + profile +"'>"
            document.getElementById("Chats").appendChild(CUser)}
function GetMesaages() {
    var xhr = new XMLHttpRequest();
          xhr.open('POST',"/cipher/php/api.php",true);
          xhr.onload = function () {
            var mesages = JSON.parse(xhr.responseText);
            console.log(mesages);
            mesages.forEach(function(message){
              console.log(message);
              
           appendMsg(message.SenderUuid, message.Content, message.ProfilePicture, message.SenderName);
            })
          }
          xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
          xhr.send("ApiMode=GetMessage&ReciverUuid=" + window.location.hash.replace("#", "")+"&IsGroup=0")}
function GetChats() {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', "/cipher/php/api.php", true);
  xhr.onload = function () {
    var chats = JSON.parse(xhr.responseText);
    console.log(chats);
    chats.forEach(function (chat) {
      console.log(chat);
      NewChat(chat.Uuid, chat.Name,chat.ProfilePicture);
    })

  }
  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhr.send("ApiMode=GetUserChats")
}
function OpenSideBar() {
    SideBar = document.getElementById('SideBar');
    MessageBox = document.getElementById('MessageBox');
    SideBar.style.display = "block";
    MessageBox.style.display ="none";
}
function CloseSideBar() {
  SideBar = document.getElementById('SideBar');
  MessageBox = document.getElementById('MessageBox');
  SideBar.style.display = "none";
  MessageBox.style.display ="block";
}  

if (getCookie("Uuid"),getCookie("PrivateKey")) {
}else{
  location.href = "/ochat/html/login.html";
}





var pusher = new Pusher("25b90454242a66f7e06d",{cluster: "ap1"})

function getPusherChannelName(Uuid1, Uuid2) {return "chat-" + [Uuid1, Uuid2].sort().join("-");}

function PusherConfig() {

const channelName = getPusherChannelName(getCookie("Uuid"), window.location.hash.replace("#", ""));
const channel = pusher.subscribe(channelName);
  channel.bind("NewMessage", function (data) {
      console.log("پیام جدید:", data);
           appendMsg(
              data.SenderUuid,
              data.Content,
              data.ProfilePicture,
              data.SenderName
          );
      })
};


window.location.hash = "";


GetChats();



// Search Functions
document.getElementById("SearchInput").addEventListener("change", function(event) {
  SearchBox = document.getElementById("SearchBox")
  SearchBox.innerHTML = ""
  if (event.target.value.trim() === "") {
    SearchBox.style.display = "none";
  } else {
      SearchBox.style.display = "block";
      SearchUserApi()
  }
});

function AppendSearchItem(Uuid,Name,ProfilePicture) {
  const div = document.createElement("div");
  div.classList.add("SearchItem");
  const span = document.createElement("span");
  span.textContent = Name;
  const img = document.createElement("img");
  img.src = "Files/ProfileIMG/" + ProfilePicture
  div.appendChild(span);
  div.appendChild(img);
  div.id = Uuid;
  div.addEventListener("click", function () {
    OnClickSearchChat(Uuid,Name,ProfilePicture); // مثلاً شناسه کاربر رو بفرستی
});
  const container = document.getElementById("SearchBox");
  container.appendChild(div);}


function SearchUserApi() {
// Get Result from API
  var xhr = new XMLHttpRequest();
  xhr.open('POST', "/cipher/php/api.php", true);
  xhr.onload = function () {
    var chats = JSON.parse(xhr.responseText);
    console.log(chats);
    chats.forEach(function (chat) {
      console.log(chat);
      AppendSearchItem(chat.Uuid,chat.Name,chat.ProfilePicture);
    })
  }
  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhr.send("ApiMode=SearchUser&searchQury=" + document.getElementById("SearchInput").value)
}

setInterval(GetChats, 1000);