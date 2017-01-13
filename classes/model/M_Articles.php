<?php

class M_Articles
{
    private static $instance; 	// ссылка на экземпляр класса
    private $mysql; 			// драйвер БД

    // Получение единственного экземпляра (одиночка)
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->mysql = M_MYSQL::getInstance();
    }

    // Выборка всех статей (у меня нигде не используется)
    public function getAll()
    {
        $query = "SELECT * FROM `articles` ORDER BY `id_article` DESC";
        return $this->mysql->select($query);
    }

    // Выборка всех статей в виде превью
    public function getIntro($sub, $page, $app)
    {
        $sub = (int)$sub;       // кол-во символов которое требуется вернуть
        $page = (int)$page;     // номер страницы

        // Постраничная навигация
        $page = !empty($page) ? $page : 1;
        $skip = ($page-1) * $app;

        $query = "SELECT `id_article`, `title`, `date_time`, SUBSTRING(`content`, 1, '$sub') AS `content` FROM `articles` ORDER BY `id_article` DESC LIMIT $skip, $app";
        return $this->mysql->select($query);
    }

    // возвращает кол-во статей в БД
    function count()
    {
        $query = "SELECT COUNT(*) AS `count` FROM `articles`";
        return $this->mysql->select($query)['0']['count'];
    }

    // выборка одной статьи по id
    public function getOne($id)
    {
        $id = (int)$id;
        $query = "SELECT * FROM `articles` WHERE `id_article` = '" . $id . "'";
        return $this->mysql->selectOne($query);
    }

    public function getComments($id)
    {
        $id = (int)$id;
        $query = "SELECT * FROM `comments` WHERE `id_article` = '" . $id . "'";
        return $this->mysql->select($query);
    }

    public function addComment($id, $name, $comment)
    {
        $id = (int)$id;
        $object = ['id_article' => $id, 'name' => $name, 'comment' => $comment];
        return $this->mysql->insert('comments', $object);
    }

    // Выборка всех статей, для списка (id и title)
    public function getList()
    {
        $query = "SELECT `id_article`, `title` FROM `articles` ORDER BY `id_article` DESC";
        return $this->mysql->select($query);
    }

    // Добавление статьи
    public function add($title, $content)
    {
        $object = ['title' => $title, 'content' => $content];
        return $this->mysql->insert('articles', $object);
    }

    // Редактирование статьи
    public function update($id, $title, $content)
    {
        $id = (int)$id;
        $object = ['title' => $title, 'content' => $content];
        $where = "`id_article` = '$id'";
        return $this->mysql->update('articles', $object, $where);
    }

    // Удаление статьи по ее id и комментариев
    public function delete($id)
    {
        $id = (int)$id;
        $where = "`id_article` = '$id'";
        $this->mysql->delete('comments', $where);
        return $this->mysql->delete('articles', $where);
    }

    public function checkComment($comment)
    {
        // Проверка на пустую строку текста
        if(mb_strlen($comment) == '') {
            $_SESSION['notice'] = 'Вы не ввели текст комментария';
            return false;
        }
        return true;
    }

    public function checkArticle($title, $content)
    {
        $title = trim($title);
        $content = trim($content);


        // Проверка на пустую строку заголовка
        if(mb_strlen($title) == '') {
            $_SESSION['notice'] = 'Вы не ввели заголовок';
            return false;
        }

        // Проверка на пустую строку текста
        if(mb_strlen($content) == '') {
            $_SESSION['notice'] = 'Вы не ввели текст статьи';
            return false;
        }

        // Проверка на длинну заголовка
        if(mb_strlen($title) > 100) {
            $_SESSION['notice'] = 'Заголовок не должен превышать 100 символов';
            return false;
        }
        return true;
    }
}