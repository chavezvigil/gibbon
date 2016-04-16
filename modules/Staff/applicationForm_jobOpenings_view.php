<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

@session_start() ;

//Module includes from User Admin (for custom fields)
include "./modules/User Admin/moduleFunctions.php" ;

$proceed=FALSE ;
$public=FALSE ;
if (isset($_SESSION[$guid]["username"])==FALSE) {
	$public=TRUE ;
	$proceed=TRUE ;
}
else {
	if (isActionAccessible($guid, $connection2, "/modules/Staff/applicationForm.php")!=FALSE) {
		$proceed=TRUE ;
	}
}

if ($proceed==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print __($guid, "You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	if (isset($_SESSION[$guid]["username"])) {
		print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __($guid, "Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __($guid, getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . $_SESSION[$guid]["organisationNameShort"] . " " . __($guid, 'Application Form') . "</div>" ;
	}
	else {
		print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __($guid, "Home") . "</a> > </div><div class='trailEnd'>" . $_SESSION[$guid]["organisationNameShort"] . " " . __($guid, 'Application Form') . "</div>" ;
	}
	print "</div>" ;
	
	}
	
	//Check for job openings
	try {
		$data=array("dateOpen"=>date("Y-m-d")); 
		$sql="SELECT * FROM gibbonStaffJobOpening WHERE active='Y' AND dateOpen<=:dateOpen ORDER BY jobTitle" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" ;
		print __($guid, "Your request failed due to a database error.") ;
		print "</div>" ;
	}
	
	if ($result->rowCount()<1) {
		print "<div class='error'>" ;
		print __($guid, "There are no job openings at this time: please try again later.") ;
		print "</div>" ;
	}
	else {
		$jobOpenings=$result->fetchAll() ;
		
		print "<div class='linkTop'>" ;
		print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/applicationForm.php'>" .  __($guid, 'Submit Application Form') . "<img style='margin-left: 5px' title='" . __($guid, 'Submit Application Form') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png'/></a>" ;
		print "</div>" ;
		
		foreach ($jobOpenings AS $jobOpening) {
			print "<h3>" . $jobOpening["jobTitle"] . "</h3>" ;
			print "<p><b>" . sprintf(__($guid, 'Job Type: %1$s'), $jobOpening["type"]) . "</b></p>" ;
			print $jobOpening["description"] . "<br/>" ;
		}
	}
?>