<?php
/* @var $this yii\web\View */
// \yidas\yii\jquery\JqueryAsset::register($this);
use yii\helpers\ArrayHelper;
use yii\helpers\BaseHtml;
use yii\helpers\BaseUrl;
use yii\web\View;

$this->registerJsFile(
    "https://code.jquery.com/jquery-2.2.4.min.js",
    [
        'position'=>View::POS_HEAD,
    ]
);
?>
<h1>Posztok hasonlítása és módosítása</h1>
