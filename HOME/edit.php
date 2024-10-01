<?php
session_start();
include 'db.php';

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'];

if (isset($_POST['update'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $file = $_FILES['file'];

    if ($file['name']) {
        $fileName = $_FILES['file']['name'];
        $fileTmpName = $_FILES['file']['tmp_name'];
        $fileDestination = 'uploads/' . $fileName;
        move_uploaded_file($fileTmpName, $fileDestination);
        $sql = "UPDATE articles SET title='$title', description='$description', cover_image='$fileDestination' WHERE id=$id";
    } else {
        $sql = "UPDATE articles SET title='$title', description='$description' WHERE id=$id";
    }

    mysqli_query($conn, $sql);
    header('Location: homepage.php');
}

$sql = "SELECT * FROM articles WHERE id=$id";
$result = mysqli_query($conn, $sql);
$article = mysqli_fetch_assoc($result);
?>

<form method="POST" action="" enctype="multipart/form-data">
    <input type="text" name="title" value="<?= $article['title'] ?>" required>
    <textarea name="description" required><?= $article['description'] ?></textarea>
    <input type="file" name="file">
    <button type="submit" name="update">อัปเดต</button>
</form>
