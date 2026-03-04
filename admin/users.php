<?php
session_start();
include '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/common.css">
    <script src="../javascript/common.js"></script>
    <title>Manage Users - Admin</title>
    <style>
        body { 
            padding: 20px;
            box-sizing: border-box;
        }
        
        @media(max-width: 768px){
            body {
                padding: 15px;
            }
            
            .table {
                font-size: 12px;
                display: block;
                overflow-x: auto;
            }
            
            .table th,
            .table td {
                padding: 8px 4px;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <h2>Manage Users</h2>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM signup ORDER BY UserID DESC";
            $result = mysqli_query($conn, $sql);
            
            while($user = mysqli_fetch_array($result)) {
            ?>
            <tr>
                <td><?php echo $user['UserID']; ?></td>
                <td><?php echo htmlspecialchars($user['Username']); ?></td>
                <td><?php echo htmlspecialchars($user['Email']); ?></td>
                <td><?php echo htmlspecialchars($user['Phone'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($user['Address'] ?? 'N/A'); ?></td>
                <td>
                    <a href="?delete=<?php echo $user['UserID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php
            }
            
            if(isset($_GET['delete'])) {
                $id = intval($_GET['delete']);
                $sql = "DELETE FROM signup WHERE UserID = $id";
                mysqli_query($conn, $sql);
                echo "<script>location.reload();</script>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

