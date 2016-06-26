<?php

require_once 'Controller.php';

class TaskController extends Controller
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
        return $this->render('tasks/index.twig',[
            'tasks' => $this->_entityManager->entity('Task')->findAll(),
        ]);
    }

    public function newAction()
    {
        $users = $this->_entityManager->entity('User')->findAll();  //Выбираем всех пользователей для выпадающего саписка исполнителей
        $statuses = $this->_entityManager->entity('Status')->findAll(); //Выбираем все статусы

        return $this->render('tasks/new.twig', [
            'users' => $users,
            'statuses' => $statuses,
        ]);
    }

    public function createAction()
    {
        $data = $this->_request['post'];    //Получаем сырые данные из POST запроса
        if (!empty($data['name'])) {    //Если нет названия задачи, то выводим ошибку
            $task = new Task($this->_db);   //Создаем экземляр класса Задачи и передаем ему экземляр БД
            $task->setName(filter_var($data['name'], FILTER_SANITIZE_STRING));  //Фильтруем введенные данные как строку
            $task->setEstimatedTime(filter_var($data['estimatedTime'], FILTER_SANITIZE_NUMBER_INT));    //Как число
            //Пытаемся создать объект Дата и преобразовать его к формату YYYY-MM-DD для сохранения в БД
            $task->setStartDate($data['startDate'] ? DateTime::createFromFormat('d.m.Y', $data['startDate'])->format("Y-m-d") : null);
            $task->setEndDate($data['endDate'] ? DateTime::createFromFormat('d.m.Y', $data['endDate'])->format("Y-m-d") : null);
            $task->setUserId(filter_var($data['userId'], FILTER_SANITIZE_NUMBER_INT));  //Как число
            $task->setStatusId(filter_var($data['statusId'], FILTER_SANITIZE_NUMBER_INT));  //Как число
            $this->_entityManager->persist($task);  //Сохраняем созданную модель в БД

            $this->redirect($this->_homeUrl . '?controller=task');  //Переходим к списку созданных задач
        }

        $this->addError('Название задачи не может быть пустым');
        return $this->render('tasks/new.twig'); //Выводим страницу с созданием задачи и ошибкой
    }

    public function editAction($id)
    {
        $task = $this->_entityManager->entity('Task')->find($id);   //Находим в БД задачу по запрошенному ID
        if (empty($task))
            return $this->notFoundAction('Задача не найдена', 'Задача с указанным ID не существует'); //Если не найден, выводим страницу 404
        $users = $this->_entityManager->entity('User')->findAll();  //Выбираем всех пользователей для выпадающего саписка исполнителей
        $statuses = $this->_entityManager->entity('Status')->findAll(); //Выбираем все статусы

        return $this->render('tasks/edit.twig', [   //Рендерим шаблон и передаем ему нужные данные
            'task' => $task,
            'users' => $users,
            'statuses' => $statuses,
        ]);
    }

    public function deleteAction($id)
    {
        $this->_entityManager->entity('Task')->delete($id); //Пытаемся удалить задачу по выбранному ID
                                                            //если такую задачу не найдет, значит ничего не удалит
        $this->redirect($this->_homeUrl . '?controller=task');
    }

    public function updateAction($id)
    {
        $data = $this->_request['post'];
        $task = $this->_entityManager->entity('Task')->find($id);   //Ищем в БД существующую задачу
        if (empty($task))
            return $this->notFoundAction('Задача не найдена', 'Задача с указанным ID не существует');
        $task->setName(filter_var($data['name'], FILTER_SANITIZE_STRING));
        $task->setEstimatedTime(filter_var($data['estimatedTime'], FILTER_SANITIZE_NUMBER_INT));
        $task->setStartDate($data['startDate'] ? DateTime::createFromFormat('d.m.Y', $data['startDate'])->format("Y-m-d") : null);
        $task->setEndDate($data['endDate'] ? DateTime::createFromFormat('d.m.Y', $data['endDate'])->format("Y-m-d") : null);
        $task->setUserId(filter_var($data['userId'], FILTER_SANITIZE_NUMBER_INT));
        $task->setStatusId(filter_var($data['statusId'], FILTER_SANITIZE_NUMBER_INT));
        $this->_entityManager->persist($task);

        $this->redirect($this->_homeUrl . '?controller=task');
    }
}