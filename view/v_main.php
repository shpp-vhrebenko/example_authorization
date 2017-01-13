<?php/*
Основной шаблон
============================
$title - заголовок
$content - содержание
*/?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>

    <header>
        <nav>
            <ul>
                <li>
                    <a <?php vHelper_print_if_true('article', $menuActive, 'red'); ?> href="index.php">Главная</a>
                </li>

                <li>
                    <a <?php vHelper_print_if_true('editor', $menuActive, 'red'); ?> href="index.php?c=editor">Консоль редактора</a>
                </li>

                <li>
                    <a <?php vHelper_print_if_true('admin', $menuActive, 'red'); ?> href="index.php?c=admin">Админ панель</a>
                </li>

                <?php if(empty($_SESSION['sid'])): ?>
                <li>
                    <a <?php vHelper_print_if_true('reg', $menuActive, 'red'); ?> href="index.php?c=reg">Регистрация</a>
                </li>
                <?php endif; ?>


            </ul>
        </nav>
        <span class="right">
            <?php if(isset($_SESSION['sid'])): ?>
                Привет, <?php echo $_SESSION['name']; ?>! <a <?php vHelper_print_if_true('auth', $menuActive, 'red'); ?> href="index.php?c=auth">Выход</a>
            <?php endif; ?>
            <?php if(!isset($_SESSION['sid'])): ?>
                <a <?php vHelper_print_if_true('auth', $menuActive, 'red'); ?> href="index.php?c=auth">Вход</a>
            <?php endif; ?>
        </span>
    </header>

    <main>
        <h1><?php echo $title_page; ?></h1>
        <?php echo $content; ?>
    </main>

    <footer>
        <small>Все права защищены. Адрес. Телефон.</small>
    </footer>

</body>
</html>