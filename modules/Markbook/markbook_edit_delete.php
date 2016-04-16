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

//Module includes
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Markbook/markbook_edit_delete.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print __($guid, "You do not have access to this action.") ;
	print "</div>" ;
}
else {
	$highestAction=getHighestGroupedAction($guid, $_GET["q"], $connection2) ;
	if ($highestAction==FALSE) {
		print "<div class='error'>" ;
		print __($guid, "The highest grouped action cannot be determined.") ;
		print "</div>" ;
	}
	else {
		//Check if school year specified
		$gibbonCourseClassID=$_GET["gibbonCourseClassID"];
		$gibbonMarkbookColumnID=$_GET["gibbonMarkbookColumnID"] ;
		if ($gibbonCourseClassID=="" OR $gibbonMarkbookColumnID=="") {
			print "<div class='error'>" ;
				print __($guid, "You have not specified one or more required parameters.") ;
			print "</div>" ;
		}
		else {
			try {
				if ($highestAction=="Edit Markbook_everything") {
					$data=array("gibbonCourseClassID"=>$gibbonCourseClassID); 
					$sql="SELECT gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class, gibbonCourseClass.gibbonCourseClassID, gibbonCourse.gibbonDepartmentID, gibbonYearGroupIDList FROM gibbonCourse, gibbonCourseClass WHERE gibbonCourse.gibbonCourseID=gibbonCourseClass.gibbonCourseID AND gibbonCourseClass.gibbonCourseClassID=:gibbonCourseClassID ORDER BY course, class" ;
				}
				else {
					$data=array("gibbonPersonID"=>$_SESSION[$guid]["gibbonPersonID"], "gibbonCourseClassID"=>$gibbonCourseClassID); 
					$sql="SELECT gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class, gibbonCourseClass.gibbonCourseClassID, gibbonCourse.gibbonDepartmentID, gibbonYearGroupIDList FROM gibbonCourse, gibbonCourseClass, gibbonCourseClassPerson WHERE gibbonCourse.gibbonCourseID=gibbonCourseClass.gibbonCourseID AND gibbonCourseClass.gibbonCourseClassID=gibbonCourseClassPerson.gibbonCourseClassID AND gibbonCourseClassPerson.gibbonPersonID=:gibbonPersonID AND role='Teacher' AND gibbonCourseClass.gibbonCourseClassID=:gibbonCourseClassID ORDER BY course, class" ;
				}	
				$result=$connection2->prepare($sql);
				$result->execute($data);
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}

			if ($result->rowCount()!=1) {
				print "<div class='error'>" ;
					print __($guid, "The selected record does not exist, or you do not have access to it.") ;
				print "</div>" ;
			}
			else {
				try {
					$data2=array("gibbonMarkbookColumnID"=>$gibbonMarkbookColumnID); 
					$sql2="SELECT * FROM gibbonMarkbookColumn WHERE gibbonMarkbookColumnID=:gibbonMarkbookColumnID" ;
					$result2=$connection2->prepare($sql2);
					$result2->execute($data2);
				}
				catch(PDOException $e) { 
					print "<div class='error'>" . $e->getMessage() . "</div>" ; 
				}

				if ($result2->rowCount()!=1) {
					print "<div class='error'>" ;
						print __($guid, "The selected record does not exist, or you do not have access to it.") ;
					print "</div>" ;
				}
				else {
					//Let's go!
					$row=$result->fetch() ;
					$row2=$result2->fetch() ;
				
					print "<div class='trail'>" ;
					print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __($guid, "Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __($guid, getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/markbook_view.php&gibbonCourseClassID=" . $_GET["gibbonCourseClassID"] . "'>" . __($guid, 'View') . " " . $row["course"] . "." . $row["class"] . " " . __($guid, 'Markbook') . "</a> > </div><div class='trailEnd'>" . __($guid, 'Delete Column') . "</div>" ;
					print "</div>" ;
				
					if ($row2["groupingID"]!="" AND $row2["gibbonPersonIDCreator"]!=$_SESSION[$guid]["gibbonPersonID"]) {
						print "<div class='error'>" ;
							print __($guid, "This column is part of a set of columns, and so cannot be individually deleted.") ;
						print "</div>" ;
					}
					else {
						if (isset($_GET["deleteReturn"])) { $deleteReturn=$_GET["deleteReturn"] ; } else { $deleteReturn="" ; }
						$deleteReturnMessage="" ;
						$class="error" ;
						if (!($deleteReturn=="")) {
							if ($deleteReturn=="fail0") {
								$deleteReturnMessage=__($guid, "Your request failed because you do not have access to this action.") ;	
							}
							else if ($deleteReturn=="fail1") {
								$deleteReturnMessage=__($guid, "Your request failed because your inputs were invalid.") ;	
							}
							else if ($deleteReturn=="fail2") {
								$deleteReturnMessage=__($guid, "Your request failed due to a database error.") ;	
							}
							else if ($deleteReturn=="fail3") {
								$deleteReturnMessage=__($guid, "Your request failed because your inputs were invalid.") ;	
							}
							print "<div class='$class'>" ;
								print $deleteReturnMessage;
							print "</div>" ;
						} 
						?>
						<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/markbook_edit_deleteProcess.php?gibbonMarkbookColumnID=$gibbonMarkbookColumnID" ?>">
							<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
								<tr>
									<td> 
										<b><?php print __($guid, 'Are you sure you want to delete this record?') ; ?></b><br/>
										<span style="font-size: 90%; color: #cc0000"><i><?php print __($guid, 'This operation cannot be undone, and may lead to loss of vital data in your system. PROCEED WITH CAUTION!') ; ?></i></span>
									</td>
									<td class="right">
								
									</td>
								</tr>
								<tr>
									<td> 
										<input name="gibbonCourseClassID" id="gibbonCourseClassID" value="<?php print $gibbonCourseClassID ?>" type="hidden">
										<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
										<input type="submit" value="<?php print __($guid, 'Yes') ; ?>">
									</td>
									<td class="right">
								
									</td>
								</tr>
							</table>
						</form>
						<?php
					}
				}
			}
		}
	}
}
?>