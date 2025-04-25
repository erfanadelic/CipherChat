function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
        return parts.pop().split(';').shift();
    }
    return null;
}
function Redirect(url) {
    window.location.href = url;
}
// Only check authentication if we're not already on the login page
if (window.location.pathname !== '/cipher/html/login.html' && 
    (getCookie("Uuid") == null || getCookie("PrivateKey") == null)) {
    Redirect("/cipher/html/login.html");
}