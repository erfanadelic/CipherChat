<?php
require_once "vendor/autoload.php";
// Load PHP Libs
foreach (glob("../Library/php/*.php") as $file) {include_once $file;}
$CipherDB = new mysqli("localhost","root","12121212ssss","cipher");
define("BaseUrl","/cipher");

use Ramsey\Uuid\Uuid;
use Pusher\Pusher;
$Pusher = new Pusher(
    "25b90454242a66f7e06d",
    "79b024b8c4e3c2b376b1",
    "1954878",
    ['cluster' => 'ap1']);

function CheckPrivateKey($Uuid,$PrivateKey){
    global $CipherDB;
    $Result = $CipherDB->query("SELECT * FROM users WHERE Uuid = '$Uuid' AND PrivateKey = '$PrivateKey'");
    if ($Result->num_rows == 1) {
        return true;}
    return false;}

function Redirect($url) {
    header("Location: $url");
    exit();
}
function GenrateUuid() {
    $uuid = Uuid::uuid4()->toString();
    return $uuid;}