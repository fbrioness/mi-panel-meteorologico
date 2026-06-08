<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300');

$group = isset($_GET['group']) ? intval($_GET['group']) : 1;

if ($group < 1 || $group > 12) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Grupo no válido'
    ]);
    exit;
}

$url = "https://api.unidadeditorial.es/sports/v1/classifications/current/?site=2&type=10&tournament=0117&group={$group}&season=2025";

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'User-Agent: Mozilla/5.0'
    ]
]);

$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($response === false || $httpCode < 200 || $httpCode >= 300) {
    http_response_code(502);
    echo json_encode([
        'status' => 'error',
        'message' => 'No fue posible consultar la API de MARCA',
        'httpCode' => $httpCode,
        'error' => $error
    ]);
    exit;
}

echo $response;
