<?php

require_once 'models/Model.php';

class User extends Model
{
    public static $tableName = 'users';

    protected $id;

    protected $name;

    protected $middleName;

    protected $secondName;

    public static function fields()
    {
        return [
            'id',
            'name',
            'middleName',
            'secondName',
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

    public function getMiddleName()
    {
        return $this->middleName;
    }

    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getSecondName()
    {
        return $this->secondName;
    }

    public function setSecondName($secondName)
    {
        $this->secondName = $secondName;

        return $this;
    }

    public function __toString()
    {
        return $this->secondName . ' ' . $this->name . ' ' . $this->middleName;
    }
}