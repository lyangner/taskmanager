<?php

require_once 'Controller.php';

class IndexController extends Controller
{
    public function execute()
    {
        $action = $this->getActionFromUrl();

        switch ($action) {
            default:
                return $this->indexAction();
        }
    }

    public function indexAction()
    {
        return $this->render('index.twig',[
            'users' => $this->_entityManager->entity('User')->findAll(),
            'tasks' => $this->_entityManager->entity('Task')->findAll(),
        ]);
    }
}