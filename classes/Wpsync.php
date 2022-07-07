<?php

/**
 * 
 * Szinkronizációs kontroll
 * 
 * Készítette: Stampel Emese Ágota
 * Év: 2021
 * 
 */

namespace app\classes;

use yii\console\Controller;
use yii\console\ExitCode;
use Yii;
use yii\helpers\Url;
use app\models\Posts;
use app\models\TablePrefix;
use app\models\WpYacht as ModelsWpYacht;
use app\models\Xml;
use app\models\Yacht;
use app\models\WpYacht;


class Wpsync
{
    const DRAFT = 'draft';
    const ELRENDEZES = 'boat-template';
    public static function savePosts($id)
    {
        $date = date('Y-m-d H:i:s');

        self::deleteOldYachtPosts($id);
        $array = [0];

        $yachtForXmls = Yacht::find()->andWhere(['is_active' => 1])->all();
        //Posts::tableName(1);
        $boatDraft = Posts::findOne(['post_title' => self::DRAFT, 'post_type' => self::ELRENDEZES]);
        foreach ($yachtForXmls as $yachtForXml) {
            $array[] = $yachtForXml->id;
            // var_dump($yachtForXml->id);
            $newBoat = new Posts();
            //$newBoat->prefix = 'rentx_';
            $newBoat->setAttributes($boatDraft->getAttributes());
            $newBoat->post_parent = 0;
            $newBoat->post_author = 6;

            $newBoat->post_type = 'boat';
            $newBoat->post_content = str_replace('boat_id', "$yachtForXml->id", $newBoat->post_content);
            $newBoat->post_name = $yachtForXml->wp_name;
            $newBoat->post_title = $yachtForXml->getPostTitle();
            $newBoat->post_excerpt = $newBoat->post_title;
            $newBoat->to_ping = "_";
            $newBoat->pinged = "_";
            $newBoat->post_content_filtered = "_";

            if (!$newBoat->save(1)) {
                var_dump($newBoat);
            }
            $newBoat->guid = str_replace("p=$boatDraft->ID", "p=$newBoat->ID", $newBoat->guid);
            $newBoat->guid = str_replace("boat-template", "boat", $newBoat->guid);
            $newBoat->post_modified = date("Y-m-d H:i:s");
            $newBoat->post_modified_gmt = gmdate("Y-m-d H:i:s", strtotime($newBoat->post_modified));
            $newBoat->post_status = 'publish';

            $newBoat->save(0);
            $WpYacht = new WpYacht();

            $WpYacht->wp_id     = $newBoat->ID;
            $WpYacht->id        = $yachtForXml->id;
            $WpYacht->wp_name   = $yachtForXml->wp_name;
            $WpYacht->wp_prefix = $id;
            if (!$WpYacht->save()) {
                var_dump($WpYacht);
                return 0;
            }

            //$yachtForXml = Yacht::find()->where(['not in', 'id', $array])->andWhere(['is_active' => 1])->one();
        }



        return 1;
    }
    public static function deleteOldPosts($id)
    {
        $wp_prefix = $id;
        $yachtNeedDelete = Yacht::findAll(['is_active' => 0]);
        foreach ($yachtNeedDelete as $yacht) {
            $postId = ModelsWpYacht::findOne(['id' => $yacht->id, 'wp_prefix' => $wp_prefix]);
            if ($postId) {
                Posts::deleteAll("ID = " . $postId->wp_id);
                ModelsWpYacht::deleteAll(['id' => $yacht->id, 'wp_prefix' => $wp_prefix]);
            }
        }
        return 1;
    }
    public static function saveNewPosts($id)
    {
        $date = date('Y-m-d H:i:s');

        $array = [0];
        $wp_prefix = $id;

        $allPosts = WpYacht::findAll(['wp_prefix' => $wp_prefix]);
        foreach ($allPosts as $post) {
            $array[] = $post->id;
        }
        self::deleteOldPosts($wp_prefix);
        $yachtForXml = Yacht::find()->where(['not in', 'id', $array])->andWhere(['is_active' => 1])->one();
        //Posts::tableName(1);
        $boatDraft = Posts::findOne(['post_title' => self::DRAFT, 'post_type' => self::ELRENDEZES]);


        while ($yachtForXml) {
            $array[] = $yachtForXml->id;
            // var_dump($yachtForXml->id);
            $newBoat = new Posts();
            //$newBoat->prefix = 'rentx_';
            $newBoat->setAttributes($boatDraft->getAttributes());
            $newBoat->post_parent = 0;
            $newBoat->post_author = 6;

            $newBoat->post_type = 'boat';
            $newBoat->post_content = str_replace('boat_id', "$yachtForXml->id", $newBoat->post_content);
            $newBoat->post_name = $yachtForXml->wp_name;
            $newBoat->post_title = $yachtForXml->getPostTitle();
            $newBoat->post_excerpt = $newBoat->post_title;
            $newBoat->to_ping = "_";
            $newBoat->pinged = "_";
            $newBoat->post_content_filtered = "_";

            if (!$newBoat->save()) {
                var_dump($newBoat->errors); exit;
            }
            $newBoat->guid = str_replace("p=$boatDraft->ID", "p={$newBoat->ID}", $newBoat->guid);
            $newBoat->guid = str_replace("boat-template", "boat", $newBoat->guid);
            $newBoat->post_modified = date("Y-m-d H:i:s");
            $newBoat->post_modified_gmt = gmdate("Y-m-d H:i:s", strtotime($newBoat->post_modified));
            $newBoat->post_status = 'publish';

            $newBoat->save(0);
            $WpYacht = new WpYacht();

            $WpYacht->wp_id     = $newBoat->ID;
            $WpYacht->id        = $yachtForXml->id;
            $WpYacht->wp_name   = $yachtForXml->wp_name;
            $WpYacht->wp_prefix = $id;
            if (!$WpYacht->save()) {
                var_dump($WpYacht);
                exit('baj van2');
            }
            $yachtForXml = Yacht::find()->where(['not in', 'id', $array])->andWhere(['is_active' => 1])->one();
        }



        return 1;
    }

    public static function deleteOldYachtPosts($id)
    {
        $prefix = '';
        $tablePrefix = TablePrefix::findOne($id);
        if ($tablePrefix)
            $prefix = $tablePrefix->prefix;
        Posts::$prefix = $prefix;
        $post = Posts::findOne('ID in (select wp_id from wp_yacht)');
        //if ($post){
        Posts::deleteAll("post_type like 'boat'");
        WpYacht::deleteAll("wp_prefix = $id");
        //}
    }
}
