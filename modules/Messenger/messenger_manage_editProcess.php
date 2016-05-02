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

include '../../functions.php';
include '../../config.php';

//New PDO DB connection
$pdo = new Gibbon\sqlConnection();
$connection2 = $pdo->getConnection();

@session_start();

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]['timezone']);

$gibbonMessengerID = $_GET['gibbonMessengerID'];
$search = null;
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}
$URL = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['address'])."/messenger_manage_edit.php&sidebar=true&search=$search&gibbonMessengerID=".$gibbonMessengerID;
$time = time();

if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_manage_edit.php') == false) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
} else {
    if (empty($_POST)) {
        $URL .= '&return=error5';
        header("Location: {$URL}");
    } else {
        $highestAction = getHighestGroupedAction($guid, $_GET['address'], $connection2);
        if ($highestAction == false) {
            $URL .= "&return=error0$params";
            header("Location: {$URL}");
        } else {
            //Proceed!
            //Validate Inputs
            $messageWall = $_POST['messageWall'];
            $date1 = null;
            if (isset($_POST['date1'])) {
                if ($_POST['date1'] != '') {
                    $date1 = dateConvert($guid, $_POST['date1']);
                }
            }
            $date2 = null;
            if (isset($_POST['date2'])) {
                if ($_POST['date2'] != '') {
                    $date2 = dateConvert($guid, $_POST['date2']);
                }
            }
            $date3 = null;
            if (isset($_POST['date3'])) {
                if ($_POST['date3'] != '') {
                    $date3 = dateConvert($guid, $_POST['date3']);
                }
            }
            $subject = $_POST['subject'];
            $body = $_POST['body'];

            if ($subject == '' or $body == '') {
                $URL .= '&return=error3';
                header("Location: {$URL}");
            } else {
                //Write to database
                try {
                    $dataUpdate = array('messageWall' => $messageWall, 'messageWall_date1' => $date1, 'messageWall_date2' => $date2, 'messageWall_date3' => $date3, 'subject' => $subject, 'body' => $body, 'timestamp' => date('Y-m-d H:i:s'), 'gibbonMessengerID' => $gibbonMessengerID);
                    $sqlUpdate = 'UPDATE gibbonMessenger SET messageWall=:messageWall, messageWall_date1=:messageWall_date1, messageWall_date2=:messageWall_date2, messageWall_date3=:messageWall_date3, subject=:subject, body=:body, timestamp=:timestamp WHERE gibbonMessengerID=:gibbonMessengerID';
                    $resultUpdate = $connection2->prepare($sqlUpdate);
                    $resultUpdate->execute($dataUpdate);
                } catch (PDOException $e) {
                    $URL .= '&return=error2';
                    header("Location: {$URL}");
                    exit();
                }

                //TARGETS
                $partialFail = false;

                try {
                    $dataRemove = array('gibbonMessengerID' => $gibbonMessengerID);
                    $sqlRemove = 'DELETE FROM gibbonMessengerTarget WHERE gibbonMessengerID=:gibbonMessengerID';
                    $resultRemove = $connection2->prepare($sqlRemove);
                    $resultRemove->execute($dataRemove);
                } catch (PDOException $e) {
                    $partialFail = true;
                }

                //Roles
                if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_role')) {
                    $_POST['role'];
                    if ($_POST['role'] == 'Y') {
                        $choices = $_POST['roles'];
                        if ($choices != '') {
                            foreach ($choices as $t) {
                                try {
                                    $data = array('gibbonMessengerID' => $gibbonMessengerID, 't' => $t);
                                    $sql = "INSERT INTO gibbonMessengerTarget SET gibbonMessengerID=:gibbonMessengerID, type='Role', id=:t";
                                    $result = $connection2->prepare($sql);
                                    $result->execute($data);
                                } catch (PDOException $e) {
                                    $partialFail = true;
                                }
                            }
                        }
                    }
                }

                //Role Categories
                if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_role')) {
                    if ($_POST['roleCategory'] == 'Y') {
                        $choices = $_POST['roleCategories'];
                        if ($choices != '') {
                            foreach ($choices as $t) {
                                try {
                                    $data = array('gibbonMessengerID' => $gibbonMessengerID, 't' => $t);
                                    $sql = "INSERT INTO gibbonMessengerTarget SET gibbonMessengerID=:gibbonMessengerID, type='Role Category', id=:t";
                                    $result = $connection2->prepare($sql);
                                    $result->execute($data);
                                } catch (PDOException $e) {
                                    $partialFail = true;
                                }
                            }
                        }
                    }
                }

                //Year Groups
                if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_yearGroups_any')) {
                    if ($_POST['yearGroup'] == 'Y') {
                        $staff = $_POST['yearGroupsStaff'];
                        $students = $_POST['yearGroupsStudents'];
                        $parents = 'N';
                        if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_yearGroups_parents')) {
                            $parents = $_POST['yearGroupsParents'];
                        }
                        $choices = $_POST['yearGroups'];
                        if ($choices != '') {
                            foreach ($choices as $t) {
                                try {
                                    $data = array('gibbonMessengerID' => $gibbonMessengerID, 't' => $t, 'staff' => $staff, 'students' => $students, 'parents' => $parents);
                                    $sql = "INSERT INTO gibbonMessengerTarget SET gibbonMessengerID=:gibbonMessengerID, type='Year Group', id=:t, staff=:staff, students=:students, parents=:parents";
                                    $result = $connection2->prepare($sql);
                                    $result->execute($data);
                                } catch (PDOException $e) {
                                    $partialFail = true;
                                }
                            }
                        }
                    }
                }

                //Roll Groups
                if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_rollGroups_my') or isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_rollGroups_any')) {
                    if ($_POST['rollGroup'] == 'Y') {
                        $staff = $_POST['rollGroupsStaff'];
                        $students = $_POST['rollGroupsStudents'];
                        $parents = 'N';
                        if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_rollGroups_parents')) {
                            $parents = $_POST['rollGroupsParents'];
                        }
                        $choices = $_POST['rollGroups'];
                        if ($choices != '') {
                            foreach ($choices as $t) {
                                try {
                                    $data = array('gibbonMessengerID' => $gibbonMessengerID, 't' => $t, 'staff' => $staff, 'students' => $students, 'parents' => $parents);
                                    $sql = "INSERT INTO gibbonMessengerTarget SET gibbonMessengerID=:gibbonMessengerID, type='Roll Group', id=:t, staff=:staff, students=:students, parents=:parents";
                                    $result = $connection2->prepare($sql);
                                    $result->execute($data);
                                } catch (PDOException $e) {
                                    $partialFail = true;
                                }
                            }
                        }
                    }
                }

                //Course Groups
                if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_courses_my') or isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_courses_any')) {
                    if ($_POST['course'] == 'Y') {
                        $staff = $_POST['coursesStaff'];
                        $students = $_POST['coursesStudents'];
                        $parents = 'N';
                        if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_courses_parents')) {
                            $parents = $_POST['coursesParents'];
                        }
                        $choices = $_POST['courses'];
                        if ($choices != '') {
                            foreach ($choices as $t) {
                                try {
                                    $data = array('gibbonMessengerID' => $gibbonMessengerID, 'id' => $t, 'staff' => $staff, 'students' => $students, 'parents' => $parents);
                                    $sql = "INSERT INTO gibbonMessengerTarget SET gibbonMessengerID=:gibbonMessengerID, type='Course', id=:id, staff=:staff, students=:students, parents=:parents";
                                    $result = $connection2->prepare($sql);
                                    $result->execute($data);
                                } catch (PDOException $e) {
                                    $partialFail = true;
                                }
                            }
                        }
                    }
                }

                //Class Groups
                if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_classes_my') or isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_classes_any')) {
                    if ($_POST['class'] == 'Y') {
                        $staff = $_POST['classesStaff'];
                        $students = $_POST['classesStudents'];
                        $parents = 'N';
                        if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_classes_parents')) {
                            $parents = $_POST['classesParents'];
                        }
                        $choices = $_POST['classes'];
                        if ($choices != '') {
                            foreach ($choices as $t) {
                                try {
                                    $data = array('gibbonMessengerID' => $gibbonMessengerID, 'id' => $t, 'staff' => $staff, 'students' => $students, 'parents' => $parents);
                                    $sql = "INSERT INTO gibbonMessengerTarget SET gibbonMessengerID=:gibbonMessengerID, type='Class', id=:id, staff=:staff, students=:students, parents=:parents";
                                    $result = $connection2->prepare($sql);
                                    $result->execute($data);
                                } catch (PDOException $e) {
                                    $partialFail = true;
                                }
                            }
                        }
                    }
                }

                //Activity Groups
                if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_activities_my') or isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_activities_any')) {
                    if ($_POST['activity'] == 'Y') {
                        $staff = $_POST['activitiesStaff'];
                        $students = $_POST['activitiesStudents'];
                        $parents = 'N';
                        if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_activities_parents')) {
                            $parents = $_POST['activitiesParents'];
                        }
                        $choices = $_POST['activities'];
                        if ($choices != '') {
                            foreach ($choices as $t) {
                                try {
                                    $data = array('gibbonMessengerID' => $gibbonMessengerID, 'id' => $t, 'staff' => $staff, 'students' => $students, 'parents' => $parents);
                                    $sql = "INSERT INTO gibbonMessengerTarget SET gibbonMessengerID=:gibbonMessengerID, type='Activity', id=:id, staff=:staff, students=:students, parents=:parents";
                                    $result = $connection2->prepare($sql);
                                    $result->execute($data);
                                } catch (PDOException $e) {
                                    $partialFail = true;
                                }
                            }
                        }
                    }
                }

                //Applicants
                if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_applicants')) {
                    if ($_POST['applicants'] == 'Y') {
                        $choices = $_POST['applicantList'];
                        if ($choices != '') {
                            foreach ($choices as $t) {
                                try {
                                    $data = array('gibbonMessengerID' => $gibbonMessengerID, 'id' => $t);
                                    $sql = "INSERT INTO gibbonMessengerTarget SET gibbonMessengerID=:gibbonMessengerID, type='Applicants', id=:id";
                                    $result = $connection2->prepare($sql);
                                    $result->execute($data);
                                } catch (PDOException $e) {
                                    $partialFail = true;
                                }
                            }
                        }
                    }
                }

                //Houses
                if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_houses_all') or isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_houses_my')) {
                    if ($_POST['houses'] == 'Y') {
                        $choices = $_POST['houseList'];
                        if ($choices != '') {
                            foreach ($choices as $t) {
                                try {
                                    $data = array('gibbonMessengerID' => $gibbonMessengerID, 'id' => $t);
                                    $sql = "INSERT INTO gibbonMessengerTarget SET gibbonMessengerID=:gibbonMessengerID, type='Houses', id=:id";
                                    $result = $connection2->prepare($sql);
                                    $result->execute($data);
                                } catch (PDOException $e) {
                                    $partialFail = true;
                                }
                            }
                        }
                    }
                }

                //Individuals
                if (isActionAccessible($guid, $connection2, '/modules/Messenger/messenger_post.php', 'New Message_individuals')) {
                    if ($_POST['individuals'] == 'Y') {
                        $choices = $_POST['individualList'];
                        if ($choices != '') {
                            foreach ($choices as $t) {
                                try {
                                    $data = array('gibbonMessengerID' => $gibbonMessengerID, 'id' => $t);
                                    $sql = "INSERT INTO gibbonMessengerTarget SET gibbonMessengerID=:gibbonMessengerID, type='Individuals', id=:id";
                                    $result = $connection2->prepare($sql);
                                    $result->execute($data);
                                } catch (PDOException $e) {
                                    $partialFail = true;
                                }
                            }
                        }
                    }
                }

                if ($partialFail == true) {
                    header("Location: {$URL}");
                } else {
                    $URL .= '&return=success0';
                    header("Location: {$URL}");
                }
            }
        }
    }
}
