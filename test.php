<?php

#Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==
#base64 encoded value of "username:password" prepended with "Basic". 

#An array of HTTP header fields to set, in the format array('Content-type: text/plain', 'Content-length: 100') 
$auth = array('user-id: '.$usr, 'password: '.$pwd);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_URL, $path.'service/test/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch,CURLOPT_HTTPHEADER,$auth);
$data = curl_exec($ch) or die(curl_error($ch));

echo $data;

curl_close($ch);
?>