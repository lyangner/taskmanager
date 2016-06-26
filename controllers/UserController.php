<?php

require_once 'Controller.php';

class UserController extends Controller
{
    public function execute()
    {
        $action = $this->getActionFromUrl();
        $id = $this->getEntityId();

        switch ($action) {
            case 'list':
                return $this->indexAction();
            case 'new':
                return $this->newAction();
            case 'create':
                return $this->createAction();
            case 'edit':
                return $id ? $this->editAction($id) : $this->notFoundAction();
            case 'update':
                return $id ? $this->updateAction($id) : $this->notFoundAction();
            case 'delete':
                return $id ? $this->deleteAction($id) : $this->notFoundAction();

            default:
                return $this->indexAction();
        }
    }

    public function indexAction()
    {
        return $this->render('users/index.twig',[   //Рендерим шаблон и передаем ему список всех пользователей
            'users' => $this->_entityManager->entity('User')->findAll(),
        ]);
    }

    public function newAction()
    {
        return $this->render('users/new.twig', [
            'error' => $this->_error,
        ]);
    }

    public function createAction()
    {
        $data = $this->_request['post'];
        if (!empty($data['name']) && !empty($data['secondName'])) { //Если не заданы имя и фамилия, показываем страницу создания пользователея с ошибкой
            $user = new User($this->_db);
            $user->setName(filter_var($data['name'], FILTER_SANITIZE_STRING));
            $user->setMiddleName(filter_var($data['middleName'], FILTER_SANITIZE_STRING));
            $user->setSecondName(filter_var($data['secondName'], FILTER_SANITIZE_STRING));
            $this->_entityManager->persist($user);

            $this->redirect($this->_homeUrl . '?controller=user');
        }

        $this->addError('Имя и фамилия не могут быть пустыми');
        return $this->render('users/new.twig');
    }

    public function editAction($id)
    {
        $user = $this->_entityManager->entity('User')->find($id);

        if ($user) {
            return $this->render('users/edit.twig', [
                'user' => $user,
            ]);
        }

        return $this->notFoundAction();
    }

    public function deleteAction($id)
    {
        $this->_entityManager->entity('User')->delete($id);

        $this->redirect($this->_homeUrl . '?controller=user');
    }

    public function updateAction($id)
    {
        $data = $this->_request['post'];
        $user = $this->_entityManager->entity('User')->find($id);
        if (empty($user))
            return $this->notFoundAction();
        $user->setName(filter_var($data['name'], FILTER_SANITIZE_STRING));
        $user->setMiddleName(filter_var($data['middleName'], FILTER_SANITIZE_STRING));
        $user->setSecondName(filter_var($data['secondName'], FILTER_SANITIZE_STRING));
        $this->_entityManager->persist($user);

        $this->redirect($this->_homeUrl . '?controller=user');
    }
}