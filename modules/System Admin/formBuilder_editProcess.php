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

use Gibbon\Domain\Forms\FormGateway;

require_once '../../gibbon.php';

$gibbonFormID = $_POST['gibbonFormID'] ?? '';

$URL = $session->get('absoluteURL').'/index.php?q=/modules/System Admin/formBuilder_edit.php&gibbonFormID='.$gibbonFormID.'&sidebar=false';

if (isActionAccessible($guid, $connection2, '/modules/System Admin/formBuilder_edit.php') == false) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
    exit;
} else {
    // Proceed!
    $formGateway = $container->get(FormGateway::class);

    $data = [
        'name'        => $_POST['name'] ?? '',
        'orientation' => $_POST['orientation'] ?? '',
        'pageSize'    => $_POST['pageSize'] ?? '',
        'marginX'     => $_POST['marginX'] ?? '',
        'marginY'     => $_POST['marginY'] ?? '',
        'stylesheet'  => $_POST['stylesheet'] ?? '',
        'flags'       => $_POST['flags'] ?? '',
    ];

    $config = [
        'fonts' => $_POST['fonts'] ?? [],
    ];

    $data['config'] = json_encode($config);

    // Validate the required values are present
    if (empty($data['name'])) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit;
    }

    // Validate the database relationships exist
    if (!$formGateway->exists($gibbonFormID)) {
        $URL .= '&return=error2';
        header("Location: {$URL}");
        exit;
    }

    // Validate that this record is unique
    if (!$formGateway->unique($data, ['name'], $gibbonFormID)) {
        $URL .= '&return=error7';
        header("Location: {$URL}");
        exit;
    }

    // Update the record
    $updated = $formGateway->update($gibbonFormID, $data);

    $URL .= !$updated
        ? "&return=error2"
        : "&return=success0";

    header("Location: {$URL}");
}
