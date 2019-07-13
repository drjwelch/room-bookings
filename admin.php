<?php
	include('php/aggsbookings.php');
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

<form action="admin_do.php" method="get">

<select name='yeargroup' id='yeargroup' style='width: 100px;'>
  <option value='Year 7'>Year 7</option>
  <option value='Year 8'>Year 8</option>
  <option value='Year 9'>Year 9</option>
  <option value='Year 10'>Year 10</option>
  <option value='Year 11'>Year 11</option>
  <option value='Year 12'>Year 12</option>
  <option value='Year 13'>Year 13</option>
  <option value='Ext.group'>Ext.group</option>
</select>

Day number (1-10): <input id="daynum" name='daynum' type='text'>
Start date: <input id="lodate" name='lodate' type='text'>
End date: <input id="hidate" name='hidate' type='text'>

<select name='period' id='period'>
<?php
	$stmt = $link->prepare("SELECT periodid,name FROM period"); 
	$stmt->bind_result($pid,$pnm);	
	$stmt->execute();
	$stmt->store_result();
	while($stmt->fetch()) {
		echo "<option value='$pid'>$pnm</option>";
	}
	$stmt->free_result();
	$stmt->close();
?>
</select>

<select name='room' id='room'>
<?php
	$stmt = $link->prepare("SELECT roomid,name FROM room"); 
	$stmt->bind_result($rid,$rnm);	
	$stmt->execute();
	$stmt->store_result();
	while($stmt->fetch()) {
		echo "<option value='$rid'>$rnm</option>";
	}
	$stmt->free_result();
	$stmt->close();
?>
</select>

<select name='tchr' id='tchr'>
<?php
	$stmt = $link->prepare("SELECT teacherid,name FROM teacher"); 
	$stmt->bind_result($tid,$tnm);	
	$stmt->execute();
	$stmt->store_result();
	while($stmt->fetch()) {
		echo "<option value='$tid'>$tnm</option>";
	}
	$stmt->free_result();
	$stmt->close();
?>
</select>

<input type="submit">Click to book for whole year</input>

<br>
Date format is 2016-10-24
</form>

</body>

</html>


