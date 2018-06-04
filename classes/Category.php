<?php

require_once __DIR__ .'/Adapter.php';

class Category extends Adapter
{
    const DEFAULT_FIELDS = [
        'category_id' => null,
        'category_parent' => '',
        'category_relation' => '',
        'category_name' => '',
        'category_sort' => ''
    ];

    public function __construct($id=0, $key='')
    {
        parent::__construct($id, $key);
    }

    public function getCategoryId($relation='', $name='', $parent=0)
    {
        $conditions = [];
        $conditions[] = sprintf("`category_parent` = %d", $parent);
        $conditions[] = "`category_relation` = ". $this->db->quote($relation);
        $conditions[] = "`category_name` = ". $this->db->quote($name);
        $rows = $this->search($conditions, '', '', '', 0, 1);

        return isset($rows[0]['category_id']) ? $rows[0]['category_id']  : 0;
    }
}
