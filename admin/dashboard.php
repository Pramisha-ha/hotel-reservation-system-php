<?php
    session_start();
    include '../config.php';

    // Data Logic
    $roombookre = mysqli_query($conn, "Select * from roombook");
    $roombookrow = mysqli_num_rows($roombookre);

    $staffre = mysqli_query($conn, "Select * from staff");
    $staffrow = mysqli_num_rows($staffre);

    $roomre = mysqli_query($conn, "Select * from room");
    $roomrow = mysqli_num_rows($roomre);

    $result = mysqli_query($conn, "SELECT finaltotal FROM payment");
    $tot = 0;
    while($row = mysqli_fetch_array($result)) {
        $tot += ($row["finaltotal"] * 0.10);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-title {
            color: #1a1a1a;
            margin-bottom: 30px;
            font-size: 28px;
        }

        /* This container forces the boxes into one line */
        .stats-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 40px;
        }

        /* Styling for the Boxes */
        .box {
            flex: 1; /* Makes all boxes exactly the same width */
            padding: 30px;
            border-radius: 15px;
            color: white;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .box:hover {
            transform: translateY(-5px);
        }

        .box p {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            opacity: 0.9;
        }

        .box h1 {
            margin: 15px 0 0;
            font-size: 40px;
            font-weight: 700;
        }

        .box h1 span {
            font-size: 18px;
            font-weight: 400;
            opacity: 0.8;
        }

        /* Box Colors */
        .booked-box { background: linear-gradient(135deg, #6e8efb, #a777e3); }
        .staff-box { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .profit-box { background: linear-gradient(135deg, #813007ff, #c03623ff); }

        /* Secondary section below boxes */
        .details {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .details h2 {
            margin-top: 0;
            font-size: 20px;
            color: #333;
            border-bottom: 2px solid #f0f2f5;
            padding-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="header-title">MomentsAway Admin</h1>

    <div class="stats-row">
        
        <div class="box booked-box">
            <p>Rooms Occupied</p>
            <h1><?php echo $roombookrow; ?> <span>/ <?php echo $roomrow; ?></span></h1>
        </div>

        

        <div class="box profit-box">
            <p>Net Profit</p>
            <h1><?php echo number_format($tot); ?> <span>Rs</span></h1>
        </div>

    </div>

    <div class="details">
        <h2>Quick Analysis</h2>
        <p>The current occupancy rate is <strong><?php echo round(($roombookrow/$roomrow)*100); ?>%</strong>.</p>
    </div>
</div>

</body>
</html>