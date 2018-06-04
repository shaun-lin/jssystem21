<?php

require_once __DIR__ .'/ServerStorage.php';

class FilemanagerSetting extends ServerStorage
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllSettings()
    {
        if (empty($this->cacheData) && $rowsSettings = $this->search()) {
            foreach ($rowsSettings as $settingItem) {
                $this->cacheData += [
                    $settingItem['setting_name'] => strpos($settingItem['setting_value'], ',') === false ? $settingItem['setting_value'] : explode(',', $settingItem['setting_value'])
                ];
            }
        }

        return $this->cacheData;
    }

    public function read($name='')
    {
        return isset($this->data[$name]) ? $this->data[$name] : '' ;
    }
}
