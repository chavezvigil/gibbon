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

use Gibbon\Forms\Prefab\DeleteForm;

//Module includes
require_once __DIR__ . '/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Data Updater/data_family_manage_delete.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    //Check if school year specified
    $gibbonFamilyUpdateID = $_GET['gibbonFamilyUpdateID'];
    if ($gibbonFamilyUpdateID == '') {
        echo "<div class='error'>";
        echo __('You have not specified one or more required parameters.');
        echo '</div>';
    } else {
        try {
            $data = array('gibbonFamilyUpdateID' => $gibbonFamilyUpdateID);
            $sql = 'SELECT * FROM gibbonFamilyUpdate WHERE gibbonFamilyUpdateID=:gibbonFamilyUpdateID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }

        if ($result->rowCount() != 1) {
            echo "<div class='error'>";
            echo __('The selected record does not exist, or you do not have access to it.');
            echo '</div>';
        } else {
            //Let's go!
            
            $form = DeleteForm::createForm($_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module']."/data_family_manage_deleteProcess.php?gibbonFamilyUpdateID=".$gibbonFamilyUpdateID);
            echo $form->getOutput();
        }
    }
}
