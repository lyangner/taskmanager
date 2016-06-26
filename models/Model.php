<?php

abstract class Model
{
    protected $_db;

    public function __construct($db)
    {
        if (empty($db))
            throw new Exception('I need a database instance');
        $this->_db = $db;

        return $this;
    }

    public static function fields()
    {
        return [];
    }

    public function __get($name)
    {
        $getterName = 'get' . ucfirst($name);   //Преобразовывает запрос к свойству класса entity->fieldName в метод entity->getFieldName()

        if (method_exists($this, $getterName)) {    //Если такой метод есть, то вызывает его
            return $this->$getterName();
        }

        return null;
    }

    public function __set($name, $value)
    {
        $setterName = 'set' . ucfirst($name);//Преобразовывает запрос к свойству класса entity->fieldName = $x в метод entity->setFieldName($x)

        if (method_exists($this, $setterName)) {
            $this->$setterName($value);
        }
    }
}