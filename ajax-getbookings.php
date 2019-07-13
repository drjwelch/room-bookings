<?php

// ajax-getbookings.php

// Get a list of bookings for a given teacher and return to currently-displayed page

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include('php/aggsbookings.php'); 

$tcode = mysqli_real_escape_string($link, $_GET['tcode']);
$tcode = intval($tcode);

$qtype = mysqli_real_escape_string($link, $_GET['qtype']);
$qtype = intval($qtype);

// Set up the query required

$query = "SELECT teacher.emailaddress,bookingid,room.name,period.name,booking.bookingdate,booking.reason,booking.status,booking.yeargroup";
$query = $query." FROM booking,teacher,room,period";
$query = $query." WHERE teacher.teacherid=? AND booking.teacherid=teacher.teacherid AND room.roomid = booking.roomid AND period.periodid = booking.periodid";
// if user has asked for only confirmable (ie provisional) bookings then we will have been passed qtype = 1 (otherwise 2)
if ($qtype == QT_CONF) { // confirmable
	$query = $query . " AND booking.status = ".ST_PROV." AND booking.bookingdate > NOW() AND booking.bookingdate < (NOW() + INTERVAL 7 DAY)";
} elseif ($qtype == QT_VIEW) {
	// no additional WHERE clause - just get all my future non-timetabled bookings
	$query = $query . " AND booking.status != ".ST_TTBL." AND booking.bookingdate > NOW()";
} else {
	// allow here for any other options added to home page
	aggslog("ajax-getbookings.php: passed unknown qtype = ".$qtype);
}
$query = $query." ORDER BY booking.bookingdate";

// Run prepared statement query

$stmt = $link->prepare($query); 
$stmt->bind_param("i",$tcode);
$stmt->bind_result($eml,$bid,$room,$period,$bookingdate,$reason,$status,$yeargroup);	
$stmt->execute();
$stmt->store_result();

// Prepare HTML to return to the home page jQuery script

echo '<div id="tblist"><table>';
echo "<tr><th>Date</th><th>Period</th><th>Room</th><th>Yeargroup</th><th>Status</th><th></th></tr>";
while($stmt->fetch()) {
	// $st contains the HTML for the final column
	if ($status==ST_PROV) {
		if ($qtype==QT_CONF) { // confirmable requested and this booking is provisional so add link to confirm it
			$st="<a href='confirmit.php?bid=$bid&eml=$eml'>Click to confirm</a></td><td>";
		} else { // provisional but just viewing
			$st = "Provisional" ;
		}
	} elseif ($status==ST_CONF) { // confirmed booking
		$st = "Confirmed";
	} elseif ($status==ST_LAPS) { // lapsed
		$st = "Lapsed";
	} else { // timetabled or other error condition
		// shouldn't occur as we removed from the query
		aggslog("ajax-getbookings.php: booking with unexpected status returned from database, status=".$status);
	}
	if ($qtype==QT_VIEW) { // option to cancel each booking if we are just viewing our bookings
		$st = $st."</td><td><a href='cancelmail.php?bid=$bid&eml=$eml&room=$room&period=$period&bdate=$bookingdate'>Cancel</a>";
		}
	echo "<tr><td>".nicedate($bookingdate)."</td><td>$period</td><td>$room</td><td>$yeargroup</td><td>".$st."</td></tr>";
//		echo "$period in $room on $bookingdate for $yeargroup with $reason and $status<br>";
}
if ($stmt->num_rows == 0) {
	if ($qtype==QT_CONF) {
		echo"<tr rowspan=2><td colspan=5>You have no bookings in the coming 7 days.</td></tr>";
	} else {
		echo"<tr rowspan=2><td colspan=5>You have not made any bookings.</td></tr>";
	}
}
echo "</table></div>";
$stmt->free_result();
$stmt->close();

?>