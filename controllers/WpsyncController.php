<?php

/**
 * 
 * Szinkronizációs kontroll
 * 
 * Készítette: Stampel Emese Ágota
 * Év: 2021
 * 
 */

namespace app\controllers;

use app\classes\Wpsync;
use app\models\Postmeta;
use Yii;
use yii\helpers\Url;
use app\models\Posts;
use app\models\TablePrefix;
use app\models\WpYacht;
use app\models\Xml;
use app\models\Yacht;
use yii\helpers\BaseUrl;

class WpsyncController extends \yii\web\Controller
{
    public static $languages = [
        1 => ['code' => 'en', 'name' => 'Angol'],
        2 => ['code' => 'hu', 'name' => 'Magyar'],
        3 => ['code' => 'en-ca', 'name' => 'Kanadai']
    ];
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $id       = $request->get('id') ? $request->get('id') : null;
        $ret = Wpsync::savePosts($id);
        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        if ($tablePrefix && $ret) {
            $prefix = $tablePrefix->prefix;
            //exit('cica');
            return $this->render('index', ['pr' => $prefix, 'prId' => $id, 'ret' => $ret]);
        }

        return $this->render('index', ['pr' => $prefix, 'prId' => $id, 'error' => 'WPSYNC ERROR']);
    }
    public function actionNewposts($id)
    {
        $ret = Wpsync::saveNewPosts($id);
        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        if ($tablePrefix && $ret) {
            $prefix = $tablePrefix->prefix;
            //exit('cica');
            return $this->render('index', ['pr' => $prefix, 'prId' => $id, 'ret' => $ret]);
        }

        return $this->render('index', ['pr' => $prefix, 'prId' => $id, 'error' => 'WPSYNC ERROR']);
    }
    public function deleteOldYachtPosts()
    {

        foreach (TablePrefix::find()->all() as $id)
            Wpsync::deleteOldYachtPosts($id);
    }

    public function actionPictures()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');

        $return = '';
        $yachtForXml = Yacht::findOne($id);
        if ($yachtForXml) {
            $dir = Yii::$app->getBasePath() . "/boat-$yachtForXml->xml_id/$id";
            $scanned_directory = array();

            if (is_dir($dir))
                $scanned_directory = array_diff(scandir($dir), array('..', '.'));


            foreach ($scanned_directory as $file) {
                if (!strpos($file, '-main.') && is_file($dir . '/' . $file)) {
                    $picture = str_replace('Boat', $yachtForXml->wp_name, '<img src="' . str_replace('/web', '', Url::base(1)) . '/boat-' . $yachtForXml->xml_id . '/' . $yachtForXml->id . '/' . $file . '" class="thumbnail" alt="Boat" title="Boat" style="height:100%;display: block; margin-left: auto; margin-right: auto;">');
                    $return .= "<div class='wrapper_1'>
                                $picture</div>";
                }
            }
        }

        return  $return;
    }

    public function actionMainpictures()
    {
        $request = Yii::$app->request;
        $ids = $request->post('ids');
        //$ids = json
        $return = [];
        $yachtForXmls = Yacht::findAll(['id' => $ids]);
        if (is_array($yachtForXmls)) {
            foreach ($yachtForXmls as $yachtForXml) {

                $dir = Yii::$app->getBasePath() . "/boat-{$yachtForXml->xml_id}/{$yachtForXml->id}";
                $scanned_directory = array();

                if (is_dir($dir))
                    $scanned_directory = array_diff(scandir($dir), array('..', '.'));

                $picture = '';
                foreach ($scanned_directory as $file) {
                    if (is_file($dir . '/' . $file) && strpos($file, 'main') > 0) {
                        $picture = str_replace('Boat', $yachtForXml->wp_name, 'src="' . str_replace('/web', '', Url::base(1)) . '/boat-' . $yachtForXml->xml_id . '/' . $yachtForXml->id . '/' . $file . '" class="thumbnail" alt="Boat" title="Boat" style="height:100%;display: block; margin-left: auto; margin-right: auto;"');
                    }
                    $return[$yachtForXml->id] = $picture;
                }
            }
        }
        $return = json_encode($return);
        echo $return;
    }

    public function actionHreflang()
    {
        $request = Yii::$app->request;
        $prId       = $request->get('id') ? $request->get('id') : null;
        $lang       = $request->get('lang') ? intval($request->get('lang')) : null;
        $searchUrl  = $request->get('search_url') ? $request->get('search_url') : null;
        $replaceUrl = $request->get('replace_url') ? $request->get('replace_url') : '';
        $url = BaseUrl::base() . '/wpsync/hreflang';
        $tablePrefixes = TablePrefix::find()->all();
        $return = 0;
        if ($prId && $lang && $lang < count(self::$languages)) {
            $hrefLangs = Postmeta::find()->where("meta_value like '%" . self::$languages[$lang]['code'] . "%" . self::$languages[($lang + 1)]['code'] . "%'")->all();

            if (is_array($hrefLangs)) {
                foreach ($hrefLangs as $hrefLang) {
                    $string = $hrefLang->meta_value;
                    $pos1   = strpos($string, self::$languages[$lang]['code']);
                    $pos2   = strpos($string, self::$languages[($lang + 1)]['code']);

                    $string = substr($string, 0, $pos1) . str_replace($searchUrl, $replaceUrl, substr($string, $pos1, ($pos2 - $pos1))) . substr($string, $pos2);


                    $hrefLang->meta_value = $string;
                    $hrefLang->save(0);
                }
            }
        }
        return $this->render('hreflang', ['url' => $url, 'tablePrefixes' => $tablePrefixes, 'languages' => self::$languages]);
    }
    public function actionDeleteoldposts() {
        $request = Yii::$app->request;
        $prefix_id = intval($request->get("id"));
        $ID        = intval($request->get("wp_id"));
        WpYacht::deleteAll(['ID' => $ID]);
        return 'OK';
    }
}
