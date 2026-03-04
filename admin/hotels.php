<?php
session_start();
include '../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/common.css">
    <script src="../javascript/common.js"></script>
    <title>Manage Hotels - Admin</title>
    <style>
        body { 
            padding: 20px;
            box-sizing: border-box;
        }
        
        /* FIX: Use Flexbox inside the card-body to prevent overlapping */
        .hotel-card .card-body .row {
            display: flex;
            align-items: flex-start;
        }

        .hotel-image { 
            width: 100%; /* Changed to 100% so it fits its column box */
            max-width: 200px; 
            height: 150px; 
            object-fit: cover; 
            border-radius: 5px;
            flex-shrink: 0; /* Prevents the image from being squashed by text */
        }
        
        @media(max-width: 768px){
            body {
                padding: 15px;
            }
            
            .hotel-card {
                margin-bottom: 15px;
            }
            
            /* On mobile, let the image take full width */
            .hotel-image {
                max-width: 100%;
                width: 100%;
                height: 200px;
            }
            
            .card-body .row {
                flex-direction: column; /* Stacks image on top of text on phones */
            }

            .btn {
                padding: 8px 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <h2>Manage Hotels</h2>
    
    <button class="btn btn-success mb-3" onclick="showAddForm()">Add New Hotel</button>
    
    <div id="addHotelForm" style="display: none; margin-bottom: 30px;">
        <div class="card">
            <div class="card-header">Add Hotel</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Hotel Name</label>
                            <input type="text" class="form-control" name="hotel_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Image Path</label>
                            <input type="text" class="form-control" name="hotel_image" placeholder="./image/hotel1.jpg" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Price Per Night</label>
                            <input type="number" step="0.01" class="form-control" name="price_per_night" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Rating</label>
                            <input type="number" step="0.1" min="0" max="5" class="form-control" name="rating" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Location</label>
                            <input type="text" class="form-control" name="location">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" name="add_hotel" class="btn btn-primary">Add Hotel</button>
                    <button type="button" class="btn btn-secondary" onclick="hideAddForm()">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <?php
    if(isset($_POST['add_hotel'])) {
        $hotel_name = mysqli_real_escape_string($conn, $_POST['hotel_name']);
        $hotel_image = mysqli_real_escape_string($conn, $_POST['hotel_image']);
        $price_per_night = floatval($_POST['price_per_night']);
        $rating = floatval($_POST['rating']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        
        $sql = "INSERT INTO hotels (hotel_name, hotel_image, price_per_night, rating, location, description, status) 
                VALUES ('$hotel_name', '$hotel_image', $price_per_night, $rating, '$location', '$description', 'active')";
        $result = mysqli_query($conn, $sql);
        
        if($result) {
            echo "<script>alert('Hotel added successfully'); location.reload();</script>";
        }
    }
    
    if(isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $sql = "UPDATE hotels SET status = 'inactive' WHERE hotel_id = $id";
        mysqli_query($conn, $sql);
        echo "<script>location.reload();</script>";
    }
    ?>

    <div class="row">
        <?php
        $sql = "SELECT * FROM hotels WHERE status = 'active' ORDER BY hotel_id DESC";
        $result = mysqli_query($conn, $sql);
        
        while($hotel = mysqli_fetch_array($result)) {
        ?>
        <div class="col-md-6 hotel-card">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row g-0"> <div class="col-md-4 text-center">
                            <img src="../<?php echo htmlspecialchars($hotel['hotel_image']); ?>" class="hotel-image" alt="Hotel">
                        </div>
                        <div class="col-md-8 ps-md-3"> <h4><?php echo htmlspecialchars($hotel['hotel_name']); ?></h4>
                            <p class="mb-1"><strong>Price:</strong> Rs <?php echo number_format($hotel['price_per_night'], 2); ?>/night</p>
                            <p class="mb-1"><strong>Rating:</strong> ⭐ <?php echo number_format($hotel['rating'], 1); ?></p>
                            <?php if($hotel['location']): ?>
                            <p class="mb-2"><strong>Location:</strong> <?php echo htmlspecialchars($hotel['location']); ?></p>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $hotel['hotel_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        }
        ?>
    </div>

    <script>
        function showAddForm() {
            document.getElementById('addHotelForm').style.display = 'block';
        }
        function hideAddForm() {
            document.getElementById('addHotelForm').style.display = 'none';
        }
    </script>
</body>
</html>