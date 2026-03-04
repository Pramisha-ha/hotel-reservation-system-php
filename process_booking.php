<?php
include 'config.php';
session_start();

$usermail = $_SESSION['usermail'] ?? '';
if($usermail == false){
    header("location: index.php");
    exit();
}

if(isset($_POST['book_submit'])) {
    $hotel_id = intval($_POST['hotel_id']);
    $room_id = intval($_POST['room_id']);
    $Name = mysqli_real_escape_string($conn, $_POST['Name']);
    $Email = mysqli_real_escape_string($conn, $_POST['Email']);
    $Phone = mysqli_real_escape_string($conn, $_POST['Phone']);
    $RoomType = mysqli_real_escape_string($conn, $_POST['room_type']);
    $Bed = mysqli_real_escape_string($conn, $_POST['bedding_type']);
    $NoofRoom = intval($_POST['NoofRoom']);
    $Meal = mysqli_real_escape_string($conn, $_POST['Meal']);
    $cin = $_POST['cin'];
    $cout = $_POST['cout'];
    
    $errors = [];
    
    if(empty($Name) || empty($Email) || empty($Phone)) {
        $errors[] = "All required fields must be filled";
    }
    
    if(empty($cin) || empty($cout)) {
        $errors[] = "Check-in and check-out dates are required";
    }
    
    if(!empty($cin) && !empty($cout)) {
        $checkinDate = new DateTime($cin);
        $checkoutDate = new DateTime($cout);
        $today = new DateTime();
        $today->setTime(0,0,0);
        
        if($checkinDate < $today) {
            $errors[] = "Check-in date cannot be in the past";
        }
        
        if($checkoutDate <= $checkinDate) {
            $errors[] = "Check-out date must be after check-in date";
        }
        
        $days = $checkinDate->diff($checkoutDate)->days;
        if($days < 1) {
            $errors[] = "Minimum stay is 1 night";
        }
    }
    
    if($hotel_id <= 0 || $room_id <= 0) {
        $errors[] = "Invalid hotel or room selection";
    }
    
    if($NoofRoom < 1 || $NoofRoom > 10) {
        $errors[] = "Number of rooms must be between 1 and 10";
    }
    
    if(empty($errors)) {
        $room_check_sql = "SELECT * FROM hotel_rooms WHERE room_id = $room_id AND hotel_id = $hotel_id AND status = 'available'";
        $room_check_result = mysqli_query($conn, $room_check_sql);
        $room_data = mysqli_fetch_array($room_check_result);
        
        if(!$room_data) {
            $errors[] = "Selected room is not available";
        } else {
            $available_rooms = $room_data['available_rooms'];
            if($NoofRoom > $available_rooms) {
                $errors[] = "Only $available_rooms room(s) available. You requested $NoofRoom room(s)";
            }
        }
    }
    
    if(empty($errors)) {
        $overlapping_sql = "SELECT COUNT(*) as count FROM roombook 
                           WHERE room_id = $room_id 
                           AND hotel_id = $hotel_id 
                           AND stat != 'Cancelled'
                           AND (
                               (cin <= '$cin' AND cout > '$cin') OR
                               (cin < '$cout' AND cout >= '$cout') OR
                               (cin >= '$cin' AND cout <= '$cout')
                           )";
        $overlapping_result = mysqli_query($conn, $overlapping_sql);
        $overlapping_data = mysqli_fetch_array($overlapping_result);
        $booked_rooms = $overlapping_data['count'] ?? 0;
        
        if($booked_rooms + $NoofRoom > $available_rooms) {
            $errors[] = "Not enough rooms available for the selected dates";
        }
    }
    
    // START OF FIX: Properly handling the result output
    if(empty($errors)) {
        $user_sql = "SELECT UserID FROM signup WHERE Email = '$usermail'";
        $user_result = mysqli_query($conn, $user_sql);
        $user_data = mysqli_fetch_array($user_result);
        $user_id = $user_data['UserID'] ?? null;
        
        $days = $checkinDate->diff($checkoutDate)->days;
        $room_price = floatval($room_data['price_per_night']);
        $total_price = $days * $room_price * $NoofRoom;
        
        $sta = "NotConfirm";
        $sql = "INSERT INTO roombook (hotel_id, room_id, user_id, Name, Email, Phone, RoomType, Bed, NoofRoom, Meal, cin, cout, nodays, stat, total_price) 
                VALUES ($hotel_id, $room_id, " . ($user_id ? $user_id : 'NULL') . ", '$Name', '$Email', '$Phone', '$RoomType', '$Bed', '$NoofRoom', '$Meal', '$cin', '$cout', $days, '$sta', $total_price)";
        
        $result = mysqli_query($conn, $sql);
        
        if($result) { ?>
            <!DOCTYPE html>
            <html>
            <head>
                <script src="./javascript/common.js"></script>
            </head>
            <body>
                <script>
                    window.onload = function() {
                        showAlert('Booking Successful!', 'Your booking request has been submitted.', 'success');
                        setTimeout(() => { window.location.href = 'customer/dashboard.php'; }, 2000);
                    };
                </script>
            </body>
            </html>
        <?php } else { ?>
            <!DOCTYPE html>
            <html>
            <head><script src="./javascript/common.js"></script></head>
            <body>
                <script>
                    window.onload = function() {
                        showAlert('Error', 'Database error. Please try again.', 'error');
                        setTimeout(() => { window.history.back(); }, 2000);
                    };
                </script>
            </body>
            </html>
        <?php }
    } else { 
        $error_message = implode('\\n', $errors); ?>
        <!DOCTYPE html>
        <html>
        <head><script src="./javascript/common.js"></script></head>
        <body>
            <script>
                window.onload = function() {
                    showAlert('Validation Error', '<?php echo $error_message; ?>', 'error');
                    setTimeout(() => { window.history.back(); }, 3000);
                };
            </script>
        </body>
        </html>
    <?php }
    exit();
} else {
    header("location: home.php");
    exit();
}
?>