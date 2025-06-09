<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "todolistdb";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
if (!function_exists('log_action')) {
    function log_action($conn, $userId, $action) {
        $stmt = $conn->prepare("INSERT INTO logs (user_id, action, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $userId, $action);
        $stmt->execute();
        $stmt->close();
    }
}

?>