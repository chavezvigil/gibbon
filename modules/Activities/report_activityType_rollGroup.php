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
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Services\Format;
use Gibbon\Tables\Prefab\ReportTable;
use Gibbon\Domain\Activities\ActivityReportGateway;
use Gibbon\Domain\Students\StudentGateway;

//Module includes
require_once __DIR__ . '/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Activities/report_activityType_rollGroup.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $gibbonFormGroupID = isset($_GET['gibbonFormGroupID'])? $_GET['gibbonFormGroupID'] : null;
    $status = isset($_GET['status'])? $_GET['status'] : null;
    $dateType = getSettingByScope($connection2, 'Activities', 'dateType');

    $viewMode = isset($_REQUEST['format']) ? $_REQUEST['format'] : '';

    if (empty($viewMode)) {
        $page->breadcrumbs->add(__('Activity Type by Form Group'));

        $form = Form::create('filter', $_SESSION[$guid]['absoluteURL'].'/index.php','get');

        $form->setTitle(__('Choose Form Group'));
        $form->setFactory(DatabaseFormFactory::create($pdo));
        $form->setClass('noIntBorder fullWidth');

        $form->addHiddenValue('q', "/modules/".$_SESSION[$guid]['module']."/report_activityType_rollGroup.php");

        $row = $form->addRow();
            $row->addLabel('gibbonFormGroupID', __('Form Group'));
            $row->addSelectRollGroup('gibbonFormGroupID', $_SESSION[$guid]['gibbonSchoolYearID'])->selected($gibbonFormGroupID)->required();

        $row = $form->addRow();
            $row->addLabel('status', __('Status'));
            $row->addSelect('status')->fromArray(array('Accepted' => __('Accepted'), 'Registered' => __('Registered')))->selected($status)->required();

        $row = $form->addRow();
            $row->addFooter();
            $row->addSearchSubmit($gibbon->session);

        echo $form->getOutput();
    }

    if (empty($gibbonFormGroupID)) return;

    $activityGateway = $container->get(ActivityReportGateway::class);
    $studentGateway = $container->get(StudentGateway::class);

    // CRITERIA
    $criteria = $activityGateway->newQueryCriteria(true)
        ->searchBy($activityGateway->getSearchableColumns(), isset($_GET['search'])? $_GET['search'] : '')
        ->sortBy(['surname', 'preferredName'])
        ->pageSize(!empty($viewMode) ? 0 : 50)
        ->fromPOST();

    $formGroups = $studentGateway->queryStudentEnrolmentByRollGroup($criteria, $gibbonFormGroupID);

    // Build a set of activity counts for each student
    $formGroups->transform(function(&$student) use ($activityGateway,  $status) {
        $activities = $activityGateway->selectActivitiesByStudent($student['gibbonSchoolYearID'], $student['gibbonPersonID'], $status)->fetchAll();
        $student['total'] = count($activities);
        $student['activities'] = array();

        foreach ($activities as $activity) {
            $type = !empty($activity['type'])? $activity['type'] : 'noType';
            $student[$type] = isset($student[$type])? $student[$type] + 1 : 1;
            $student['activities'][] = $activity['name'];
        }
    });

    $activityTypeSetting = getSettingByScope($connection2, 'Activities', 'activityTypes');
    $activityTypes = array_map('trim', explode(',', $activityTypeSetting));

    // DATA TABLE
    $table = ReportTable::createPaginated('activityType_rollGroup', $criteria)->setViewMode($viewMode, $gibbon->session);

    $table->setTitle(__('Activity Type by Form Group'));

    $table->addColumn('rollGroup', __('Form Group'))->width('10%');
    $table->addColumn('student', __('Student'))
        ->width('25%')
        ->sortable(['surname', 'preferredName'])
        ->format(function ($student) use ($guid) {
            $title = implode('<br>', $student['activities']);
            $name = Format::name('', $student['preferredName'], $student['surname'], 'Student', true);
            $url = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Students/student_view_details.php&gibbonPersonID='.$student['gibbonPersonID'].'&subpage=Activities';

            return Format::link($url, $name, $title);
        });

    $table->addColumn('noType', __('No Type'))->notSortable()->width('10%');

    foreach ($activityTypes as $type) {
        $table->addColumn($type, __($type))->notSortable()->width('10%');
    }

    $table->addColumn('total', __('Total'))->notSortable()->width('10%');

    echo $table->render($formGroups);
}
