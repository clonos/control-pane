<?php

$ch = curl_init("https://bitclouds.convectix.com:1443/clusters");
//$fp = fopen("example_homepage.txt", "w");

curl_setopt($ch, CURLOPT_FILE, $stdout);
curl_setopt($ch, CURLOPT_HEADER, 0);

curl_exec($ch);
if(curl_error($ch)) {
    fwrite($stdout, curl_error($ch));
}
curl_close($ch);
//fclose($fp);
?>

