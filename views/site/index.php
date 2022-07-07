<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?php foreach ($allTablePrefix as $tablePrefix): ?>
                <p><a href="/web/sync?id=<?=$tablePrefix->id ?>"><?=$tablePrefix->url ?></a></p>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    
    </div>
</div>
