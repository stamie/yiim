<?php
/**
 * 
 * Szinkronizációs kontroll
 * 
 * Készítette: Stampel Emese Ágota
 * Év: 2021
 * 
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use Yii;
use yii\helpers\Url;
use app\models\Posts;
use app\models\TablePrefix;
use app\models\Xml;
use app\models\WpYacht;


class WpsyncController extends Controller
{
    public function actionIndex($id)
    {
        $date = date(\app\classes\Sync::$dateString);

        $this->deleteOldYachtPosts();

        $yachtForXml = WpYacht::findOne(['wp_id' => 0, 'wp_prefix' => $id]);

        $boatDraft = Posts::findOne(['post_title' => 'draft', 'post_type' => 'boat-template']);

        while ($yachtForXml){

            $newBoat = new Posts();

            $newBoat->setAttributes($boatDraft->getAttributes());
            $newBoat->post_parent = 0;
       
            $newBoat->post_content = str_replace('boat_id', "$yachtForXml->id", $newBoat->post_content);
            $newBoat->post_name = $yachtForXml->wp_name;
            $newBoat->post_title = $yachtForXml->getPostTitle();     
            $newBoat->guid = str_replace("p=$boatDraft->ID", "p=$newBoat->ID", $newBoat->guid );

            $newBoat->post_modified = date("Y-m-d H:i:s");
            $newBoat->post_modified_gmt = gmdate("Y-m-d H:i:s", strtotime($newBoat->post_modified));
            $newBoat->post_status = 'publish';
            $newBoat->save(false);

            $yachtForXml->wp_id = $newBoat->ID;
            $yachtForXml->save(false);
        
            $yachtForXml = WpYacht::findOne(['wp_id' => 0, 'wp_prefix' => $id]);

        }

        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        if ($tablePrefix)
            $prefix = $tablePrefix->prefix;

        $boat = Posts::find()->where("post_modified < '$date' and post_status  = 'publish' and post_type = 'boat'")->one();
        
        if (empty($boat)) {
            return ExitCode::CONFIG;
        }
        while ($boat) {

            $yachtForXml =WpYacht::findOne(['wp_id' => $boat->ID, 'wp_prefix' => $id]);
            
            if ($yachtForXml){
                $boat->post_content = str_replace('boat_id', "$yachtForXml->id", $boatDraft->post_content);
                $boat->post_modified = date("Y-m-d H:i:s");
                $boat->post_modified_gmt = gmdate("Y-m-d H:i:s", strtotime($boat->post_modified));
                $boat->post_status = 'publish';

                $boat->save(0);

                $boat = Posts::find()->where("post_modified < '$date' and post_status = 'publish' and post_type = 'boat'")->one();
            } else {
                return ExitCode::DATAERR;

            }

        }

        return ExitCode::OK;
    }

    public function deleteOldYachtPosts() {
        Posts::deleteAll('ID in (select wp_id from wp_yacht)');
    }


}