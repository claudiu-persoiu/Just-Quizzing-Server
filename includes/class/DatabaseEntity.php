<?php

/**
 * Copyright (c) 2013 Claudiu Persoiu (http://www.claudiupersoiu.ro/)
 *
 * This file is part of "Just quizzing".
 *
 * Official project page: http://blog.claudiupersoiu.ro/just-quizzing/
 *
 * You can download the latest version from https://github.com/claudiu-persoiu/Just-Quizzing
 *
 * "Just quizzing" is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * Just quizzing is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class DatabaseEntity
{

    static protected $_resource;

    static protected $_entities = array();

    protected $_entity;

    /**
     * DatabaseEntity factory
     *
     * @param $entity
     * @return DatabaseEntity
     */
    public static function getEntity($entity)
    {

        if (!isset(self::$_entities[$entity])) {
            self::$_entities[$entity] = new self($entity);
        }

        return self::$_entities[$entity];
    }

    public function __construct($entity)
    {
        if (!$entity) {
            throw new Exception('no entity specified');
        }

        $this->_entity = $entity;
    }

    public static function getResource()
    {
        if (!self::$_resource) {
            self::$_resource = new SQLite3('data/db.info');
            self::$_resource->exec("PRAGMA journal_mode = MEMORY;
                       PRAGMA temp_store   = MEMORY;
                       PRAGMA foreign_keys = ON;
                       PRAGMA encoding     = 'UTF-8';");
        }

        return self::$_resource;
    }

    public function getAll(array $fieldsArray = array(), array $conditions = array(), $orderBy = false)
    {

        if (count($fieldsArray)) {
            $fieldsString = implode(', ', $fieldsArray);
        } else {
            $fieldsString = '*';
        }

        return $this->executeSelect($fieldsString, $conditions, $orderBy);
    }

    public function getOne(array $fieldsArray = array(), array $conditions = array())
    {

        $result = $this->getAll($fieldsArray, $conditions);

        return $result[0];
    }

    public function insert(array $fields)
    {

        if (count($fields) == 0) {
            return false;
        }

        $resource = self::getResource();

        $fieldsUpdate = '(';
        $valueUpdate = '(';

        foreach ($fields as $field => $value) {
            $fieldsUpdate .= ' ' . $field . ',';
            $valueUpdate .= ' :' . $field . ',';
        }

        $fieldsUpdate = substr($fieldsUpdate, 0, -1) . ')';
        $valueUpdate = substr($valueUpdate, 0, -1) . ')';

        $stmt = $resource->prepare('INSERT INTO ' . $this->_entity . ' ' . $fieldsUpdate . ' VALUES ' . $valueUpdate);

        foreach ($fields as $field => $value) {
            $stmt->bindValue(':' . $field, $value);
        }

        return $this->execute($stmt);
    }

    public function update(array $fields, array $conditions)
    {

        if (count($fields) == 0) {
            return false;
        }

        $resource = self::getResource();

        $fieldsUpdate = ' SET';
        foreach ($fields as $field => $value) {
            $fieldsUpdate .= ' ' . $field . ' = :f' . $field . ',';
        }

        $fieldsUpdate = substr($fieldsUpdate, 0, -1);

        $conditionsWhere = $this->conditionsArrayToString($conditions);

        $stmt = $resource->prepare('UPDATE ' . $this->_entity . ' ' . $fieldsUpdate . $conditionsWhere);

        foreach ($fields as $field => $value) {
            $label = ':f' . $field;
            $stmt->bindValue($label, $value);
        }

        foreach ($conditions as $field => $value) {
            $label = ':c' . $field;
            $stmt->bindValue($label, $value);
        }

        return $this->execute($stmt);
    }

    protected function executeSelect($fieldsString, $conditions, $orderBy = false, $limit = false)
    {
        $conditionsWhere = $this->conditionsArrayToString($conditions);

        $stmt = self::getResource()->prepare('SELECT ' . $fieldsString . ' FROM ' . $this->_entity . $conditionsWhere . $this->getOrderBy($orderBy) . $this->getLimit($limit));

        foreach ($conditions as $field => $value) {
            $stmt->bindValue(':c' . $field, $value);
        }

        $result = $this->execute($stmt);

        $resultValues = array();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $resultValues[] = $row;
        }

        return $resultValues;
    }

    protected function getOrderBy($orderBy)
    {
        $result = '';

        if ($orderBy) {
            $result = ' ORDER BY ' . $orderBy . ' ';
        }

        return $result;
    }

    public function delete(array $conditions = array(), $limit = false)
    {

        $conditionsWhere = $this->conditionsArrayToString($conditions);

        $stmt = self::getResource()->prepare('DELETE FROM ' . $this->_entity . $conditionsWhere .
            $this->getLimit($limit));

        foreach ($conditions as $field => $value) {
            $stmt->bindValue(':c' . $field, $value);
        }

        return $this->execute($stmt);
    }

    protected function getLimit($limit)
    {

        $result = '';
        if ($limit !== false) {
            if (gettype($limit) == 'integer') {
                $result .= ' LIMIT ' . (int)$limit;
            } else if (gettype($limit) == 'array') {
                $result .= ' LIMIT ' . $limit[0] . ', ' . $limit[1];
            }
        }

        return $result;
    }

    protected function conditionsArrayToString($conditions)
    {

        if (count($conditions) == 0) {
            return '';
        }

        $conditionsWhere = ' WHERE';

        foreach ($conditions as $field => $value) {
            $conditionsWhere .= ' ' . $field . ' = :c' . $field . ' AND';
        }

        return substr($conditionsWhere, 0, -3);
    }

    public function lastInsertRowid()
    {

        return $this->getResource()->lastInsertRowid();
    }

    protected function execute(SQLite3Stmt $stmt)
    {
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception('There was a problem performing this operation!');
        }

        return $result;
    }

}