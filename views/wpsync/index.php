<?php
/* @var $this yii\web\View */
?>
<h1>wpsync/index</h1>

<p>
    You may change the content of this page by modifying
    the file <code>"<?= $pr; ?>"</code>.
    ret: <?= isset($ret)?$ret:-1?>
    <br>
    <?php if (isset($error)): ?>
        <p>Hiba csúszott a számításba <?= $error ?></p>
    <?php endif; ?>
    <br>
    <a href="/web/sync/country?id=<?=$prId ?>">CountrySync</a> 
    <br>
    <a href="/web/sync/equipmentcategory?id=<?=$prId ?>">EquipmentCategorySync</a> 
    <br>
    <a href="/web/sync/equipment?id=<?=$prId ?>">EquipmentSync</a> 
    <br>
    <a href="/web/sync/yachtbuilder?id=<?=$prId ?>">YachtBuilderSync</a> 
    <br>
    <a href="/web/sync/enginebuilder?id=<?=$prId ?>">EngineBuilderSync</a> 
    <br>
    <a href="/web/sync/yachtcategory?id=<?=$prId ?>">YachtCategorySync</a><br>
    <a href="/web/sync/yachtmodel?id=<?=$prId ?>">YachtModelSync</a> <br>
    <a href="/web/sync/discountitemsync?id=<?=$prId ?>">DiscountItemSync</a><br> 
    <a href="/web/sync/seasonsync?id=<?=$prId ?>">SeasonSync</a><br> 
    <a href="/web/sync/yacht?id=<?=$prId ?>">YachtSync</a><br>
     

    <a href="/web/sync/region?id=<?=$prId ?>">RegionSync</a><br>
    <a href="/web/sync/base?id=<?=$prId ?>">BaseSync</a><br><a href="/web/sync/port?id=<?=$prId ?>">PortSync</a><br>
    <a href="/web/sync/steeringtype?id=<?=$prId ?>">SteeringTypeSync</a>
    <br>
    <a href="/web/sync/service?id=<?=$prId ?>">ServiceSync</a><br>
    <a href="/web/sync/company?id=<?=$prId ?>">CompanySync</a><br> <a href="/web/sync/yacht?id=<?=$prId ?>">YachtSync</a>  <br>
    <a href="/web/wpsync?id=<?=$prId ?>">Teljes WPSync</a><br> 
    <a href="/web/wpsync/newposts?id=<?=$prId ?>">WPSync</a><br> 

</p>
