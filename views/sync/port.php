<?php
/* @var $this yii\web\View */
?>
<h1>sync/port</h1>

<p>
Prefix: <code>"<?= $pr; ?>"</code>.
<br>
Return: <code>"<?= $return; ?>"</code>.
<br>
<?= $this->render('syncrons', ['prId' => $prId] ) ?>
</p>