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
<h1>Posztok hasonlítása</h1>
<style>
table.valtoztato-tabla > tbody > tr > th,
table.valtoztato-tabla > tbody > tr > td{
    padding: 3px;
    border-style: solid;
    border-width: 3px;
    border-color: gray;
}
</style>
<table class="valtoztato-tabla">
    <tr><th colspan="2">Alap POST</th><th colspan="2">Változtatni való post</th></tr>
<?php 
foreach($postMiket as $key => $postMit){
?>
<tr>
    <td><?php echo $postMit->ID; ?></td>
<?php
    echo '<td>'.$postMit->post_name.'</td>';
    if ($postMikel[$key]){
        echo '<td>'.BaseHtml::dropDownList('valtozo_'.$postMit->ID, null, ArrayHelper::map($postMikel[$key], 'ID', 'post_name')).'</td>';
        echo '<td>'.BaseHtml::button ( '<= változtat', $options = ['id' => $postMit->ID, 'class' => 'valtoztato-button'] ).'</td>';
    } else {
    ?>
        <td colspan="2">NINCS AJÁNLOTT POST!</td>
    <?php
    } 
    ?>

</tr>
<?php
}
?>
</table>
<script>
jQuery(".valtoztato-button").on('click', function(){
    var alapID = $(this).attr('id');
    var valtoztatDD = "";
    $("select").each(function(){
        if($(this).attr("name")=='valtozo_'+alapID){
            valtozoDD = $(this).val();
        }
    });
    open('<?php echo BaseUrl::home()."posts/replace?id_1=" ?>');
})
</script>
<?php ?>
