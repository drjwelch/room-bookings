<?php

// book_slots.php

// Inserts booking into database

include('php/aggsbookings.php'); 

if(isset($_POST['periodid'])) $periodid = mysqli_real_escape_string($link, $_POST['periodid']);
if(isset($_POST['roomid'])) $roomid = mysqli_real_escape_string($link, $_POST['roomid']);
if(isset($_POST['statusbid'])) $statusbid = mysqli_real_escape_string($link, $_POST['statusbid']);
if(isset($_POST['reason'])) $reason = mysqli_real_escape_string($link, $_POST['reason']);
if(isset($_POST['yeargroup'])) $yeargroup = mysqli_real_escape_string($link, $_POST['yeargroup']);
if(isset($_POST['tchrcode'])) $tchrcode = mysqli_real_escape_string($link, $_POST['tchrcode']);
if(isset($_POST['name'])) $name = mysqli_real_escape_string($link, $_POST['name']);
if(isset($_POST['temail'])) $temail = mysqli_real_escape_string($link, $_POST['temail']);
if(isset($_POST['booking_date'])) $booking_date = mysqli_real_escape_string($link, $_POST['booking_date']);

// Prepare INSERT statement

$stmt = $link->prepare("INSERT INTO booking (roomid,teacherid,reason,periodid,bookingdate,status,yeargroup) VALUES (?, ?, ?, ?, ?, ?, ?)"); 
$st = ST_PROV;
$stmt->bind_param('iiiisis', $roomid, $tchrcode, $reason, $periodid, $booking_date, $st, $yeargroup);
$stmt->execute();
if ($stmt->affected_rows != 1) {
	aggslog("book_slots.php:  error in making booking: ".$roomid." ".$tchrcode." ".$reason." ".$periodid." ".$booking_date." ".$st." ".$yeargroup);
}
$stmt->close();

//  Actions done after booking is inserted
//  Either email user to say prov booking made or handle clash when over-booking a lapsed slot

$query = "SELECT room.name,period.name FROM room,period WHERE room.roomid = ? AND period.periodid = ?";
$stmt = $link->prepare($query); 
$stmt->bind_param("ii",$roomid,$periodid);
$stmt->bind_result($roomname,$periodname);	
$stmt->execute();
$stmt->store_result();
$stmt->fetch();
$stmt->close();

echo "$periodid #$periodname# $roomid #$roomname#";

if (!isset($statusbid)) { // statusbid has format st-num#bkid@email - should always be set (test for it is historic)
	$statusbid = "1#9999@nobody";
}
$status = intval(substr($statusbid,0,1));
if ($status==3) {// this is an over-booking of a lapsed booking
	$oldbkid = intval(substr($statusbid,2,strpos($statusbid,"@")-2));
	$otheremail = substr($statusbid,strpos($statusbid,"@")+1); // email of original booking user
	$newbkid = $link->insert_id;
	if ($newbkid==0) {
		aggslog("book_slots.php: id of inserted over-booking record is zero: oldid was ".$oldbkid);
	}

	// set original booking to provisional again instead of lapsed to prevent further over-booking
	// no longer needed now that booking grid is ORDER BY status; new prov booking will be found first and prevent re-over-booking
	//$stmt = $link->prepare("UPDATE booking SET status = ".ST_PROV." WHERE bookingid = ?"); 
	//$stmt->bind_param('i', $oldbkid);
	//$stmt->execute();
	//if ($stmt->affected_rows != 1) {
	//	aggslog("book_slots.php:  error in setting over-booking back to provisional: ".$oldbkid);
	//}
	//$stmt->close();

	// send email to resolve conflict
	$message = "Dear ".$otheremail." (cc:".$email.")";
	$message = $message."\r\nBooking attempted by ".$temail." for room ".$roomname." on ".$booking_date." in period ".$periodname;
	$message = $message."\r\nYou have a lapsed booking in this slot.  Do you still need the booking?  Please discuss with the other member of staff.";
	$message = $message."\r\nClick <a href='http://10.16.56.184/cancelit.php?bid=".$oldbkid."'>here</a> to cancel your booking, ".$otheremail.".";
	$message = $message."\r\nClick <a href='http://10.16.56.184/cancelit.php?bid=".$newbkid."'>here</a> to cancel the booking by ".$temail.".";
	bookingmail($temail, 'Room Booking Clash', $message);
	bookingmail($otheremail, 'Room Booking Clash', $message);
} else { // normal booking
	$message = "Booking made by ".$temail." for room ".$roomname." on ".$booking_date." in period ".$periodname;
	if (strtotime("today + 5 days")>strtotime($booking_date)) {
	    $message = $message."\r\n** Your booking is within 5 days so you will not receive a reminder.  Please confirm the booking now. **";
	}
	bookingmail($temail, 'Room Booking Made', $message);
}

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

	doneItPage("The booking has been made into the database.");

?>

</body>

</html>
