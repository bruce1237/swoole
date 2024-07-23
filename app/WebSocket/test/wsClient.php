<?php

function websocket_handshake($host, $port, $path)
{
    $key = base64_encode(openssl_random_pseudo_bytes(16));
    $header = "GET " . $path . " HTTP/1.1\r\n" .
              "Host: $host:$port\r\n" .
              "Upgrade: websocket\r\n" .
              "Connection: Upgrade\r\n" .
              "Sec-WebSocket-Key: $key\r\n" .
              "Sec-WebSocket-Version: 13\r\n\r\n";

    $socket = fsockopen($host, $port, $errno, $errstr, 2);
    if (!$socket) {
        die("$errstr ($errno)\n");
    }

    fwrite($socket, $header);
    $response = fread($socket, 1500);
    if (strpos($response, ' 101 ') === false || strpos($response, 'Sec-WebSocket-Accept: ') === false) {
        die("Failed to receive valid handshake response.\n");
    }
    
    return $socket;
}

function websocket_send($socket, $data)
{
    $header = chr(129) . chr(strlen($data));
    fwrite($socket, $header . $data);
}

function websocket_receive($socket)
{
    $data = fread($socket, 1500);
    if ($data === false) {
        return false;
    }
    
    $payload = "";
    if (ord($data[1]) & 127) {
        $payloadLength = ord($data[1]) & 127;
        $mask = substr($data, 2, 4);
        $encodedPayload = substr($data, 6, $payloadLength);
        for ($i = 0; $i < $payloadLength; ++$i) {
            $payload .= $encodedPayload[$i] ^ $mask[$i % 4];
        }
    }
    
    return $payload;
}

$host = 'localhost';
$port = 9501;
$path = '/';

$socket = websocket_handshake($host, $port, $path);

websocket_send($socket, 'Hello, WebSocket Server!');
$response = websocket_receive($socket);
echo "Received response: $response\n";

fclose($socket);