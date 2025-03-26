<?php
require 'db_connect.php';

check_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['add_to_favorites'])) {
        try {
            $required = ['title', 'price', 'url', 'image', 'gender'];
            foreach ($required as $field) {
                if (!isset($input[$field])) {
                    throw new Exception("Brakujące pole: $field");
                }
            }

            $stmt = $pdo->prepare("INSERT INTO offers 
                (user_id, title, price, url, image, gender) 
                VALUES (?, ?, ?, ?, ?, ?)");
                
            $stmt->execute([
                $_SESSION['user_id'],
                $input['title'],
                $input['price'],
                $input['url'],
                $input['image'],
                $input['gender']
            ]);

            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
            exit;

        } catch(PDOException $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Błąd bazy danych: ' . $e->getMessage()
            ]);
            exit;
        } catch(Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OLX Scraper</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .filter-btn {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php require_once 'navbar.php'; ?>

    <script>
        const USER_ID = <?= json_encode($_SESSION['user_id'] ?? 0, JSON_HEX_TAG) ?>;
    </script>
    
    <div class="container mx-auto p-4">
        <div class="flex gap-4 mb-6" id="filters">
            <button data-gender="all" class="filter-btn bg-blue-500 text-white px-4 py-2 rounded-lg shadow-md">
                Wszystkie
            </button>
            <button data-gender="female" class="filter-btn bg-white px-4 py-2 rounded-lg shadow-md">
                Damskie
            </button>
            <button data-gender="male" class="filter-btn bg-white px-4 py-2 rounded-lg shadow-md">
                Męskie
            </button>
        </div>
        
        <script>
            const USER_ID = <?= json_encode($_SESSION['user_id'] ?? 0) ?>;
        </script>

        <h2 class="text-2xl font-bold mb-4" id="sectionTitle">🔥 Wszystkie oferty</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="offersContainer"></div>
    </div>

    <script src="main.js"></script>
</body>
</html>