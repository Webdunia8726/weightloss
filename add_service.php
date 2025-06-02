<?php
require 'connect.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $alt_text = $_POST['alt_text'] ?? '';
    $btn_link = $_POST['btn_link'] ?? '';

    // Image upload handling
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'assets/img/';
        $tmpName = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $targetFile = $uploadDir . time() . "_" . $fileName;

        // Basic image validation
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $error = "Invalid image type";
        } else if (move_uploaded_file($tmpName, $targetFile)) {
            // Insert into DB
            $stmt = $pdo->prepare("INSERT INTO services (title, description, image, alt_text, btn_link) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $targetFile, $alt_text, $btn_link]);
            $success = "Service added successfully!";
        } else {
            $error = "Failed to upload image.";
        }
    } else {
        $error = "Image is required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Add Service</title></head>
<body>
  <h2>Add New Service</h2>
  <a href="dashboard.php">Back to Dashboard</a><br><br>

  <?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>
  <?php if ($success): ?>
    <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data">
    <label>Title:<br><input type="text" name="title" required></label><br><br>
    <label>Description:<br><textarea name="description" required></textarea></label><br><br>
    <label>Alt Text:<br><input type="text" name="alt_text"></label><br><br>
    <label>Button Link:<br><input type="url" name="btn_link" value="#contact"></label><br><br>
    <label>Image:<br><input type="file" name="image" accept="image/*" required></label><br><br>
    <button type="submit">Add Service</button>
  </form>
</body>
</html>
