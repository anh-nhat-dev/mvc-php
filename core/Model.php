<?php

namespace core;

use core\DB;
use PDO;

abstract class Model {
    protected $db = NULL;

    public $table;

    /**
     * 
     */
    protected $columns;

    protected $where;

    protected $limit;

    protected $offset;

    /**
     * Available comparisons for where clause.
     */
    protected $comparisons = [
        'equal' => '=',
        'not_equal' => '<>',
        'not_equal_other' => '!=',
        'less' => '<',
        'less_or_equal' => '<=',
        'greater' => '>',
        'greater_or_equal' => '>=',
        'like' => 'like',
        'in' => 'in',
        'not_in' => 'not in',
        'between' => 'between',
        'not_between' => 'not between',
    ];

    protected $fetch_style = PDO::FETCH_OBJ;


    /**
     * 
     * 
     */
    final public function __construct(){
        $this->db = DB::getDB();
    }

     /**
      * 
      */
     public function select($columns = ["*"]) {
        $this->columns = [];

        $columns = \is_array($columns) ? $columns : \func_get_args();

        foreach($columns as $column) {
            $this->columns[] = $column;            
        }

        return $this;

     }

     /**
      * 
      */
     public function where(...$params){
        return $this->andWhere(...$params);
     }

     /**
      * 
      */

      public function andWhere(...$params){
        return $this->whereLogicOperator('and', ...$params);
      }

    /**
     * 
     */

     public function orWhere(...$params) {
        return $this->whereLogicOperator('or', ...$params);
     }

      /**
       * 
       */
    protected function whereLogicOperator($logicOperator, ...$params)
    {
        list($field, $operator, $value) = $this->getParseWhereParameters($params);

        $this->addWhere($field, $operator, $value, $logicOperator);

        return $this;
    }

    /**
     * 
     */
    protected function getParseWhereParameters(array $params)
    {
        if (count($params) === 3) {
            return $params;
        }
        
        if (count($params) === 2) {
            return [$params[0], $this->comparisons['equal'], $params[1]];
        }

        die('Not valid where parameters.');
    }

    
    /**
     * 
     */
    protected function addWhere($field, $operator, $value, $logicOperator = 'and')
    {
        if (! in_array($operator, $this->comparisons)) {
            die("$operator is invalid operator.");
        }

        switch ($operator) {
            case $this->comparisons['in']:
            case $this->comparisons['not_in']:
                $value = '(' . implode(', ', $value) . ')';
                break;
            case $this->comparisons['between']:
            case $this->comparisons['not_between']:
                $value = $value[0] . ' and ' . $value[1];
                break;
            default:
        }

        $this->where[] = [
            'logic_operator' => $logicOperator,
            'params' => compact('field', 'operator', 'value'),
        ];
    }

    /**
     * 
     */
    protected function getCompiledWhere()
    {
        if (empty($this->where)) {
            return '';
        }

        $conditions = '';

        foreach ($this->where as $key => $where) {
            $conditions .= $where['logic_operator'] . ' '
                . $where['params']['field'] . ' '
                . $where['params']['operator'] . ' '
                . $this->stringValue($where['params']['value']) . ' ';
        }

        $conditions = '(' . ltrim(ltrim($conditions, 'or'), 'and') . ')';

        return 'where ' . trim($conditions);
    }

    /**
     * 
     */
    protected function stringValue($value) {
        return \is_string($value) ? '"'. $value. '"' : $value;
    }
    

     /**
      * 
      */
     public function limit($limit = 10) {
        $this->limit = (int) $limit;

        return $this;
     }

     /**
      * 
      */
      public function offset($offset = 10) {
        $this->offset = (int) $offset;

        return $this;
     }

    /**
     * 
    */
    protected function getCompileSelect($columns = ["*"]){
        if (is_null($this->columns)) {
        $this->columns = $columns;
        }
        return  'select '. \implode(", ", $this->columns). ' ';
    }

    /**
     * 
     */

    protected function getCompileFrom() {
        return 'from '. $this->table. ' ';
        
    }

    /**
    * 
    */
    protected function getCompileLimit() {
        return isset($this->limit) ? 'limit '.$this->limit. ' ' : ''; 
    }

    /**
    * 
    */
    protected function getCompileOffset() {
        return isset($this->offset) ? 'offset '.$this->offset. ' ' : ''; 
    }

       
    /**
    * 
    */
    protected function getCompileSelectStatement(){

        $statements[] = $this->getCompileSelect();
        $statements[] = $this->getCompileFrom();
        $statements[] = $this->getCompiledWhere();
        $statements[] = $this->getCompileLimit();
        $statements[] = $this->getCompileOffset();


        $this->clearAll();
        
        return implode(" ", $statements);
    }

    /**
    * 
    */
    public function first() {
        return $this->db->query($this->getCompileSelectStatement())->fetch($this->fetch_style);
    }

     /**
      * 
      */
      public function get(){
        return $this->db->query($this->getCompileSelectStatement())->fetchAll($this->fetch_style);
     }

    /**
    * 
    */
    protected function clearAll() {
        $this->columns   = NULL;
        $this->where   = NULL;
        $this->limit     = NULL;
        $this->offset    = NULL;
    }

    
}