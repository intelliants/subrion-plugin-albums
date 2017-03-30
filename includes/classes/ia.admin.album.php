<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2015 Intelliants, LLC <http://www.intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link http://www.subrion.org/
 *
 ******************************************************************************/

class iaAlbum extends abstractModuleAdmin
{
    protected static $_table = 'albums_photos';


    public function delete($id)
    {
        $imagePath = $this->iaDb->one('path', iaDb::convertIds($id), self::getTable());
        $result = (bool)$this->iaDb->delete(iaDb::convertIds($id), self::getTable());

        if ($result && $imagePath) {
            $iaPicture = $this->iaCore->factory('picture');
            $iaPicture->delete($imagePath);
        }

        return $result;
    }

    public function gridRead($params, $columns, array $filterParams = [], array $persistentConditions = [])
    {
        $params || $params = [];

        $start = isset($params['start']) ? (int)$params['start'] : 0;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 15;

        $sort = $params['sort'];
        $dir = in_array($params['dir'], [iaDb::ORDER_ASC, iaDb::ORDER_DESC]) ? $params['dir'] : iaDb::ORDER_ASC;
        $order = ($sort && $dir) ? " ORDER BY `{$sort}` {$dir}" : '';

        $where = $values = [];
        foreach ($filterParams as $name => $type) {
            if (isset($params[$name]) && $params[$name]) {
                $value = iaSanitize::sql($params[$name]);

                switch ($type) {
                    case 'equal':
                        $where[] = sprintf('`%s` = :%s', $name, $name);
                        $values[$name] = $value;
                        break;
                    case 'like':
                        $where[] = sprintf('`%s` LIKE :%s', $name, $name);
                        $values[$name] = '%' . $value . '%';
                }
            }
        }

        $where = array_merge($where, $persistentConditions);
        $where || $where[] = iaDb::EMPTY_CONDITION;
        $where = implode(' AND ', $where);
        $this->iaDb->bind($where, $values);

        if (is_array($columns)) {
            $columns = array_merge(['id', 'delete' => 1], $columns);
        }

        $stmtFields = $columns;

        if (is_array($columns)) {
            $stmtFields = '';
            foreach ($columns as $key => $field) {
                $stmtFields .= is_int($key)
                    ? 'p.`' . $field . '`'
                    : sprintf('%s `%s`', is_numeric($field) ? $field : '`' . $field . '`', $key);
                $stmtFields .= ', ';
            }
            $stmtFields = substr($stmtFields, 0, -2);
        }

        $sql = 'SELECT ' . $stmtFields . ', IF(m.`fullname` != "", m.`fullname`, m.`username`) `username`'
            . ' FROM `' . self::getTable(true) . '` p'
            . ' LEFT JOIN `' . iaUsers::getTable(true) . '` m ON p.`member_id` = m.`id`'
            . ' WHERE ' . $where
            . $order;

        if ($limit && stripos($where, 'limit') === false) {
            $sql .= ' LIMIT ' . $start . ', ' . $limit;
        }

        return [
            'data' => $this->iaDb->getAll($sql),
            'total' => (int)$this->iaDb->one(iaDb::STMT_COUNT_ROWS, $where)
        ];
    }
}