<?php
include '../config.php';
session_start();

$usermail = $_SESSION['usermail'] ?? '';
if($usermail == true){
} else {
    header("location: ../index.php");
    exit();
}

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($booking_id == 0) {
    header("location: dashboard.php");
    exit();
}

$user_sql = "SELECT UserID FROM signup WHERE Email = '$usermail'";
$user_result = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_array($user_result);

$booking_sql = "SELECT * FROM roombook WHERE id = $booking_id AND Email = '$usermail'";
$booking_result = mysqli_query($conn, $booking_sql);
$booking = mysqli_fetch_array($booking_result);

if(!$booking) {
    header("location: dashboard.php");
    exit();
}

if(isset($_POST['cancel_submit'])) {
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $refund_amount = $booking['total_price'] * 0.8;
    
    $cancel_sql = "INSERT INTO cancellations (booking_id, user_id, cancellation_reason, refund_amount, status) 
                   VALUES ($booking_id, " . intval($user['UserID']) . ", '$reason', $refund_amount, 'pending')";
    $cancel_result = mysqli_query($conn, $cancel_sql);
    
    if($cancel_result) {
        $update_sql = "UPDATE roombook SET stat = 'Cancelled' WHERE id = $booking_id";
        mysqli_query($conn, $update_sql);
        
        echo "<script>
            showAlert('Cancellation Requested', 'Your cancellation request has been submitted. Refund will be processed after approval.', 'success');
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 2000);
        </script>";
    } else {
        echo "<script>
            showAlert('Error', 'Something went wrong. Please try again.', 'error');
            setTimeout(() => {
                window.history.back();
            }, 2000);
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/common.css">
    <script src="../javascript/common.js"></script>
    <title>Cancel Booking - MomentsAway</title>
    <style>
        @media(max-width: 768px){
            .container {
                padding: 15px;
            }
            
            .card {
                margin: 20px 0;
            }
            
            .card-body {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Cancel Booking</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Booking ID:</strong> <?php echo $booking['id']; ?></p>
                        <p><strong>Room Type:</strong> <?php echo htmlspecialchars($booking['RoomType']); ?></p>
                        <p><strong>Check-in:</strong> <?php echo date('M d, Y', strtotime($booking['cin'])); ?></p>
                        <p><strong>Check-out:</strong> <?php echo date('M d, Y', strtotime($booking['cout'])); ?></p>
                        <?php if($booking['total_price'] > 0): ?>
                        <p><strong>Total Price:</strong> Rs <?php echo number_format($booking['total_price'], 2); ?></p>
                        <p><strong>Estimated Refund (80%):</strong> Rs <?php echo number_format($booking['total_price'] * 0.8, 2); ?></p>
                        <?php endif; ?>
                        
                        <form method="POST" class="mt-4">
                            <div class="mb-3">
                                <label class="form-label">Cancellation Reason</label>
                                <textarea class="form-control" name="reason" rows="4" required></textarea>
                            </div>
                            <button type="submit" name="cancel_submit" class="btn btn-danger">Confirm Cancellation</button>
                            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

