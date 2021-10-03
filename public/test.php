<?php
exit;
$host = 'clonos.bsdstore.ru';  //where is the websocket server
$port = 8082;
$local = "http://clonos.bsdstore.ru/";  //url where this script run
$data = '{"author":"user","body":"hello world!"}';  //data to be send

$head = "GET / HTTP/1.1"."\r\n".
            "Upgrade: WebSocket"."\r\n".
            "Connection: Upgrade"."\r\n".
            "Origin: $local"."\r\n".
            "Host: $host"."\r\n".
            "Content-Length: ".strlen($data)."\r\n"."\r\n";
//WebSocket handshake
$sock = fsockopen($host, $port, $errno, $errstr, 2);
fwrite($sock, $head ) or die('error:'.$errno.':'.$errstr);
$headers = fread($sock, 2000);
fwrite($sock, "\x00$data\xff" ) or die('error:'.$errno.':'.$errstr);
$wsdata = fread($sock, 2000);  //receives the data included in the websocket package "\x00DATA\xff"
fclose($sock);


exit;






function wall_get()
{
    $url = 'https://api.vk.com/method/wall.get';
    $params = array(
        'owner_id'=>'kirishi_net',
		'count'=>10,
		'filter'=>'owner',
        'v'=>'5.60',
    );

    // � $result �������� id ������������� ���������
    $result = file_get_contents($url, false, stream_context_create(array(
        'http' => array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($params)
        )
    )));
}

echo '<pre>';
print_r(wall_get());