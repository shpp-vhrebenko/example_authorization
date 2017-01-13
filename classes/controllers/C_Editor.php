<?php
require_once('functions/view_helper.php');

class C_Editor extends C_Base
{
    protected function before()
    {
        parent::before();
        $this->menuActive = 'editor';
    }

    function __construct()
    {
        parent::__construct();

        // Может ли пользователь редактировать статьи?
        if (!$this->mUsers->Can('EDITOR_ARTICLES'))
        {
            $this->redirect('index.php', 1, 'Отказано в доступе.');
        }
    }


    // Консоль редактора
    public function action_index()
    {

        $this->title_page = 'Консоль редактора';    // Заголовок страницы
        $this->title .= '::' . $this->title_page;   // Заголовок сайта


        // Проверка существования ГЕТ запроса
        if(isset($_GET['delete'])) {
            // Удаление статьи
            if($this->mArticles->delete($_GET['delete']) > 0) {

                // Запись сообщения об успешном удалении и редирекет
                $_SESSION['notice'] = 'Статья успешно удаленна';
                $this->redirect('index.php?c=editor&act=index');
            } else {
                $_SESSION['notice'] = 'Ошибка';     // Запись сообщения в случаи ошибки
            }
        }

        // Выборка всех статей в виде списка
        $articles = $this->mArticles->getList();

        // Шаблон консоли редактора
        $this->content = $this->template('view/templates/v_editor.php', ['articles' => $articles]);
    }

    // Страница создания новой статьи
    public function action_new()
    {
        $this->title_page = 'Новая статья';         // Заголовок страницы
        $this->title .= '::' . $this->title_page;   // Заголовок сайта

        if($this->isPost()) {
            // Проверка отправки формы
            if(!empty($_POST) && isset($_POST['title']) && isset($_POST['content'])) {

                // Проверка введенных данных
                if($this->mArticles->checkArticle($_POST['title'], $_POST['content'])) {

                    // Добавление данных в БД
                    $this->mArticles->add($_POST['title'], $_POST['content']);

                    // Запись в сессию сообщеня об успешной загрузке
                    $_SESSION['notice'] = 'Статья успешно загружена';
                    $this->redirect('index.php?c=editor&act=index');
                } else {

                    // Если данные не прошли проверку, сохраняем их для повторного вывода в форму
                    $_SESSION['title'] = $_POST['title'];
                    $_SESSION['content'] = $_POST['content'];
                    $this->redirect('index.php?c=editor&act=new');
                }
            }
        }

        // Шаблон добавления новой статьи
        $this->content = $this->template('view/templates/v_new.php');
    }

    // Страница редактирования статьи
    public function action_edit()
    {
        // Редирект, если id не передан
        if(empty($_GET['id'])) {
            $this->redirect('index.php?c=editor&act=index');
        }
        // Выборка одной статьки, по id
        $article = $this->mArticles->getOne($_GET['id']);
        $id = $_GET['id'];

        // Проверка отправки формы
        if(!empty($_POST) && isset($_POST['title']) && isset($_POST['content'])) {

            // Сохрание введенных данных в переменную
            $title_new = $_POST['title'];
            $content_new = $_POST['content'];

            // Проверка введенных данных
            if($this->mArticles->checkArticle($title_new, $content_new)) {

                // Обновление введенных данных в БД
                $this->mArticles->update($id, $title_new, $content_new);

                // Запись в сессию сообщеня об успешном редактировании
                $_SESSION['notice'] = 'Статья успешно отредактирована';
                $this->redirect('index.php?c=editor&act=index');
            } else {
                $this->redirect("index.php?c=editor&act=edit&id=$id");
            }
        }

        $this->title_page = 'Редактирование статьи';    // Заголовок страницы
        $this->title .= '::' . $this->title_page;       // Заголовок сайта

        // Шаблон редактирования статьи
        $this->content = $this->template('view/templates/v_edit.php', ['article' => $article]);
    }
}