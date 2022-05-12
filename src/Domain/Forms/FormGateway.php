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

namespace Gibbon\Domain\Forms;

use Gibbon\Domain\Traits\TableAware;
use Gibbon\Domain\QueryCriteria;
use Gibbon\Domain\QueryableGateway;

class FormGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'gibbonForm';
    private static $primaryKey = 'gibbonFormID';
    private static $searchableColumns = ['gibbonForm.name'];
    
    /**
     * @param QueryCriteria $criteria
     * @return DataSet
     */
    public function queryForms(QueryCriteria $criteria)
    {
        $query = $this
            ->newQuery()
            ->distinct()
            ->cols(['gibbonForm.gibbonFormID', 'gibbonForm.name', 'gibbonForm.description', 'gibbonForm.active', 'gibbonForm.type', 'COUNT(gibbonFormPageID) as pages'])
            ->from($this->getTableName())
            ->leftJoin('gibbonFormPage', 'gibbonFormPage.gibbonFormID=gibbonForm.gibbonFormID')
            ->groupBy(['gibbonForm.gibbonFormID']);

        $criteria->addFilterRules([
            'active' => function ($query, $active) {
                return $query
                    ->where('gibbonForm.active = :active')
                    ->bindValue('active', $active);
            },
            'public' => function ($query, $public) {
                return $query
                    ->where('gibbonForm.public = :public')
                    ->bindValue('public', $public);
            },
            'type' => function ($query, $type) {
                return $query
                    ->where('gibbonForm.type = :type')
                    ->bindValue('type', $type);
            },
        ]);

        return $this->runQuery($query, $criteria);
    }

    public function selectFieldsByForm($gibbonFormID)
    {
        $select = $this
            ->newSelect()
            ->cols(['gibbonFormField.fieldName as groupBy', 'gibbonFormField.*', 'gibbonFormPage.sequenceNumber as pageNumber'])
            ->from('gibbonFormField')
            ->innerJoin('gibbonFormPage', 'gibbonFormPage.gibbonFormPageID=gibbonFormField.gibbonFormPageID')
            ->where('gibbonFormPage.gibbonFormID=:gibbonFormID')
            ->bindValue('gibbonFormID', $gibbonFormID)
            ->orderBy(['gibbonFormPage.sequenceNumber', 'gibbonFormField.sequenceNumber']);

        return $this->runSelect($select);
    }

    public function getNewUniqueIdentifier(string $gibbonFormID)
    {
        $data = ['gibbonFormID' => $gibbonFormID];
        $sql = "SELECT gibbonFormSubmissionID FROM gibbonFormSubmission WHERE gibbonFormID=:gibbonFormID AND identifier=:identifier";

        do {
            $data['identifier'] =  bin2hex(random_bytes(20));
            $result = $this->db()->selectOne($sql, $data);
        } while (!empty($result));

        return $data['identifier'];
    }
}