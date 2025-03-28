<?php
require 'db_connect.php';
check_auth();

header('Content-Type: application/json');

$urls = [
    'female' => 'https://www.olx.pl/api/v1/offers?offset=0&limit=40&category_id=2534&sort_by=filter_float_price:asc&filter_enum_fashionbrand[0]=adidas&filter_enum_fashionbrand[1]=nike&filter_enum_fashionbrand[2]=puma&filter_float_price:to=20',
    'male' => 'https://www.olx.pl/api/v1/offers?offset=0&limit=40&category_id=2586&sort_by=filter_float_price:asc&filter_enum_fashionbrand[0]=adidas&filter_enum_fashionbrand[1]=nike&filter_enum_fashionbrand[2]=puma&filter_float_price:to=20'
];

$options = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
    ]
];

$allOffers = [];
foreach ($urls as $gender => $url) {
    try {
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        
        if (isset($data['data'])) {
            foreach ($data['data'] as $offer) {
                $offer['gender'] = $gender;
                $allOffers[] = $offer;
            }
        }
    } catch (Exception $e) {
        error_log('Błąd pobierania ofert: ' . $e->getMessage());
        continue;
    }
}

echo json_encode($allOffers);