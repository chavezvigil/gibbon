<?php

use Gibbon\Services\Format;
use Gibbon\Tables\DataTable;
use Gibbon\Tables\View\GridView;
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

//Module includes
require_once __DIR__ . '/moduleFunctions.php';

$makeDepartmentsPublic = getSettingByScope($connection2, 'Departments', 'makeDepartmentsPublic');
if (isActionAccessible($guid, $connection2, '/modules/Departments/departments.php') == false and $makeDepartmentsPublic != 'Y') {
    //Acess denied
    echo "<div class='error'>";
    echo __('You do not have access to this action.');
    echo '</div>';
} else {
    $page->breadcrumbs->add(__('View All'));

    // Data Table
    $gridRenderer = new GridView($container->get('twig'));
    $table = $container->get(DataTable::class)->setRenderer($gridRenderer);

    $table->addColumn('logo')
        ->setClass('text-center')
        ->format(function ($department) {
            return Format::userPhoto($department['logo'], 125, 'w-20 h-20 sm:w-32 sm:h-32 p-1');
        });

    $table->addColumn('name')
        ->setClass('text-center text-xs font-bold mt-1 mb-4')
        ->format(function ($department) {
            $url = "./index.php?q=/modules/Departments/department.php&gibbonDepartmentID=".$department['gibbonDepartmentID'];
            return Format::link($url, $department['name']);
        });

    // Learning Areas
    $sql = "SELECT * FROM gibbonDepartment WHERE type='Learning Area' ORDER BY name";
    $learningAreas = $pdo->select($sql)->toDataSet();
    
    $tableLA = clone $table;
    $tableLA->setTitle(__('Learning Areas'));
    
    echo $tableLA->render($learningAreas);
    
    // Administration
    $sql = "SELECT * FROM gibbonDepartment WHERE type='Administration' ORDER BY name";
    $administration = $pdo->select($sql)->toDataSet();

    $tableAdmin = clone $table;
    $tableAdmin->setTitle(__('Administration'));

    echo $tableAdmin->render($administration);


    if (empty($learningAreas) && empty($administration)) {
        echo "<div class='warning'>";
        echo __('There are no records to display.');
        echo '</div>';
    }

    if (isset($_SESSION[$guid]['username'])) {
        //Print sidebar
        $sidebarExtra = '';

        try {
            $data = array('gibbonSchoolYearID' => $_SESSION[$guid]['gibbonSchoolYearID'], 'gibbonPersonID' => $_SESSION[$guid]['gibbonPersonID']);
            $sql = 'SELECT gibbonCourse.nameShort AS course, gibbonCourseClass.nameShort AS class, gibbonCourseClass.gibbonCourseClassID FROM gibbonCourse, gibbonCourseClass, gibbonCourseClassPerson WHERE gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonCourse.gibbonCourseID=gibbonCourseClass.gibbonCourseID AND gibbonCourseClass.gibbonCourseClassID=gibbonCourseClassPerson.gibbonCourseClassID AND gibbonCourseClassPerson.gibbonPersonID=:gibbonPersonID AND NOT role LIKE \'% - Left%\' ORDER BY course, class';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }

        if ($result->rowCount() > 0) {
            $sidebarExtra .= '<div class="column-no-break">';
            $sidebarExtra .= "<h2 class='sidebar'>";
            $sidebarExtra .= __('My Classes');
            $sidebarExtra .= '</h2>';

            $sidebarExtra .= '<ul>';
            while ($row = $result->fetch()) {
                $sidebarExtra .= "<li><a href='index.php?q=/modules/Departments/department_course_class.php&gibbonCourseClassID=".$row['gibbonCourseClassID']."'>".$row['course'].'.'.$row['class'].'</a></li>';
            }
            $sidebarExtra .= '</ul>';
            $sidebarExtra .= '</div>';

            $_SESSION[$guid]['sidebarExtra'] = $sidebarExtra;
        }
    }
}
