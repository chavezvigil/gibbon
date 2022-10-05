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

use Gibbon\Services\Module\Action;
use Gibbon\Services\Format;
use Gibbon\Forms\Prefab\DeleteForm;
use Gibbon\Domain\DataUpdater\StaffUpdateGateway;

// Module includes
require_once __DIR__ . '/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, new Action('Data Updater', 'data_staff_manage_delete')) == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!

    // Check required values
    $gibbonStaffUpdateID = $_GET['gibbonStaffUpdateID'];
    if ($gibbonStaffUpdateID == '') {
        $page->addError(__('You have not specified one or more required parameters.'));
        return;
    }

    // Check database records exist
    $values = $container->get(StaffUpdateGateway::class)->getByID($gibbonStaffUpdateID);
    if (empty($values)) {
        $page->addError(__('The selected record does not exist, or you do not have access to it.'));
        return;
    }

    $form = DeleteForm::createForm($session->get('absoluteURL').'/modules/'.$session->get('module')."/data_staff_manage_deleteProcess.php?gibbonStaffUpdateID=".$gibbonStaffUpdateID);
    echo $form->getOutput();
}
