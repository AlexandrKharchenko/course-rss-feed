<? foreach ($lenta as $item) : ?>

    <div class="col-md-3 item-grid">
        <div class="panel panel-default">
            <div class="panel-heading"><?=$item['title']?></div>
            <div class="panel-body">
                <? if($item['img_link'] != '') : ?>
                    <img class="rss-image" src="<?=$item['img_link']?>">
                <? endif; ?>
                <p>
                    <?=$item['description']?>
                </p>
            </div>
            <div class="panel-footer">
                <a href="<?=$item['link']?>" class="btn btn-primary btn-md btn-block" target="_blank">Подробнее</a>
            </div>
        </div>
    </div>

<? endforeach; ?>