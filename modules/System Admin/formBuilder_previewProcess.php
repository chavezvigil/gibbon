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

use Gibbon\Http\Url;
use Gibbon\Domain\Forms\FormPageGateway;
use Gibbon\Forms\Builder\FormBuilder;
use Gibbon\Forms\Builder\Storage\FormSessionStorage;
use Gibbon\Forms\Builder\Processor\FormProcessorFactory;
use Gibbon\Forms\Builder\Storage\FormDatabaseStorage;

require_once '../../gibbon.php';

$gibbonFormID = $_REQUEST['gibbonFormID'] ?? '';
$pageNumber = $_REQUEST['page'] ?? 1;

$URL = Url::fromModuleRoute('System Admin', 'formBuilder_preview')->withQueryParams(['gibbonFormID' => $gibbonFormID, 'page' => $pageNumber]);

if (isActionAccessible($guid, $connection2, '/modules/System Admin/formBuilder_edit.php') == false) {
    header("Location: {$URL->withReturn('error0')}");
    exit;
} else {
    // Proceed!
    if (empty($gibbonFormID) || empty($pageNumber)) {
        header("Location: {$URL->withReturn('error1')}");
        exit;
    }
    
    // Setup the form data
    $formBuilder = $container->get(FormBuilder::class)->populate($gibbonFormID, $pageNumber);
    // $formData = $container->get(FormSessionStorage::class);
    $formData = $container->get(FormDatabaseStorage::class)->setSubmissionDetails($formBuilder, 'preview', 1);
    $formData->load('preview');

    // Get any submitted values, the lazy way
    $data = $_POST + $_FILES;

    $formData->addData($data);
    $formData->save('preview');

    $validated = $formBuilder->validate($data);

    if (!$validated) {
        header("Location: {$URL->withReturn('error1')}");
        exit;
    }

    // Determine how to handle the next page
    $formPageGateway = $container->get(FormPageGateway::class);
    $finalPageNumber = $formPageGateway->getFinalPageNumber($gibbonFormID);
    $nextPage = $formPageGateway->getNextPageByNumber($gibbonFormID, $pageNumber);
    $maxPage = max($nextPage['sequenceNumber'] ?? $pageNumber, $formData->get('maxPage') ?? 1);

    if ($pageNumber >= $finalPageNumber) {
        // Run the form processor on this data
        $formProcessor = $container->get(FormProcessorFactory::class)->getProcessor($formBuilder->getFormType());
        $formProcessor->submitForm($formBuilder, $formData);

        $session->set('formpreview', []);

        $URL = $URL
            ->withQueryParam('return', 'success0')
            ->withQueryParam('page', $pageNumber+1);

    } elseif ($nextPage) {
        $formData->addData(['maxPage' => $maxPage]);
        $formData->save('preview');
        $URL = $URL->withQueryParam('page', $nextPage['sequenceNumber']);
    }

    header("Location: {$URL}");
}
