<?php
include 'config.php';
session_start();


// Update this with your admin email
$admin_email = "Admin@gmail.com"; 
$password = "1234";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "UPDATE emp_login SET Emp_Password = ? WHERE Emp_Email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashed_password, $admin_email);

function prepareAndExecute($conn, $sql, $params)
{
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('mysqli error: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    return $stmt;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/login.css">
    <link rel="stylesheet" href="./css/common.css">
    <link rel="stylesheet" href="./css/flash.css">
    <script src="./javascript/common.js"></script>
    <title>MomentsAway</title>
</head>

<body >
    <!-- Main Section -->
    <section id="auth_section">
        <div class="logo">
            <img class="bluebirdlogo" src="./image/momentsawaylogo.png" alt="logo">
            <p>MomentsAway</p>
        </div>
        <div class="auth_container">
            <!-- Login -->
            <div id="Log_in">
                <h2>Log In</h2>
                <div class="role_btn" >
                    <div class="btns active">User</div>
                    <div class="btns">Admin</div>
                </div>

                <!-- User Login -->
                <?php
                if (isset($_POST['user_login_submit'])) {
                    $email = $_POST['Email'];
                    $password = $_POST['Password'];
                    $sql = "SELECT * FROM signup WHERE Email = ?";
                    $stmt = prepareAndExecute($conn, $sql, [$email]);
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $user = $result->fetch_assoc();
                        // Verify password using password_verify for hashed passwords
                        if (password_verify($password, $user['Password'])) {
                            $_SESSION['usermail'] = $email;
                            header("Location: home.php");
                            exit();
                        } else {
                            echo "<script>showAlert('Error', 'Something went wrong', 'error');</script>";
                        }
                    } else {
                        echo "<script>showAlert('Error', 'Something went wrong', 'error');</script>";
                    }
                }
                ?>
                <form class="user_login authsection active" id="userlogin" action="" method="POST">
                    <div class="form-floating">
                        <input type="email" class="form-control" name="Email" placeholder=" ">
                        <label for="Email">Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="Password" placeholder=" ">
                        <label for="Password">Password</label>
                    </div>
                    <button type="submit" name="user_login_submit" class="auth_btn">Log in</button>
                    <div class="footer_line">
                        <h6>Don't have an account? <span class="page_move_btn" onclick="signuppage()">sign up</span></h6>
                    </div>
                </form>

                <!-- Employee Login -->
                <?php
                if (isset($_POST['Emp_login_submit'])) {
                    $email = $_POST['Emp_Email'];
                    $password = $_POST['Emp_Password'];
                    $sql = "SELECT * FROM emp_login WHERE Emp_Email = ?";
                    $stmt = prepareAndExecute($conn, $sql, [$email]);
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $emp = $result->fetch_assoc();
                        // Verify password using password_verify for hashed passwords
                        if (password_verify($password, $emp['Emp_Password'])) {
                            $_SESSION['usermail'] = $email;
                            header("Location: admin/admin.php");
                            exit();
                        } else {
                            echo "<script>showAlert('Error', 'Something went wrong', 'error');</script>";
                        }
                    } else {
                        echo "<script>showAlert('Error', 'Something went wrong', 'error');</script>";
                    }
                }
                ?>
                <form class="employee_login authsection" id="employeelogin" action="" method="POST">
                    <div class="form-floating">
                        <input type="email" class="form-control" name="Emp_Email" placeholder=" ">
                        <label for="floatingInput">Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="Emp_Password" placeholder=" ">
                        <label for="floatingPassword">Password</label>
                    </div>
                    <button type="submit" name="Emp_login_submit" class="auth_btn">Log in</button>
                </form>
            </div>

            <!-- Sign Up -->
            <?php
            if (isset($_POST['user_signup_submit'])) {
                $username = $_POST['Username'];
                $email = $_POST['Email'];
                $password = $_POST['Password'];
                $cpassword = $_POST['CPassword'];

                if ($username == "" || $email == "" || $password == "") {
                    echo "<script>showAlert('Error', 'Fill the proper details', 'error');</script>";
                } else {
                    if ($password == $cpassword) {
                        $sql_check = "SELECT * FROM signup WHERE Email = ?";
                        $stmt_check = prepareAndExecute($conn, $sql_check, [$email]);
                        $result = $stmt_check->get_result();

                        if ($result->num_rows > 0) {
                            echo "<script>showAlert('Error', 'Email already exists', 'error');</script>";
                        } else {
                            // Hash the password before storing
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            $sql_insert = "INSERT INTO signup (Username, Email, Password) VALUES (?, ?, ?)";
                            $stmt_insert = prepareAndExecute($conn, $sql_insert, [$username, $email, $hashed_password]);

                            if ($stmt_insert->affected_rows > 0) {
                                $_SESSION['usermail'] = $email;
                                header("Location: home.php");
                                exit();
                            } else {
                                echo "<script>showAlert('Error', 'Something went wrong', 'error');</script>";
                            }
                        }
                    } else {
                        echo "<script>showAlert('Error', 'Password does not match', 'error');</script>";
                    }
                }
            }
            ?>
            <div id="sign_up">
                <h2>Sign Up</h2>
                <form class="user_signup" id="usersignup" action="" method="POST">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="Username" placeholder=" ">
                        <label for="Username">Username</label>
                    </div>
                    <div class="form-floating">
                        <input type="email" class="form-control" name="Email" placeholder=" ">
                        <label for="Email">Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="Password" placeholder=" ">
                        <label for="Password">Password</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="CPassword" placeholder=" ">
                        <label for="CPassword">Confirm Password</label>
                    </div>
                    <button type="submit" name="user_signup_submit" class="auth_btn">Sign up</button>
                    <div class="footer_line">
                        <h6>Already have an account? <span class="page_move_btn" onclick="loginpage()">Log in</span></h6>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script src="./javascript/index.js"></script>
</body>

</html>