<?php

require_once 'models/Model.php';
require_once 'models/User.php';
require_once 'models/Status.php';

class Task extends Model
{
    public static $tableName = 'tasks';

    protected $id;

    protected $name;

    protected $estimatedTime;

    protected $startDate;

    protected $endDate;

    protected $userId;

    protected $statusId;

    public static function fields()
    {
        return [
            'id',
            'name',
            'estimatedTime',
            'startDate',
            'endDate',
            'userId',
            'statusId',
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

    public function getEstimatedTime()
    {
        return $this->estimatedTime;
    }

    public function setEstimatedTime($estimatedTime)
    {
        $this->estimatedTime = intval($estimatedTime) > 0 ? intval($estimatedTime) : null; //Если введено не число, то ничего не присваиваем

        return $this;
    }

    public function getStartDate()
    {
        return $this->startDate ? new DateTime($this->startDate) : null;    //Возвращает объект DateTime
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate()
    {
        return $this->endDate ? new DateTime($this->endDate) : null;
    }

    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getUser()
    {//Если исполнитель назначен и в $userId находится его ID, то берем его из БД
        if (!$this->userId) return null;
        $userData = $this->_db->find(User::$tableName, $this->userId);
        $user = new User($this->_db);   //Создаем экземпляр класса
        $user->setId($userData['id'])   //Заполняем его поля
            ->setName($userData['name'])
            ->setMiddleName($userData['middle_name'])
            ->setSecondName($userData['second_name']);
        return $user;   //И возвращаем готовый объект User
    }

    public function setUserId($userId)
    {
        $this->userId = $userId ? $userId : null;

        return $this;
    }

    public function setUser(User $user)
    {
        $this->userId = $user->id;

        return $this;
    }

    public function getStatusId()
    {
        return $this->statusId;
    }

    public function getStatus()
    {
        if (!$this->statusId) return null;
        $statusData = $this->_db->find(Status::$tableName, $this->statusId);
        $status = new Status($this->_db);
        $status->setId($statusData['id'])
            ->setName($statusData['name']);
        return $status;
    }

    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;

        return $this;
    }

    public function setStatus(Status $status)
    {
        $this->statusId = $status->id;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->name;
    }
}