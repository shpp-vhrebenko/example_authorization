<?php/*
Шаблон создания новой статьи
============================
$notice - уведомление
title - заголовок
content - текст
*/?>
<section>
    <b class="red"><?php echo vHelper_flashMessage('notice'); ?></b>
    <form method="post" enctype="multipart/form-data" autocomplete="off">
        <label>
            Заголовок:
            <br>
            <input type="text" name="title" value="<?php echo htmlspecialchars($article['title']); ?>">
        </labeL>
        <br>
        <br>
        <label>
            Содержание:
            <br>
            <textarea name="content"><?php echo htmlspecialchars($article['content']);?></textarea>
        </label>
        <br>
        <input type="submit" value="Сохранить">
    </form>
</section>