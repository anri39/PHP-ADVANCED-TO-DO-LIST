<?php
session_start();
include 'database.php';

if (!isset($_POST['username'], $_POST['password'])) {
    echo "Please enter username and password.";
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    if (password_verify($password, $row['Password'])) {
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $row['ID'];     
        $_SESSION['role'] = $row['role'];

        $action = "User logged in";
        $log_sql = "INSERT INTO logs (user_id, action) VALUES (?, ?)";
        $log_stmt = mysqli_prepare($conn, $log_sql);
        mysqli_stmt_bind_param($log_stmt, 'is', $row['ID'], $action); 
        mysqli_stmt_execute($log_stmt);
        mysqli_stmt_close($log_stmt);

        header("Location: index.php");
        exit();
    } else {
        echo "Incorrect password. Redirecting in 3 seconds...";
        echo '<meta http-equiv="refresh" content="3;url=login.php">';
        exit();
    }
} else {
    echo "No user found. Redirecting in 3 seconds...";
    echo '<meta http-equiv="refresh" content="3;url=login.php">';
    exit();
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
