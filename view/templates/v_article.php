<?php/*
Шаблон статьи
============================
$articles - статья
content - текст
date_time - дата загрузки статьи
*/?>
<section>
    <p><?php echo nl2br($article['content']); ?></p>
    <small>Дата добавления: <?php echo $article['date_time']; ?></small>
</section>

<?php if(!empty($comments)):?>
<section>
    <?php foreach($comments as $comment): ?>
        <p>
            <b><?php echo $comment['name']; ?>:</b>
            <span class="comment"><?php echo $comment['comment']; ?></span>
        </p>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<?php echo $comment_form; ?>
