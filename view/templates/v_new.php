<?php/*
Шаблон создания новой статьи
============================
$notice - уведомление
$title_form - сохраняет введенный заголовок при неудачной отправке
$content_form - сохраняет введенный текст при неудачной отправке
*/?>
<section>
    <b class="red"><?php echo vHelper_flashMessage('notice'); ?></b>
    <form method="post" enctype="multipart/form-data" autocomplete="off">
        <label>
            Заголовок:
            <br>
            <input type="text" name="title" value="<?php echo htmlspecialchars(vHelper_flashMessage('title')); ?>">
        </label>
        <br>
        <br>
        <label>
            Содержание:
            <br>
            <textarea name="content"><?php echo htmlspecialchars(vHelper_flashMessage('content')); ?></textarea>
        </label>
        <br>
        <input type="submit" value="Добавить">
    </form>
</section>