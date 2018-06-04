<?php

class Mysql
{
    public $mysqli = null;
    public $result = null;
    public $total = 0;
    public $record = [];
    public $current = [];
    public $insert_id = null;

    public function __construct($host='', $user='', $password='', $database='')
    {
        $this->init($host, $user, $password, $database);
    }

    public function init($host='', $user='', $password='', $database='')
    {
        date_default_timezone_set('Asia/Taipei');

        $this->mysqli = new mysqli($host, $user, $password, $database);

        if ($this->mysqli->connect_errno) {
            printf("Connect failed: %s.\n", $this->mysqli->connect_error);
            $this->mysqli->close();
            exit();
        }

        $this->mysqli->query("SET NAMES 'UTF8MB4'");
    }

    public function query($sql='')
    {
        if ($this->mysqli === null) {
            $this->init();
        }

        if ($this->result = $this->mysqli->query($sql)) {
            $this->insert_id = $this->mysqli->insert_id;
            if ($this->result === true) {

            } else if (is_object($this->result)) {
                if ($this->total = $this->result->num_rows) {
                    $this->current = $this->result->fetch_array(MYSQLI_BOTH);
                } else {
                    $this->current = [];
                }
            }
        } else {
            printf("<h3 style=\"color: red;\"><strong>SQL error: %s.</strong></h3><br/><br/><br/><br/><div style=\"color: white;\">%s</div>", time(), $this->mysqli->error);
            echo('SQL error: '. $this->mysqli->error);
            echo "\n".$sql;
            $this->mysqli->close();
            die();
        }
    }

    public function next_record()
    {
        $current = $this->current;

        if (is_object($this->result)) {
            $next = ($this->current = $this->result->fetch_array(MYSQLI_BOTH));

            if (empty($next)) {
                $this->current = null;
            }
        } else {
            $this->current = null;
        }

        return ($this->record = $current);
    }

    public function get_num_rows()
    {
        return $this->total;
    }

    public function get_last_insert_id()
    {
        return $this->insert_id;
    }

    public function quote($value, $type='value')
    {
        return $this->value_quote($value, $type);
    }

    public function value_quote($value, $type='value')
    {
        return "'". $this->mysqli->real_escape_string($value) ."'";
    }

    public function name_quote($value='')
    {
        if (!is_string($value)) {
            return false;
        }

        $value = trim($value);

        if (preg_match('/^`(.+)`$/', $value, $matches)) {
            return "`{$matches[1]}`";
        }

        if (preg_match('/\W/', $value)) {
            return "`{$value}`";
        }

        return $value;
    }

    function close()
    {
        $this->mysqli->close();
        $this->mysqli = null;
    }
}
