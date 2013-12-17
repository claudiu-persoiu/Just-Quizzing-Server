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

class DatabaseEntity {

    static protected $_resource;

    static protected $_entities = array();

    protected $_entity;

    public static function getEntity($entity) {

        if(!isset(self::$_entities[$entity])) {
            self::$_entities[$entity] = new self($entity);
        }

        return self::$_entities[$entity];
    }

    public function __construct($entity) {
        if (!$entity) {
            throw new Exception('no entity specified');
        }

        $this->_entity = $entity;
    }

    public static function getResource() {
        if(!self::$_resource) {
            self::$_resource = new SQLite3('data/db.info');
            self::$_resource->exec("PRAGMA journal_mode = MEMORY;
                       PRAGMA temp_store   = MEMORY;
                       PRAGMA encoding     = 'UTF-8';");
        }

        return self::$_resource;
    }

    public function getAll(array $fieldsArray = array(), array $conditions = array()) {

        if(count($fieldsArray)) {
            $fieldsString = implode(', ', $fieldsArray);
        } else {
            $fieldsString = '*';
        }

        return $this->executeSelect($fieldsString, $conditions);
    }

    public function getOne(array $fieldsArray = array(), array $conditions = array()) {

        if(count($fieldsArray)) {
            $fieldsString = implode(', ', $fieldsArray);
        } else {
            $fieldsString = '*';
        }

        $result = $this->executeSelect($fieldsString, $conditions);
        return $result[0];
    }

    public function insert(array $fields) {

        if(count($fields) == 0) {
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

        return $stmt->execute();
    }

    public function update(array $fields, array $conditions) {

        if(count($fields) == 0) {
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

        return $stmt->execute();
    }

    protected function executeSelect($fieldsString, $conditions, $limit = false) {

        $resource = self::getResource();

        $limit = '';
        if($limit !== false) {
            if (gettype($limit) == 'integer') {
                $limit .= ' LIMIT ' . (int)$limit;
            } else if(gettype($limit) == 'array') {
                $limit .= ' LIMIT ' . $limit[0] . ', ' . $limit[1];
            }
        }

        $conditionsWhere = $this->conditionsArrayToString($conditions);

        $result = $resource->prepare('SELECT ' . $fieldsString . ' FROM ' . $this->_entity . $conditionsWhere . $limit);

        foreach ($conditions as $field => $value) {
            $result->bindValue(':c' . $field, $value);
        }

        $stmtResult = $result->execute();

        $resultValues = array();

        while($row = $stmtResult->fetchArray(SQLITE3_ASSOC)) {
            $resultValues[] = $row;
        }

        return $resultValues;
    }

    public function delete(array $conditions = array()) {

        $conditionsWhere = $this->conditionsArrayToString($conditions);

        $stmt = self::getResource()->prepare('DELETE FROM ' . $this->_entity . $conditionsWhere);

        foreach ($conditions as $field => $value) {
            $stmt->bindValue(':c' . $field, $value);
        }

        return $stmt->execute();

    }

    protected function conditionsArrayToString($conditions) {

        if(count($conditions) == 0) {
            return '';
        }

        $conditionsWhere = ' WHERE';

        foreach ($conditions as $field => $value) {
            $conditionsWhere .= ' ' . $field . ' = :c' . $field . ' AND';
        }

        return substr($conditionsWhere, 0, -3);

    }

    public function lastInsertRowid() {

        return $this->getResource()->lastInsertRowid();

    }

}