<?php

	include('php/aggsbookings.php');
	
	// Admin do
	// insert records from admin form
	
	// get params
	
	$yrg = $_GET['yeargroup'];
	$dnm = $_GET['daynum'];
	$pid = $_GET['period'];
	$tid = $_GET['tchr'];
	$rid = $_GET['room'];
	$lodate = $_GET['lodate'];
	$hidate = $_GET['hidate'];
	$dnm = intval($dnm);
	$pid = intval($pid);
	$tid = intval($tid);
	$rid = intval($rid);

	// setup static params
	
	$reason = 255; // special code for timetable lessons - never used (at the time of writing, anyways)
	$st = ST_TTBL;
	
	echo $yrg."#".$dnm."#".$pid."#".$tid."#".$rid."#".$lodate."#".$hidate;
	
	// get all dates matching this day of cycle
	
	$query = "SELECT thedate FROM termdates WHERE daynumber = ".$dnm." AND DATE(thedate)>='".$lodate."' AND DATE(thedate)<='".$hidate."';";
	$stmt = $link->prepare($query); 
	$stmt->bind_result($thedate);
	$stmt->execute();
	$stmt->store_result();
echo $stmt->affected_rows;
	while ($stmt->fetch()) { $bdates[] = $thedate; }
	$stmt->free_result();
	$stmt->close();

	// for each such date, book the room
	
	foreach ($bdates as $booking_date) {
		$stmt = $link->prepare("INSERT INTO booking (roomid,teacherid,reason,periodid,bookingdate,status,yeargroup) VALUES (?, ?, ?, ?, ?, ?, ?)"); 
		$stmt->bind_param('iiiisis', $rid, $tid, $reason, $pid, $booking_date, $st, $yrg);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->affected_rows != 1) {
			aggslog("admin_do.php:  error in making booking: ".$rid." ".$tid." ".$reason." ".$pid." ".$booking_date." ".$st." ".$yrg);
		}
		$stmt->free_result();
		$stmt->close();
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Booking Admin</title>
<link href="style.css" rel="stylesheet" type="text/css">

<link href="http://fonts.googleapis.com/css?family=Droid+Serif" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet" type="text/css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>

</head>

<body>

Done

</body>

</html>


