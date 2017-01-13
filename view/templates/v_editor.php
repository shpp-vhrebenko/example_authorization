<?php/*
Шаблон страницы редактора
============================
$articles - статьи в виде списка
id_article - идентифицатор
title - заголовок
$notice - уведомление
*/?>
<section>
    <b><a href="index.php?c=editor&act=new">Новая статья</a></b>
    <ul>
    <?php foreach ($articles as $article): ?>
        <li>
            <a href="index.php?c=editor&act=edit&id=<?php echo $article['id_article']; ?>">
                <?php echo $article['title']; ?>
            </a>
            <br>
            <a class="red" href="index.php?c=editor&act=index&delete=<?php echo $article['id_article']; ?>">удалить</a>
        </li>
    <?php endforeach; ?>
    </ul>
    <b class="green"><?php echo vHelper_flashMessage('notice'); ?></b>
</section>