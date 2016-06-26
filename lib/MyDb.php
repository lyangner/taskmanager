<?php

class MyDB
{
    protected $db;

    public function __construct($host, $dbname, $user, $pass)
    {
        $this->db = new PDO(
            "mysql:host=".$host.";dbname=".$dbname,
            $user,
            $pass,
            [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'']
        );
    	$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function find($table, $id, $fields='*')
    {
		$entity = $this->fetch($table, $fields, 'id = ' . $id);
        return !empty($entity) ? $entity[0] : [];
    }

    public function findAll($table)
    {
        return $this->fetch($table);
    }

    public function findAllWith($table, $with, $conditions)
    {
        if (!$this->db instanceof PDO) return false;
        $sth = $this->db->query('SELECT * FROM ' . $table . ' inner join ' . $with .' on ' . $conditions);
		$sth->setFetchMode(PDO::FETCH_ASSOC);
		return $sth->fetchAll();
    }

    public function query($sql)
    {
        if (!$this->db instanceof PDO) return false;
        $sth = $this->db->query($sql);
		$sth->setFetchMode(PDO::FETCH_ASSOC);
		return $sth->fetchAll();
    }

    public function fetch($table, $fields='*', $conditions=null, $order=null, $limit=null){
        if (!$this->db instanceof PDO) return false;
        $_fields = is_array($fields) ? implode(',', $fields) : $fields; //Преобразовываем массив столбцов в строку
        $_conditions = strlen(trim($conditions)) > 1 ? ' where ' . $conditions : '';
        $_order = strlen(trim($order)) > 1 ? ' order by ' . $order : '';
        $_limit = strlen(trim($limit)) > 0 ? ' limit ' . $limit : '';
        $sql = 'select '.$_fields.' from '.$table.$_conditions.$_order.$_limit;
        $sth = $this->db->prepare($sql);
        $sth->setFetchMode(PDO::FETCH_ASSOC);   //После запроса вернет ассоциативный массив
        $sth->execute();
        return $sth->fetchAll();
    }

    public function insert($table, $fields)
    {
        if (!$this->db instanceof PDO) return false;
        //Преобразовываем массив данных в строку вида: `field1`, `field2`, `field3`
        $values = $this->implode_with_quotes(array_keys($fields), '`');
        $data = $this->make_values(array_keys($fields));
        //Преобразовываем массив данных в строку вида: :field1, :field2, :field3 для последующего биндинга
        $sql = "INSERT INTO `$table` ($values) VALUES ($data)";
        $sth = $this->db->prepare($sql);
        foreach ($fields as $key=>$value) {
            $sth->bindValue(':' . $key, $value);    //Подставляем значения
        }
        try {
            $sth->execute();
        } catch(PDOException $e) {
            return $e->getCode();
        }
        return $this->db->lastInsertId();
    }

    public function update($table, $fields, $conditions)
    {
        if (!$this->db instanceof PDO) return false;
        $sql_fields = '';
        foreach ($fields as $field=>$value)
            $sql_fields .= '`'.$field.'`=:'.$field.',';
        $sql_fields = substr($sql_fields, 0, -1); //Удаляем последнюю запятую
        $sql = 'UPDATE '.$table.' SET ' . $sql_fields . ' WHERE ' . $conditions;
        $sth = $this->db->prepare($sql);
        foreach ($fields as $field=>$value)
            $sth->bindValue(':' . $field, $value);
        try {
            $sth->execute();
        } catch(PDOException $e) {
            return $e->getCode();
        }
        return true;
    }

    public function delete($table, $conditions)
    {
        if (!$this->db instanceof PDO) return false;
        $sql = 'DELETE FROM `'.$table.'` WHERE ' . $conditions;
        $sth = $this->db->prepare($sql);
        try {
            $sth->execute();
        } catch(PDOException $e) {
            return $e->getCode();
        }
        return true;
    }

    private function implode_with_quotes($array, $quote="'"){
        if (!is_array($array)) return null;
        $str = "";
        foreach ($array as $item)
            $str .= $quote . $item . $quote . ',';
        $str = substr($str, 0, -1); //Удаляем последнюю запятую
        return $str;
    }

    private function make_values($array){
        if (!is_array($array)) return null;
        $str = "";
        foreach ($array as $item)
            $str .= ':' . $item . ',';
        $str = substr($str, 0, -1); //Удаляем последнюю запятую
        return $str;
    }
}