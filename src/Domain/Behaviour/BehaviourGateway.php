<?php
/*
Gibbon: the flexible, open school platform
Founded by Ross Parker at ICHK Secondary. Built by Ross Parker, Sandra Kuipers and the Gibbon community (https://gibbonedu.org/about/)
Copyright © 2010, Gibbon Foundation
Gibbon™, Gibbon Education Ltd. (Hong Kong)

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

namespace Gibbon\Domain\Behaviour;

use Gibbon\Domain\QueryCriteria;
use Gibbon\Domain\QueryableGateway;
use Gibbon\Domain\ScrubbableGateway;
use Gibbon\Domain\Traits\Scrubbable;
use Gibbon\Domain\Traits\TableAware;
use Gibbon\Domain\Traits\ScrubByPerson;

/**
 * Behaviour Gateway
 *
 * @version v17
 * @since   v17
 */
class BehaviourGateway extends QueryableGateway implements ScrubbableGateway
{
    use TableAware;
    use Scrubbable;
    use ScrubByPerson;

    private static $tableName = 'gibbonBehaviour';
    private static $primaryKey = 'gibbonBehaviourID';

    private static $searchableColumns = [];

    private static $scrubbableKey = 'gibbonPersonID';
    private static $scrubbableColumns = ['descriptor' => null, 'level' => null, 'comment' => ''];
    
    /**
     * @param QueryCriteria $criteria
     * @return DataSet
     */
    public function queryBehaviourBySchoolYear(QueryCriteria $criteria, $gibbonSchoolYearID, $gibbonPersonIDCreator = null)
    {
        $query = $this
            ->newQuery()
            ->from($this->getTableName())
            ->cols([
                'gibbonBehaviour.gibbonBehaviourID',
                'gibbonBehaviour.type',
                'gibbonBehaviour.descriptor',
                'gibbonBehaviour.level',
                'gibbonBehaviour.date',
                'gibbonBehaviour.timestamp',
                'gibbonBehaviour.comment',
                'gibbonBehaviour.followup',
                'student.gibbonPersonID',
                'student.surname',
                'student.preferredName',
                'gibbonFormGroup.nameShort AS formGroup',
                'creator.title AS titleCreator',
                'creator.surname AS surnameCreator',
                'creator.preferredName AS preferredNameCreator',
            ])
            ->innerJoin('gibbonPerson AS student', 'gibbonBehaviour.gibbonPersonID=student.gibbonPersonID')
            ->innerJoin('gibbonStudentEnrolment', 'student.gibbonPersonID=gibbonStudentEnrolment.gibbonPersonID')
            ->innerJoin('gibbonFormGroup', 'gibbonStudentEnrolment.gibbonFormGroupID=gibbonFormGroup.gibbonFormGroupID')
            ->leftJoin('gibbonPerson AS creator', 'gibbonBehaviour.gibbonPersonIDCreator=creator.gibbonPersonID')
            ->where('gibbonBehaviour.gibbonSchoolYearID = :gibbonSchoolYearID')
            ->bindValue('gibbonSchoolYearID', $gibbonSchoolYearID)
            ->where('gibbonStudentEnrolment.gibbonSchoolYearID=gibbonBehaviour.gibbonSchoolYearID');

        if (!empty($gibbonPersonIDCreator)) {
            $query->where('gibbonBehaviour.gibbonPersonIDCreator = :gibbonPersonIDCreator')
                ->bindValue('gibbonPersonIDCreator', $gibbonPersonIDCreator);
        }

        $criteria->addFilterRules([
            'student' => function ($query, $gibbonPersonID) {
                return $query
                    ->where('gibbonBehaviour.gibbonPersonID = :gibbonPersonID')
                    ->bindValue('gibbonPersonID', $gibbonPersonID);
            },
            'formGroup' => function ($query, $gibbonFormGroupID) {
                return $query
                    ->where('gibbonStudentEnrolment.gibbonFormGroupID = :gibbonFormGroupID')
                    ->bindValue('gibbonFormGroupID', $gibbonFormGroupID);
            },
            'yearGroup' => function ($query, $gibbonYearGroupID) {
                return $query
                    ->where('gibbonStudentEnrolment.gibbonYearGroupID = :gibbonYearGroupID')
                    ->bindValue('gibbonYearGroupID', $gibbonYearGroupID);
            },
            'type' => function ($query, $type) {
                return $query
                    ->where('gibbonBehaviour.type = :type')
                    ->bindValue('type', $type);
            },
        ]);

        return $this->runQuery($query, $criteria);
    }

    public function queryBehaviourPatternsBySchoolYear(QueryCriteria $criteria, $gibbonSchoolYearID)
    {
        $query = $this
            ->newQuery()
            ->from('gibbonPerson')
            ->cols([
                'gibbonPerson.gibbonPersonID',
                'gibbonStudentEnrolmentID',
                'gibbonPerson.surname',
                'gibbonPerson.preferredName',
                'gibbonYearGroup.nameShort AS yearGroup',
                'gibbonFormGroup.nameShort AS formGroup',
                'gibbonPerson.dateStart',
                'gibbonPerson.dateEnd',
                "COUNT(DISTINCT gibbonBehaviourID) AS count",
            ])
            ->innerJoin('gibbonStudentEnrolment', 'gibbonStudentEnrolment.gibbonPersonID=gibbonPerson.gibbonPersonID')
            ->innerJoin('gibbonFormGroup', 'gibbonFormGroup.gibbonFormGroupID=gibbonStudentEnrolment.gibbonFormGroupID')
            ->innerJoin('gibbonYearGroup', 'gibbonYearGroup.gibbonYearGroupID=gibbonStudentEnrolment.gibbonYearGroupID')
            ->leftJoin('gibbonBehaviour', "gibbonBehaviour.gibbonPersonID=gibbonPerson.gibbonPersonID 
                AND gibbonBehaviour.gibbonSchoolYearID=gibbonStudentEnrolment.gibbonSchoolYearID")
            ->where('gibbonStudentEnrolment.gibbonSchoolYearID = :gibbonSchoolYearID')
            ->bindValue('gibbonSchoolYearID', $gibbonSchoolYearID)
            ->where("gibbonPerson.status = 'Full'")
            ->groupBy(['gibbonPerson.gibbonPersonID']);

        $criteria->addFilterRules([
            'type' => function ($query, $type) {
                return $query
                    ->where('(gibbonBehaviourID IS NULL OR gibbonBehaviour.type = :type)')
                    ->bindValue('type', $type);
            },
            'descriptor' => function ($query, $descriptor) {
                return $query
                    ->where('(gibbonBehaviourID IS NULL OR gibbonBehaviour.descriptor = :descriptor)')
                    ->bindValue('descriptor', $descriptor);
            },
            'level' => function ($query, $level) {
                return $query
                    ->where('(gibbonBehaviourID IS NULL OR gibbonBehaviour.level = :level)')
                    ->bindValue('level', $level);
            },
            'fromDate' => function ($query, $fromDate) {
                return $query
                    ->where('(gibbonBehaviourID IS NULL OR gibbonBehaviour.date >= :fromDate)')
                    ->bindValue('fromDate', $fromDate);
            },
            'formGroup' => function ($query, $gibbonFormGroupID) {
                return $query
                    ->where('gibbonStudentEnrolment.gibbonFormGroupID = :gibbonFormGroupID')
                    ->bindValue('gibbonFormGroupID', $gibbonFormGroupID);
            },
            'yearGroup' => function ($query, $gibbonYearGroupID) {
                return $query
                    ->where('gibbonStudentEnrolment.gibbonYearGroupID = :gibbonYearGroupID')
                    ->bindValue('gibbonYearGroupID', $gibbonYearGroupID);
            },
            'minimumCount' => function ($query, $minimumCount) {
                return $query
                    ->having('count >= :minimumCount')
                    ->bindValue('minimumCount', $minimumCount);
            },
        ]);

        return $this->runQuery($query, $criteria);
    }

    public function queryBehaviourLettersBySchoolYear(QueryCriteria $criteria, $gibbonSchoolYearID)
    {
        $query = $this
            ->newQuery()
            ->from('gibbonBehaviourLetter')
            ->cols([
                'gibbonBehaviourLetter.*',
                'gibbonPerson.gibbonPersonID',
                'gibbonPerson.surname',
                'gibbonPerson.preferredName',
                'gibbonFormGroup.nameShort AS formGroup',
            ])
            ->innerJoin('gibbonPerson', 'gibbonBehaviourLetter.gibbonPersonID=gibbonPerson.gibbonPersonID')
            ->innerJoin('gibbonStudentEnrolment', 'gibbonStudentEnrolment.gibbonPersonID=gibbonPerson.gibbonPersonID 
                AND gibbonStudentEnrolment.gibbonSchoolYearID=gibbonBehaviourLetter.gibbonSchoolYearID')
            ->innerJoin('gibbonFormGroup', 'gibbonFormGroup.gibbonFormGroupID=gibbonStudentEnrolment.gibbonFormGroupID')
            ->where('gibbonBehaviourLetter.gibbonSchoolYearID = :gibbonSchoolYearID')
            ->bindValue('gibbonSchoolYearID', $gibbonSchoolYearID)
            ->where("gibbonPerson.status = 'Full'");

        $criteria->addFilterRules([
            'student' => function ($query, $gibbonPersonID) {
                return $query
                    ->where('gibbonBehaviourLetter.gibbonPersonID = :gibbonPersonID')
                    ->bindValue('gibbonPersonID', $gibbonPersonID);
            },
        ]);

        return $this->runQuery($query, $criteria);
    }

    public function queryBehaviourRecordsByPerson(QueryCriteria $criteria, $gibbonSchoolYearID, $gibbonPersonID)
    {
        $query = $this
            ->newQuery()
            ->from($this->getTableName())
            ->cols([
                'gibbonBehaviour.*',
                'creator.title AS titleCreator',
                'creator.surname AS surnameCreator',
                'creator.preferredName AS preferredNameCreator',
            ])
            ->leftJoin('gibbonPerson AS creator', 'gibbonBehaviour.gibbonPersonIDCreator=creator.gibbonPersonID')
            ->where('gibbonBehaviour.gibbonPersonID = :gibbonPersonID')
            ->bindValue('gibbonPersonID', $gibbonPersonID)
            ->where('gibbonBehaviour.gibbonSchoolYearID = :gibbonSchoolYearID')
            ->bindValue('gibbonSchoolYearID', $gibbonSchoolYearID);

            return $this->runQuery($query, $criteria);
    }

    public function getBehaviourDetails($gibbonSchoolYearID, $gibbonBehaviourID)
    {
        $data = ['gibbonSchoolYearID' => $gibbonSchoolYearID, 'gibbonBehaviourID' => $gibbonBehaviourID];
        $sql = 'SELECT gibbonBehaviour.*, student.surname AS surnameStudent, student.preferredName AS preferredNameStudent, creator.surname AS surnameCreator, creator.preferredName AS preferredNameCreator, creator.title FROM gibbonBehaviour JOIN gibbonPerson AS student ON (gibbonBehaviour.gibbonPersonID=student.gibbonPersonID) JOIN gibbonPerson AS creator ON (gibbonBehaviour.gibbonPersonIDCreator=creator.gibbonPersonID) WHERE gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonBehaviourID=:gibbonBehaviourID ORDER BY date DESC';
        
        return $this->db()->selectOne($sql, $data);
    }

    public function getBehaviourDetailsByCreator($gibbonSchoolYearID, $gibbonBehaviourID, $gibbonPersonID)
    {
        $data = array('gibbonSchoolYearID' => $gibbonSchoolYearID, 'gibbonBehaviourID' => $gibbonBehaviourID, 'gibbonPersonID' => $gibbonPersonID);
        $sql = 'SELECT gibbonBehaviour.*, student.surname AS surnameStudent, student.preferredName AS preferredNameStudent, creator.surname AS surnameCreator, creator.preferredName AS preferredNameCreator, creator.title FROM gibbonBehaviour JOIN gibbonPerson AS student ON (gibbonBehaviour.gibbonPersonID=student.gibbonPersonID) JOIN gibbonPerson AS creator ON (gibbonBehaviour.gibbonPersonIDCreator=creator.gibbonPersonID) WHERE gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonBehaviourID=:gibbonBehaviourID AND gibbonPersonIDCreator=:gibbonPersonID ORDER BY date DESC';

        return $this->db()->selectOne($sql, $data);
    }

    public function selectMultipleStudentsOfOneIncident($gibbonMultiIncidentID)
    {
        $data = ['gibbonMultiIncidentID' => $gibbonMultiIncidentID];
        $sql = 'SELECT gibbonBehaviour.gibbonPersonID AS gibbonPersonID, student.preferredName AS preferredNameStudent, student.surname AS surnameStudent FROM gibbonBehaviour JOIN gibbonPerson AS student ON (gibbonBehaviour.gibbonPersonID=student.gibbonPersonID)WHERE gibbonMultiIncidentID = :gibbonMultiIncidentID ORDER BY preferredNameStudent';

        return $this->db()->select($sql, $data);
    }
    public function queryFollowUpByBehaviourID($gibbonBehaviourID)
    {
        $query = $this
            ->newSelect()
            ->from($this->getTableName())
            ->cols([
                'gibbonBehaviourFollowUp.*',
                'gibbonBehaviourFollowUp.followUp as comment',
                'gibbonPerson.surname',
                'gibbonPerson.preferredName',
                'gibbonPerson.image_240'
            ])
            ->innerJoin('gibbonBehaviourFollowUp', 'gibbonBehaviourFollowUp.gibbonBehaviourID=gibbonBehaviour.gibbonBehaviourID')
            ->innerJoin('gibbonPerson', 'gibbonBehaviourFollowUp.gibbonPersonID=gibbonPerson.gibbonPersonID')
            ->where('gibbonBehaviourFollowUp.gibbonBehaviourID=:gibbonBehaviourID')
            ->bindValue('gibbonBehaviourID', $gibbonBehaviourID);

        return $this->runSelect($query);
    }
}
