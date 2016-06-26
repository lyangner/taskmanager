<?php


abstract class Controller
{
    protected $_db;

    protected $_view;

    protected $_entityManager;

    protected $_homeUrl;

    protected $_request;

    protected $_error;

    public function __construct($db, $view, $homeUrl, $entityManager, $request=null, $postData=null)
    {
        $this->_db = $db;
        $this->_view = $view;
        $this->_homeUrl = $homeUrl;
        $this->_request['get'] = $request;
        $this->_request['post'] = $postData;
        $this->_entityManager = $entityManager;
    }

    abstract public function execute();

    protected function addError($error)
    {
        $this->_error = $error;
    }

    protected function render($page, $params=[])
    {
        //Перед рендерингом, объединяем переданные шаблону параметры с основными
        $params = array_merge($params, [
            'error' => $this->_error,
            'homeUrl' => $this->_homeUrl,
        ]);
        return $this->_view->render($page, $params);
    }

    protected function getEntityId()
    {
        return !empty($this->_request['get']['entity']) ?
            filter_var($this->_request['get']['entity'], FILTER_SANITIZE_NUMBER_INT) : null;
    }

    protected function getActionFromUrl()
    {
        return !empty($this->_request['get']['action']) ?
            filter_var($this->_request['get']['action'], FILTER_SANITIZE_STRING) : null;
    }

    public function notFoundAction($header=null, $message=null)
    {
        header("HTTP/1.0 404 Not Found");
        return $this->render('404.twig', [
            'errorHeader' => $header,
            'errorMessage' => $message
        ]);
    }

    public function redirect($url)
    {
        header('Location: ' . $url);
    }
}