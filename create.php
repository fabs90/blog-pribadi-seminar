<?php
require_once 'config/connection.php';

if (!empty($_POST)) {
    // Prepare the input data
    $category = $_POST['category'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $date = date('Y-m-d H:i:s');


    // Prepare and execute the SQL statement to insert the post
    $stmt = $conn->prepare("INSERT INTO posts (title, content, slug, created_at) VALUES (:title, :content, :slug, :created_at)");
    $stmt->execute([':title' => $title, ':content' => $content, ':slug' => $slug, ':created_at' => $date]);

    // Prepare and execute the SQL statement to insert the post category
    $postId = $conn->lastInsertId();
    $stmt = $conn->prepare("INSERT INTO post_categories (post_id,category_id) VALUES (:post_id, :category_id)");
    $stmt->execute([':post_id' => $postId, 'category_id' => $category]);

    // Redirect after sucessfull insert data
    header('Location: dashboard.php');
    exit();
}
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Create</title>
</head>

<body>
    <?php
    // Find the cattegory column
    $stmt = $conn->prepare("SELECT * FROM category");
    $stmt->execute();
    $rowCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);


    ?>
    <form action="create.php" method="POST">
        <div>
            <label for="category">Choose category:</label>
            <select name="category" id="category">
                <?php
                foreach ($rowCategories as $category):
                    ?>
                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                    <?php
                endforeach;
                ?>
            </select>
            <br>
            <input type="text" name="title" placeholder="Title" required>
            <br>
            <input type="text" name="content" placeholder="Content" required>
            <br>

            <button type="submit" name="submit">Create</button>
        </div>

    </form>
</body>

</html>