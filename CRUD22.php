<?php
// Database configuration
$host = 'localhost';
$dbname = 'crud_db';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Create Operation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_user"])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    if (!empty($name) && is_numeric($age)) {
        $query = "INSERT INTO users (name, age) VALUES ('$name', $age)";
        $conn->query($query);
    }
}

// Handle Update Operation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_user"])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    if (!empty($name) && is_numeric($age)) {
        $query = "UPDATE users SET name='$name', age=$age WHERE id=$id";
        $conn->query($query);
    }
}

// Handle Delete Operation
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM users WHERE id = $id";
    $conn->query($query);
}

// Fetch users
$result = $conn->query("SELECT * FROM users");
$users = $result->fetch_all(MYSQLI_ASSOC);
$result->free();

// Get user for editing
$editUser = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editResult = $conn->query("SELECT * FROM users WHERE id = $id");
    $editUser = $editResult->fetch_assoc();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP CRUD with Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
        }
        form {
            margin-bottom: 20px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        canvas {
            display: block;
            margin: auto;
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h2>User Management</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $editUser['id'] ?? '' ?>">
        <input type="text" name="name" placeholder="User Name" value="<?= $editUser['name'] ?? '' ?>" required>
        <input type="number" name="age" placeholder="Age" value="<?= $editUser['age'] ?? '' ?>" required>
        <?php if ($editUser): ?>
            <button type="submit" name="update_user">Update User</button>
            <a href="index.php"><button type="button">Cancel</button></a>
        <?php else: ?>
            <button type="submit" name="add_user">Add User</button>
        <?php endif; ?>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= $user['age'] ?></td>
                <td>
                    <a href="?edit=<?= $user['id'] ?>">Edit</a> |
                    <a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <canvas id="chart"></canvas>
    <script>
        const ctx = document.getElementById('chart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($users, 'name')) ?>,
                datasets: [{
                    label: 'User Age',
                    data: <?= json_encode(array_column($users, 'age')) ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>