<?php
require 'db_connect.php';
check_auth();

if (isset($_GET['remove'])) {
    $stmt = $pdo->prepare("DELETE FROM offers WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['remove'], $_SESSION['user_id']]);
    header("Location: favorites.php");
    exit;
}

$gender = $_GET['gender'] ?? 'all';

$query = "SELECT * FROM offers WHERE user_id = ?";
$params = [$_SESSION['user_id']];

if ($gender !== 'all') {
    $query .= " AND gender = ?";
    $params[] = $gender;
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$favorites = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulubione</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php require_once 'navbar.php'; ?>
    
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Moje ulubione oferty</h1>
        
        <div class="flex gap-4 mb-6">
            <a href="?gender=all" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-md">Wszystkie</a>
            <a href="?gender=female" class="bg-white px-4 py-2 rounded-lg shadow-md">Damskie</a>
            <a href="?gender=male" class="bg-white px-4 py-2 rounded-lg shadow-md">Męskie</a>
        </div>

        <?php if (empty($favorites)): ?>
            <div class="text-center text-gray-500">Brak ulubionych ofert</div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($favorites as $item): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="<?= htmlspecialchars($item['image']) ?>" 
                             alt="<?= htmlspecialchars($item['title']) ?>" 
                             class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold mb-2"><?= htmlspecialchars($item['title']) ?></h3>
                            <div class="text-green-600 font-bold mb-2"><?= htmlspecialchars($item['price']) ?></div>
                            <div class="flex justify-between items-center">
                                <a href="<?= htmlspecialchars($item['url']) ?>" 
                                   target="_blank" 
                                   class="text-blue-600 hover:text-blue-800">
                                    Zobacz ofertę
                                </a>
                                <a href="favorites.php?remove=<?= $item['id'] ?>" 
                                   class="text-red-500 hover:text-red-700"
                                   onclick="return confirm('Na pewno usunąć?')">
                                    Usuń
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>