<?php

	
class booking_diary {


// Mysqli connection
function __construct($link) {
    $this->link = $link;
}

// Settings you can change:


// Time Related Variables
public $booking_start_time          = "09:30";			// The time of the first slot in 24 hour H:M format  
public $booking_end_time            = "19:00"; 			// The time of the last slot in 24 hour H:M format  
public $booking_frequency           = 30;   			// The slot frequency per hour, expressed in minutes.  	

// Day Related Variables

public $day_format					= 1;				// Day format of the table header.  Possible values (1, 2, 3)   
															// 1 = Show First digit, eg: "M"
															// 2 = Show First 3 letters, eg: "Mon"
															// 3 = Full Day, eg: "Monday"
	
public $day_closed					= array("Saturday", "Sunday"); 	// If you don't want any 'closed' days, remove the day so it becomes: = array();
public $day_closed_text				= ""; 		// If you don't want any any 'closed' remove the text so it becomes: = "";

// Cost Related Variables
public $cost_per_slot				= 20.50;			// The cost per slot
public $cost_currency_tag			= "&pound;";		// The currency tag in HTML such as &euro; &pound; &yen;


//  DO NOT EDIT BELOW THIS LINE

public $day_order	 				= array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
public $day, $month, $year, $selected_date, $back, $back_month, $back_year, $forward, $forward_month, $forward_year, $bookings, $count, $days, $is_slot_booked_today;


/*========================================================================================================================================================*/


//  None of this stuff has been modified from the original calendar code


function make_calendar($selected_date, $back, $forward, $day, $month, $year) {

    // $day, $month and $year are the $_GET variables in the URL
    $this->day = $day;    
    $this->month = $month;
    $this->year = $year;
    
	// $back and $forward are Unix Timestamps of the previous / next month, used to give the back arrow the correct month and year 
    $this->selected_date = $selected_date;       
    $this->back = $back;
    $this->back_month = date("m", $back);
    $this->back_year = date("Y", $back); // Minus one month back arrow
    
    $this->forward = $forward;
    $this->forward_month = date("m", $forward);
    $this->forward_year = date("Y", $forward); // Add one month forward arrow    
    
    // Make the booking array
    $this->make_booking_array($year, $month);
    
}


//  Make booking array has been modified it seems but causing no problem ... not sure what it does!

function make_booking_array($year, $month, $j = 0) { 

	$stmt = $this->link->prepare("SELECT teacherid,roomid,status FROM booking WHERE teacherid=3"); 
	$this->is_slot_booked_today = 0; // Defaults to 0
        //$stmt->bind_param('ss', $year, $month);	
    $stmt->bind_result($name, $date, $start);	
	$stmt->execute();
	$stmt->store_result();
	
	while($stmt->fetch()) {    

		$this->bookings_per_day[$date][] = $start;

		$this->bookings[] = array(
            "name" => $name, 
            "date" => $date, 
            "start" => $start        
 		); 
	
		// Used by the 'booking_form' function later to check whether there are any booked slots on the selected day  		
		if($date == $this->year . '-' . $this->month . '-' . $this->day) {
			$this->is_slot_booked_today = 1;
		} 

	}

	// Calculate how many slots there are per day
	$this->slots_per_day = 0;	
	for($i = strtotime($this->booking_start_time); $i<= strtotime($this->booking_end_time); $i = $i + $this->booking_frequency * 60) {
		$this->slots_per_day ++;
	}	

	$stmt->close();		
    $this->make_days_array($year, $month);    
            
} // Close function


// All unmodified again ...
 
function make_days_array($year, $month) { 

    // Calculate the number of days in the selected month                 
    $num_days_month = cal_days_in_month(CAL_GREGORIAN, $month, $year); 
    
    // Make $this->days array containing the Day Number and Day Number in the selected month	   
	
	for ($i = 1; $i <= $num_days_month; $i++) {	
	
		// Work out the Day Name ( Monday, Tuesday... ) from the $month and $year variables
        $d = mktime(0, 0, 0, $month, $i, $year); 
		
		// Create the array
        $this->days[] = array("daynumber" => $i, "dayname" => date("l", $d)); 		
    }   

	/*	
	Sample output of the $this->days array:
	
	[0] => Array
        (
            [daynumber] => 1
            [dayname] => Monday
        )

    [1] => Array
        (
            [daynumber] => 2
            [dayname] => Tuesday
        )
	*/
	
	$this->make_blank_start($year, $month);
	$this->make_blank_end($year, $month);	

} // Close function


function make_blank_start($year, $month) {

	/*
	Calendar months start on different days
	Therefore there are often blank 'unavailable' days at the beginning of the month which are showed as a grey block
	The code below creates the blank days at the beginning of the month
	*/	
	
	// Get first record of the days array which will be the First Day in the month ( eg Wednesday )
	$first_day = $this->days[0]['dayname'];	$s = 0;
		
		// Loop through $day_order array ( Monday, Tuesday ... )
		foreach($this->day_order as $i => $r) {
		
			// Compare the $first_day to the Day Order
			if($first_day == $r && $s == 0) {
				
				$s = 1;  // Set flag to 1 stop further processing
				
			} elseif($s == 0) {

				$blank = array(
					"daynumber" => 'blank',
					"dayname" => 'blank'
				);
			
				// Prepend elements to the beginning of the $day array
				array_unshift($this->days, $blank);
			}
			
	} // Close foreach	

} // Close function
	

function make_blank_end($year, $month) {

	/*
	Calendar months start on different days
	Therefore there are often blank 'unavailable' days at the end of the month which are showed as a grey block
	The code below creates the blank days at the end of the month
	*/
	
	// Add blank elements to end of array if required.
    $pad_end = 7 - (count($this->days) % 7);

    if ($pad_end < 7) {
	
		$blank = array(
			"daynumber" => 'blank',
			"dayname" => 'blank'
		);
	
        for ($i = 1; $i <= $pad_end; $i++) {							
			array_push($this->days, $blank);
		}
		
    } // Close if
		
	$this->calendar_top(); 

} // Close function
   
    
function calendar_top() {

	// This function creates the top of the table containg the date and the forward and back arrows 

	echo "
    <div id='lhs'><div id='outer_calendar'>
    
	<table border='0' cellpadding='0' cellspacing='0' id='calendar'>
        <tr id='week'>
        <td align='left'><a href='?month=" . date("m", $this->back) . "&amp;year=" . date("Y", $this->back) . "'>&laquo;</a></td>
        <td colspan='5' id='center_date'>" . date("F, Y", $this->selected_date) . "</td>    
        <td align='right'><a href='?month=" . date("m", $this->forward) . "&amp;year=" . date("Y", $this->forward) . "'>&raquo;</a></td>
    </tr>
    <tr>";
		
	/*
	Make the table header with the appropriate day of the week using the $day_format variable as user defined above
	Definition:
	
		1: Show First digit, eg: "M"
		2: Show First 3 letters, eg: "Mon"
		3: Full Day, eg: "Monday"		
		
	*/
	
	foreach($this->day_order as $r) {
	
		switch($this->day_format) {
		
			case(1): 	
				echo "<th>" . substr($r, 0, 1) . "</th>";					
			break;
			
			case(2):
				echo "<th>" . substr($r, 0, 3) . "</th>";			
			break;
			
			case(3): 	
				echo "<th>" . $r . "</th>";
			break;
			
		} // Close switch
	
	} // Close foreach

			
	echo "</tr>";   

	$this->make_cells();
    
} // Close function


function make_cells($table = '') {

	echo "<tr>";

	foreach($this->days as $i => $r) { // Loop through the date array

		$j = $i + 1; $tag = 0;	 		

		// If the the current day is found in the day_closed array, bookings are not allowed on this day  
		if(in_array($r['dayname'], $this->day_closed)) {			
			echo "\r\n<td width='21' valign='top' class='closed'>" . $this->day_closed_text . "</td>";		
			$tag = 1;
		}
		

		// Past days are greyed out
		if (mktime(0, 0, 0, $this->month, sprintf("%02s", $r['daynumber']) + 1, $this->year) < strtotime("now") && $tag != 1) {		
			
			echo "\r\n<td width='21' valign='top' class='past'>";			
				// Output day number 
				if($r['daynumber'] != 'blank') echo $r['daynumber']; 

			echo "</td>";		
			$tag = 1;
		}
		

		// If the element is set as 'blank', insert blank day
		if($r['dayname'] == 'blank' && $tag != 1) {		
			echo "\r\n<td width='21' valign='top' class='unavailable'></td>";	
			$tag = 1;
		}
				
				
		// Now check the booking array $this->booking to see whether we have a booking on this day 				
		$current_day = $this->year . '-' . $this->month . '-' . sprintf("%02s", $r['daynumber']);

		if(isset($this->bookings_per_day[$current_day]) && $tag == 0) {
		
			$current_day_slots_booked = count($this->bookings_per_day[$current_day]);

				if($current_day_slots_booked < $this->slots_per_day) {
				
					echo "\r\n<td width='21' valign='top'>
					<a href='calendar.php?month=" .  $this->month . "&amp;year=" .  $this->year . "&amp;day=" . sprintf("%02s", $r['daynumber']) . "' class='part_booked' title='This day is part booked'>" . 
					$r['daynumber'] . "</a></td>"; 
					$tag = 1;
				
				} else {
				
					echo "\r\n<td width='21' valign='top'>
					<a href='calendar.php?month=" .  $this->month . "&amp;year=" .  $this->year . "&amp;day=" . sprintf("%02s", $r['daynumber']) . "' class='fully_booked' title='This day is fully booked'>" . 
					$r['daynumber'] . "</a></td>"; 
					$tag = 1;			
				
				} // Close else	
		
		} // Close if

		
		if($tag == 0) {
		
			// JW added teaching day lookup
			
			$stmt = $this->link->prepare("SELECT daynumber,nonteach FROM termdates WHERE thedate = ?"); 
			$thedate = $this->year . "-" . $this->month . "-" . $r['daynumber'];
			$stmt->bind_param("s",$thedate);	
			$stmt->bind_result($daynum,$nonteach);	
			$stmt->execute();
			$stmt->store_result();
			$stmt->fetch();
			$stmt->free_result();
			$stmt->close();
			$daytip = $daynum;
			if ($nonteach) { $daytip = $daytip." (non-teaching)";}

			echo "\r\n<td width='21' valign='top'>
			<a href='calendar.php?month=" .  $this->month . "&amp;year=" .  $this->year . "&amp;day=" . sprintf("%02s", $r['daynumber']) . "' class='green' title='Day ".$daytip.".  Click to make/view bookings.'>" . 
			$r['daynumber'] . "</a></td>";			
		
		}
		
		// The modulus function below ($j % 7 == 0) adds a <tr> tag to every seventh cell + 1;
			if($j % 7 == 0 && $i >1) {
			echo "\r\n</tr>\r\n<tr>"; // Use modulus to give us a <tr> after every seven <td> cells
		}		
		
	}		
		
	echo "</tr></table></div><!-- Close outer_calendar DIV -->";
	
	if(isset($_GET['year']))
	$this->basket();
		
	echo "</div><!-- Close LHS DIV -->";

	// Check booked slots for selected date and only show the booking form if there are available slots	
	$current_day = $this->year . '-' . $this->month . '-' . $this->day;	
	$slots_selected_day = 0;
	
	if(isset($this->bookings_per_day[$current_day]))
	$slots_selected_day = count($this->bookings_per_day[$current_day]);
	
	if($this->day != 0 && $slots_selected_day < $this->slots_per_day) { 
		$this->booking_form();
	}
	
	
} // Close function


//  Booking_form gives the second block of the booking page - a grid of times/rooms you can book


function booking_form() {

	// Get day number to put in heading

	$stmt = $this->link->prepare("SELECT daynumber,nonteach FROM termdates WHERE thedate = ?"); 
	$thedate = $this->year . "-" . $this->month . "-" . $this->day;
	$stmt->bind_param("s",$thedate);	
	$stmt->bind_result($daynum,$nonteach);	
	$stmt->execute();
	$stmt->store_result();
	$stmt->fetch();
	$stmt->free_result();
	$stmt->close();
	$daytip = "Day ".$daynum;
	if ($nonteach) { $daytip = $daytip.", non-teaching";}

	echo "
	<div id='outer_booking'><h2>Available Rooms</h2>

	<p>
	Rooms available on <span> " . nicedate($this->day . "-" . $this->month . "-" . $this->year) . " (".$daytip.")</span><br>
	</p>
	
	<table width='400' border='0' cellpadding='2' cellspacing='0' id='booking'>
		<tr>
			<th width='40' align='left'>Period</th>";
			
	// Add list of rooms as column headings

	$stmt = $this->link->prepare("SELECT roomid,name FROM room ORDER BY name"); 
	$stmt->bind_result($roomid,$roomname);	
	$stmt->execute();
	$stmt->store_result();
	while($stmt->fetch()) {
		$room[] = array($roomid,$roomname);
		echo"
				<th width='30' align='center'>" . $roomname . "</th>";
	}
	echo"
			</tr>";

	// Create $period array of the booking times

	$stmt = $this->link->prepare("SELECT periodid,name FROM period"); 
	$stmt->bind_result($periodid,$periodname);	
	$stmt->execute();
	$stmt->store_result();
	
	while($stmt->fetch()) {
		$period[] = array($periodid,$periodname);
	}
	$stmt->free_result();
	$stmt->close();
	
	// Create $reason array of the booking reasons

	$stmt = $this->link->prepare("SELECT name,value FROM reason"); 
	$stmt->bind_result($rname,$rval);	
	$stmt->execute();
	$stmt->store_result();
	
	while($stmt->fetch()) {
		$rson[] = array($rname,$rval);
	}
	$stmt->free_result();
	$stmt->close();

	// Now layout the grid for booking choices

	foreach($period as $theperiod) {
		echo "
		<tr>\r\n<td>" . $theperiod[1] . "</td>\r\n";
		foreach($room as $theroom) {
			$stmt = $this->link->prepare("SELECT bookingid,teacherid,reason,status,yeargroup FROM booking WHERE periodid = ? AND roomid = ? AND bookingdate = ? ORDER BY status"); 
			$thedate = $this->year . "-" . $this->month . "-" . $this->day;
			$stmt->bind_param("iis",$theperiod[0],$theroom[0],$thedate);	
			$stmt->bind_result($bkid,$teacherid,$reason,$status,$yeargroup);	
			$stmt->execute();
			$stmt->store_result();
			$booked = $stmt->num_rows; // NB if booked is >=1 then booked evaluates to TRUE; only if booked is 0 does it equal FALSE
			if ($booked) { $stmt->fetch(); }
			$stmt->free_result();
			$stmt->close();
			if ($booked) {    // prepare the tooltip to show who's booked it
				$stmt = $this->link->prepare("SELECT name,emailaddress from teacher where teacherid = ?"); 
				$stmt->bind_param("i",$teacherid);	
				$stmt->bind_result($teachername,$tchremail);	
				$stmt->execute();
				$stmt->store_result();
				$stmt->fetch();
				$stmt->free_result();
				$stmt->close();
				$tooltip = $teachername;
				if ($status==ST_PROV) {
					$tooltip=$tooltip." (Provisional) ";
				} elseif ($status==ST_CONF) {
					$tooltip=$tooltip." (Confirmed) ";
				} elseif ($status==ST_LAPS) {
					$tooltip=$tooltip." (Lapsed) ";
				} elseif ($status==ST_TTBL) {
					$tooltip=$tooltip." (Timetabled) ";
				} 
				$tooltip=$tooltip."with ".$yeargroup; 
				if ($status!=ST_TTBL) { // add the reason unless timetabled lesson
					$tooltip=$tooltip." for ";
					$r = intval($reason);
					foreach ($rson as $thereason) { // bitwise AND with value to see if bit set for that reason
						if ($r & $thereason[1]) {$tooltip=$tooltip.$thereason[0].", "; } // if so, add reason name
					}
					$tooltip = substr($tooltip, 0, strlen($tooltip) - 2); // remove the final comma space
				}
			}
			// insert HTML for selecting room to book
			// data-val was date-time now using room%periodid@roomid and data-status is status#bkid@email
			$radiobut = "<input data-val='" . $theroom[1] . "%" . $theperiod[0] . "@" . $theroom[0];
			if ($booked && $status==ST_LAPS) { $radiobut = $radiobut."' data-status='".$status."#".$bkid."@".$tchremail; }
			$radiobut = $radiobut."' class='fields' type='radio' name='bookchoice'>";
			if ($booked) {
				if ($status==ST_LAPS) { // lapsed so orange and button
					echo "<td align='center' style='background-color: #999922;' title='".$tooltip."'>".$radiobut."</td>\r\n";
				} elseif ($status==ST_TTBL) { // timetabled so dark grey and no button
					echo "<td align='center' style='background-color: #666666;' title='".$tooltip."'></td>\r\n";
				} else { // provisional or confirmed so red and no button
					if ($booked>1) { $tooltip = "Lapsed booking: now booked by ".$teachername."."; } // replace tooltip if over-booked
					echo "<td align='center' style='background-color: #AA2222;' title='".$tooltip."'></td>\r\n";
				}
			}
			else {
				echo "<td align='center' style='background-color: #228822;'>".$radiobut."</td>\r\n";
			}
		} // close foreachroom (column)
		echo"
		</tr>";
	} // close foreach period (row)
			
	
	echo "</table></div><!-- Close outer_booking DIV -->";
		

} // Close function


// basket produces the final block display where the user selects their SIMS name, year group and booking reason

function basket($selected_day = '') {

	// Original code ...

	if(!isset($_GET['day']))
	$day = '01';
	else
	$day = $_GET['day'];	

	// Validate GET date values
	if(checkdate($_GET['month'], $day, $_GET['year']) !== false) {
		$selected_day = $_GET['year'] . '-' . $_GET['month'] . '-' . $day;	
	} else { 
		echo 'Invalid date!';
		exit();
	}
	
	// modified from here to point to my book_slots.php

	echo "<div id='outer_basket'>
	
	<h2><div id='make'></div></h2>
		
			<div id='basket_details'>
			
				<form method='post' action='book_slots.php'>
				
					<table style='layout: fixed;'>
					<tr>
					<td style='width: 120px;'>
					
					<label>Teacher Code</label>
					</td><td style='width: 120px;'>
					<select name='tchrcode' id='tchrcode' style='width: 100px;'>";
					
	// get the list of teachers for the dropdown

	$stmt = $this->link->prepare("SELECT teacherid, name, emailaddress FROM teacher"); 
	$stmt->bind_result($teacherid,$teachername,$email);	
	$stmt->execute();
	$stmt->store_result();
	echo"
				  <option value='' data-email='' selected>Please choose your ID</option>";
	while($stmt->fetch()) {
		echo"
					  <option value='$teacherid' data-email='$email'>$teachername</option>";
	}
	$stmt->free_result();
	$stmt->close();
	
	// make the dropdown for year groups - hard coded but not going to change (surely!)

	echo "
					</select>
					</td><td style='width: 200px;'>
					<label>Reason</label>
					</td></tr>
					<tr><td>
					<label>Year Group</label>
					</td><td>
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
					</td>";


	// get the list of reasons and their bit values then make checkboxes for each

	$stmt = $this->link->prepare("SELECT name,value FROM reason"); 
	$stmt->bind_result($rname,$rval);	
	$stmt->execute();
	$stmt->store_result();
	$stmt->fetch(); // do the first row
	echo"
		 			<td>
					<input name='reason' id = 'reason".$rval."' type='checkbox' value='".$rval."'>".$rname."<br>
					</td>
					</tr>";
	while($stmt->fetch()) { // all subsequent rows repeat
		echo"
					<tr>
					<td></td>
					<td></td>
		 			<td>
					<input name='reason' id = 'reason".$rval."' type='checkbox' value='".$rval."'>".$rname."<br>
					</td>
					</tr>";
	}
	$stmt->free_result();
	$stmt->close();

	// and finally close the table and add the hidden form inputs to pass to book_slots.php

	echo "				</table>
					
						<div id='outer_price'>
							<div id='currency'>" . "</div>
							<div id='total'></div>
						</div>									
					
					<input type='hidden' name='periodid' id='periodid'>
					<input type='hidden' name='roomid' id='roomid'>
					<input type='hidden' name='statusbid' id='statusbid'>
					<input type='hidden' name='temail' id='temail'>
					<input type='hidden' name='reason' id='reason'>
					<input type='hidden' name='cost_per_slot' id='cost_per_slot' value='" . $this->cost_per_slot . "'>
					<input type='hidden' name='booking_date' value='" . $_GET['year'] . '-' . $_GET['month'] . '-' . $day . "'>
					<input type='submit' class='classname' value='Make Booking'>

				</form>
			
			</div><!-- Close basket_details DIV -->
		
	</div><!-- Close outer_basket DIV -->";

} // Close function
                 
} // Close Class

?>
