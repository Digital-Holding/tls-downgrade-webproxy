<?php

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[] = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))) . ': ' . $value;
            }
        }

        return $headers;
    }
}

function logRequest($contents, $headers, $query)
{
    $entry = [
        'time' => date('Y-m-d H:i:s'),
        'body' => $contents,
        'headers' => $headers,
        'query' => $query,
    ];

    file_put_contents('/var/log/requests.log', json_encode($entry), FILE_APPEND);
}

$inputHeaders = getallheaders();
$input = file_get_contents("php://input");
$certFile = '/cert/cert.pem';
$url = getenv('PROXY_TO');
$passphrase = getenv('PASSPHRASE');
$query = $_SERVER['REQUEST_URI'];

logRequest($input, $inputHeaders, $query);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url . $query);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
curl_setopt($ch, CURLOPT_HTTPHEADER, $inputHeaders);
curl_setopt($ch, CURLOPT_SSLCERT, $certFile);
if (!empty($passphrase)) {
    curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $passphrase);
}
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
curl_close($ch);
echo $output;
