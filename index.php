<?php
include("php/config.php");
echo file_get_contents("html/index.html");

if(!CheckPrivateKey($_COOKIE["Uuid"],$_COOKIE["PrivateKey"])){
    Redirect("html/login.html");
}