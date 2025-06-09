<?php
session_start();             

include 'database.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo "User not logged in.";
    exit();
}

$title = $_POST['taskName'] ?? '';
$priority = $_POST['priority'] ?? '';
$category = $_POST['category'] ?? '';
$status = $_POST['status'] ?? 'Pending';

$sql = "INSERT INTO tasks (Title, Priority, category, Status, user_id) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $title, $priority, $category, $status, $user_id);

if ($stmt->execute()) {

    $action = "Added task: " . $title;
    $logStmt = $conn->prepare("INSERT INTO logs (user_id, action, created_at) VALUES (?, ?, NOW())");
    $logStmt->bind_param("is", $user_id, $action);
    $logStmt->execute();
    $logStmt->close();

    header("Location: index.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
?>
