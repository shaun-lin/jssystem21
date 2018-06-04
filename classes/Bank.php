<?php

require_once __DIR__ .'/ServerStorage.php';

class Bank extends ServerStorage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function read($name='')
    {
        return isset($this->data[$name]) ? $this->data[$name] : '' ;
    }
}
