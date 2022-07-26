<?php
/* @var $this yii\web\View */
use yii\helpers\BaseHtml;
echo BaseHtml::beginForm ( '/web/posts/search', 'post');
?>
<h1>Posztok hasonlítása</h1>

<table>
<tr>
    <th>Alap, ami nem változik</th><th>=></th><th>Változik, ha kell</th>
</tr>
<tr>
    <td>
        <?php echo BaseHtml::dropDownList('mit', null, $models); ?>
       
    </td>
    <td></td>
    <td><?php echo BaseHtml::dropDownList('mivel', null, $models); ?></td>
    
</tr>
<tr>
    <td colspan="3"><?php echo BaseHtml::submitButton('Hasonlítás') ?></td>
</tr>
</table>
<?php echo BaseHtml::endForm(); ?>