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

$bookings_sql = "SELECT rb.*, h.hotel_name, h.hotel_image, hr.room_type, hr.bedding_type 
                  FROM roombook rb 
                  LEFT JOIN hotels h ON rb.hotel_id = h.hotel_id 
                  LEFT JOIN hotel_rooms hr ON rb.room_id = hr.room_id 
                  WHERE rb.Email = '$usermail' 
                  ORDER BY rb.id DESC";
$bookings_result = mysqli_query($conn, $bookings_sql);

$cancellations_sql = "SELECT c.*, rb.hotel_id, h.hotel_name, rb.RoomType, rb.cin, rb.cout 
                      FROM cancellations c 
                      INNER JOIN roombook rb ON c.booking_id = rb.id 
                      LEFT JOIN hotels h ON rb.hotel_id = h.hotel_id 
                      WHERE c.user_id = " . intval($user['UserID']) . " 
                      ORDER BY c.cancellation_date DESC";
$cancellations_result = mysqli_query($conn, $cancellations_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/common.css">
    <script src="../javascript/common.js"></script>
    <title>Customer Dashboard - MomentsAway</title>
    <style>
        body {
            background: #f5f5f5;
            padding-top: 80px;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
        }
        
        @media(max-width: 768px){
            body {
                padding-top: 60px;
            }
            
            .dashboard-container {
                padding: 15px;
            }
            
            .dashboard-header {
                padding: 20px;
            }
            
            .profile-section {
                flex-direction: column;
                gap: 20px;
            }
            
            .profile-image {
                width: 120px;
                height: 120px;
            }
            
            .booking-card {
                padding: 15px;
            }
            
            .hotel-image-small {
                width: 80px;
                height: 80px;
            }
            
            .tab-content {
                padding: 20px;
            }
        }
        .dashboard-header {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
        }
        .nav-tabs .nav-link {
            color: #495057;
            border: none;
            border-bottom: 3px solid transparent;
        }
        .nav-tabs .nav-link.active {
            color: #0040ff;
            border-bottom-color: #0040ff;
            background: transparent;
        }
        .tab-content {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .profile-section {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #0040ff;
        }
        .booking-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .booking-status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-confirm {
            background: #d4edda;
            color: #155724;
        }
        .status-notconfirm {
            background: #fff3cd;
            color: #856404;
        }
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        .hotel-image-small {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="../home.php">
                <img src="../image/momentsaway.png" height="40" class="d-inline-block align-top" alt="">
                MomentsAway
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../home.php">Home</a>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($user['Username']); ?>!</h1>
            <p class="text-muted">Manage your bookings and profile</p>
        </div>

        <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-target="profile" type="button">Profile</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="bookings-tab" data-target="bookings" type="button">My Bookings</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cancellations-tab" data-target="cancellations" type="button">Cancellations</button>
            </li>
        </ul>

        <div class="tab-content" id="dashboardTabsContent">
            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                <h2>My Profile</h2>
                <div class="profile-section">
                    <img src="<?php echo $user['ProfileImage'] ? '../' . htmlspecialchars($user['ProfileImage']) : '../image/Profile.png'; ?>" alt="Profile" class="profile-image">
                    <div class="profile-info">
                        <h3><?php echo htmlspecialchars($user['Username']); ?></h3>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['Phone'] ?? 'Not provided'); ?></p>
                        <?php if($user['Address']): ?>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($user['Address']); ?></p>
                        <?php endif; ?>
                        <button class="btn btn-primary mt-3" onclick="editProfile()">Edit Profile</button>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="bookings" role="tabpanel">
                <h2>My Bookings</h2>
                <?php
                if(mysqli_num_rows($bookings_result) > 0) {
                    while($booking = mysqli_fetch_array($bookings_result)) {
                        $status_class = '';
                        if($booking['stat'] == 'Confirm') $status_class = 'status-confirm';
                        else if($booking['stat'] == 'NotConfirm') $status_class = 'status-notconfirm';
                        else if($booking['stat'] == 'Cancelled') $status_class = 'status-cancelled';
                ?>
                <div class="booking-card">
                    <div class="row">
                        <div class="col-md-2">
                            <?php if($booking['hotel_image']): ?>
                            <img src="../<?php echo htmlspecialchars($booking['hotel_image']); ?>" alt="Hotel" class="hotel-image-small">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <h4><?php echo htmlspecialchars($booking['hotel_name'] ?? 'Hotel'); ?></h4>
                            <p><strong>Room:</strong> <?php echo htmlspecialchars($booking['RoomType']); ?> - <?php echo htmlspecialchars($booking['Bed']); ?></p>
                            <p><strong>Check-in:</strong> <?php echo date('M d, Y', strtotime($booking['cin'])); ?></p>
                            <p><strong>Check-out:</strong> <?php echo date('M d, Y', strtotime($booking['cout'])); ?></p>
                            <p><strong>Duration:</strong> <?php echo $booking['nodays']; ?> night(s)</p>
                            <p><strong>Rooms:</strong> <?php echo $booking['NoofRoom']; ?></p>
                            <p><strong>Meal:</strong> <?php echo htmlspecialchars($booking['Meal']); ?></p>
                            <?php if($booking['total_price'] > 0): ?>
                            <p><strong>Total Price:</strong> Rs<?php echo number_format($booking['total_price'], 2); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-2 text-end">
                            <span class="booking-status <?php echo $status_class; ?>"><?php echo htmlspecialchars($booking['stat']); ?></span>
                            <?php if($booking['stat'] == 'Confirm' || $booking['stat'] == 'NotConfirm'): ?>
                            <br><br>
                            <button class="btn btn-danger btn-sm" onclick="cancelBooking(<?php echo $booking['id']; ?>)">Cancel</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo "<p>No bookings found.</p>";
                }
                ?>
            </div>

            <div class="tab-pane fade" id="cancellations" role="tabpanel">
                <h2>Booking Cancellations</h2>
                <?php
                if(mysqli_num_rows($cancellations_result) > 0) {
                    while($cancellation = mysqli_fetch_array($cancellations_result)) {
                        $status_class = '';
                        if($cancellation['status'] == 'approved') $status_class = 'status-confirm';
                        else if($cancellation['status'] == 'pending') $status_class = 'status-notconfirm';
                        else if($cancellation['status'] == 'rejected') $status_class = 'status-cancelled';
                ?>
                <div class="booking-card">
                    <h4><?php echo htmlspecialchars($cancellation['hotel_name'] ?? 'Hotel'); ?></h4>
                    <p><strong>Room Type:</strong> <?php echo htmlspecialchars($cancellation['RoomType']); ?></p>
                    <p><strong>Original Dates:</strong> <?php echo date('M d, Y', strtotime($cancellation['cin'])); ?> - <?php echo date('M d, Y', strtotime($cancellation['cout'])); ?></p>
                    <p><strong>Cancellation Date:</strong> <?php echo date('M d, Y H:i', strtotime($cancellation['cancellation_date'])); ?></p>
                    <?php if($cancellation['cancellation_reason']): ?>
                    <p><strong>Reason:</strong> <?php echo htmlspecialchars($cancellation['cancellation_reason']); ?></p>
                    <?php endif; ?>
                    <?php if($cancellation['refund_amount'] > 0): ?>
                    <p><strong>Refund Amount:</strong> Rs<?php echo number_format($cancellation['refund_amount'], 2); ?></p>
                    <?php endif; ?>
                    <span class="booking-status <?php echo $status_class; ?>"><?php echo ucfirst($cancellation['status']); ?></span>
                </div>
                <?php
                    }
                } else {
                    echo "<p>No cancellations found.</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        function initTabs() {
            const tabButtons = document.querySelectorAll('.nav-link');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const targetId = button.getAttribute('data-target');
                    
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabPanes.forEach(pane => pane.classList.remove('active', 'show'));
                    
                    button.classList.add('active');
                    const targetPane = document.getElementById(targetId);
                    if(targetPane) {
                        targetPane.classList.add('active', 'show');
                    }
                });
            });
        }
        
        document.addEventListener('DOMContentLoaded', initTabs);
        
        function editProfile() {
            window.location.href = 'edit_profile.php';
        }

        function cancelBooking(bookingId) {
            if(confirm('Are you sure you want to cancel this booking?')) {
                window.location.href = 'cancel_booking.php?id=' + bookingId;
            }
        }
    </script>
</body>
</html>

