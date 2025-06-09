<?php
session_start();
include 'database.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo "User not logged in.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $status = trim($_POST['status']);
    $priority = trim($_POST['priority']);
    $category = trim($_POST['category']);

    $oldStmt = $conn->prepare("SELECT Title, Status FROM tasks WHERE ID = ?");
    $oldStmt->bind_param("i", $id);
    $oldStmt->execute();
    $oldResult = $oldStmt->get_result();
    if ($oldResult->num_rows === 0) {
        echo "Task not found.";
        exit();
    }
    $oldTask = $oldResult->fetch_assoc();
    $oldStmt->close();

    $stmt = $conn->prepare("UPDATE tasks SET Title = ?, Status = ?, Priority = ?, category = ? WHERE ID = ?");
    $stmt->bind_param("ssssi", $title, $status, $priority, $category, $id);

    if ($stmt->execute()) {
        if ($oldTask['Status'] !== $status && $status === 'Completed') {
            $action = "Marked task '{$title}' as Completed";
        } else {
            $action = "Updated task '{$title}'";
        }

        $logStmt = $conn->prepare("INSERT INTO logs (user_id, action, created_at) VALUES (?, ?, NOW())");
        $logStmt->bind_param("is", $user_id, $action);
        $logStmt->execute();
        $logStmt->close();

        $stmt->close();
        $conn->close();

        header("Location: index.php");
        exit();
    } else {
        echo "Error updating task: " . $stmt->error;
        exit();
    }
}

if (!isset($_GET['id'])) {
    echo "No task selected.";
    exit();
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM tasks WHERE ID = $id");

if (!$result || $result->num_rows === 0) {
    echo "Task not found.";
    exit();
}

$task = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Task</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f6f8;
      margin: 0;
      padding: 0;
    }

    .header {
      background-color: #3498db;
      color: white;
      padding: 20px;
      text-align: center;
      font-size: 24px;
      font-weight: bold;
    }

    .form-container {
      max-width: 500px;
      margin: 40px auto;
      background-color: #ffffff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h1.edit {
      text-align: center;
      color: #3498db;
      margin-bottom: 30px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
      color: #2c3e50;
    }

    input[type="text"],
    select {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
      box-sizing: border-box;
    }

    button {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
    }

    button:hover {
      background-color: #2980b9;
    }

    .back-link {
      text-align: center;
      margin-top: 20px;
    }

    .back-link a {
      color: #3498db;
      text-decoration: none;
      font-weight: bold;
    }

    .back-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="header">To-Do LIST</div>

<div class="form-container">
  <h1 class="edit">Edit Task</h1>

  <form action="edit.php" method="post">
    <input type="hidden" name="id" value="<?= htmlspecialchars($task['ID']) ?>" />

    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="<?= htmlspecialchars($task['Title']) ?>" required />

    <label for="status">Status</label>
    <select name="status" id="status" required>
      <option value="Pending" <?= $task['Status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
      <option value="Completed" <?= $task['Status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
    </select>

    <label for="priority">Priority</label>
    <select name="priority" id="priority" required>
      <option value="Low" <?= $task['Priority'] === 'Low' ? 'selected' : '' ?>>Low</option>
      <option value="Medium" <?= $task['Priority'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
      <option value="High" <?= $task['Priority'] === 'High' ? 'selected' : '' ?>>High</option>
    </select>

    <label for="category">Category</label>
    <input type="text" id="category" name="category" value="<?= htmlspecialchars($task['category']) ?>" required />

    <button type="submit">Update Task</button>
  </form>

  <div class="back-link">
    <a href="index.php">Back to Dashboard</a>
  </div>
</div>

</body>
</html>
