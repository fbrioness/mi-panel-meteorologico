<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300');
header('Access-Control-Allow-Origin: *');

$group = isset($_GET['group']) ? intval($_GET['group']) : 1;

if ($group < 1 || $group > 12) {
    http_response_code(400);
    print json_encode(array(
        'status' => 'error',
        'message' => 'Grupo no válido'
    ));
    exit;
}

$url = 'https://api.unidadeditorial.es/sports/v1/classifications/current/?site=2&type=10&tournament=0117&group=' . $group . '&season=2025';

if (!function_exists('curl_init')) {
    http_response_code(500);
    print json_encode(array(
        'status' => 'error',
        'message' => 'cURL no está habilitado en el servidor'
    ));
    exit;
}

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: application/json',
    'User-Agent: Mozilla/5.0'
));

$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($response === false || $httpCode < 200 || $httpCode >= 300) {
    http_response_code(502);
    print json_encode(array(
        'status' => 'error',
        'message' => 'No fue posible consultar la API de MARCA',
        'httpCode' => $httpCode,
        'error' => $error
    ));
    exit;
}

$jsonTest = json_decode($response, true);

if ($jsonTest === null) {
    http_response_code(502);
    print json_encode(array(
        'status' => 'error',
        'message' => 'La respuesta recibida no es JSON válido'
    ));
    exit;
}

print $response;
exit;
