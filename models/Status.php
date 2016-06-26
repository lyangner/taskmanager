<?php

require_once 'models/Model.php';

class Status extends Model
{
    public static $tableName = 'statuses';

    protected $id;

    protected $name;

    public static function fields()
    {
        return [
            'id',
            'name'
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->name;
    }
}