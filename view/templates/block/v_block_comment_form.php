<?php
/**
 * Created by PhpStorm.
 * User: Irreligious
 * Date: 09.12.2015
 * Time: 23:31
 */
?>

<section>
    <b class="green"><?php echo vHelper_flashMessage('notice'); ?></b>
    <form method="post" autocomplete="off">
        <label>
            Сообщение:
            <br>
            <textarea name="comment"><?php echo htmlspecialchars(vHelper_flashMessage('comment')); ?></textarea>
        </label>
        <br>
        <input type="submit" value="Добавить">
    </form>
</section>
