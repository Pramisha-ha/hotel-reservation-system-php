<?php
include '../config.php';
session_start();

$usermail = $_SESSION['usermail'] ?? '';
if($usermail == true){
} else {
    header("location: ../index.php");
    exit();
}

$user_sql = "SELECT * FROM signup WHERE Email = '$usermail'";
$user_result = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_array($user_result);

$message = ""; // To store our custom notification message
$msg_type = ""; // To store 'success' or 'error'

if(isset($_POST['update_profile'])) {
    $username = mysqli_real_escape_string($conn, $_POST['Username']);
    $phone = mysqli_real_escape_string($conn, $_POST['Phone']);
    $address = mysqli_real_escape_string($conn, $_POST['Address']);
    
    $update_sql = "UPDATE signup SET Username = '$username', Phone = '$phone', Address = '$address' WHERE Email = '$usermail'";
    $update_result = mysqli_query($conn, $update_sql);
    
    if($update_result) {
        $message = "Profile updated successfully!";
        $msg_type = "success";
        echo "<script>
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 2000);
        </script>";
    } else {
        $message = "Error: Could not update profile.";
        $msg_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - MomentsAway</title>
    <style>
        /* Custom UI Styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .profile-card {
            background: #ffffff;
            width: 100%;
            max-width: 450px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h2 {
            margin-top: 0;
            color: #333;
            text-align: center;
            border-bottom: 2px solid #4a90e2;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
            font-size: 14px;
        }

        .input-field {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; /* Ensures padding doesn't break layout */
            font-size: 15px;
        }

        .input-field:focus {
            border-color: #4a90e2;
            outline: none;
        }

        .input-field:disabled {
            background-color: #eee;
            cursor: not-allowed;
        }

        .btn-container {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
        }

        .btn-save {
            background-color: #4a90e2;
            color: white;
        }

        .btn-cancel {
            background-color: #999;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* Pure CSS Notification Toast */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            color: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
        }

        .success { background-color: #4CAF50; }
        .error { background-color: #f44336; }

        @keyframes slideIn {
            from { right: -300px; opacity: 0; }
            to { right: 20px; opacity: 1; }
        }
    </style>
</head>
<body>

    <?php if($message != ""): ?>
        <div class="notification <?php echo $msg_type; ?>" id="toast">
            <?php echo $message; ?>
        </div>
        <script>
            // Simple JS to hide the notification after 3 seconds
            setTimeout(() => {
                document.getElementById('toast').style.display = 'none';
            }, 3000);
        </script>
    <?php endif; ?>

    <div class="profile-card">
        <h2>Edit Profile</h2>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" class="input-field" name="Username" value="<?php echo htmlspecialchars($user['Username']); ?>" required>
            </div>

            <div class="form-group">
                <label>Email (Permanent)</label>
                <input type="email" class="input-field" value="<?php echo htmlspecialchars($user['Email']); ?>" disabled>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" class="input-field" name="Phone" value="<?php echo htmlspecialchars($user['Phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea class="input-field" name="Address" rows="3"><?php echo htmlspecialchars($user['Address'] ?? ''); ?></textarea>
            </div>

            <div class="btn-container">
                <button type="submit" name="update_profile" class="btn btn-save">Update Profile</button>
                <a href="dashboard.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

</body>
</html>