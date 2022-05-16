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

use Gibbon\Data\UsernameGenerator;
use Gibbon\Domain\User\UserGateway;
use Gibbon\Forms\Builder\AbstractFormProcess;
use Gibbon\Forms\Builder\FormBuilderInterface;
use Gibbon\Forms\Builder\Storage\FormDataInterface;
use Gibbon\Forms\Builder\View\CreateStudentView;

class CreateStudent extends AbstractFormProcess implements ViewableProcess
{
    protected $requiredFields = ['preferredName', 'surname'];

    private $userGateway;
    private $usernameGenerator;

    public function __construct(UserGateway $userGateway, UsernameGenerator $usernameGenerator)
    {
        $this->userGateway = $userGateway;
        $this->usernameGenerator = $usernameGenerator;
    }

    public function getViewClass() : string
    {
        return CreateStudentView::class;
    }

    public function isEnabled(FormBuilderInterface $builder)
    {
        return $builder->getConfig('createStudent') == 'Y';
    }

    public function process(FormBuilderInterface $builder, FormDataInterface $formData)
    {
        // Generate user details
        $this->generateUsername($formData);
        $this->generatePassword($formData);

        if (!$formData->has('username') || !$formData->has('passwordStrong')) {
            $formData->set('createStudentResult', false);
            return;
        }

        // Set and assign default values
        $this->setStatus($formData);
        $this->setDefaults($formData);
        $this->setLastSchool($formData);
        $this->setStudentEmail($builder, $formData);
        $this->setStudentWebsite($builder, $formData);

        $data = [
            'username'           => $formData->get('username'),
            'passwordStrong'     => $formData->get('passwordStrong'),
            'passwordStrongSalt' => $formData->get('passwordStrongSalt'),
            'status'             => $formData->get('status'), 
            'surname'            => $formData->get('surname'),
            'firstName'          => $formData->get('firstName'),
            'preferredName'      => $formData->get('preferredName'),
            'officialName'       => $formData->get('officialName'),
            'nameInCharacters'   => $formData->get('nameInCharacters'),
            'gender'             => $formData->get('gender'),
            'dob'                => $formData->get('dob'),
            'languageFirst'      => $formData->get('languageFirst'),
            'languageSecond'     => $formData->get('languageSecond'),
            'languageThird'      => $formData->get('languageThird'),
            'countryOfBirth'     => $formData->get('countryOfBirth'),
            'email'              => $formData->get('email'),
            'emailAlternate'     => $formData->get('emailAlternate'),
            'website'            => $formData->get('website'),
            'phone1Type'         => $formData->get('phone1Type'),
            'phone1CountryCode'  => $formData->get('phone1CountryCode'),
            'phone1'             => $formData->get('phone1'),
            'phone2Type'         => $formData->get('phone2Type'),
            'phone2CountryCode'  => $formData->get('phone2CountryCode'),
            'phone2'             => $formData->get('phone2'),
            'lastSchool'         => $formData->get('lastSchool'),
            'dateStart'          => $formData->get('dateStart'),
            'privacy'            => $formData->get('privacy'),
            'dayType'            => $formData->get('dayType'),
            'studentID'          => $formData->get('studentID'),
            'fields'             => $formData->get('fields', ''),
        ];

        $gibbonPersonIDStudent = $this->userGateway->insert($data);

        $formData->set('gibbonPersonIDStudent', $gibbonPersonIDStudent ?? '');
        $formData->set('createStudentResult', true);
    }

    public function rollback(FormBuilderInterface $builder, FormDataInterface $formData)
    {
        if (!$formData->has('gibbonPersonIDStudent')) return;

        $this->userGateway->delete($formData->get('gibbonPersonIDStudent'));

        $formData->set('gibbonPersonIDStudent', null);
        $formData->set('createStudentResult', false);
    }

    /**
     * Generate a unique username for the new student, or use the pre-defined one.
     *
     * @param FormDataInterface $formData
     */
    private function generateUsername(FormDataInterface $formData)
    {
        if (!empty($formData['username'])) {
            return;
        }

        $this->usernameGenerator->addToken('preferredName', $formData['preferredName'] ?? '');
        $this->usernameGenerator->addToken('firstName', $formData['firstName'] ?? '');
        $this->usernameGenerator->addToken('surname', $formData['surname'] ?? '');

        $formData->set('username', $this->usernameGenerator->generateByRole('003'));
    }

    /**
     * Generate a random password
     *
     * @param FormDataInterface $formData
     */
    private function generatePassword(FormDataInterface $formData)
    {
        $formData->set('passwordStrongSalt', getSalt());
        $formData->set('passwordStrong', hash('sha256', $formData->get('passwordStrongSalt').randomPassword(8)));
    }

    /**
     * Set the initial status for the student based on the school year of entry.
     *
     * @param FormDataInterface $formData
     */
    private function setStatus(FormDataInterface $formData)
    {
        // $schoolYearEntry['status'] == 'Upcoming' && $informStudent != 'Y' ? 'Expected' : 'Full'
        $formData->set('status', 'Full');
    }

    /**
     * Set default values for those not provided by the form.
     *
     * @param FormDataInterface $formData
     */
    private function setDefaults(FormDataInterface $formData)
    {
        if (!$formData->has('firstName')) {
            $formData->set('firstName', $formData->get('preferredName'));
        }

        if (!$formData->has('officialName')) {
            $formData->set('officialName', $formData->get('firstName').' '.$formData->get('surname'));
        }
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
