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
use Gibbon\Forms\Builder\Processor\FormProcessorFactory;
use Gibbon\Forms\Builder\Storage\ApplicationFormStorage;
use Gibbon\Domain\Admissions\AdmissionsAccountGateway;

require_once '../../gibbon.php';

$accessID = $_REQUEST['accessID'] ?? '';
$gibbonFormID = $_REQUEST['gibbonFormID'] ?? '';
$identifier = $_REQUEST['identifier'] ?? null;
$pageNumber = $_REQUEST['page'] ?? 1;

$URL = Url::fromModuleRoute('Admissions', 'applicationForm')->withQueryParams(['gibbonFormID' => $gibbonFormID, 'page' => $pageNumber, 'identifier' => $identifier, 'accessID' => $accessID]);

if (empty($gibbonFormID) || empty($identifier)) {
    header("Location: {$URL->withReturn('error0')}");
    exit;
} else {
    // Proceed!
    if (empty($gibbonFormID) || empty($pageNumber)) {
        header("Location: {$URL->withReturn('error1')}");
        exit;
    }
    $admissionsAccountGateway = $container->get(AdmissionsAccountGateway::class);
    $account = $admissionsAccountGateway->getAccountByAccessID($accessID);
    if (empty($account)) {
        header("Location: {$URL->withReturn('error1')}");
        exit;
    }
    
    // Setup the form data
    $formBuilder = $container->get(FormBuilder::class)->populate($gibbonFormID, $pageNumber, ['identifier' => $identifier, 'accessID' => $accessID]);
    $formData = $container->get(ApplicationFormStorage::class)->setContext($formBuilder, 'gibbonAdmissionsAccount', $account['gibbonAdmissionsAccountID'], $account['email']);
    $formData->load($identifier);

    // Acquire data and handle file uploads - on error, return to the current page
    $data = $formBuilder->acquire();
    if (!$data) {
        header("Location: {$URL->withReturn('error1')}");
        exit;
    }

    // Save data before validation, so users don't lose data?
    $formData->addData($data);
    $formData->save($identifier);

    // Validate submitted data - on error, return to the current page
    $validated = $formBuilder->validate($data);
    if (!empty($validated)) {
        header("Location: {$URL->withReturn('error1')}");
        exit;
    }

    // Update the admissions account activity
    $admissionsAccountGateway->update($account['gibbonAdmissionsAccountID'], [
        'timestampActive' => date('Y-m-d H:i:s'),
        'ipAddress'       => $_SERVER['REMOTE_ADDR'] ?? '',
    ]);

    // Determine how to handle the next page
    $formPageGateway = $container->get(FormPageGateway::class);
    $finalPageNumber = $formPageGateway->getFinalPageNumber($gibbonFormID);
    $nextPage = $formPageGateway->getNextPageByNumber($gibbonFormID, $pageNumber);
    $maxPage = max($nextPage['sequenceNumber'] ?? $pageNumber, $formData->get('maxPage') ?? 1);

    if ($pageNumber >= $finalPageNumber) {
        // Run the form processor on this data
        $formProcessor = $container->get(FormProcessorFactory::class)->getProcessor($formBuilder->getDetail('type'));
        $formProcessor->submitForm($formBuilder, $formData);

        $formData->setStatus('Pending');
        $formData->save($identifier);

        $URL = $URL->withQueryParam('return', 'success0')->withQueryParam('page', $pageNumber+1);

    } elseif ($nextPage) {
        // Save data and proceed to the next page
        $formData->addData(['maxPage' => $maxPage]);
        $formData->save($identifier);

        $URL = $URL->withQueryParam('page', $nextPage['sequenceNumber']);
    }

    header("Location: {$URL}");
}
