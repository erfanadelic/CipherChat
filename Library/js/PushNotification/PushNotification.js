function ShowNotification(Type,Info){
    var PN = document.createElement("div")
    PN.classList.add("PushNotificationShow");    
    PN.classList.add("PushNotification"); 
    PN.classList.add("PushNotification"+Type);
    PN.id = "PushNotification"
    PN.innerHTML = '<span class="Icon"><i class="bi bi-info-circle"></i></span><span class="Info">'+Info+'</span>'
    document.getElementById("Body").appendChild(PN);
    setTimeout(() => {
        PN.classList.remove("PushNotificationShow");
    }, 3000);

}
function loadCSS(filename) {
    let link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = filename;
    link.type = "text/css";
    document.head.appendChild(link);
}
loadCSS("/cipher/Library/js/PushNotification/PushNotification.css");

