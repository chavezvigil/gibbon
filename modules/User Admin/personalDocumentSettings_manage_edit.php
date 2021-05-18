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

use Gibbon\Forms\Form;
use Gibbon\Domain\User\PersonalDocumentTypeGateway;
use Gibbon\Forms\DatabaseFormFactory;

if (isActionAccessible($guid, $connection2, '/modules/User Admin/personalDocumentSettings_manage_edit.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $page->breadcrumbs
        ->add(__('Personal Document'), 'personalDocumentSettings.php')
        ->add(__('Edit Personal Document Type'));

    $gibbonPersonalDocumentTypeID = $_GET['gibbonPersonalDocumentTypeID'] ?? '';
    $personalDocumentTypeGateway = $container->get(PersonalDocumentTypeGateway::class);

    $values = $personalDocumentTypeGateway->getByID($gibbonPersonalDocumentTypeID);

    if (empty($values)) {
        $page->addError(__('The specified record cannot be found.'));
        return;
    }

    $form = Form::create('personalDocumentType', $_SESSION[$guid]['absoluteURL'].'/modules/User Admin/personalDocumentSettings_manage_editProcess.php');
    $form->setFactory(DatabaseFormFactory::create($pdo));

    $form->addHiddenValue('address', $_SESSION[$guid]['address']);
    $form->addHiddenValue('gibbonPersonalDocumentTypeID', $gibbonPersonalDocumentTypeID);

    $form->addRow()->addHeading(__('Basic Details'));

    $row = $form->addRow();
        $row->addLabel('name', __('Document Name'))->description(__('Must be unique.'));
        $row->addTextField('name')->required()->maxLength(60);

    $row = $form->addRow();
        $row->addLabel('description', __('Description'));
        $row->addTextField('description')->maxLength(255);

    $row = $form->addRow();
        $row->addLabel('active', __('Active'));
        $row->addYesNo('active')->required();

    $row = $form->addRow();
        $row->addLabel('required', __('Required'));
        $row->addYesNo('required')->required();

    $row = $form->addRow();
        $row->addLabel('sequenceNumber', __('Sequence Number'));
        $row->addSequenceNumber('sequenceNumber', 'gibbonPersonalDocumentType', $values['sequenceNumber'])->maxLength(3);

    $form->addRow()->addHeading(__('Configure'));

    $types = [
        'Passport'      => __('Passport'),
        'Identity Card' => __('Identity Card'),
        'Other'         => __('Other'),
    ];
    $row = $form->addRow();
        $row->addLabel('type', __('Type'));
        $row->addSelect('type')->fromArray($types)->required()->placeholder();

    $fieldOptions = [
        'documentName'   => __('Name on Document'),
        'documentNumber' => __('Document Number'),
        'documentType'   => __('Document Type'),
        'dateIssue'      => __('Issue Date'),
        'dateExpiry'     => __('Expiry Date'),
        'filePath'       => __('File Upload'),
        'country'        => __('Country'),
    ];
    $fields = json_decode($values['fields'] ?? '');
    $fields = array_combine($fields, $fields);
    unset($values['fields']);

    $checked = array_filter(array_intersect_key($fields, $fieldOptions));
    $row = $form->addRow();
        $row->addLabel('fields', __('Fields'));
        $row->addCheckbox('fields')->fromArray($fieldOptions)->checked(array_keys($checked));

    $form->addRow()->addHeading(__('Visibility'));

    $activePersonOptions = array(
        'activePersonStudent' => __('Student'),
        'activePersonStaff'   => __('Staff'),
        'activePersonParent'  => __('Parent'),
        'activePersonOther'   => __('Other'),
    );
    $checked = array_filter(array_intersect_key($values, $activePersonOptions));
    $row = $form->addRow();
        $row->addLabel('roleCategories', __('Role Categories'));
        $row->addCheckbox('roleCategories')->fromArray($activePersonOptions)->checked(array_keys($checked));

    $row = $form->addRow();
        $row->addLabel('activeDataUpdater', __('Include In Data Updater?'));
        $row->addSelect('activeDataUpdater')->fromArray(array('1' => __('Yes'), '0' => __('No')))->required();

    $row = $form->addRow();
        $row->addLabel('activeApplicationForm', __('Include In Application Form?'));
        $row->addSelect('activeApplicationForm')->fromArray(array('1' => __('Yes'), '0' => __('No')))->required();
        
    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    $form->loadAllValuesFrom($values);

    echo $form->getOutput();
}