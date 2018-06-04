<?php

require_once __DIR__ .'/Adapter.php';

class Message extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'message' => '',
        'link' => ''
    ];

    public $cacheData = [];

    public function __construct($id=0, $key='id')
    {
        parent::__construct($id, $key);
    }

    public function getRandomMessage()
    {
        if ($this->load(rand(1, 369))) {
            return $this->getVar('message');
        }

        return $this->getRandomMessage();
    }
}
