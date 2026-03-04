<?php
include 'config.php';
session_start();

$usermail = $_SESSION['usermail'] ?? '';
if($usermail == true){
} else {
    header("location: index.php");
    exit();
}

$hotel_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$book_mode = isset($_GET['book']) ? true : false;

if($hotel_id == 0) {
    header("location: home.php");
    exit();
}

$hotel_sql = "SELECT * FROM hotels WHERE hotel_id = $hotel_id AND status = 'active'";
$hotel_result = mysqli_query($conn, $hotel_sql);
$hotel = mysqli_fetch_array($hotel_result);

if(!$hotel) {
    header("location: home.php");
    exit();
}

$facilities_sql = "SELECT f.* FROM facilities f 
                   INNER JOIN hotel_facilities hf ON f.facility_id = hf.facility_id 
                   WHERE hf.hotel_id = $hotel_id";
$facilities_result = mysqli_query($conn, $facilities_sql);

$rooms_sql = "SELECT * FROM hotel_rooms WHERE hotel_id = $hotel_id AND status = 'available' ORDER BY price_per_night ASC";
$rooms_result = mysqli_query($conn, $rooms_sql);

$user_sql = "SELECT * FROM signup WHERE Email = '$usermail'";
$user_result = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_array($user_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/home.css">
    <link rel="stylesheet" href="./css/common.css">
    <script src="./javascript/common.js"></script>
    <title><?php echo htmlspecialchars($hotel['hotel_name']); ?> - MomentsAway</title>
    <style>
        .hotel-detail-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 20px;
            box-sizing: border-box;
        }
        
        @media(max-width: 768px){
            .hotel-detail-container {
                margin: 80px auto 30px;
                padding: 15px;
            }
            
            .hotel-header {
                padding: 20px;
            }
            
            .hotel-image-large {
                height: 250px;
            }
            
            .hotel-info h1 {
                font-size: 24px;
            }
            
            .hotel-rating {
                font-size: 18px;
            }
            
            .hotel-price {
                font-size: 20px;
            }
            
            .room-card {
                padding: 15px;
            }
            
            .room-type {
                font-size: 16px;
            }
            
            .room-price {
                font-size: 18px;
            }
            
            .booking-form {
                padding: 20px;
            }
            
            .facilities-section {
                padding: 20px;
            }
            
            .facility-item {
                margin: 5px 10px;
                padding: 8px 15px;
                font-size: 14px;
            }
        }
        .hotel-header {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .hotel-image-large {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .hotel-info h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .hotel-rating {
            font-size: 24px;
            color: #ffd700;
            margin: 10px 0;
        }
        .hotel-price {
            font-size: 28px;
            color: #4CAF50;
            font-weight: bold;
            margin: 10px 0;
        }
        .facilities-section {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .facility-item {
            display: inline-block;
            margin: 10px 15px;
            padding: 10px 20px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .facility-item i {
            margin-right: 8px;
            color: #0040ff;
        }
        .rooms-section {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .room-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .room-type {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .room-price {
            font-size: 24px;
            color: #4CAF50;
            font-weight: bold;
        }
        .booking-form {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .back-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">
            <img class="bluebirdlogo" src="./image/momentsawaylogo.png" alt="logo">
            <p>MomentsAway</p>
        </div>
        <ul>
            <li><a href="home.php#firstsection">Home</a></li>
            <li><a href="home.php#secondsection">Hotels</a></li>
            <li><a href="customer/dashboard.php">Dashboard</a></li>
            <a href="./logout.php"><button class="btn btn-danger">Logout</button></a>
        </ul>
    </nav>

    <div class="hotel-detail-container">
        <a href="home.php#secondsection" class="btn btn-secondary back-btn">← Back to Hotels</a>
        
        <div class="hotel-header">
            <img src="<?php echo htmlspecialchars($hotel['hotel_image']); ?>" alt="<?php echo htmlspecialchars($hotel['hotel_name']); ?>" class="hotel-image-large">
            <div class="hotel-info">
                <h1><?php echo htmlspecialchars($hotel['hotel_name']); ?></h1>
                <div class="hotel-rating">⭐ <?php echo number_format($hotel['rating'], 1); ?></div>
                <div class="hotel-price">Rs <?php echo number_format($hotel['price_per_night'], 2); ?> per night</div>
                <?php if($hotel['location']): ?>
                <p><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($hotel['location']); ?></p>
                <?php endif; ?>
                <?php if($hotel['description']): ?>
                <p><?php echo htmlspecialchars($hotel['description']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="facilities-section">
            <h2>Facilities</h2>
            <div class="facilities-list">
                <?php
                while($facility = mysqli_fetch_array($facilities_result)) {
                ?>
                <div class="facility-item">
                    <i class="fa-solid <?php echo htmlspecialchars($facility['facility_icon']); ?>"></i>
                    <?php echo htmlspecialchars($facility['facility_name']); ?>
                </div>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="rooms-section">
            <h2>Available Rooms</h2>
            <?php
            if(mysqli_num_rows($rooms_result) > 0) {
                while($room = mysqli_fetch_array($rooms_result)) {
                    $available = $room['available_rooms'];
            ?>
            <div class="room-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="room-type"><?php echo htmlspecialchars($room['room_type']); ?> - <?php echo htmlspecialchars($room['bedding_type']); ?></div>
                        <p class="mb-1">Max Occupancy: <?php echo $room['max_occupancy']; ?> guests</p>
                        <p class="mb-1">Available: <?php echo $available; ?> room(s)</p>
                        <?php if($room['description']): ?>
                        <p class="text-muted"><?php echo htmlspecialchars($room['description']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="text-end">
                        <div class="room-price">Rs <?php echo number_format($room['price_per_night'], 2); ?>/night</div>
                        <?php if($available > 0): ?>
                        <button class="btn btn-primary mt-2" onclick="selectRoom(<?php echo $room['room_id']; ?>, <?php echo $room['price_per_night']; ?>, '<?php echo htmlspecialchars($room['room_type']); ?>', '<?php echo htmlspecialchars($room['bedding_type']); ?>')">Select Room</button>
                        <?php else: ?>
                        <button class="btn btn-secondary mt-2" disabled>Not Available</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                echo "<p>No rooms available at this hotel.</p>";
            }
            ?>
        </div>

        <div class="booking-form" id="bookingForm" style="display: none;">
            <h2>Complete Your Booking</h2>
            <form action="process_booking.php" method="POST" id="bookingFormElement">
                <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
                <input type="hidden" name="room_id" id="selected_room_id">
                <input type="hidden" name="room_price" id="selected_room_price">
                <input type="hidden" name="room_type" id="selected_room_type">
                <input type="hidden" name="bedding_type" id="selected_bedding_type">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="Name" value="<?php echo htmlspecialchars($user['Username'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="Email" value="<?php echo htmlspecialchars($usermail); ?>" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="Phone" value="<?php echo htmlspecialchars($user['Phone'] ?? ''); ?>" required>
                    </div>
                   
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Check-In Date</label>
                        <input type="date" class="form-control" name="cin" id="checkin_date" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Check-Out Date</label>
                        <input type="date" class="form-control" name="cout" id="checkout_date" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Number of Rooms</label>
                        <select class="form-control" name="NoofRoom" id="num_rooms" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Meal Plan</label>
                        <select class="form-control" name="Meal" required>
                            <option value="Room only">Room only</option>
                            <option value="Breakfast">Breakfast</option>
                            <option value="Half Board">Half Board</option>
                            <option value="Full Board">Full Board</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Total Price</label>
                        <input type="text" class="form-control" id="total_price_display" readonly>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success btn-lg w-100" name="book_submit">Confirm Booking</button>
            </form>
        </div>
    </div>

    <script>
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('checkin_date').setAttribute('min', today);
        document.getElementById('checkout_date').setAttribute('min', today);

        function selectRoom(roomId, price, roomType, beddingType) {
            document.getElementById('selected_room_id').value = roomId;
            document.getElementById('selected_room_price').value = price;
            document.getElementById('selected_room_type').value = roomType;
            document.getElementById('selected_bedding_type').value = beddingType;
            document.getElementById('bookingForm').style.display = 'block';
            document.getElementById('bookingForm').scrollIntoView({ behavior: 'smooth' });
            calculateTotal();
        }

        function calculateTotal() {
            const checkin = document.getElementById('checkin_date').value;
            const checkout = document.getElementById('checkout_date').value;
            const numRooms = parseInt(document.getElementById('num_rooms').value);
            const roomPrice = parseFloat(document.getElementById('selected_room_price').value);
            
            if(checkin && checkout && checkin < checkout) {
                const checkinDate = new Date(checkin);
                const checkoutDate = new Date(checkout);
                const days = Math.ceil((checkoutDate - checkinDate) / (1000 * 60 * 60 * 24));
                const total = days * roomPrice * numRooms;
                document.getElementById('total_price_display').value = 'Rs' + total.toFixed(2);
            } else {
                document.getElementById('total_price_display').value = '';
            }
        }

        document.getElementById('checkin_date').addEventListener('change', function() {
            const checkin = this.value;
            if(checkin) {
                const nextDay = new Date(checkin);
                nextDay.setDate(nextDay.getDate() + 1);
                document.getElementById('checkout_date').setAttribute('min', nextDay.toISOString().split('T')[0]);
            }
            calculateTotal();
        });

        document.getElementById('checkout_date').addEventListener('change', calculateTotal);
        document.getElementById('num_rooms').addEventListener('change', calculateTotal);

        document.getElementById('bookingFormElement').addEventListener('submit', function(e) {
            const checkin = document.getElementById('checkin_date').value;
            const checkout = document.getElementById('checkout_date').value;
            
            if(!checkin || !checkout) {
                e.preventDefault();
                alert('Please select both check-in and check-out dates');
                return false;
            }
            
            if(checkin >= checkout) {
                e.preventDefault();
                alert('Check-out date must be after check-in date');
                return false;
            }
            
            const checkinDate = new Date(checkin);
            const today = new Date();
            today.setHours(0,0,0,0);
            
            if(checkinDate < today) {
                e.preventDefault();
                alert('Check-in date cannot be in the past');
                return false;
            }
        });
    </script>
</body>
</html>

