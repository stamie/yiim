<?php
/* @var $this yii\web\View */
?>
<h1>wpsync/hreflang</h1>
<form method="GET" action="<?php echo $url; ?>">
    <?php if (is_array($tablePrefixes) && count($tablePrefixes) > 0) : ?>
        <select name="id">
            <?php foreach ($tablePrefixes as $tablePrefix) : ?>
                <option value="<?php echo $tablePrefix->id ?>"><?php echo $tablePrefix->url; ?></option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>
    <?php if (is_array($languages) && count($languages) > 0) : ?>
        <select name="lang">
            <option value="0">Válasz ki egy értéket!!!</option>
            <?php foreach ($languages as $key => $language) : ?>
                <?php if ($key < count($languages)) : ?>
                    <option value="<?php echo $key; ?>"><?php echo $language['name']; ?> - <?php echo $languages[($key + 1)]['name'] ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>
    <label for="search_url">Keresendő Url<input type="text" id="search_url" name="search_url" /></label>
    <label for="replace_url">Erre az Url-re kell cserélni az Url-t<input type="text" id="replace_url" name="replace_url" /></label>
    <button type="submit">Csere indítása</button>
</form>