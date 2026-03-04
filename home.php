<?php

include 'config.php';
session_start();

// page redirect
$usermail="";
$usermail=$_SESSION['usermail'];
if($usermail == true){

}else{
  header("location: index.php");
}

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
    <title>MomentsAway</title>
    <style>
      #guestdetailpanel{
        display: none;
      }
      #guestdetailpanel .middle{
        height: 450px;
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
      <li><a href="#firstsection">Home</a></li>
      <li><a href="#secondsection">Hotels</a></li>
      <li><a href="#thirdsection">Facilities</a></li>
      <li><a href="#contactus">Contact Us</a></li>
      <li><a href="customer/dashboard.php">Dashboard</a></li>
      <li><a href="./logout.php"><button class="btn btn-danger">Logout</button></a></li>
    </ul>
  </nav>

<section id="firstsection" class="carousel slide carousel_section" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            
            <img class="carousel-image" src="./image/h1.jpg" style="opacity: 0.5; width: 100%;">
        </div>
        <div class="carousel-item">
            <img class="carousel-image" src="./image/hotel2.jpg" style="opacity: 0.5; width: 100%;">
        </div>
        <div class="carousel-item">
            <img class="carousel-image" src="./image/hotel3.jpg" style="opacity: 0.5; width: 100%;">
        </div>
        <div class="carousel-item">
            <img class="carousel-image" src="./image/hotel4.jpg" style="opacity: 0.5; width: 100%;">
        </div>

        <div class="welcomeline" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10;">
          <h1 class="welcometag" style="color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Welcome to MomentsAway</h1>
        </div>

      <!-- bookbox -->
      <div id="guestdetailpanel">
        <form action="" method="POST" class="guestdetailpanelform">
            <div class="head">
                <h3>RESERVATION</h3>
                <i class="fa-solid fa-circle-xmark" onclick="closebox()"></i>
            </div>
            <div class="middle">
                <div class="guestinfo">
                    <h4>Guest information</h4>
                    <input type="text" name="Name" placeholder="Enter Full name">
                    <input type="email" name="Email" placeholder="Enter Email">

                    
                    </select>
                    <input type="text" name="Phone" placeholder="Enter Phoneno">
                </div>

                <div class="line"></div>

                <div class="reservationinfo">
                    <h4>Reservation information</h4>
                    <select name="RoomType" class="selectinput">
						<option value selected >Type Of Room</option>
                        <option value="Superior Room">SUPERIOR ROOM</option>
                        <option value="Deluxe Room">DELUXE ROOM</option>
						<option value="Guest House">GUEST HOUSE</option>
						<option value="Single Room">SINGLE ROOM</option>
                    </select>
                    <select name="Bed" class="selectinput">
						<option value selected >Bedding Type</option>
                        <option value="Single">Single</option>
                        <option value="Double">Double</option>
						<option value="Triple">Triple</option>
                        <option value="Quad">Quad</option>
						<option value="None">None</option>
                    </select>
                    <select name="NoofRoom" class="selectinput">
						<option value selected >No of Room</option>
                        <option value="1">1</option>
                        <!-- <option value="1">2</option>
                        <option value="1">3</option> -->
                    </select>
                    <select name="Meal" class="selectinput">
						<option value selected >Meal</option>
                        <option value="Room only">Room only</option>
                        <option value="Breakfast">Breakfast</option>
						<option value="Half Board">Half Board</option>
						<option value="Full Board">Full Board</option>
					</select>
                    <div class="datesection">
                        <span>
                            <label for="cin"> Check-In</label>
                            <input name="cin" type ="date">
                        </span>
                        <span>
                            <label for="cin"> Check-Out</label>
                            <input name="cout" type ="date">
                        </span>
                    </div>
                </div>
            </div>
            <div class="footer">
                <button class="btn btn-success" name="guestdetailsubmit">Submit</button>
            </div>
        </form>

        <!-- ==== room book php ====-->
        <?php       
            if (isset($_POST['guestdetailsubmit'])) {
                $Name = $_POST['Name'];
                $Email = $_POST['Email'];
                $Phone = $_POST['Phone'];
                $RoomType = $_POST['RoomType'];
                $Bed = $_POST['Bed'];
                $NoofRoom = $_POST['NoofRoom'];
                $Meal = $_POST['Meal'];
                $cin = $_POST['cin'];
                $cout = $_POST['cout'];

                if($Name == "" || $Email == ""){
                    echo "<script>showAlert('Error', 'Fill the proper details', 'error');</script>";
                }
                else{
                    $sta = "NotConfirm";
                    $sql = "INSERT INTO roombook(Name,Email,Phone,RoomType,Bed,NoofRoom,Meal,cin,cout,stat,nodays) VALUES ('$Name','$Email','$Phone','$RoomType','$Bed','$NoofRoom','$Meal','$cin','$cout','$sta',datediff('$cout','$cin'))";
                    $result = mysqli_query($conn, $sql);

                    
                        if ($result) {
                            echo "<script>showAlert('Success', 'Reservation successful', 'success');</script>";
                        } else {
                            echo "<script>showAlert('Error', 'Something went wrong', 'error');</script>";
                        }
                }
            }
            ?>
          </div>

    </div>
  </section>
    
  <section id="secondsection"> 
    <img src="./image/homeanimatebg.svg">
    <div class="ourroom">
      <h1 class="head">≼ Our Hotels ≽</h1>
      <div class="roomselect">
        <?php
        $hotels_sql = "SELECT h.*, GROUP_CONCAT(f.facility_icon) as facility_icons 
                       FROM hotels h 
                       LEFT JOIN hotel_facilities hf ON h.hotel_id = hf.hotel_id 
                       LEFT JOIN facilities f ON hf.facility_id = f.facility_id 
                       WHERE h.status = 'active' 
                       GROUP BY h.hotel_id 
                       ORDER BY h.rating DESC";
        $hotels_result = mysqli_query($conn, $hotels_sql);
        
        while($hotel = mysqli_fetch_array($hotels_result)) {
          $facility_icons = explode(',', $hotel['facility_icons']);
        ?>
        <div class="roombox">
          <div class="hotelphoto" style="background-image: url('<?php echo $hotel['hotel_image']; ?>');"></div>
          <div class="roomdata">
            <h2><?php echo htmlspecialchars($hotel['hotel_name']); ?></h2>
            <div class="rating-price">
              <span class="rating">⭐ <?php echo number_format($hotel['rating'], 1); ?></span>
             <span class="price">Rs <?php echo number_format($hotel['price_per_night'], 2); ?>/night</span>
            </div>
            <div class="services">
              <?php
              foreach($facility_icons as $icon) {
                if(!empty($icon)) {
                  echo '<i class="fa-solid ' . htmlspecialchars($icon) . '"></i>';
                }
              }
              ?>
            </div>
            <div class="hotel-buttons">
              <a href="hotel_detail.php?id=<?php echo $hotel['hotel_id']; ?>" class="btn btn-info" style="flex: 1; min-width: 80px;">View</a>
              <a href="hotel_detail.php?id=<?php echo $hotel['hotel_id']; ?>&book=1" class="btn btn-primary" style="flex: 1; min-width: 80px;">Book</a>
            </div>
          </div>
        </div>
        <?php
        }
        ?>
      </div>
    </div>
  </section>

  <section id="thirdsection">
    <h1 class="head">≼ Facilities ≽</h1>
    <div class="facility">
      <div class="box">
        <h2>Swiming pool</h2>
      </div>
      <div class="box">
        <h2>Spa</h2>
      </div>
      <div class="box">
        <h2>24*7 Restaurants</h2>
      </div>
      <div class="box">
        <h2>24*7 Gym</h2>
      </div>
      <div class="box">
        <h2>Heli service</h2>
      </div>
    </div>
  </section>

  <section id="contactus">
    <div class="social">
      <i class="fa-brands fa-instagram"></i>
      <i class="fa-brands fa-facebook"></i>
      <i class="fa-solid fa-envelope"></i>
    </div>
    <div class="createdby">
      <h5>MomentsAway</h5>
    </div>
  </section>
</body>

<script>

    var bookbox = document.getElementById("guestdetailpanel");

    openbookbox = () =>{
      bookbox.style.display = "flex";
    }
    closebox = () =>{
      bookbox.style.display = "none";
    }
</script>
</html>