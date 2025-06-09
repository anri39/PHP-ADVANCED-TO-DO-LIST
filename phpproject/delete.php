<?php
session_start();
include 'database.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo "User not logged in.";
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT Title, user_id FROM tasks WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo "Task not found.";
        exit();
    }
    $task = $result->fetch_assoc();
    $stmt->close();

    if ($task['user_id'] != $user_id) {
        echo "You do not have permission to delete this task.";
        exit();
    }

    $taskTitle = $task['Title'];

    $delStmt = $conn->prepare("DELETE FROM tasks WHERE ID = ?");
    $delStmt->bind_param("i", $id);
    if ($delStmt->execute()) {
        $action = "Deleted task: " . $taskTitle;
        $logStmt = $conn->prepare("INSERT INTO logs (user_id, action, created_at) VALUES (?, ?, NOW())");
        $logStmt->bind_param("is", $user_id, $action);
        $logStmt->execute();
        $logStmt->close();
    } else {
        echo "Error deleting task: " . $delStmt->error;
        exit();
    }
    $delStmt->close();
}

$conn->close();
header("Location: index.php"); 
exit();
?>
