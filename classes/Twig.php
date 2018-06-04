<?php

class Twig
{
    public $view;
    public $fields = [];

    public function __construct($templateDir='', $filename='', $assign=[])
    {
        $this->view = new \Twig_Environment(new \Twig_Loader_Filesystem($templateDir), []);
        $this->setFile($filename, $assign);
    }

    public function setFile($filename='', $assign=[])
    {
        $this->filename = $filename;
        $this->assign($assign);
    }

    public function assign($fields=[])
    {
        if (empty($this->fields)) {
            $this->fields = $fields;
        } else if (is_array($fields)) {
            foreach ($fields as $name => $value) {
                $this->fields[$name] = $value;
            }
        }
    }

    public function display()
    {
        echo $this->getContent();
    }

    public function getContent()
    {
        return $this->view->render($this->filename, $this->fields);
    }
}