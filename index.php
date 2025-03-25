<?php
require 'db_connect.php';

check_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_favorites'])) {
    $stmt = $pdo->prepare("INSERT INTO offers 
        (user_id, title, price, url, image) 
        VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['title'],
        $_POST['price'],
        $_POST['url'],
        $_POST['image']
    ]);
}

$url = 'https://www.olx.pl/api/v1/offers?offset=0&limit=40&category_id=2586&sort_by=created_at:desc&filter_enum_fashionbrand[0]=adidas&filter_enum_fashionbrand[1]=nike&filter_enum_fashionbrand[2]=puma&filter_float_price:to=50';

$options = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
    ]
];

try {
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $data = json_decode($response, true);
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OLX Scraper</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php require_once 'navbar.php'; ?>
    
    <div class="container mx-auto p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($data['data'] as $offer): ?>
                <?php 
                    $title = htmlspecialchars($offer['title'] ?? 'Brak tytułu');
                    $price = 'Cena do negocjacji';
                    foreach ($offer['params'] as $param) {
                        if ($param['key'] === 'price' && isset($param['value']['label'])) {
                            $price = htmlspecialchars($param['value']['label']);
                            break;
                        }
                    }
                    
                    $url = htmlspecialchars($offer['url'] ?? '#');
                    $image = 'https://via.placeholder.com/300x200?text=Brak+zdjęcia';
                    if (!empty($offer['photos'][0]['link'])) {
                        $image = str_replace('{width}x{height}', '800x600', $offer['photos'][0]['link']);
                        $image = htmlspecialchars($image);
                    }
                ?>

                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="<?= $image ?>" alt="<?= $title ?>" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2"><?= $title ?></h3>
                        <div class="text-green-600 font-bold mb-2"><?= $price ?></div>
                        <div class="flex justify-between items-center">
                            <a href="<?= $url ?>" target="_blank" class="text-blue-600 hover:text-blue-800">Zobacz ofertę</a>
                            <form method="POST">
                                <input type="hidden" name="title" value="<?= $title ?>">
                                <input type="hidden" name="price" value="<?= $price ?>">
                                <input type="hidden" name="url" value="<?= $url ?>">
                                <input type="hidden" name="image" value="<?= $image ?>">
                                <button type="submit" name="add_to_favorites" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                    ♥ Dodaj do ulubionych
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>