<?php
require_once('functions/view_helper.php');

class C_Registration extends C_Base
{
    protected function before()
{
    parent::before();
    $this->menuActive = 'reg';
}

    public function action_index()
    {
        $this->title_page = 'Регистрация';          // Заголовок страницы
        $this->title .= '::' . $this->title_page;   // Заголовок сайта

        $mUsers = M_Users::getInstance();


        // Очистка старых сессий.
        $mUsers->ClearSessions();

        if(!empty($_SESSION['sid'])) {
            $this->redirect('index.php');
        }


        // Обработка отправки формы.
        if($this->isPost()) {
            if(!empty($_POST) && isset($_POST['login']) && isset($_POST['password']) && isset($_POST['name'])) {

            }

        }
        if (!empty($_POST))
        {
            if ($_POST['login'] != '' && $_POST['password'] != '' && $_POST['name'] != ''){
                $mUsers->registration($_POST['login'], $_POST['password'], $_POST['name']);
                header('Location: index.php');
                die();
            }
        }

/*        if($this->isPost())
        {
            if($mUsers->login($_POST['login'], $_POST['password'], isset($_POST['remember'])))
                $this->redirect('index.php');
        }*/
        $this->content = $this->template('view/templates/v_registration.php');
    }
}