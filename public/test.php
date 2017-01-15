<?php

function wall_get()
{
    $url = 'https://api.vk.com/method/wall.get';
    $params = array(
        'owner_id'=>'kirishi_net',
		'count'=>10,
		'filter'=>'owner',
        'v'=>'5.60',
    );

    // В $result вернется id отправленного сообщения
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