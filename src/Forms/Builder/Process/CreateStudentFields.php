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

namespace Gibbon\Forms\Builder\Process;

use Gibbon\Domain\User\UserGateway;
use Gibbon\Forms\Builder\AbstractFormProcess;
use Gibbon\Forms\Builder\FormBuilderInterface;
use Gibbon\Forms\Builder\Storage\FormDataInterface;
use Gibbon\Forms\Builder\Exception\FormProcessException;

class CreateStudentFields extends AbstractFormProcess
{
    protected $requiredFields = ['preferredName', 'surname'];

    private $userGateway;

    public function __construct(UserGateway $userGateway)
    {
        $this->userGateway = $userGateway;
    }
    
    public function isEnabled(FormBuilderInterface $builder)
    {
        return $builder->getConfig('createStudent') == 'Y';
    }

    public function process(FormBuilderInterface $builder, FormDataInterface $formData)
    {
        if (!$formData->has('gibbonPersonIDStudent')) {
            throw new FormProcessException('Failed to generate username or password');
            return;
        }

        // Update custom data
        $this->transferCustomFields($builder, $formData);
        $this->transferPersonalDocuments($builder, $formData);

        // Set and assign default values
        $this->setLastSchool($formData);
        $this->setStudentEmail($builder, $formData);
        $this->setStudentWebsite($builder, $formData);

        $data = [
            'email'               => $formData->get('email'),
            'emailAlternate'      => $formData->get('emailAlternate'),
            'website'             => $formData->get('website', ''),
            'lastSchool'          => $formData->get('lastSchool', ''),
            'fields'              => $formData->get('fields', ''),
        ];

        $updated = $this->userGateway->update($formData->get('gibbonPersonIDStudent'), $data);

        $this->setResult($updated);
    }

    public function rollback(FormBuilderInterface $builder, FormDataInterface $formData)
    {
        if (!$formData->has('gibbonPersonIDStudent')) return;

        $this->userGateway->delete($formData->get('gibbonPersonIDStudent'));

        $formData->set('gibbonPersonIDStudent', null);
    }

    private function transferCustomFields(FormBuilderInterface $builder, FormDataInterface $formData)
    {
        
    }

    private function transferPersonalDocuments(FormBuilderInterface $builder, FormDataInterface $formData)
    {
        
    }

    /**
     * Determine the last school based on dates provided
     *
     * @param FormDataInterface $formData
     */
    private function setLastSchool(FormDataInterface $formData)
    {
        if ($formData->get('schoolDate2', date('Y-m-d')) > $formData->get('schoolDate1', date('Y-m-d'))) {
            $formData->set('lastSchool', $formData->get('schoolName2'));
        } else {
            $formData->set('lastSchool', $formData->get('schoolName1'));
        }
    }

    /**
     * Set default email address for student
     *
     * @param FormBuilderInterface $builder
     * @param FormDataInterface $formData
     */
    private function setStudentEmail(FormBuilderInterface $builder, FormDataInterface $formData)
    {
        if (!$builder->hasConfig('studentDefaultEmail')) return;

        $formData->set('emailAlternate', $formData->get('email'));
        $formData->set('email', str_replace('[username]', $formData->get('username'), $builder->getConfig('studentDefaultEmail')));
    }

    /**
     * Set default website address for student
     *
     * @param FormBuilderInterface $builder
     * @param FormDataInterface $formData
     */
    private function setStudentWebsite(FormBuilderInterface $builder, FormDataInterface $formData)
    {
        if (!$builder->hasConfig('studentDefaultWebsite'))

        $formData->set('website', str_replace('[username]', $formData->get('username'), $builder->getConfig('studentDefaultWebsite')));
    }
}
