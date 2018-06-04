<?php

require_once __DIR__ .'/Adapter.php';

class Tag extends Adapter
{
    const DEFAULT_FIELDS = [
        'tag_id' => null,
        'tag_relation' => '',
        'tag_name' => '',
        'tag_color' => '#FFE7CD',
        'tag_sort' => ''
    ];

    public $cacheData = [];

    public function __construct($id=0, $relation='')
    {
        parent::__construct($id);

        if ($relation) {
            $this->initRelation($relation);
        }
    }

    public function getDetail($id=0, $relation='')
    {
        if ($relation) {
            if (isset($this->cacheData[$relation])) {
                return isset($this->cacheData[$relation][$id]) ? $this->cacheData[$relation][$id] : null;
            } else {
                $this->initRelation($relation);
            }
        }

        return null;
    }

    public function initRelation($relation='')
    {   
        if ($relation) {
            $condition = "`tag_relation` = ". $this->db->quote($relation);

            foreach ($this->searchAll($condition, 'tag_sort', 'ASC') as $item) {
                $this->cacheData[$relation][$item['tag_id']] = $item;
            }

            if (!isset($this->cacheData[$relation])) {
                $this->cacheData[$relation] = [];
            }

            return $this->cacheData[$relation];
        }

        return null;
    }

    public function getRelationData($relation='')
    {
        if ($relation) {
            if (!isset($this->cacheData[$relation])) {
                $this->initRelation($relation);
            }

            return $this->cacheData[$relation];
        }

        return null;
    }
}
