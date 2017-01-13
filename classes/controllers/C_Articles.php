<?php
require_once('functions/view_helper.php');

class C_Articles extends C_Base
{
    protected function before()
    {
        parent::before();
        $this->menuActive = 'article';
    }

	// Главная страница
	public function action_index()
	{
        $this->title_page = 'Главная';              // Заголовок страницы
        $this->title .= '::' . $this->title_page;   // Заголовок сайта

        // Значение по умолчанию для кол-ва статей на странице
        if($_SESSION['num'] === null) {
            $_SESSION['num'] = 5;
        }

        if(isset($_GET['num'])) {
            $valid_a = [3, 5, 10];                    // Допустимые значения при выборе сортировки статей
            if($this->validateParam($_GET['num'], $valid_a)) {
                $_SESSION['num'] = $_GET['num'];
            }
            $this->redirect($_SERVER['PHP_SELF']);
        }


        // Очистка старых сессий.
        $this->mUsers->ClearSessions();

		$count = $this->mArticles->count();	                // Подсчет кол-ва статей в БД

		$n = $count / $_SESSION['num'];

		// Проверка ГЕТ запроса, содержащего номер страницы
        if(isset($_GET['page'])) {
            $valid_a = range(1, ceil($n));
            if(!$this->validateParam($_GET['page'], $valid_a)) {
                $this->redirect($_SERVER['PHP_SELF']);
            }
        }

        // Список онлайн пользователей
        $usersOnline = $this->mUsers->isOnline();

        // Выборка статей в виде превью
        $articles = $this->mArticles->getIntro(40, $_GET['page'], $_SESSION['num']);

        // Шаблон с выбором кол-ва статей на одной странице
        $sort = $this->template('view/templates/block/v_block_sort.php');

        // Шаблон постраничной навигации
        $nav = $this->template('view/templates/block/v_block_nav.php', ['n' => $n]);

        // Массив с данными передаваемыми в шаблон
        $array = ['articles' => $articles, 'nav' => $nav, 'sort' => $sort, 'usersOnline' => $usersOnline];

        // Шаблон главной страницы
		$this->content = $this->template('view/templates/v_index.php', $array);
	}

	// Страница просмотра одной статьи
	public function action_article()
	{
        // Если попали на страницу GET запросом
        if($this->isGet()) {
            $article = $this->mArticles->getOne($_GET['id']);         // Выборка одной статьи
            $comments = $this->mArticles->getComments($_GET['id']);   // Выборка комментариев
        }

        // Если попали на страницу POST запросом
        if($this->isPost()) {
            if(isset($_SESSION['name']) && isset($_POST['comment']) && isset($_GET['id'])) {

                // Проверка введенных данных
                if($this->mArticles->checkComment($_POST['comment'])) {

                    // Добавление данных в БД
                    $this->mArticles->addComment($_GET['id'], $_SESSION['name'], $_POST['comment']);

                    // Запись в сессию сообщеня об успешной загрузке
                    $_SESSION['notice'] = 'Комментарий успешно добавлен';
                    $this->redirect($_SERVER['REQUEST_URI']);
                } else {
                    // Если данные не прошли проверку, сохраняем их для повторного вывода в форму
                    $_SESSION['comment'] = $_POST['comment'];
                    $this->redirect($_SERVER['REQUEST_URI']);
                }
            }
        }

        $this->title_page = $article['title'];      // Заголовок страницы
        $this->title .= '::' . $this->title_page;   // Заголовок сайта



        $comment_form = $this->template('view/templates/block/v_block_comment_form.php');

        // Может ли пользователь оставлять комментарии ?
        if (!$this->mUsers->Can('ADD_COMMENTS')) {
            $comment_form = '';
        }

        $array = ['article' => $article, 'comments' => $comments, 'comment_form' => $comment_form];

        // Шаблон одной статьи
		$this->content = $this->template('view/templates/v_article.php', $array);
	}
}