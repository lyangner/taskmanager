<?php

require_once 'models/User.php';
require_once 'models/Task.php';

class Repository    //Класс для синхронизации моделей с БД
{
    protected $entity;  //Текущая сущность

    private $_db;

    public function __construct($db, $entity=null)
    {
        $this->_db = $db;
        $this->entity = $entity;

        return $this;
    }

    public function entity($name)
    {
        $this->entity = $name;  //Выбор сущности для создания цепочки вызовов

        return $this;
    }

    public function find($id)
    {
        $entityName = $this->entity;    //Получаем класс сущности
        $_entityData = $this->_db->find($entityName::$tableName, $id);  //Находим её в БД
        return empty($_entityData) ? null : $this->build($this->entity, $_entityData);  //Создаем модель на основе полученных данных
    }

    public function findAll()
    {
        $entityName = $this->entity;
        $_entitiesData = $this->_db->findAll($entityName::$tableName);
        if (empty($_entitiesData)) return null;
        $entities = [];
        foreach ($_entitiesData as $_entityData) {
            $entities[] = $this->build($this->entity, $_entityData);
        }

        return $entities;   //Возвращаем массив созданных сущностей
    }

    public function persist($entity)
    {
        if (empty($entity)) return false;

        $fields = $this->getEntityFields($entity);  //Получаем из модели данные в виде ассоциативного массива

        if ($entity->id)  //Если у неё уже есть ID, значит она существующая
            return $this->_db->update($entity::$tableName, $fields, 'id = ' . $entity->id); //Обновляем

        return $this->_db->insert($entity::$tableName, $fields);    //Иначе новая и надо её создать в БД
    }

    public function delete($id)
    {
        $entityName = $this->entity;

        return $this->_db->delete($entityName::$tableName, 'id = ' . $id);
    }

    protected function build($model, $data)
    {
        $entity = new $model($this->_db);   //Создаем экземпляр выбранного класса сущности
        foreach ($data as $prop => $value) {
            $setter = $this->camelCase($prop);  //Преобразовываем имена столбцов из БД к виду camelCase
            $entity->$setter = $value;  //Устанавливаем значение каждого поля сущности значением из БД
        }
        return $entity; //Возвращаем готовый объект
    }

    private function getEntityFields($entity)
    {
        $fields = $entity::fields();    //Сначала получаем список свойств сущности
        $data = [];
        foreach ($fields as $field) {
            $dbField = $this->decamelize($field);   //Приводим их имена к виду name_of_field
            $data[$dbField] = $entity->$field;  //Формируем ассоциативный массив для сохранения в БД
            if ($data[$dbField] instanceof DateTime) { // Если свойство - дата, то преобразуем в строку
                var_dump($data[$dbField]);
                $data[$dbField] = $data[$dbField]->format("Y-m-d");
            }
        }
        return $data;
    }

    private function decamelize($camelCase)
    {
        return strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $camelCase));
    }

    private function camelCase($input)
    {
        return str_replace('_', '', ucwords($input));
    }
}