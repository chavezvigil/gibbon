<?php
/*
Gibbon: the flexible, open school platform
Founded by Ross Parker at ICHK Secondary. Built by Ross Parker, Sandra Kuipers and the Gibbon community (https://gibbonedu.org/about/)
Copyright © 2010, Gibbon Foundation
Gibbon™, Gibbon Education Ltd. (Hong Kong)

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
use Gibbon\Data\Validator;

require_once '../../gibbon.php';

$_POST = $container->get(Validator::class)->sanitize($_POST, ['template' => 'HTML']);

$gibbonStudentNoteCategoryID = $_GET['gibbonStudentNoteCategoryID'] ?? '';
$URL = $session->get('absoluteURL').'/index.php?q=/modules/'.getModuleName($_POST['address'])."/studentsSettings_noteCategory_edit.php&gibbonStudentNoteCategoryID=$gibbonStudentNoteCategoryID";

if (isActionAccessible($guid, $connection2, '/modules/User Admin/studentsSettings_noteCategory_edit.php') == false) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
} else {
    //Proceed!
    //Check if gibbonStudentNoteCategoryID specified
    if ($gibbonStudentNoteCategoryID == '') {
        $URL .= '&return=error1';
        header("Location: {$URL}");
    } else {
        try {
            $data = array('gibbonStudentNoteCategoryID' => $gibbonStudentNoteCategoryID);
            $sql = 'SELECT * FROM gibbonStudentNoteCategory WHERE gibbonStudentNoteCategoryID=:gibbonStudentNoteCategoryID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
            exit();
        }

        if ($result->rowCount() != 1) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
        } else {
            $name = $_POST['name'] ?? '';
            $active = $_POST['active'] ?? '';
            $template = $_POST['template'] ?? '';

            //Validate Inputs
            if ($name == '' or $active == '') {
                $URL .= '&return=error3';
                header("Location: {$URL}");
            } else {
                //Check unique inputs for uniquness
                try {
                    $data = array('name' => $name, 'gibbonStudentNoteCategoryID' => $gibbonStudentNoteCategoryID);
                    $sql = 'SELECT * FROM gibbonStudentNoteCategory WHERE name=:name AND NOT gibbonStudentNoteCategoryID=:gibbonStudentNoteCategoryID';
                    $result = $connection2->prepare($sql);
                    $result->execute($data);
                } catch (PDOException $e) {
                    $URL .= '&return=error2';
                    header("Location: {$URL}");
                    exit();
                }

                if ($result->rowCount() > 0) {
                    $URL .= '&return=error3';
                    header("Location: {$URL}");
                } else {
                    //Write to database
                    try {
                        $data = array('name' => $name, 'active' => $active, 'template' => $template, 'gibbonStudentNoteCategoryID' => $gibbonStudentNoteCategoryID);
                        $sql = 'UPDATE gibbonStudentNoteCategory SET name=:name, active=:active, template=:template WHERE gibbonStudentNoteCategoryID=:gibbonStudentNoteCategoryID';
                        $result = $connection2->prepare($sql);
                        $result->execute($data);
                    } catch (PDOException $e) {
                        $URL .= '&return=error2';
                        header("Location: {$URL}");
                        exit();
                    }

                    $URL .= '&return=success0';
                    header("Location: {$URL}");
                }
            }
        }
    }
}
