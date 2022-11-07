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

use Gibbon\Auth\Access\Resource;
use Gibbon\Forms\Form;
if (isActionAccessible($guid, $connection2, Resource::fromRoute('Timetable', 'tt')) == false) {
        // Access denied
        $page->addError(__('You do not have access to this action.'));
    } else {
        //Proceed!
    $form = Form::create('timetableSubManage', $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module'].'/tt_exportProcess.php');

    // Readonly
    $row = $form->addRow();
        $row->addLabel('value', __('Export to Calendar'))->description(__('Please configure your export settings below.'));
    // Select
    $row = $form->addRow();
        $row->addLabel('value', __('Reminders'))->description(__('When to send an alert reminding you of each event'));
        $form->addHiddenValue('address', $session->get('address'));
        $options = array('No Reminder', '5 minutes before', '10 minutes before', '15 minutes before'); //TODO: Turn this into key => value pairs, and modify the select to use them, so that you can have the values be the relevant time specifier to go directly into your $vAlarm, so you require less logic when exporting
        $row->addSelect('options')->fromArray($options);
        $form->addHiddenValue('superSecretHiddenValue',$_GET['gibbonPersonID']);

    $row = $form->addRow();
        $row->addLabel('prefix', __('Prefix'))->description(__('Optionally add your input as a prefix to calendar events.'));
        $row->addTextField('prefix');

    $row = $form->addRow();
        $row->addSubmit();

    echo $form->getOutput();

}
    ?>
        <script>
        const button = document.querySelector('#timetableSubManage input[type=submit]');
        button.addEventListener('click', event => {
                setTimeout(()=>{
                  tb_remove();
                },1000);
          });
        </script>
