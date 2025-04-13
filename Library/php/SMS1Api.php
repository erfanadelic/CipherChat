<?php
$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiIxWmdmdnFhOHdHQlhibnV2RWR1Rm1nREd5SkVYdUQ4UnVZSEpFTUg2VUNjPSIsImlzcyI6Imh0dHBzOi8vc21zMS5pci8iLCJpYXQiOiIxNzQwODQ3MzE1IiwiVXNlcklkIjoiMTkzMDkiLCJBY2NvdW50SWQiOiIyMjk2NiIsIkMiOiJqbkQyUGs5TnE4Q0I3WGMyV0VpMU13bFF0dlgvclJWZHc0QWQzemNvbnk0PSIsIkQiOiIxMzc1MyIsIkIiOiIyIiwiQSI6IjE4MCIsIkUiOiI0NzQxIiwiRiI6IjEiLCJhdWQiOiJBbnkifQ.sg8uMGxvRU6RbmY-z7TCF2hpCiX7QNq-t-0c3oNj2yw";
$api_url = 'https://app.sms1.ir:7001/api/service/patternSend';
function SendPatternMessaage($PatternId,$Recipient,$Pairs){
    global $token,$api_url;
    $DataArray = array(
        "patternId" => $PatternId,
        "recipient" => $Recipient,
        "pairs" => $Pairs);
    $curl = curl_init($api_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer $token",
        "Content-Type: application/json",
        "Accept: application/json"));
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($DataArray));
    $ResponseContent =  curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($http_code == 200) {return true;
    }else{echo $ResponseContent; return false;}}
?>
