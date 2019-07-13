<?php

// confirmit.php

// Called by home page when user clicks on link to confirm a provisional booking

include('php/aggsbookings.php'); 

$bkid = htmlspecialchars($_GET['bid']);
$bkid = intval($bkid);
$eml = htmlspecialchars($_GET['eml']);

// Prepare and execute SQL UPDATE statement
$stmt = $link->prepare("UPDATE booking SET status = ".ST_CONF." where bookingid = ?"); 
$stmt->bind_param('i', $bkid);
$stmt->execute();
if ($stmt->affected_rows != 1) {
	aggslog("confirmit.php: error in setting status for booking id ".$bkid);
}
$stmt->close();

// Send confirmation email to user
$stmt = $link->prepare("SELECT room.name, period.name, bookingdate FROM booking, room, period WHERE bookingid = ? AND booking.roomid = room.roomid AND booking.periodid = period.periodid");
$stmt->bind_param('i',$bkid);
$stmt->bind_result($room,$period,$bdate);
$stmt->execute();
$stmt->fetch();
$stmt->close();
$message = "Booking for ".$room.", ".$period." on ".$bdate." has been confirmed.";
bookingmail($eml, 'Booking Confirmed', $message);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Booking Confirmed</title>
<link href="style.css" rel="stylesheet" type="text/css">

<link href="http://fonts.googleapis.com/css?family=Droid+Serif" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet" type="text/css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>

</head>

<body>

<?php
	doneItPage("The booking has been confirmed in the database.");
?>

</body>

</html>
