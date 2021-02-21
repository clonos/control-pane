<?php
$ch = curl_init( 'http://144.76.225.238/api/v1/create/move' );
# Setup request to send json via POST.

$payload = json_encode( array( "email"=>"root@my.domain", "init_masters"=>"1" ) );


curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
# Return response instead of printing.
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
# Send request.
$result = curl_exec($ch);
curl_close($ch);
# Print response.
echo "<pre>$result</pre>";

