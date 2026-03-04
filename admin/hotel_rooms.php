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
    <title>Manage Hotel Rooms - Admin</title>
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
            }
            
            .table th,
            .table td {
                padding: 8px 4px;
            }
            
            .card-body {
                padding: 15px;
            }
            
            .form-control {
                font-size: 14px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <h2>Manage Hotel Rooms</h2>
    
    <button class="btn btn-success mb-3" onclick="showAddForm()">Add New Room</button>
    
    <div id="addRoomForm" style="display: none; margin-bottom: 30px;">
        <div class="card">
            <div class="card-header">Add Hotel Room</div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Hotel</label>
                            <select class="form-control" name="hotel_id" required>
                                <option value="">Select Hotel</option>
                                <?php
                                $hotels_sql = "SELECT * FROM hotels WHERE status = 'active'";
                                $hotels_result = mysqli_query($conn, $hotels_sql);
                                while($hotel = mysqli_fetch_array($hotels_result)) {
                                    echo '<option value="' . $hotel['hotel_id'] . '">' . htmlspecialchars($hotel['hotel_name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Room Type</label>
                            <select class="form-control" name="room_type" required>
                                <option value="Superior Room">Superior Room</option>
                                <option value="Deluxe Room">Deluxe Room</option>
                                <option value="Guest House">Guest House</option>
                                <option value="Single Room">Single Room</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Bedding Type</label>
                            <select class="form-control" name="bedding_type" required>
                                <option value="Single">Single</option>
                                <option value="Double">Double</option>
                                <option value="Triple">Triple</option>
                                <option value="Quad">Quad</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Price Per Night</label>
                            <input type="number" step="0.01" class="form-control" name="price_per_night" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Max Occupancy</label>
                            <input type="number" class="form-control" name="max_occupancy" value="2" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Total Rooms</label>
                            <input type="number" class="form-control" name="total_rooms" value="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Available Rooms</label>
                            <input type="number" class="form-control" name="available_rooms" value="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    <button type="submit" name="add_room" class="btn btn-primary">Add Room</button>
                    <button type="button" class="btn btn-secondary" onclick="hideAddForm()">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <?php
    if(isset($_POST['add_room'])) {
        $hotel_id = intval($_POST['hotel_id']);
        $room_type = mysqli_real_escape_string($conn, $_POST['room_type']);
        $bedding_type = mysqli_real_escape_string($conn, $_POST['bedding_type']);
        $price_per_night = floatval($_POST['price_per_night']);
        $max_occupancy = intval($_POST['max_occupancy']);
        $total_rooms = intval($_POST['total_rooms']);
        $available_rooms = intval($_POST['available_rooms']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        
        $sql = "INSERT INTO hotel_rooms (hotel_id, room_type, bedding_type, price_per_night, max_occupancy, total_rooms, available_rooms, description) 
                VALUES ($hotel_id, '$room_type', '$bedding_type', $price_per_night, $max_occupancy, $total_rooms, $available_rooms, '$description')";
        $result = mysqli_query($conn, $sql);
        
        if($result) {
            echo "<script>showAlert('Success', 'Room added successfully', 'success');</script>";
            echo "<script>setTimeout(() => location.reload(), 2000);</script>";
        }
    }
    
    if(isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $sql = "UPDATE hotel_rooms SET status = 'unavailable' WHERE room_id = $id";
        mysqli_query($conn, $sql);
        echo "<script>location.reload();</script>";
    }
    ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Hotel</th>
                <th>Room Type</th>
                <th>Bedding</th>
                <th>Price/Night</th>
                <th>Total</th>
                <th>Available</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT hr.*, h.hotel_name FROM hotel_rooms hr 
                    LEFT JOIN hotels h ON hr.hotel_id = h.hotel_id 
                    WHERE hr.status = 'available' 
                    ORDER BY hr.room_id DESC";
            $result = mysqli_query($conn, $sql);
            
            while($room = mysqli_fetch_array($result)) {
            ?>
            <tr>
                <td><?php echo htmlspecialchars($room['hotel_name']); ?></td>
                <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                <td><?php echo htmlspecialchars($room['bedding_type']); ?></td>
                <td>Rs<?php echo number_format($room['price_per_night'], 2); ?></td>
                <td><?php echo $room['total_rooms']; ?></td>
                <td><?php echo $room['available_rooms']; ?></td>
                <td>
                    <a href="?delete=<?php echo $room['room_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

    <script>
        function showAddForm() {
            document.getElementById('addRoomForm').style.display = 'block';
        }
        function hideAddForm() {
            document.getElementById('addRoomForm').style.display = 'none';
        }
    </script>
</body>
</html>

