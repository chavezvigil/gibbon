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
use Gibbon\Domain\System\SettingGateway;
use Gibbon\Forms\Form;
use Gibbon\Tables\DataTable;
use Gibbon\Services\Format;
use Gibbon\Domain\Students\StudentNoteGateway;

if (isActionAccessible($guid, $connection2, Resource::fromRoute('User Admin', 'studentsSettings')) == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $page->breadcrumbs->add(__('Students Settings'));

    echo '<h3>';
    echo __('Student Note Categories');
    echo '</h3>';
    echo '<p>';
    echo __('This section allows you to manage the categories which can be associated with student notes. Categories can be given templates, which will pre-populate the student note on selection.');
    echo '</p>';


    $studentNoteGateway = $container->get(StudentNoteGateway::class);

    // QUERY
    $criteria = $studentNoteGateway->newQueryCriteria(true)
        ->sortBy(['name'])
        ->fromArray($_POST);

    $studentNoteCategories = $studentNoteGateway->queryStudentNoteCategories($criteria);

    // DATA TABLE
    $table = DataTable::createPaginated('studentNoteCategoriesManage', $criteria);

    $table->addHeaderAction('add', __('Add'))
        ->setURL('/modules/User Admin/studentsSettings_noteCategory_add.php')
        ->displayLabel();

    $table->modifyRows(function ($values, $row) {
        if ($values['active'] == 'N') $row->addClass('error');
        return $row;
    });

    $table->addColumn('name', __('Name'));
    $table->addColumn('active', __('Active'))->format(Format::using('yesNo', 'active'));

    // ACTIONS
    $table->addActionColumn()
        ->addParam('gibbonStudentNoteCategoryID')
        ->format(function ($values, $actions) {
            $actions->addAction('edit', __('Edit'))
                ->setURL('/modules/User Admin/studentsSettings_noteCategory_edit.php');

            $actions->addAction('delete', __('Delete'))
                ->setURL('/modules/User Admin/studentsSettings_noteCategory_delete.php');
        });

    echo $table->render($studentNoteCategories);

    echo '<h3>';
    echo __('Settings');
    echo '</h3>';

    $form = Form::create('studentsSettings', $session->get('absoluteURL').'/modules/'.$session->get('module').'/studentsSettingsProcess.php');

    $form->addHiddenValue('address', $session->get('address'));

    $form->addRow()->addHeading('Student Notes', __('Student Notes'));

    $settingGateway = $container->get(SettingGateway::class);
    $setting = $settingGateway->getSettingByScope('Students', 'enableStudentNotes', true);
    $row = $form->addRow();
        $row->addLabel($setting['name'], __($setting['nameDisplay']))->description(__($setting['description']));
        $row->addYesNo($setting['name'])->selected($setting['value'])->required();

    $setting = $settingGateway->getSettingByScope('Students', 'noteCreationNotification', true);
    $noteCreationNotificationRoles = array(
        'Tutors' => __('Tutors'),
        'Tutors & Teachers' => __('Tutors & Teachers')
    );
    $row = $form->addRow();
        $row->addLabel($setting['name'], __($setting['nameDisplay']))->description(__($setting['description']));
        $row->addSelect($setting['name'])->fromArray($noteCreationNotificationRoles)->selected($setting['value'])->required();

    $form->addRow()->addHeading('Alerts', __('Alerts'));

    $setting = $settingGateway->getSettingByScope('Students', 'academicAlertLowThreshold', true);
    $row = $form->addRow();
        $row->addLabel($setting['name'], __($setting['nameDisplay']))
            ->description(__($setting['description']));
        $row->addNumber($setting['name'])
            ->setValue($setting['value'])
            ->decimalPlaces(0)
            ->minimum(0)
            ->maximum(50)
            ->required();

    $setting = $settingGateway->getSettingByScope('Students', 'academicAlertMediumThreshold', true);
    $row = $form->addRow();
        $row->addLabel($setting['name'], __($setting['nameDisplay']))
            ->description(__($setting['description']));
        $row->addNumber($setting['name'])
            ->setValue($setting['value'])
            ->decimalPlaces(0)
            ->minimum(0)
            ->maximum(50)
            ->required();

    $setting = $settingGateway->getSettingByScope('Students', 'academicAlertHighThreshold', true);
    $row = $form->addRow();
        $row->addLabel($setting['name'], __($setting['nameDisplay']))
            ->description(__($setting['description']));
        $row->addNumber($setting['name'])
            ->setValue($setting['value'])
            ->decimalPlaces(0)
            ->minimum(0)
            ->maximum(50)
            ->required();

        $setting = $settingGateway->getSettingByScope('Students', 'behaviourAlertLowThreshold', true);
        $row = $form->addRow();
            $row->addLabel($setting['name'], __($setting['nameDisplay']))
                ->description(__($setting['description']));
            $row->addNumber($setting['name'])
                ->setValue($setting['value'])
                ->decimalPlaces(0)
                ->minimum(0)
                ->maximum(50)
                ->required();

        $setting = $settingGateway->getSettingByScope('Students', 'behaviourAlertMediumThreshold', true);
        $row = $form->addRow();
            $row->addLabel($setting['name'], __($setting['nameDisplay']))
                ->description(__($setting['description']));
            $row->addNumber($setting['name'])
                ->setValue($setting['value'])
                ->decimalPlaces(0)
                ->minimum(0)
                ->maximum(50)
                ->required();

        $setting = $settingGateway->getSettingByScope('Students', 'behaviourAlertHighThreshold', true);
        $row = $form->addRow();
            $row->addLabel($setting['name'], __($setting['nameDisplay']))
                ->description(__($setting['description']));
            $row->addNumber($setting['name'])
                ->setValue($setting['value'])
                ->decimalPlaces(0)
                ->minimum(0)
                ->maximum(50)
                ->required();

    $row = $form->addRow()->addHeading('Day-Type Options', __('Day-Type Options'));

    $setting = $settingGateway->getSettingByScope('User Admin', 'dayTypeOptions', true);
    $row = $form->addRow();
        $row->addLabel($setting['name'], __($setting['nameDisplay']))->description(__($setting['description']));
        $row->addTextArea($setting['name'])->setValue($setting['value']);

    $setting = $settingGateway->getSettingByScope('User Admin', 'dayTypeText', true);
    $row = $form->addRow();
        $row->addLabel($setting['name'], __($setting['nameDisplay']))->description(__($setting['description']));
        $row->addTextArea($setting['name'])->setValue($setting['value']);

    $form->addRow()->addHeading('Miscellaneous', __('Miscellaneous'));

    $setting = $settingGateway->getSettingByScope('School Admin', 'studentAgreementOptions', true);
    $row = $form->addRow();
        $row->addLabel($setting['name'], __($setting['nameDisplay']))->description(__($setting['description']));
        $row->addTextArea($setting['name'])->setValue($setting['value']);

    $setting = $settingGateway->getSettingByScope('Students', 'firstAidDescriptionTemplate', true);
    $row = $form->addRow();
        $row->addLabel($setting['name'], __($setting['nameDisplay']))->description(__($setting['description']));
        $row->addTextArea($setting['name'])->setValue($setting['value']);

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
