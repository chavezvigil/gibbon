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

namespace Gibbon\Forms\Builder\Processor;

use Gibbon\Forms\Builder\AbstractFormProcessor;
use Gibbon\Forms\Builder\Process\SendSubmissionEmail;
use Gibbon\Forms\Builder\Process\SendAcceptanceEmail;
use Gibbon\Forms\Builder\Process\ApplicationReferee;
use Gibbon\Forms\Builder\Process\ApplicationCheck;
use Gibbon\Forms\Builder\Process\ApplicationStatus;
use Gibbon\Forms\Builder\Process\CreateStudent;
use Gibbon\Forms\Builder\Process\CreateFamily;
use Gibbon\Forms\Builder\Process\CreateParents;
use Gibbon\Forms\Builder\Process\EnrolStudent;
use Gibbon\Forms\Builder\Process\AssignHouse;
use Gibbon\Forms\Builder\Process\NewStudentDetails;
use Gibbon\Forms\Builder\Process\CreateMedicalRecord;
use Gibbon\Forms\Builder\Process\CreateINRecord;
use Gibbon\Forms\Builder\Process\CreateInvoicee;

class ApplicationFormProcessor extends AbstractFormProcessor 
{
    protected function submitProcess()
    {
        $this->run(ApplicationReferee::class);
        $this->run(SendSubmissionEmail::class);
    }

    protected function editProcess()
    {

    }

    protected function acceptProcess()
    {
        $this->run(ApplicationCheck::class);
        $this->run(CreateStudent::class);
        $this->run(CreateFamily::class);
        $this->run(CreateParents::class);
        $this->run(EnrolStudent::class);
        $this->run(AssignHouse::class);
        $this->run(NewStudentDetails::class);
        $this->run(CreateMedicalRecord::class);
        $this->run(CreateINRecord::class);
        $this->run(CreateInvoicee::class);
        $this->run(ApplicationStatus::class);
        $this->run(SendAcceptanceEmail::class);
    }
}