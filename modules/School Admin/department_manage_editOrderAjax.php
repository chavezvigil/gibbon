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
use Gibbon\Domain\Departments\DepartmentGateway;

$_POST['address'] = '/modules/School Admin/department_manage.php';

require_once '../../gibbon.php';

if (isActionAccessible($guid, $connection2, new Action('School Admin', 'department_manage')) == false) {
    exit;
} else {
    // Proceed!
    $data = $_POST['data'] ?? [];
    $order = json_decode($_POST['order']);

    if (empty($order)) {
        exit;
    } else {
        $departmentGateway = $container->get(DepartmentGateway::class);

        $count = 1;
        foreach ($order as $gibbonDepartmentID) {
            $updated = $departmentGateway->update($gibbonDepartmentID, ['sequenceNumber' => $count]);
            $count++;
        }
    }
}
