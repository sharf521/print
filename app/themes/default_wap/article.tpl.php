<?php require 'header.php'; ?>
<div class="page-content">
    <div class="rich_media_title"><?= $article->title ?></div>
    <div class="rich_media_meta_list">
        <span class="date"><?= date('Y-m-d') ?></span>
        <span class="nickname"><?= app('\App\Model\System')->getCode('webname'); ?></span>
    </div>
    <div class="content_txt">
        <?= $article->content ?>
    </div>
</div>
<?php require 'footer.php'; ?>