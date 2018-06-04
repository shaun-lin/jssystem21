<?php

class Adapter
{
    public $db;
    public $fields = [];
    public $specificKey = null;
    public $specificDatabase = null;

    public function __construct($id=0, $primaryKey='', $database=null)
    {
        $reflector = new ReflectionClass($this);
        $model = preg_split('/(?=[A-Z])/', $reflector->getShortName());
        array_shift($model);
        $model = implode('_', $model);
        $this->db = clone($GLOBALS['app']->db);
        $this->table = strtolower($model);

        if (!empty($primaryKey)) {
            $this->specificKey = $primaryKey;
        }

        if (!empty($database)) {
            $this->specificDatabase = $database;
        }

        if (is_numeric($id) && $id > 0) {
            $this->load($id);
        }
    }
    
    public function getKey()
    {
        if (!empty($this->specificKey)) {
            return $this->specificKey;
        }
        
        $key = explode('_', $this->getTable());
        $key = end($key);
        $key = strtolower($key) .'_id';
        return $key;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function searchQuery($query='*', $where='', $join='', $orderby='', $orderdir='', $key='', $start=0, $limit=-1, $groupby='', $having='')
    {
        if ($orderby) {
            $sql = "SELECT %s FROM %s%s %s WHERE %s ORDER BY %s %s";
        } else {
            $sql = "SELECT %s FROM %s%s %s WHERE %s";
        }

        if ($query == '*') {
            $query_sql = "*";
        } elseif (is_array($query)) {
            $query_sql = implode(", ", $query);
        } else {
            $query_sql = $query;
        }

        if (empty($where)) {
            $where = "1=1";
        } elseif (is_array($where)) {
            $tmp = implode(" AND ", $where);
            $where = $tmp;
        }
        
        if (empty($orderby)) {
            $orderby = $this->getKey();
        }

        if (empty($orderdir)) {
            $orderdir = 'ASC';
        }

        $orders = [];
        if (is_array($orderby)) {
            foreach ($orderby as $k => $name) {
                if (isset($orderdir[$k])) {
                    $orders[] = "$name {$orderdir[$k]}";
                } else {
                    $orders[] = "$name ASC";
                }
            }
        }

        $extra_sql = '';
        if ($groupby) {
            $extra_sql = ' GROUP BY ' . $groupby;

            if ($having) {
                $extra_sql .= ' HAVING ' . $having;
            }
        }

        if ($orders) {
            $orderby = implode(', ', $orders);
            $orderdir = "";
        }

        $statement = sprintf($sql, $query_sql, empty($this->specificDatabase) ? '' : "{$this->specificDatabase}." , $this->getTable(), $join, $where . $extra_sql, $orderby,  $orderdir);
        if ($start >= 0 && $limit > 0) {
            $statement .= ' LIMIT '. $start .', '. $limit;
        }

        $this->db->query($statement);
        $result = array();

        do {
            $this->db->next_record();
            $tmp = $this->db->record;

            if ($tmp) {
                if ($key) {
                    $result[$tmp[$key]] = $tmp;
                } else {
                    $result[] = $tmp;
                }
            }
        } while (is_array($tmp));
        return $result;
    }

    public function searchWithFields($fields, $where='', $orderby='', $orderdir='', $key='', $start=0, $limit=-1, $join='')
    {
        return $this->searchQuery($fields, $where, $join, $orderby, $orderdir, $key, $start, $limit);
    }

    public function searchAll($where='', $orderby='', $orderdir='', $key='', $join='', $query='*', $groupby='')
    {
        return $this->searchQuery($query, $where, $join, $orderby, $orderdir, $key, 0, -1, $groupby);
    }
    
    public function search($where='', $orderby='', $orderdir='', $key='', $start=0, $limit=0, $join='', $query='*', $groupby='')
    {
        $limit = empty($limit) ? 30 : $limit;
        return $this->searchQuery($query, $where, $join, $orderby, $orderdir, $key, $start, $limit, $groupby);
    }

    public function searchCount($where='', $join='')
    {
        $sql = "SELECT count(%s) FROM %s %s WHERE %s";

        if (empty($where)) {
            $where = "1=1";
        } elseif (is_array($where)) {
            $tmp = implode(" AND ", $where);
            $where = $tmp;
        }

        $this->db->query(sprintf($sql, $this->getKey(), $this->getTable(), $join, $where));
        $this->db->next_record();
        $tmp = $this->db->record;

        return intval($tmp[0]);
    }

    public function store($force_insert=false)
    {
        $key = $this->getKey();

        foreach (static::DEFAULT_FIELDS as $fieldName => $fieldVar) {
            if (!IsId($this->getId()) && EndsWith($fieldName, '_creator')) {
                $this->setVar($fieldName, GetCurrentUserId());
            } else if (!IsId($this->getId()) && EndsWith($fieldName, '_created')) {
                $this->setVar($fieldName, date('Y-m-d H:i:s'));
            } else if (EndsWith($fieldName, '_modifier')) {
                $this->setVar($fieldName, GetCurrentUserId());
            } else if (EndsWith($fieldName, '_modified')) {
                $this->setVar($fieldName, date('Y-m-d H:i:s'));
            }
        }

        if (empty($key) || empty($this->fields[$key]) || $force_insert === true) {
            $this->db_insert($force_insert);
        } else {
            $this->db_update();
        }
        
        return $this->fields[$key];
    }

    public function delete($key = '')
    {
        if (!$key) {
            $key = $this->getKey();
        }
        
        $key_value = $this->fields[$key];
        $table = $this->getTable();

        if ($table && $key && $key_value) {
            $sql = sprintf("DELETE FROM %s WHERE %s = %s;", 
                                $this->db->name_quote($table), 
                                $this->db->name_quote($key),
                                $this->db->value_quote($key_value));

            return $this->db->query($sql);
        }
    }

    public function delete_by($where='')
    {
        $table = $this->getTable();

        if ($table && is_string($where) && $where) {
            $sql = sprintf("DELETE FROM %s WHERE %s", 
                                $this->db->name_quote($table), 
                                $where);
            
            return $this->db->query($sql);
        }
    }

    public function db_query($sql)
    {
        $this->db->query($sql);
    }

    public function db_update()
    {
        $sql = "UPDATE %s SET %s WHERE %s";
        $values = array();

        foreach (static::DEFAULT_FIELDS as $field_name => $type) {
            if ($field_name != $this->getKey()) {
                if (isset($this->fields[$field_name]) && $this->fields[$field_name] !== null) {
                    $values[] = '`'. $this->db->name_quote($field_name) ."` = " . $this->db->value_quote($this->fields[$field_name]);
                }
            }
        }

        $key = $this->getKey();
        $where = '`'. $this->db->name_quote($this->getKey()) . "` = " . $this->db->value_quote($this->fields[$key]);
        if (count($values)) {
            $this->db->query(sprintf($sql, $this->getTable(), implode(", ", $values), $where));
        }
    }

    public function db_insert($force_insert=false)
    {
        $sql = "INSERT INTO %s (%s) VALUES (%s)";

        $values = array();
        
        if ($force_insert === true && isset($this->fields[$this->getKey()]) && $this->fields[$this->getKey()] !== null) {
            $values[$this->db->name_quote($this->getKey())] = $this->db->value_quote($this->fields[$this->getKey()]);
        }

        foreach (static::DEFAULT_FIELDS as $field_name => $type) {
            if ($field_name != $this->getKey()) {
                if (isset($this->fields[$field_name]) && $this->fields[$field_name] !== null) {
                    $values['`'. $this->db->name_quote($field_name) .'`'] = $this->db->value_quote($this->fields[$field_name]);
                } elseif (!isset($this->fields[$field_name])) {
                    $values['`'. $this->db->name_quote($field_name) .'`'] = $this->db->value_quote(static::DEFAULT_FIELDS[$field_name]);
                }
            }
        }

        if (count($values)) {
            $this->db->query(sprintf($sql, $this->getTable(), implode(", ", array_keys($values)), implode(",", array_values($values))));

            if ($this->getKey()) {
                $key = $this->getKey();
                $this->fields[$key] = $this->db->get_last_insert_id($this->getTable(), $this->getKey());
            }
        }
    }

    public function bind($data)
    {
        foreach (static::DEFAULT_FIELDS as $field_name => $default_var) {
            if (isset($data[$field_name])) {
                $this->fields[$field_name] = $data[$field_name];
            }
        }
    }

    public function load($id)
    {
        $where = $this->db->name_quote($this->getKey()) . '=' . $this->db->value_quote($id);
        $record = $this->search($where);

        if (is_array($record) && count($record) == 1) {
            $data = $record[0];

            foreach (static::DEFAULT_FIELDS as $field_name => $default_var) {
                if (isset($data[$field_name])) {
                    $this->fields[$field_name] = $data[$field_name];
                }
            }

            return true;
        } else {
            return false;
        }
    }

    public function getAllFields()
    {
        return $this->fields;
    }

    public function reset()
    {
        $this->fields = [];
    }

    public function ignore($key)
    {
        if (is_array($this->fields) && array_key_exists($key, $this->fields)) {
            unset($this->fields[$key]);
        }
    }

    public function loadKey($key, $id)
    {
        $where = $this->db->name_quote($key) . '=' . $this->db->value_quote($id);
        $record = $this->search($where);

        if (is_array($record) && count($record) == 1) {
            $data = $record[0];

            foreach (static::DEFAULT_FIELDS as $field_name => $default_var) {
                $this->fields[$field_name] = $data[$field_name];
            }

            return true;
        }

        return false;
    }

    public function toString()
    {
        $out = array();

        $vars = $this->fields;
        foreach ($vars as $var => $type) {
            $out[$var] = $this->fields[$var];
        }

        return $out;
    }
    
    public function getId()
    {
        return $this->getVar($this->getKey());
    }
    
    public function setVar($key='', $var='')
    {
        if (!empty($key) && is_array(static::DEFAULT_FIELDS) && array_key_exists($key, static::DEFAULT_FIELDS)) {
            $this->fields[$key] = $var;
        }
    }
    
    public function getVar($key='')
    {
        return (!empty($key) && is_array($this->fields) && array_key_exists($key, $this->fields)) ? $this->fields[$key] : '' ;
    }
    
    public function isId($id=0, $type='number')
    {
        if ($type == 'number') {
            return (!empty($id) && is_numeric($id) && $id > 0);
        } else {
            return true;
        }
    }
}
