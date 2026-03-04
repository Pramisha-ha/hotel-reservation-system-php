<?php

include '../config.php';

// fetch room data
$id = $_GET['id'];

$sql ="Select * from roombook where id = '$id'";
$re = mysqli_query($conn,$sql);
while($row=mysqli_fetch_array($re))
{
    $Name = $row['Name'];
    $Email = $row['Email'];
    $Phone = $row['Phone'];
    $cin = $row['cin'];
    $cout = $row['cout'];
    $noofday = $row['nodays'];
    $stat = $row['stat'];
}

if (isset($_POST['guestdetailedit'])) {
    $EditName = $_POST['Name'];
    $EditEmail = $_POST['Email'];
    $EditPhone = $_POST['Phone'];
    $EditRoomType = $_POST['RoomType'];
    $EditBed = $_POST['Bed'];
    
    // FIX: Cast these to integers immediately
    $EditNoofRoom = (int)$_POST['NoofRoom']; 
    $EditMeal = $_POST['Meal'];
    $Editcin = $_POST['cin'];
    $Editcout = $_POST['cout'];

    // Update main roombook table
    $sql = "UPDATE roombook SET Name = '$EditName',Email = '$EditEmail',Phone='$EditPhone',RoomType='$EditRoomType',Bed='$EditBed',NoofRoom='$EditNoofRoom',Meal='$EditMeal',cin='$Editcin',cout='$Editcout',nodays = datediff('$Editcout','$Editcin') WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    // Pricing Logic
    $type_of_room = 0;
    if($EditRoomType=="Superior Room") { $type_of_room = 3000; }
    else if($EditRoomType=="Deluxe Room") { $type_of_room = 2000; }
    else if($EditRoomType=="Guest House") { $type_of_room = 1500; }
    else if($EditRoomType=="Single Room") { $type_of_room = 1000; }
    
    // FIX: Calculate Bed Type with explicit floats
    $type_of_bed = 0.0;
    if($EditBed=="Single") { $type_of_bed = (float)$type_of_room * 0.01; }
    else if($EditBed=="Double") { $type_of_bed = (float)$type_of_room * 0.02; }
    else if($EditBed=="Triple") { $type_of_bed = (float)$type_of_room * 0.03; }
    else if($EditBed=="Quad") { $type_of_bed = (float)$type_of_room * 0.04; }
    else if($EditBed=="None") { $type_of_bed = 0.0; }

    // FIX: Calculate Meal Type
    $type_of_meal = 0.0;
    if($EditMeal=="Room only") { $type_of_meal = $type_of_bed * 0; }
    else if($EditMeal=="Breakfast") { $type_of_meal = $type_of_bed * 2; }
    else if($EditMeal=="Half Board") { $type_of_meal = $type_of_bed * 3; }
    else if($EditMeal=="Full Board") { $type_of_meal = $type_of_bed * 4; }
    
    // Fetch updated noofday from DB (after datediff update)
    $psql ="Select * from roombook where id = '$id'";
    $presult = mysqli_query($conn,$psql);
    $prow=mysqli_fetch_array($presult);
    
    // FIX: Ensure this is an integer
    $Editnoofday = (int)$prow['nodays'];

    /* THE CALCULATION FIX: 
       Explicitly cast everything to (float) or (int) to prevent 
       "Unsupported operand types" error.
    */
    $editttot = (float)$type_of_room * (int)$Editnoofday * (int)$EditNoofRoom;
    $editmepr = (float)$type_of_meal * (int)$Editnoofday;
    $editbtot = (float)$type_of_bed * (int)$Editnoofday;

    $editfintot = $editttot + $editmepr + $editbtot;

    // Update payment table
    $psql = "UPDATE payment SET Name = '$EditName',Email = '$EditEmail',RoomType='$EditRoomType',Bed='$EditBed',NoofRoom='$EditNoofRoom',Meal='$EditMeal',cin='$Editcin',cout='$Editcout',noofdays = '$Editnoofday',roomtotal = '$editttot',bedtotal = '$editbtot',mealtotal = '$editmepr',finaltotal = '$editfintot' WHERE id = '$id'";

    $paymentresult = mysqli_query($conn,$psql);

    if ($paymentresult) {
        header("Location:roombook.php");
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
    <link rel="stylesheet" href="./css/roombook.css">
    <style>
        #editpanel{
            position : fixed;
            z-index: 1000;
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: center;
            /* align-items: center; */
            background-color: #00000079;
        }
        #editpanel .guestdetailpanelform{
            height: 620px;
            width: 1170px;
            background-color: #ccdff4;
            border-radius: 10px;  
            /* temp */
            position: relative;
            top: 20px;
            animation: guestinfoform .3s ease;
        }

    </style>
    <title>Document</title>
</head>
<body>
    <div id="editpanel">
        <form method="POST" class="guestdetailpanelform">
            <div class="head">
                <h3>EDIT RESERVATION</h3>
                <a href="./roombook.php"><i class="fa-solid fa-circle-xmark"></i></a>
            </div>
            <div class="middle">
                <div class="guestinfo">
                    <h4>Guest information</h4>
                    <input type="text" name="Name" placeholder="Enter Full name" value="<?php echo $Name ?>">
                    <input type="email" name="Email" placeholder="Enter Email" value="<?php echo $Email ?>">
                    <input type="text" name="Phone" placeholder="Enter Phoneno"  value="<?php echo $Phone ?>">
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
                            <input name="cin" type ="date" value="<?php echo $cin ?>">
                        </span>
                        <span>
                            <label for="cin"> Check-Out</label>
                            <input name="cout" type ="date" value="<?php echo $cout ?>">
                        </span>
                    </div>
                </div>
            </div>
            <div class="footer">
                <button class="btn btn-success" name="guestdetailedit">Edit</button>
            </div>
        </form>
    </div>
</body>
</html>