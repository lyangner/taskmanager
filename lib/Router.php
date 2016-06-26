<?php

class Router    //Класс для разбора URL
{
    protected $_db;

    protected $_view;

    protected $_entityManager;

    protected $_homeUrl;

    protected $_homePath;

    protected $_request;

    protected $_error;

    public function __construct($db, $view, $homeUrl, $homePath, $entityManager, $request=null, $postData=null)
    {
        $this->_db = $db;
        $this->_view = $view;
        $this->_homeUrl = $homeUrl;
        $this->_homePath = $homePath;
        $this->_request['get'] = $request;
        $this->_request['post'] = $postData;
        $this->_entityManager = $entityManager;
    }

    public function execute()
    {
        $controller = $this->getControllerFromUrl();    //Находим в URL имя контроллера
        if ($controller) {
            $controller = ucfirst($controller) . 'Controller';  //Формируем имя класса контроллера вида NameController
            $controllerFile = $this->_homePath . 'controllers/' . $controller . '.php';
            if (file_exists($controllerFile)) { //Проверяем есть ли файл такого класса
                require_once $controllerFile;   //Если есть, то включаем его
                $controller = new $controller(  //Создаем экземпляр этого класса
                    $this->_db,
                    $this->_view,
                    $this->_homeUrl,
                    $this->_entityManager,
                    $this->_request['get'],
                    $this->_request['post']
                );

                return $controller->execute();  //Передаем ему управлени
            }
        }

        return $this->notFoundAction();
    }

    protected function getControllerFromUrl()
    {
        return !empty($this->_request['get']['controller']) ? filter_var($this->_request['get']['controller'], FILTER_SANITIZE_STRING) : 'index';
    }
}