<?php
require_once('functions/view_helper.php');

class C_AdminPanel extends C_Base
{
    protected function before()
    {
        parent::before();
        $this->menuActive = 'admin';
    }

    function __construct()
    {
        parent::__construct();

        // Может ли пользователь заходить в админ панель ?
        if (!$this->mUsers->Can('ADMIN_PANEL')) {
            $this->redirect('index.php', 1, 'Отказанно в доступе.');
        }
    }

    //TODO доделать админ панель
    public function action_index()
    {
        $this->title_page = 'Админ панель';         // Заголовок страницы
        $this->title .= '::' . $this->title_page;   // Заголовок сайта

        // Список с юзерами и их ролями
        $usersList = $this->mUsers->usersList();

        if($this->isPost()) {
            $arr = [];
            $i = 0;

            $usersRole = $this->mUsers->usersRole();

            foreach ($_POST as $key => $value) {

                if($value != $usersRole[$i++]['id_role']) {
                    $arr[$key] = $value;
                }
            }
            $this->mUsers->usersRoleUpdate($arr);
            $this->redirect($_SERVER['REQUEST_URI']);
        }

        $this->content = $this->template('view/templates/v_admin_panel.php', ['usersList' => $usersList]);
    }

}