<?php

require_once __DIR__ .'/Adapter.php';

class TagMap extends Adapter
{
    const DEFAULT_FIELDS = [
        'map_id' => null,
        'map_tag' => '',
        'map_relation' => '',
        'map_item_id' => ''
    ];

    public function __construct($id=0)
    {
        parent::__construct($id);
    }

    public function deleteMapping($itemId=0, $relation='')
    {
        if ($relation) {
            $condition = sprintf("`map_item_id` = %d", $itemId) ." AND `map_relation` = ". $this->db->quote($relation);
            $this->delete_by($condition);
        }
    }
}
