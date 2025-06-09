<?php
session_start();
include 'database.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$currentUser = $_SESSION['username'];

$stmt = $conn->prepare("SELECT id, role FROM users WHERE username = ?");
$stmt->bind_param("s", $currentUser);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    session_destroy();
    header("Location: login.html");
    exit();
}
$userData = $result->fetch_assoc();
if ($userData['role'] !== 'admin') {
    echo "Access denied. You are not an admin.";
    exit();
}
$stmt->close();

$sql = "
  SELECT tasks.ID, tasks.Title, users.username, tasks.Status, tasks.Priority, tasks.category
  FROM tasks
  JOIN users ON tasks.user_ID = users.id
  ORDER BY tasks.ID DESC
  LIMIT 200
";

$tasksResult = $conn->query($sql);

if (!$tasksResult) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin - All Tasks</title>
<style>
  * {
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  body {
    background: #f4f6f8;
    margin: 0;
    padding: 0;
    color: #2c3e50;
    display: flex;
  }

  .sidebar {
    height: 100vh;
    width: 250px;
    position: fixed;
    left: -250px;
    top: 0;
    background: #3498db;
    transition: left 0.3s ease;
    padding-top: 60px;
    z-index: 1100;
    display: flex;
    flex-direction: column;
    gap: 15px;
  }

  .sidebar.active {
    left: 0;
  }

  .sidebar a {
    padding: 15px 30px;
    text-decoration: none;
    font-size: 18px;
    color: #fff;
    display: block;
    transition: background 0.3s;
  }

  .sidebar a:hover {
    background: #2980b9;
  }

  .sidebar a.active {
    background: #1f618d;
    font-weight: bold;
  }

  .sidebar .close-btn {
    position: absolute;
    top: 12px;
    right: 18px;
    font-size: 30px;
    color: white;
    cursor: pointer;
    user-select: none;
    z-index: 1201;
  }

  .main-content {
    margin: 80px auto 40px auto;
    max-width: 1000px;
    padding: 30px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    flex-grow: 1;
    overflow-x: auto;
  }

  .header {
    background: #3498db;
    color: #fff;
    padding: 18px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
    position: fixed;
    top: 0;
    width: 100%;
    left: 0;
    z-index: 1200;
  }

  .header h2 {
    margin: 0;
    font-weight: bold;
    font-size: 28px;
  }

  .logout-btn {
    background: #fff;
    color: #3498db;
    border: none;
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(52, 152, 219, 0.4);
    transition: background-color 0.25s ease;
  }

  .logout-btn:hover {
    background: #e1e7ee;
  }

  .burger {
    font-size: 28px;
    cursor: pointer;
    color: #fff;
    margin-right: 20px;
    user-select: none;
    z-index: 1300;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
    border-radius: 8px;
    overflow: hidden;
  }

  th, td {
    padding: 14px 16px;
    text-align: left;
  }

  th {
    background-color: #3498db;
    color: white;
  }

  tr {
    background-color: #fdfdfd;
    transition: background 0.2s ease-in-out;
  }

  tr:hover {
    background-color: #f1f9ff;
  }

  tr:nth-child(even) {
    background-color: #f9f9f9;
  }

  button.delete-btn {
    background: #e74c3c;
    color: #fff;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
  }

  button.delete-btn:hover {
    background: #c0392b;
  }
     .sidebar a:first-of-type {
  margin-top: 15px;
}
</style>
</head>
<body>

<div class="sidebar" id="sidebar">
  <span class="close-btn" id="closeBtn">&times;</span>
  <a href="index.php">Dashboard</a>
  <a href="admin.php">Users</a>
  <a href="logs.php">Logs</a>
  <a href="admin_tasks.php" class="active">All Tasks</a>
</div>

<div class="main-content" id="mainContent">
  <div class="header">
    <span class="burger" id="burger">&#9776;</span>
    <h2>Admin - All Tasks</h2>
    <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>User</th>
        <th>Status</th>
        <th>Priority</th>
        <th>Category</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($task = $tasksResult->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($task['ID']); ?></td>
        <td><?php echo htmlspecialchars($task['Title']); ?></td>
        <td><?php echo htmlspecialchars($task['username']); ?></td>
        <td><?php echo htmlspecialchars($task['Status']); ?></td>
        <td><?php echo htmlspecialchars($task['Priority']); ?></td>
        <td><?php echo htmlspecialchars($task['category']); ?></td>
        <td>
          <form method="POST" action="deleteforadmin.php" onsubmit="return confirm('Are you sure you want to delete this task?');" style="display:inline;">
            <input type="hidden" name="task_id" value="<?php echo $task['ID']; ?>">
            <button type="submit" class="delete-btn">Delete</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<script>
  const burger = document.getElementById('burger');
  const sidebar = document.getElementById('sidebar');
  const closeBtn = document.getElementById('closeBtn');

  burger.addEventListener('click', () => {
    sidebar.classList.toggle('active');
  });

  closeBtn.addEventListener('click', () => {
    sidebar.classList.remove('active');
  });
</script>

</body>
</html>

<?php $conn->close(); ?>
