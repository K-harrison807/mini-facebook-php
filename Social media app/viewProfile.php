<?php
session_start();
include('Database.php');
mysqli_select_db($conn, $databaseName);

if (!isset($_GET['userID'])) {
    die("No user selected.");
}
$userID = (int)$_GET['userID'];

$userRes = mysqli_query($conn, "SELECT userName FROM users WHERE userID = $userID");
if (!$userRes || mysqli_num_rows($userRes) == 0) {
    die("User not found.");
}
$userRow = mysqli_fetch_assoc($userRes);
$userName = htmlspecialchars($userRow['userName']);


$uploadMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uploadBtn'])) {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $uploadMessage = "No file uploaded or upload error.";
    } else {
        $file = $_FILES['image'];
        $maxSize = 3 * 1024 * 1024; 
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($file['size'] > $maxSize) {
            $uploadMessage = "File too large (max 3MB).";
        } elseif (!in_array($ext, $allowed)) {
            $uploadMessage = "Only JPG, JPEG, PNG, GIF allowed.";
        } else {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $webPath = 'uploads/' . $newName;

            
                $check = mysqli_query($conn, "SELECT imageID, image FROM images WHERE userID = $userID LIMIT 1");
                if ($check && mysqli_num_rows($check) > 0) {
                    $old = mysqli_fetch_assoc($check);
                   
                    if (!empty($old['image']) && file_exists(__DIR__ . '/' . $old['image'])) {
                        @unlink(__DIR__ . '/' . $old['image']);
                    }
                    mysqli_query($conn, "UPDATE images SET image='".mysqli_real_escape_string($conn,$webPath)."' WHERE imageID=".$old['imageID']);
                } else {
                    mysqli_query($conn, "INSERT INTO images (image, userID) VALUES ('".mysqli_real_escape_string($conn,$webPath)."', $userID)");
                }

                $uploadMessage = "Profile picture uploaded successfully!";
                header("Location: viewProfile.php?userID=$userID");
                exit();
            } else {
                $uploadMessage = "Failed to move uploaded file.";
            }
        }
    }
}


$imgPath = "default.png"; 
$imgRes = mysqli_query($conn, "SELECT image FROM images WHERE userID = $userID ORDER BY imageID DESC LIMIT 1");
if ($imgRes && mysqli_num_rows($imgRes) > 0) {
    $imgRow = mysqli_fetch_assoc($imgRes);
    if (!empty($imgRow['image']) && file_exists(__DIR__ . '/' . $imgRow['image'])) {
        $imgPath = $imgRow['image'];
    }
}

$postRes = mysqli_query($conn, "SELECT userPost, posted_At FROM posts WHERE userID = $userID ORDER BY posted_At DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile of <?php echo $userName; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">

    <style>
    .profile-header { display:flex; gap:20px; align-items:center; margin-bottom:20px; }
    .profile-pic { width:120px; height:120px; border-radius:50%; object-fit:cover; border:3px solid #ddd; }
    .upload-box { display:flex; gap:10px; align-items:center; margin-top:10px; }
    .post { border:1px solid #ddd; padding:12px; border-radius:8px; margin-bottom:10px; max-width:80%; word-wrap:break-word; }
    </style>
    
    </head>
<body>

<div class="profile-header">
    <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Profile" class="profile-pic">
    <div>
        <h1>Profile of <?php echo $userName; ?></h1>
        <?php if ($uploadMessage): ?>
            <p style="color:green"><?php echo htmlspecialchars($uploadMessage); ?></p>
        <?php endif; ?>
        
        <form action="viewProfile.php?userID=<?php echo $userID; ?>" method="post" enctype="multipart/form-data" class="upload-box">
            <input type="file" name="image" accept="image/*" required>
            <button type="submit" name="uploadBtn">Upload</button>
        </form>
    </div>
</div>

<h2>Your Posts</h2>
<?php

if ($postRes && mysqli_num_rows($postRes) > 0) {
    while ($p = mysqli_fetch_assoc($postRes)) {
        echo "<div class='post'>";
        echo "<p>" . nl2br(htmlspecialchars($p['userPost'])) . "</p>";
        echo "<small>Posted: " . $p['posted_At'] . "</small>";
        echo "</div>";
    }
} else {
    echo "<p>No posts yet.</p>";
}
?>
<form action="viewProfile.php?userID=<?php echo $_SESSION['userID']?>" method="POST">

<button type="submit" name="backBtn">Go back</button></a>

</form>


</body>
</html>

<?php
if(isset($_POST["backBtn"])){
    header("Location:userTimeline.php");
}
mysqli_close($conn);
?>
