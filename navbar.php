<nav class="bg-gray-800 p-4">
    <div class="container mx-auto flex justify-between items-center">
        <a href="index.php" class="text-white text-xl font-bold">OLX Scraper</a>
        <div class="space-x-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="favorites.php" class="text-gray-300 hover:text-white">Ulubione</a>
                <a href="logout.php" class="text-red-400 hover:text-red-300">Wyloguj</a>
            <?php else: ?>
                <a href="login.php" class="text-gray-300 hover:text-white">Zaloguj</a>
            <?php endif; ?>
        </div>
    </div>
</nav>