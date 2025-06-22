<?php
require_once 'config/connection.php';
$results = [];

$batas = 5;
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$previous = $halaman - 1;
$next = $halaman + 1;

// Hitung total data (tanpa limit)
$search = isset($_GET['search']) ? $_GET['search'] : '';
$countQuery = "SELECT COUNT(DISTINCT posts.id) as total FROM posts
               JOIN post_categories ON posts.id = post_categories.post_id
               JOIN category ON post_categories.category_id = category.id";

if (!empty($search)) {
    $countQuery .= " WHERE posts.title LIKE :search OR posts.content LIKE :search";
    $countStmt = $conn->prepare(query: $countQuery);
    $countStmt->execute(params: [':search' => "%$search%"]);
} else {
    $countStmt = $conn->prepare($countQuery);
    $countStmt->execute();
}

$total_data = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_halaman = ceil($total_data / $batas);

// Ambil data post
if (!empty($search)) {
    $stmt = $conn->prepare(query: "SELECT posts.id, posts.title, posts.content, posts.slug, posts.created_at,
                            GROUP_CONCAT(category.name SEPARATOR ', ') AS category_names
                            FROM posts
                            JOIN post_categories ON posts.id = post_categories.post_id
                            JOIN category ON post_categories.category_id = category.id
                            WHERE posts.title LIKE :search OR posts.content LIKE :search
                            GROUP BY posts.id
                            ORDER BY posts.created_at DESC
                            LIMIT $halaman_awal, $batas");
    $stmt->execute([':search' => "%$search%"]);
} else {
    $stmt = $conn->prepare(query: "SELECT posts.id, posts.title, posts.content, posts.slug, posts.created_at,
                            GROUP_CONCAT(category.name SEPARATOR ', ') AS category_names
                            FROM posts
                            JOIN post_categories ON posts.id = post_categories.post_id
                            JOIN category ON post_categories.category_id = category.id
                            GROUP BY posts.id
                            ORDER BY posts.created_at DESC
                            LIMIT $halaman_awal, $batas");
    $stmt->execute();
}

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
</head>

<body>
    <h1>Welcome to MyBlog</h1>

    <!-- Search Box -->
    <form action="index.php" method="get">
        <label>Cari Post:</label>
        <input type="text" name="search" placeholder="Search by title or content">
        <input type="submit" value="Cari">
    </form>

    <!-- Filter by Category -->


    <!-- Post Data Card -->
    <div style="margin-top: 10px;">
        <?php
        //   if (count($results) === 0):
        if (!empty($results)):
            foreach ($results as $row):
                ?>
        <div style="border:1px solid #ccc; padding:16px; margin-bottom:16px; border-radius:8px; max-width:400px;">
            <h2>
                <?= htmlspecialchars($row['title']) ?>
            </h2>
            <p><?= htmlspecialchars($row['content']) ?></p>
            <small>Category: <?= htmlspecialchars($row['category_names']) ?> | Created at:
                <?= date('Y-m-d', strtotime($row['created_at'])) ?></small>
        </div>
        <?php
            endforeach;
        else: ?>
        <div style="border:1px solid #ccc; padding:16px; margin-bottom:16px; border-radius:8px; max-width:400px;">
            <h2>No posts found.</h2>
            <p>Please try searching for something else.</p>
            <?php
        endif; ?>
        </div>
        <?php if (!empty($results)): ?>
        <!-- Pagination -->
        <div style="margin-top: 20px;">
            <?php if ($halaman > 1): ?>
            <a href="?halaman=<?= $previous ?>&search=<?= urlencode($search) ?>">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
            <a href="?halaman=<?= $i ?>&search=<?= urlencode($search) ?>"
                style="<?= $i == $halaman ? 'font-weight:bold;' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>

            <?php if ($halaman < $total_halaman): ?>
            <a href="?halaman=<?= $next ?>&search=<?= urlencode($search) ?>">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

</body>

</html>