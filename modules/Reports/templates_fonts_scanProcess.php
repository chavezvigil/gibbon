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

use Gibbon\Domain\System\SettingGateway;
use Gibbon\Module\Reports\Domain\ReportTemplateFontGateway;
use TCPDF_FONTS;

$_POST['address'] = '/modules/Reports/templates_manage.php';

require_once '../../gibbon.php';

$URL = $gibbon->session->get('absoluteURL').'/index.php?q=/modules/Reports/templates_fonts.php';

if (isActionAccessible($guid, $connection2, '/modules/Reports/templates_fonts.php') == false) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
    exit;
} else {
    // Proceed!
    $partialFail = false;
    $count = 0;

    $fontGateway = $container->get(ReportTemplateFontGateway::class);

    $parseAndUpdate = function ($directoryPath, $templateType) use (&$absolutePath, &$fontGateway, &$partialFail, &$count){
        // Get all font files in this folder and sub-folders
        $directoryPath = '/'.trim($directoryPath, '/');
        $directoryFiles = glob($directoryPath.'{,/*,/.../*}/*.ttf', GLOB_BRACE);

        foreach ($directoryFiles as $filePath) {
            $fontTCPDF = \TCPDF_FONTS::addTTFfont($filePath, 'TrueTypeUnicode', '', 32);
            
            if (empty($fontTCPDF)) continue;

            // Update the font details in the database
            $data = [
                'fontName' => str_replace(['.ttf'], [''], basename($filePath)),
                'fontPath' => str_replace($absolutePath.'/', '', $filePath),
                'fontTCPDF' => $fontTCPDF,
            ];

            $inserted = $fontGateway->insertAndUpdate($data, [
                'fontPath'  => $data['fontPath'],
            ]);

            $partialFail &= !$inserted;
            $count += $inserted == true;
        }
    };

    $absolutePath = $gibbon->session->get('absolutePath');
    $customAssetPath = $container->get(SettingGateway::class)->getSettingByScope('Reports', 'customAssetPath');

    $parseAndUpdate($absolutePath.$customAssetPath.'/fonts', 'Additional');
    $parseAndUpdate($absolutePath.'/resources/assets/fonts', 'Core');

    $URL .= $partialFail
        ? "&return=warning1"
        : "&return=success0";

    header("Location: {$URL}&count={$count}");
}
