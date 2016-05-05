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

@session_start();

//Module includes
include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Activities/activities_manage_edit.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __($guid, 'You do not have access to this action.');
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>".__($guid, 'Home')."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".__($guid, getModuleName($_GET['q']))."</a> > <a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Activities/activities_manage.php'>".__($guid, 'Manage Activities')."</a> > </div><div class='trailEnd'>".__($guid, 'Edit Activity').'</div>';
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, array('error3' => 'Your request failed due to an attachment error.'));
    }

    //Check if school year specified
    $gibbonActivityID = $_GET['gibbonActivityID'];
    if ($gibbonActivityID == 'Y') {
        echo "<div class='error'>";
        echo __($guid, 'You have not specified one or more required parameters.');
        echo '</div>';
    } else {
        try {
            $data = array('gibbonActivityID' => $gibbonActivityID);
            $sql = 'SELECT * FROM gibbonActivity WHERE gibbonActivityID=:gibbonActivityID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }

        if ($result->rowCount() != 1) {
            echo "<div class='error'>";
            echo __($guid, 'The selected record does not exist, or you do not have access to it.');
            echo '</div>';
        } else {
            //Let's go!
            $row = $result->fetch();
            if ($_GET['search'] != '') {
                echo "<div class='linkTop'>";
                echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Activities/activities_manage.php&search='.$_GET['search']."'>".__($guid, 'Back to Search Results').'</a>';
                echo '</div>';
            }
            ?>
			<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module']."/activities_manage_editProcess.php?gibbonActivityID=$gibbonActivityID&search=".$_GET['search'] ?>">
				<table class='smallIntBorder fullWidth' cellspacing='0'>	
					<tr class='break'>
						<td colspan=2> 
							<h3><?php echo __($guid, 'Basic Information') ?></h3>
						</td>
					</tr>
					<tr>
						<td style='width: 275px'> 
							<b><?php echo __($guid, 'Name') ?> *</b><br/>
						</td>
						<td class="right">
							<input name="name" id="name" maxlength=40 value="<?php echo $row['name'] ?>" type="text" class="standardWidth">
							<script type="text/javascript">
								var name2=new LiveValidation('name');
								name2.add(Validate.Presence);
							</script>
						</td>
					</tr>
					
					<tr>
						<td> 
							<b><?php echo __($guid, 'Provider') ?> *</b><br/>
							<span class="emphasis small"></span>
						</td>
						<td class="right">
							<select name="provider" id="provider" class="standardWidth">
								<option <?php if ($row['provider'] == 'School') { echo 'selected '; } ?>value="School"><?php echo $_SESSION[$guid]['organisationNameShort'] ?></option>
								<option <?php if ($row['provider'] == 'External') { echo 'selected '; } ?>value="External"><?php echo __($guid, 'External') ?></option>
							</select>
						</td>
					</tr>
					
					<?php
                    try {
                        $dataType = array();
                        $sqlType = "SELECT * FROM gibbonSetting WHERE scope='Activities' AND name='activityTypes'";
                        $resultType = $connection2->prepare($sqlType);
                        $resultType->execute($dataType);
                    } catch (PDOException $e) {
                    }

					if ($resultType->rowCount() == 1) {
						$rowType = $resultType->fetch();

						$options = $rowType['value'];
						if ($options != '') {
							$options = explode(',', $options);
							?>
							<tr>
								<td> 
									<b><?php echo __($guid, 'Type') ?></b><br/>
									<span class="emphasis small"></span>
								</td>
								<td class="right">
									<select name="type" id="type" class="standardWidth">
										<option value=""></option>
										<?php
                                        for ($i = 0; $i < count($options); ++$i) {
                                            ?>
											<option <?php if ($row['type'] == trim($options[$i])) { echo 'selected '; } ?>value="<?php echo trim($options[$i]) ?>"><?php echo trim($options[$i]) ?></option>
										<?php
                                        }
                                        ?>
									</select>
								</td>
							</tr>
							<?php
							}
						}
						?>
					<tr>
						<td> 
							<b><?php echo __($guid, 'Active') ?> *</b><br/>
							<span class="emphasis small"></span>
						</td>
						<td class="right">
							<select name="active" id="active" class="standardWidth">
								<option <?php if ($row['active'] == 'Y') { echo 'selected '; } ?>value="Y"><?php echo __($guid, 'Yes') ?></option>
								<option <?php if ($row['active'] == 'N') { echo 'selected '; } ?>value="N"><?php echo __($guid, 'No') ?></option>
							</select>
						</td>
					</tr>
					
					<tr>
						<td> 
							<b><?php echo __($guid, 'Registration') ?> *</b><br/>
							<span class="emphasis small"><?php echo __($guid, 'Assuming system-wide registration is open, should this activity be open for registration?') ?></span>
						</td>
						<td class="right">
							<select name="registration" id="registration" class="standardWidth">
								<option <?php if ($row['registration'] == 'Y') { echo 'selected '; } ?>value="Y"><?php echo __($guid, 'Yes') ?></option>
								<option <?php if ($row['registration'] == 'N') { echo 'selected '; } ?>value="N"><?php echo __($guid, 'No') ?></option>
							</select>
						</td>
					</tr>
					
					<?php
                    //Should we show date as term or date?
                    $dateType = getSettingByScope($connection2, 'Activities', 'dateType');

					echo "<input type='hidden' name='dateType' value='$dateType'>";

					if ($dateType != 'Date') {
						?>
						<tr>
							<td> 
								<b><?php echo __($guid, 'Terms') ?></b><br/>
								<span class="emphasis small"><?php echo __($guid, 'Terms in which the activity will run.') ?><br/></span>
							</td>
							<td class="right">
								<?php 
                                $terms = getTerms($connection2, $_SESSION[$guid]['gibbonSchoolYearID']);
								if ($terms == '') {
									echo '<i>'.__($guid, 'No terms available.').'</i>';
								} else {
									for ($i = 0; $i < count($terms); $i = $i + 2) {
										$checked = '';
										if (is_numeric(strpos($row['gibbonSchoolYearTermIDList'], $terms[$i]))) {
											$checked = 'checked ';
										}
										echo $terms[($i + 1)]." <input $checked type='checkbox' name='gibbonSchoolYearTermID[]' value='$terms[$i]'><br/>";
									}
								}
								?>
							</td>
						</tr>
						<?php
						} else {
						?>
						<tr>
							<td> 
								<b><?php echo __($guid, 'Listing Start Date') ?> *</b><br/>
								<span class="emphasis small"><?php echo __($guid, 'Format:') ?> <?php if ($_SESSION[$guid]['i18n']['dateFormat'] == '') {
									echo 'dd/mm/yyyy';
								} else {
									echo $_SESSION[$guid]['i18n']['dateFormat'];
								} ?><br/><?php echo __($guid, 'Default: 2 weeks before the end of the current term.') ?></span>
							</td>
							<td class="right">
								<input name="listingStart" id="listingStart" maxlength=10 value="<?php echo dateConvertBack($guid, $row['listingStart']) ?>" type="text" class="standardWidth">
								<script type="text/javascript">
									var listingStart=new LiveValidation('listingStart');
									listingStart.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]['i18n']['dateFormatRegEx'] == '') {
										echo "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i";
									} else {
										echo $_SESSION[$guid]['i18n']['dateFormatRegEx'];
									}
													?>, failureMessage: "Use <?php if ($_SESSION[$guid]['i18n']['dateFormat'] == '') {
										echo 'dd/mm/yyyy';
									} else {
										echo $_SESSION[$guid]['i18n']['dateFormat'];
									}
													?>." } ); 
								</script>
								 <script type="text/javascript">
									$(function() {
										$( "#listingStart" ).datepicker();
									});
								</script>
							</td>
						</tr>
						<tr>
							<td> 
								<b><?php echo __($guid, 'Listing End Date') ?> *</b><br/>
								<span class="emphasis small"><?php echo __($guid, 'Format:') ?> <?php if ($_SESSION[$guid]['i18n']['dateFormat'] == '') {
									echo 'dd/mm/yyyy';
								} else {
									echo $_SESSION[$guid]['i18n']['dateFormat'];
								}?><br/><?php echo __($guid, 'Default: 2 weeks after the start of next term.') ?></span>
							</td>
							<td class="right">
								<input name="listingEnd" id="listingEnd" maxlength=10 value="<?php echo dateConvertBack($guid, $row['listingEnd']) ?>" type="text" class="standardWidth">
								<script type="text/javascript">
									var listingEnd=new LiveValidation('listingEnd');
									listingEnd.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]['i18n']['dateFormatRegEx'] == '') {
										echo "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i";
									} else {
										echo $_SESSION[$guid]['i18n']['dateFormatRegEx'];
									}
													?>, failureMessage: "Use <?php if ($_SESSION[$guid]['i18n']['dateFormat'] == '') {
										echo 'dd/mm/yyyy';
									} else {
										echo $_SESSION[$guid]['i18n']['dateFormat'];
									}
													?>." } ); 
								</script>
								 <script type="text/javascript">
									$(function() {
										$( "#listingEnd" ).datepicker();
									});
								</script>
							</td>
						</tr>
						<tr>
							<td> 
								<b><?php echo __($guid, 'Program Start Date') ?> *</b><br/>
								<span class="emphasis small"><?php echo __($guid, 'Format:') ?> <?php if ($_SESSION[$guid]['i18n']['dateFormat'] == '') {
									echo 'dd/mm/yyyy';
								} else {
									echo $_SESSION[$guid]['i18n']['dateFormat'];
								} ?><br/><?php echo __($guid, 'Default: first day of next term.') ?></span>
							</td>
							<td class="right">
								<input name="programStart" id="programStart" maxlength=10 value="<?php echo dateConvertBack($guid, $row['programStart']) ?>" type="text" class="standardWidth">
								<script type="text/javascript">
									var programStart=new LiveValidation('programStart');
									programStart.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]['i18n']['dateFormatRegEx'] == '') {
										echo "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i";
									} else {
										echo $_SESSION[$guid]['i18n']['dateFormatRegEx'];
									}
													?>, failureMessage: "Use <?php if ($_SESSION[$guid]['i18n']['dateFormat'] == '') {
										echo 'dd/mm/yyyy';
									} else {
										echo $_SESSION[$guid]['i18n']['dateFormat'];
									}
									?>." } ); 
								</script>
								 <script type="text/javascript">
									$(function() {
										$( "#programStart" ).datepicker();
									});
								</script>
							</td>
						</tr>
						<tr>
							<td> 
								<b><?php echo __($guid, 'Program End Date') ?> *</b><br/>
								<span class="emphasis small"><?php echo __($guid, 'Format:') ?> <?php if ($_SESSION[$guid]['i18n']['dateFormat'] == '') {
									echo 'dd/mm/yyyy';
								} else {
									echo $_SESSION[$guid]['i18n']['dateFormat'];
								} ?><br/><?php echo __($guid, 'Default: last day of the next term.') ?></span>
							</td>
							<td class="right">
								<input name="programEnd" id="programEnd" maxlength=10 value="<?php echo dateConvertBack($guid, $row['programEnd']) ?>" type="text" class="standardWidth">
								<script type="text/javascript">
									var programEnd=new LiveValidation('programEnd');
									programEnd.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]['i18n']['dateFormatRegEx'] == '') {
										echo "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i";
									} else {
										echo $_SESSION[$guid]['i18n']['dateFormatRegEx'];
									}
													?>, failureMessage: "Use <?php if ($_SESSION[$guid]['i18n']['dateFormat'] == '') {
										echo 'dd/mm/yyyy';
									} else {
										echo $_SESSION[$guid]['i18n']['dateFormat'];
									}
                					?>." } ); 
								</script>
								 <script type="text/javascript">
									$(function() {
										$( "#programEnd" ).datepicker();
									});
								</script>
							</td>
						</tr>
						<?php
						}
						?>
						<tr>
						<td> 
							<b><?php echo __($guid, 'Year Groups') ?></b><br/>
						</td>
						<td class="right">
							<?php 
                            $yearGroups = getYearGroups($connection2, $_SESSION[$guid]['gibbonSchoolYearID']);
							if ($yearGroups == '') {
								echo '<i>'.__($guid, 'No year groups available.').'</i>';
							} else {
								for ($i = 0; $i < count($yearGroups); $i = $i + 2) {
									$checked = '';
									if (is_numeric(strpos($row['gibbonYearGroupIDList'], $yearGroups[$i]))) {
										$checked = 'checked ';
									}
									echo __($guid, $yearGroups[($i + 1)])." <input $checked type='checkbox' name='gibbonYearGroupIDCheck".($i) / 2 ."'><br/>";
									echo "<input type='hidden' name='gibbonYearGroupID".($i) / 2 ."' value='".$yearGroups[$i]."'>";
								}
							}
							?>
							<input type="hidden" name="count" value="<?php echo(count($yearGroups)) / 2 ?>">
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php echo __($guid, 'Max Participants') ?> *</b><br/>
						</td>
						<td class="right">
							<input name="maxParticipants" id="maxParticipants" maxlength=4 value="<?php echo $row['maxParticipants'] ?>" type="text" class="standardWidth">
							<script type="text/javascript">
								var maxParticipants=new LiveValidation('maxParticipants');
								maxParticipants.add(Validate.Presence);
								maxParticipants.add(Validate.Numericality);
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php echo __($guid, 'Cost') ?> *</b><br/>
							<span class="emphasis small"><?php echo __($guid, 'For entire programme').'. '.$_SESSION[$guid]['currency'].'.' ?><br/></span>
						</td>
						<td class="right">
							<?php
                                if (getSettingByScope($connection2, 'Activities', 'payment') == 'None' or getSettingByScope($connection2, 'Activities', 'payment') == 'Single') {
                                    ?>
									<input readonly name="paymentNote" id="paymentNote" maxlength=100 value="<?php echo __($guid, 'Per Activty payment is switched off') ?>" type="text" class="standardWidth">
									<?php

                                } else {
                                    ?>
									<input name="payment" id="payment" maxlength=7 value="<?php echo $row['payment'] ?>" type="text" class="standardWidth">
									<script type="text/javascript">
										var payment=new LiveValidation('payment');
										payment.add(Validate.Presence);
										payment.add(Validate.Numericality);
									</script>
									 <?php
                                }
                                ?>
						</td>
					</tr>
					<tr>
						<td colspan=2> 
							<b><?php echo __($guid, 'Description') ?></b> 
							<?php echo getEditor($guid,  true, 'description', $row['description'], 10, true) ?>
						</td>
					</tr>
					
					<tr class='break'>
						<td colspan=2> 
							<h3><?php echo __($guid, 'Current Time Slots') ?></h3>
						</td>
					</tr>
					<tr>
						<td colspan=2> 
							<?php
                            try {
                                $data = array('gibbonActivityID' => $gibbonActivityID);
                                $sql = 'SELECT * FROM gibbonActivitySlot JOIN gibbonDaysOfWeek ON (gibbonActivitySlot.gibbonDaysOfWeekID=gibbonDaysOfWeek.gibbonDaysOfWeekID) WHERE gibbonActivityID=:gibbonActivityID ORDER BY gibbonDaysOfWeek.gibbonDaysOfWeekID';
                                $result = $connection2->prepare($sql);
                                $result->execute($data);
                            } catch (PDOException $e) {
                                echo "<div class='error'>".$e->getMessage().'</div>';
                            }

							if ($result->rowCount() < 1) {
								echo "<div class='error'>";
								echo __($guid, 'There are no records to display.');
								echo '</div>';
							} else {
								echo '<i><b>Warning</b>: If you delete a time slot, any unsaved changes to this planner entry will be lost!</i>';
								echo "<table cellspacing='0' style='width: 100%'>";
								echo "<tr class='head'>";
								echo '<th>';
								echo __($guid, 'Name');
								echo '</th>';
								echo '<th>';
								echo __($guid, 'Time');
								echo '</th>';
								echo '<th>';
								echo __($guid, 'Location');
								echo '</th>';
								echo '<th>';
								echo __($guid, 'Actions');
								echo '</th>';
								echo '</tr>';

								$count = 0;
								$rowNum = 'odd';
								while ($row = $result->fetch()) {
									if ($count % 2 == 0) {
										$rowNum = 'even';
									} else {
										$rowNum = 'odd';
									}
									++$count;

									//COLOR ROW BY STATUS!
									echo "<tr class=$rowNum>";
									echo '<td>';
									echo __($guid, $row['name']);
									echo '</td>';
									echo '<td>';
									echo substr($row['timeStart'], 0, 5).' - '.substr($row['timeEnd'], 0, 5);
									echo '</td>';
									echo '<td>';
									if ($row['gibbonSpaceID'] != '') {
										try {
											$dataSpace = array('gibbonSpaceID' => $row['gibbonSpaceID']);
											$sqlSpace = 'SELECT * FROM gibbonSpace WHERE gibbonSpaceID=:gibbonSpaceID';
											$resultSpace = $connection2->prepare($sqlSpace);
											$resultSpace->execute($dataSpace);
										} catch (PDOException $e) {
											echo "<div class='error'>".$e->getMessage().'</div>';
										}

										if ($resultSpace->rowCount() == 1) {
											$rowSpace = $resultSpace->fetch();
											echo $rowSpace['name'];
										}
									} else {
										echo $row['locationExternal'];
									}
									echo '</td>';
									echo '<td>';
									echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module'].'/activities_manage_edit_slot_deleteProcess.php?address='.$_GET['q'].'&gibbonActivitySlotID='.$row['gibbonActivitySlotID']."&gibbonActivityID=$gibbonActivityID&search=".$_GET['search']."'><img title='".__($guid, 'Delete')."' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/garbage.png'/></a>";
									echo '</td>';
									echo '</tr>';
								}
								echo '</table>';
							}
							?>
						</td>
					</tr>
					
					<tr class='break'>
						<td colspan=2> 
							<h3><?php echo __($guid, 'New Time Slots') ?></h3>
						</td>
					</tr>
					
					<script type="text/javascript">
						/* Resource 1 Option Control */
						$(document).ready(function(){
							$("#slot1InternalRow").css("display","none");
							$("#slot1ExternalRow").css("display","none");
							$("#slot1ButtonRow").css("display","none");
							
							$(".slot1Location").click(function(){
								if ($('input[name=slot1Location]:checked').val()=="External" ) {
									$("#slot1InternalRow").css("display","none");
									$("#slot1ExternalRow").slideDown("fast", $("#slot1ExternalRow").css("display","table-row")); 
									$("#slot1ButtonRow").slideDown("fast", $("#slot1ButtonRow").css("display","table-row")); 
								} else {
									$("#slot1ExternalRow").css("display","none");
									$("#slot1InternalRow").slideDown("fast", $("#slot1InternalRow").css("display","table-row")); 
									$("#slot1ButtonRow").slideDown("fast", $("#slot1ButtonRow").css("display","table-row")); 
								}
							 });
						});
						
						/* Resource 2 Display Control */
						$(document).ready(function(){
							$("#slot2Row").css("display","none");
							$("#slot2DayRow").css("display","none");
							$("#slot2StartRow").css("display","none");
							$("#slot2EndRow").css("display","none");
							$("#slot2LocationRow").css("display","none");
							$("#slot2InternalRow").css("display","none");
							$("#slot2ExternalRow").css("display","none");
							$("#slot2ButtonRow").css("display","none");
							
							$("#slot1Button").click(function(){
								$("#slot2Button").css("display","none");
								$("#slot2Row").slideDown("fast", $("#slot2Row").css("display","table-row")); 
								$("#slot2DayRow").slideDown("fast", $("#slot2DayRow").css("display","table-row")); 
								$("#slot2StartRow").slideDown("fast", $("#slot2StartRow").css("display","table-row")); 
								$("#slot2EndRow").slideDown("fast", $("#slot2EndRow").css("display","table-row")); 
								$("#slot2LocationRow").slideDown("fast", $("#slot2LocationRow").css("display","table-row")); 
							});
						});
						
						/* Resource 2 Option Control */
						$(document).ready(function(){
							$(".slot2Location").click(function(){
								if ($('input[name=slot2Location]:checked').val()=="External" ) {
									$("#slot2InternalRow").css("display","none");
									$("#slot2ExternalRow").slideDown("fast", $("#slot2ExternalRow").css("display","table-row")); 
								} else {
									$("#slot2ExternalRow").css("display","none");
									$("#slot2InternalRow").slideDown("fast", $("#slot2InternalRow").css("display","table-row")); 
								}
							 });
						});
					</script>
						
					<?php
                    for ($i = 1; $i < 3; ++$i) {
                        ?>
						<tr id="slot<?php echo $i ?>Row">
							<td colspan=2> 
								<h4><?php echo sprintf(__($guid, 'Slot %1$s'), $i) ?></h4>
							</td>
						</tr>
						<tr id="slot<?php echo $i ?>DayRow">
							<td> 
								<b><?php echo sprintf(__($guid, 'Slot %1$s Day'), $i) ?></b><br/>
							</td>
							<td class="right">
								<select name="gibbonDaysOfWeekID<?php echo $i ?>" id="gibbonDaysOfWeekID<?php echo $i ?>" class="standardWidth">
									<option value=""></option>
									<?php
                                    try {
                                        $dataSelect = array();
                                        $sqlSelect = 'SELECT * FROM gibbonDaysOfWeek ORDER BY sequenceNumber';
                                        $resultSelect = $connection2->prepare($sqlSelect);
                                        $resultSelect->execute($dataSelect);
                                    } catch (PDOException $e) {
                                    }

									while ($rowSelect = $resultSelect->fetch()) {
										echo "<option value='".$rowSelect['gibbonDaysOfWeekID']."'>".__($guid, $rowSelect['name']).'</option>';
									}
									?>
								</select>
							</td>
						</tr>
						<tr id="slot<?php echo $i ?>StartRow">
							<td> 
								<b><?php echo sprintf(__($guid, 'Slot %1$s Start Time'), $i) ?></b><br/>
								<span class="emphasis small"><?php echo __($guid, 'Format: hh:mm') ?></span>
							</td>
							<td class="right">
								<input name="timeStart<?php echo $i ?>" id="timeStart<?php echo $i ?>" maxlength=5 value="" type="text" class="standardWidth">
								<script type="text/javascript">
									$(function() {
										var availableTags=[
											<?php
                                            try {
                                                $dataAuto = array();
                                                $sqlAuto = 'SELECT DISTINCT timeStart FROM gibbonActivitySlot ORDER BY timeStart';
                                                $resultAuto = $connection2->prepare($sqlAuto);
                                                $resultAuto->execute($dataAuto);
                                            } catch (PDOException $e) {
                                            }
											while ($rowAuto = $resultAuto->fetch()) {
												echo '"'.substr($rowAuto['timeStart'], 0, 5).'", ';
											}
											?>
										];
										$( "#timeStart<?php echo $i ?>" ).autocomplete({source: availableTags});
									});
								</script>
							</td>
						</tr>
						<tr id="slot<?php echo $i ?>EndRow">
							<td> 
								<b><?php echo sprintf(__($guid, 'Slot %1$s End Time'), $i) ?></b><br/>
								<span class="emphasis small"><?php echo __($guid, 'Format: hh:mm') ?></span>
							</td>
							<td class="right">
								<input name="timeEnd<?php echo $i ?>" id="timeEnd<?php echo $i ?>" maxlength=5 value="" type="text" class="standardWidth">
								<script type="text/javascript">
									$(function() {
										var availableTags=[
											<?php
                                            try {
                                                $dataAuto = array();
                                                $sqlAuto = 'SELECT DISTINCT timeEnd FROM gibbonActivitySlot ORDER BY timeEnd';
                                                $resultAuto = $connection2->prepare($sqlAuto);
                                                $resultAuto->execute($dataAuto);
                                            } catch (PDOException $e) {
                                            }
											while ($rowAuto = $resultAuto->fetch()) {
												echo '"'.substr($rowAuto['timeEnd'], 0, 5).'", ';
											}
											?>
										];
										$( "#timeEnd<?php echo $i ?>" ).autocomplete({source: availableTags});
									});
								</script>
							</td>
						</tr>
						<tr id="slot<?php echo $i ?>LocationRow">
							<td> 
								<b><?php echo sprintf(__($guid, 'Slot %1$s Location'), $i) ?></b><br/>
							</td>
							<td class="right">
								<input type="radio" name="slot<?php echo $i ?>Location" value="Internal" class="slot<?php echo $i ?>Location" /> Internal
								<input type="radio" name="slot<?php echo $i ?>Location" value="External" class="slot<?php echo $i ?>Location" /> External
							</td>
						</tr>
						<tr id="slot<?php echo $i ?>InternalRow">
							<td> 
								
							</td>
							<td class="right">
								<select name="gibbonSpaceID<?php echo $i ?>" id="gibbonSpaceID<?php echo $i ?>" class="standardWidth">
									<option value=""></option>
									<?php
                                    try {
                                        $dataSelect = array();
                                        $sqlSelect = 'SELECT * FROM gibbonSpace ORDER BY name';
                                        $resultSelect = $connection2->prepare($sqlSelect);
                                        $resultSelect->execute($dataSelect);
                                    } catch (PDOException $e) {
                                    }
									while ($rowSelect = $resultSelect->fetch()) {
										echo "<option value='".$rowSelect['gibbonSpaceID']."'>".$rowSelect['name'].'</option>';
									}
									?>
								</select>
							</td>
						</tr>
						<tr id="slot<?php echo $i ?>ExternalRow">
							<td> 
								
							</td>
							<td class="right">
								<input name="location<?php echo $i ?>External" id="location<?php echo $i ?>External" maxlength=50 value="" type="text" class="standardWidth">
							</td>
						</tr>
						<tr id="slot<?php echo $i ?>ButtonRow">
							<td> 
							</td>
							<td class="right">
								<input class="buttonAsLink" id="slot<?php echo $i ?>Button" type="button" value="Add Another Slot">
								<a href=""></a>
							</td>
						</tr>
						<?php
						}
                    ?>
							
					<tr class='break'>
						<td colspan=2> 
							<h3><?php echo __($guid, 'Current Staff') ?></h3>
						</td>
					</tr>
					<tr>
						<td colspan=2> 
							<?php
                            try {
                                $data = array('gibbonActivityID' => $gibbonActivityID);
                                $sql = "SELECT preferredName, surname, gibbonActivityStaff.* FROM gibbonActivityStaff JOIN gibbonPerson ON (gibbonActivityStaff.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE gibbonActivityID=:gibbonActivityID AND gibbonPerson.status='Full' ORDER BY surname, preferredName";
                                $result = $connection2->prepare($sql);
                                $result->execute($data);
                            } catch (PDOException $e) {
                                echo "<div class='error'>".$e->getMessage().'</div>';
                            }
							if ($result->rowCount() < 1) {
								echo "<div class='error'>";
								echo __($guid, 'There are no records to display.');
								echo '</div>';
							} else {
								echo '<i><b>Warning</b>: If you delete a guest, any unsaved changes to this planner entry will be lost!</i>';
								echo "<table cellspacing='0' style='width: 100%'>";
								echo "<tr class='head'>";
								echo '<th>';
								echo __($guid, 'Name');
								echo '</th>';
								echo '<th>';
								echo __($guid, 'Role');
								echo '</th>';
								echo '<th>';
								echo __($guid, 'Actions');
								echo '</th>';
								echo '</tr>';

								$count = 0;
								$rowNum = 'odd';
								while ($row = $result->fetch()) {
									if ($count % 2 == 0) {
										$rowNum = 'even';
									} else {
										$rowNum = 'odd';
									}
									++$count;

														//COLOR ROW BY STATUS!
														echo "<tr class=$rowNum>";
									echo '<td>';
									echo formatName('', $row['preferredName'], $row['surname'], 'Staff', true, true);
									echo '</td>';
									echo '<td>';
									echo $row['role'];
									echo '</td>';
									echo '<td>';
									echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module'].'/activities_manage_edit_staff_deleteProcess.php?address='.$_GET['q'].'&gibbonActivityStaffID='.$row['gibbonActivityStaffID']."&gibbonActivityID=$gibbonActivityID&search=".$_GET['search']."'><img title='".__($guid, 'Delete')."' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/garbage.png'/></a>";
									echo '</td>';
									echo '</tr>';
								}
								echo '</table>';
							}
							?>
						</td>
					</tr>
					<tr class='break'>
						<td colspan=2> 
							<h3><?php echo __($guid, 'New Staff') ?></h3>
						</td>
					</tr>
					<tr>
					<td> 
						<b><?php echo __($guid, 'Staff') ?></b><br/>
						<span class="emphasis small"><?php echo __($guid, 'Use Control, Command and/or Shift to select multiple.') ?></span>
					</td>
					<td class="right">
						<select name="staff[]" id="staff[]" multiple style="width: 302px; height: 150px">
							<?php
                            echo "<optgroup label='--".__($guid, 'Staff')."--'>";
							try {
								$dataSelect = array();
								$sqlSelect = "SELECT * FROM gibbonPerson JOIN gibbonStaff ON (gibbonPerson.gibbonPersonID=gibbonStaff.gibbonPersonID) WHERE status='Full' ORDER BY surname, preferredName";
								$resultSelect = $connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							} catch (PDOException $e) {
							}
							while ($rowSelect = $resultSelect->fetch()) {
								echo "<option value='".$rowSelect['gibbonPersonID']."'>".formatName(htmlPrep($rowSelect['title']), ($rowSelect['preferredName']), htmlPrep($rowSelect['surname']), 'Staff', true, true).'</option>';
							}
							echo '</optgroup>';
							echo "<optgroup label='--".__($guid, 'All Users')."--'>";
							try {
								$dataSelect = array();
								$sqlSelect = "SELECT gibbonPersonID, surname, preferredName, status FROM gibbonPerson WHERE status='Full' ORDER BY surname, preferredName";
								$resultSelect = $connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							} catch (PDOException $e) {
							}
							while ($rowSelect = $resultSelect->fetch()) {
								$selected = '';
								if ($row['gibbonPersonIDStatusResponsible'] == $rowSelect['gibbonPersonID']) {
									$selected = 'selected';
								}
								echo "<option $selected value='".$rowSelect['gibbonPersonID']."'>".formatName('', htmlPrep($rowSelect['preferredName']), htmlPrep($rowSelect['surname']), 'Student', true)."$expected</option>";
							}
							echo '</optgroup>'; ?>
						</select>
					</td>
					<tr>
						<td> 
							<b><?php echo __($guid, 'Role') ?></b><br/>
						</td>
						<td class="right">
							<select name="role" id="role" class="standardWidth">
								<option value="Organiser"><?php echo __($guid, 'Organiser') ?></option>
								<option value="Coach"><?php echo __($guid, 'Coach') ?></option>
								<option value="Assistant"><?php echo __($guid, 'Assistant') ?></option>
								<option value="Other"><?php echo __($guid, 'Other') ?></option>
							</select>
						</td>
					</tr>
					
					<tr>
						<td>
							<span class="emphasis small">* <?php echo __($guid, 'denotes a required field'); ?></span>
						</td>
						<td class="right">
							<input type="hidden" name="address" value="<?php echo $_SESSION[$guid]['address'] ?>">
							<input type="submit" value="<?php echo __($guid, 'Submit'); ?>">
						</td>
					</tr>
				</table>
			</form>
			<?php

        }
    }
}
?>