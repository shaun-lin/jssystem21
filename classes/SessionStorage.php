<?php

class SessionStorage
{
    public function set($name='', $value=null)
    {
        if (!isset($_SESSION['storage'])) {
            $_SESSION['storage'] = [];
        }
        
        $_SESSION['storage'][$name] = $value;
    }

    public function get($name='')
    {   
        return !empty($name) && isset($_SESSION['storage'][$name]) ? $_SESSION['storage'][$name] : null;
    }

    public function clear()
    {
        if (isset($_SESSION['storage'])) {
            unset($_SESSION['storage']);
        }
    }
}
