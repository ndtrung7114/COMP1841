<?php
// UPDATE feature: edit existing data from the database
if (isset($_POST['edit_profile'])) {
    try {
        include "../connect.php";
        require "../function.php";
$valid = true;
        // Validate file upload
$target_dir = "../images/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $valid = false;
    }
}



// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $valid = false;
}

// Allow certain file formats
if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $valid = false;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
    $valid = false;
}

// If any validation failed, stop script execution
if (!$valid) {
    exit();
}

// If everything is valid, move the uploaded file to the desired directory
if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.";
} else {
    echo "Sorry, there was an error uploading your file.";
    exit();
}
        
        $sql = "UPDATE user
                SET name = :name,
                country = :country,
                email = :email,
                avatar = :avatar,
                user_name = :user_name,
                password = :password
                WHERE user_id = :user_id";
        $stm = $connect->prepare($sql);
        $stm->bindValue(":name", $_POST['name']);
        $stm->bindValue(":country", $_POST['country']);
        $stm->bindValue(":email", $_POST['email']);
        $stm->bindValue(":user_name", $_POST['user_name']);
        $stm->bindValue(":user_id", $_POST['user_id']);
        $stm->bindValue(":avatar", $_FILES["fileToUpload"]["name"]);
        $stm->bindValue(":password", $_POST['password']);

        $stm->execute();
        header("location: user.php");
    } catch (PDOException $exception) {
        echo "Connect to DB failed" . $exception;
    }
} else {
    try {
        include "../connect.php";
        require "../function.php";
        $user_id = $_POST['user_id']; 
        $sql = "SELECT * FROM user WHERE user_id = :user_id";
        $stm = $connect->prepare($sql);
        $stm->bindValue(":user_id", $user_id);
        $stm->execute();
        
        $profile = $stm->fetch();
        $title = 'Edit profile user';
        ob_start();
        include  '../pages_admin/edit_profile.html.php';
        $output = ob_get_clean();

        
    } catch (PDOException $e) {
        $title = 'An error has occurred';
        $output = 'Database error: ' . $e->getMessage();
    }
}
include '../pages_admin/layout.html.php';
?>