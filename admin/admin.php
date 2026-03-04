<?php

include '../config.php';
session_start();

// page redirect
$usermail="";
$usermail=$_SESSION['usermail'];
if($usermail == true){

}else{
  header("location: http://localhost/Hotel-Management-System-main/index.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/admin.css">
    <!-- loading bar -->
    <link rel="stylesheet" href="../css/flash.css">
     <title>MomentsAway - Admin</title>
</head>

<body>
    <!-- mobile view -->
    <div id="mobileview">
        <h5>Admin panel doesn't show in mobile view</h4>
    </div>
  
    <!-- nav bar -->
    <nav class="uppernav">
        <div class="logo">
            <img class="bluebirdlogo" src="../image/momentsawaylogo.png" alt="logo">
            <p>MomentsAway</p>
        </div>
        <div class="logout">
        <a href="../logout.php"><button class="btn btn-primary">Logout</button></a>
        </div>
    </nav>
    <nav class="sidenav">
        <ul>
            <li class="pagebtn active"><img src="../image/icon/dashboard.png">&nbsp&nbsp&nbsp Dashboard</li>
            <li class="pagebtn"><img src="../image/icon/bed.png">&nbsp&nbsp&nbsp Room Booking</li>
            <li class="pagebtn"><img src="../image/icon/wallet.png">&nbsp&nbsp&nbsp Payment</li>
            <li class="pagebtn"><img src="../image/icon/bedroom.png">&nbsp&nbsp&nbsp Hotels</li>
            <li class="pagebtn"><img src="../image/icon/bedroom.png">&nbsp&nbsp&nbsp Hotel Rooms</li>
            <li class="pagebtn"><img src="../image/icon/bedroom.png">&nbsp&nbsp&nbsp Rooms</li>
            <li class="pagebtn"><img src="../image/icon/staff.png">&nbsp&nbsp&nbsp Users</li>
        </ul>
    </nav>

    <!-- main section -->
    <div class="mainscreen">
        <iframe class="frames frame1 active" src="./dashboard.php" frameborder="0"></iframe>
        <iframe class="frames frame2" src="./roombook.php" frameborder="0"></iframe>
        <iframe class="frames frame3" src="./payment.php" frameborder="0"></iframe>
        <iframe class="frames frame4" src="./hotels.php" frameborder="0"></iframe>
        <iframe class="frames frame5" src="./hotel_rooms.php" frameborder="0"></iframe>
        <iframe class="frames frame6" src="./room.php" frameborder="0"></iframe>
        <iframe class="frames frame7" src="./users.php" frameborder="0"></iframe>
    </div>
</body>

<script src="./javascript/script.js"></script>

</html>
