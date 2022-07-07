<?php

namespace app\classes\booking;

use app\classes\booking\NausysBooking as NausysBooking;
use app\models\Cities;
use Yii;
use yii\helpers\Url;
use app\models\Xml;
use app\models\Yacht;
use app\models\CityDestination;
use app\models\Posts;
use app\models\Options;
use app\models\WpYacht;
use app\models\YachtDatas1;
use app\models\YachtModel;

class CashYachts extends Cashing
{
    static $foDest = 'destinations';
    public static function threeFreeYachtsToCash()
    {
        $request = Yii::$app->request;
        $destId = $request->get('dest') ? intval($request->get('dest')) : null;
        $prefix = $request->get('id') ? intval($request->get('id')) : null;
        if ($destId && $prefix) {
            self::$fileName = "/three_$destId.html";
            self::$path     = Yii::$app->basePath . '/cash/landing_page/prefix_' . $prefix;

            $content = self::threefreeyachts();
            $ret = parent::saveCashFile($content);

            return $ret;
        }
        return 1;
    }
    public static function threeFreeYachtsToCash2($prefix)
    {
        if (!$prefix)
            return 0;
        self::$path = Yii::$app->basePath . '/cash/landing_page/prefix_' . $prefix;
        $return = 1;
        var_dump(self::$path);
        if (is_dir(self::$path)) {
            $files = scandir(self::$path);
            var_dump($files);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && strpos($file, 'hree_') > 0 && !strpos($file, '_new')) {
                    $destId = str_replace(array('three_', '.html'), '', $file);
                    var_dump($destId);
                    $destId = intval($destId);
                    if ($destId && $prefix) {
                        self::$fileName = "/three_$destId.html";
                        $content = self::threefreeyachts2($destId, $prefix);
                        $ret = parent::saveCashFile($content);
                        if (!$ret) {
                            $return = 0;
                        }
                    }
                }
            }
            return $return;
        }
        return 0;
    }
    public static function threefreeyachts2($dest_id, $prefix)
    {
        if ($dest_id && $prefix) {
            $xmls = Xml::find()->all();
            foreach ($xmls as $xml) {
                $portsInCities = CityDestination::getAllPortsForAllCitiesDestIds([$dest_id], $xml->id, $prefix);
                return self::selcetYachts($xml, $prefix, $portsInCities);
            }
        }
        return "nem megy be";
    }
    public static function threefreeyachts()
    {
        $request = Yii::$app->request;
        $dest_id = $request->get('dest') ? intval($request->get('dest')) : null;
        $prefix  = $request->get('id') ? intval($request->get('id')) : null;
        if ($dest_id && $prefix) {
            $xmls = Xml::find()->all();
            foreach ($xmls as $xml) {
                $portsInCities = CityDestination::getAllPortsForAllCitiesDestIds([$dest_id], $xml->id, $prefix);
                return self::selcetYachts($xml, $prefix, $portsInCities);
            }
        }
        return "nem megy be";
    }

    private static function selcetYachts(Xml $xml, $prefix, $portsInCities = null)
    {
        if (is_array($portsInCities) && count($portsInCities) > 0) {
            $ports = $portsInCities;
            $sailingYacht = NausysBooking::threeFreeYachtsSearch($xml->id, "Sailing yacht", $ports, 1);
            if (empty($sailingYacht)) {
                $sailingYacht = NausysBooking::threeFreeYachtsSearch($xml->id, "Sailing yacht", $ports, 0);
            }
            if (empty($sailingYacht)) {
                $sailingYacht = [];
            }
            $catamaran    = NausysBooking::threeFreeYachtsSearch($xml->id, "Catamaran", $ports, 1);

            if (empty($catamaran)) {
                $catamaran    = NausysBooking::threeFreeYachtsSearch($xml->id, "Catamaran", $ports, 0);
            }
            $index = -1;
            while (empty($catamaran) && $index < 10) {
                $index++;
                $catamaran    = NausysBooking::threeFreeYachtsSearch($xml->id, "Luxury sailing yacht", $ports, 0);
            }
            if (empty($catamaran)) {
                $catamaran    = [];
            }

            $motorYacht   = NausysBooking::threeFreeYachtsSearch($xml->id, "Motor yacht",   $ports, 1);
            if (empty($motorYacht))
                $motorYacht = NausysBooking::threeFreeYachtsSearch($xml->id, "Motor yacht",   $ports, 0);
            if (empty($motorYacht))
                $motorYacht = NausysBooking::threeFreeYachtsSearch($xml->id, "Motor boat",   $ports, 1);
            if (empty($motorYacht))
                $motorYacht = NausysBooking::threeFreeYachtsSearch($xml->id, "Motor boat",   $ports, 0);
            $index = -1;
            while (empty($motorYacht) && $index < 10) {
                $index++;
                $motorYacht = NausysBooking::threeFreeYachtsSearch($xml->id, "Catamaran",   $ports, 0);
            }
            $index = -1;
            if ($index < 10 && (empty($motorYacht) ||
                (isset($motorYacht) && isset($catamaran["id"]) && $motorYacht["id"] == $catamaran["id"]))) {
                $index++;
                $motorYacht = NausysBooking::threeFreeYachtsSearch($xml->id, "Catamaran",   $ports, 0);
                if ($motorYacht && isset($catamaran["id"]) && $motorYacht["id"] == $catamaran["id"])
                    $motorYacht = NausysBooking::threeFreeYachtsSearch($xml->id, "Sailing yacht",   $ports, 0);
            }
            if (empty($motorYacht))
                $motorYacht = [];
            $exec = ["SailingYacht" => $sailingYacht, "MotorYacht" => $motorYacht, "Catamaran" => $catamaran];
            if ($exec) {
                $returnArray = $exec;
                $contentObj = Posts::find()->where(["post_title" => 'hajo_ajanlo', "post_status" => 'draft', "post_type" => 'boat-template'])->one();
                $content = isset($contentObj) ? $contentObj->post_content : 'Nincs ilyen template';
                $content = self::do_fs_shortcode($content);

                $content = self::insertContent("SailingYacht", $returnArray, $content, $prefix);
                $content = self::insertContent("Catamaran", $returnArray, $content, $prefix);
                $content = self::insertContent("MotorYacht", $returnArray, $content, $prefix);

                return $content;
            }
            return "nem jött le hajó";
        }
        return "hiba a kódban";
    }
    private static function insertContent($key, $value, $content, $prefix)
    {
        $data = $value[$key];
        if (is_array($data) && count($data) > 0) {
            $datas[$data["id"]] = $data;
            $id = $data["id"];
            $cityUrl = self::city_url($value, $key, $prefix);
            $picture = self::boat_picture($value, $key);
            $boatUrl   = self::boat_url($value, $key, $prefix);
            $dates = self::dateFromTo($value, $key, $prefix);
            $city    = '<span class="sale_in_city"><a href="' . $cityUrl . '" target="_blank">' . $data["cityFrom"] . '</a></span>';
            $boatTitle = '<a href="' . $boatUrl . '" target="_blank">' . self::boat_title($value, $key) . '</a>';
            $boatWidth = self::boat_width($value, $key);
            $sale      = '<a href="' . $boatUrl . '" target="_blank"><div class="sale">' . number_format(round(floatval($data["priceForUser"])), 0, '.', ' ') . ' ' . $data["currency"] . ' / <span class="minidate">'.$dates.'</span></div></a>';
            $boatCabinsAndBeds = self::cabinAndBed($value, $key);
            $insertContent = '<div';
            switch ($key) {
                case "SailingYacht":
                    $insertContent .= ' class="sailboat">';
                    break;
                case "Catamaran":
                    $insertContent .= ' class="catamaran">';
                    break;
                case "MotorYacht":
                    $insertContent .= ' class="motoryacht">';
                    break;
            }
            $insertContent .= '<div class="ajanlo_kep">' . $picture . '' . $city . '' . '</div>';
            //$insertContent .= '<div>' . $city . '</div>';
            $insertContent .= '<div class="boatTitle">' . $boatTitle . '</div>';
            $insertContent .= '<div class="boatDatas"><span class="boatWidth" >' . $boatWidth . '</span><span class="boatCabinsAndBeds">' . $boatCabinsAndBeds . '</span></div>';
            $insertContent .= '<div class="boatSale">' . $sale . '</div>';
            //$insertContent .= '</div>';
            switch ($key) {
                case "SailingYacht":
                    $content = str_replace('<div class="sailboat">', $insertContent, $content);
                    break;
                case "Catamaran":
                    $content = str_replace('<div class="catamaran">', $insertContent, $content);
                    break;
                case "MotorYacht":
                    $content = str_replace('<div class="motoryacht">', $insertContent, $content);
                    break;
            }

            return $content;
            return;
        }
        return $content;
        return;
    }
 public static function dateFromTo($value, $key)
 {
    $data = $value[$key];

    $datesString = '';

    if ($data && isset($data["date_from"]) && isset($data["date_to"])) {
        $from = date('Y.m.d', strtotime($data["date_from"])); var_dump($data["date_from"]);
        $to   = date('m.d', strtotime($data["date_to"]));
        $datesString = $from.' - '.$to; var_dump($datesString);
    }
    return $datesString; 


 }

    public static function boat_picture($value, $key)
    {

        $data = $value[$key];

        $discountsString = '';

        if ($data && isset($data["id"])) {
            $id = $data["id"];
            $item = $data;
            if (is_array($item) && isset($item['discounts']) && is_array($item['discounts'])) {
                foreach ($item['discounts'] as $discount) {
                    $amount = ' % ';
                    if (!is_array($discount) && isset($discount->type) && $discount->type !== 'PERCENTAGE')
                        $amount = ' ' . $item['currency'] . ' ';
                    else if (is_array($discount) && isset($discount['type']) && $discount['type'] !== 'PERCENTAGE')
                        $amount = ' ' . $item['currency'] . ' ';

                    if (!is_array($discount))
                        $discountsString .= $discount->amount . $amount . '+ ';
                    else
                        $discountsString .= $discount['amount'] . $amount . '+ ';
                }
            } else if (!is_array($item) && isset($item->discounts) && is_array($item->discounts)) {
                foreach ($item->discounts as $discount) {
                    $amount = ' % ';
                    if (!is_array($discount) && isset($discount->type) && $discount->type !== 'PERCENTAGE')
                        $amount = ' ' . $item->currency . ' ';
                    else if (is_array($discount) && isset($discount['type']) && $discount['type'] !== 'PERCENTAGE')
                        $amount = ' ' . $item->currency . ' ';

                    if (!is_array($discount))
                        $discountsString .= $discount->amount . $amount . '+ ';
                    else
                        $discountsString .= $discount['amount'] . $amount . '+ ';
                }
            }
        }
        if ($discountsString !== '')
            $discountsString = '<span class="discounts_in_the_picture">' . trim($discountsString, ' + ') . '</span>';


        $xmlId = 0;
        $yacht = Yacht::findOne($id); 
        if ($yacht) {
            $xmlId = $yacht->xml_id;
        }

        if ($xmlId) {
            $return = '';
            $yachtForXml = Yacht::findOne($id);
            if ($yachtForXml) {

                $dir = Yii::$app->getBasePath() . "/boat-$yachtForXml->xml_id/$id";
                $scanned_directory = array();

                if (is_dir($dir))
                    $scanned_directory = array_diff(scandir($dir), array('..', '.'));

                $picture = '';
                foreach ($scanned_directory as $file) {

                    if (is_file($dir . '/' . $file) && strpos($file, 'land_main') > 0) {
                        $picture = str_replace('Boat', $yachtForXml->wp_name, '<img src="' . str_replace('/web', '', Url::base(1)) . '/boat-' . $yachtForXml->xml_id . '/' . $yachtForXml->id . '/' . $file . '" class="thumbnail" alt="Boat" title="Boat" style="height:100%;display: block; margin-left: auto; margin-right: auto;">');
                    }
                    $return = "$picture";
                }
            }

            $gallery = $return;
            $gallery = $discountsString . (($gallery == "") ? '<img src="/wp-content/plugins/boat-shortcodes/include/pictures/boat-noimage.jpg" />' : $gallery);
            return $gallery;
        }

        return $discountsString . '<img src="/wp-content/plugins/boat-shortcodes/include/pictures/boat-noimage.jpg" />';
    }
    public static function get_option($key)
    {
        $option = Options::findOne(["option_name" => $key]);
        if ($option)
            return $option->option_value;
        return null;
    }

    public static function do_fs_shortcode($content)
    {
        //        [row style="small" v_align="equal"]

        //      [col span="4" span__sm="12"]


        $return = '';
        $content2 = $content;
        $content2 = str_replace(
            [
                '[row style="small" v_align="equal"]',
                '[col span="4" span__sm="12"]',
                '[/col]',
                '[/row]'
            ],
            [
                '<div class="row row-small align-equal">',
                '<div class="col medium-4 small-12 large-4"><div class="col-inner">', //<div class="col-inner">',
                '</div></div>',
                '</div>',

            ],
            $content2
        );
        $return = $content2;
        return $return;
    }
    public static function boat_title($value, $key)
    {

        $data = $value[$key];

        $discountsString = '';

        if ($data && isset($data["id"])) {
            $id = $data["id"];


            $object = Posts::find()->innerJoin('wp_yacht', Posts::$prefix . 'posts.ID=wp_yacht.wp_id')->where(['wp_yacht.id' => $id])->one();

            if ($object) {

                return '<span class="title" attr-id="' . $id . '">' . $object->post_title . '</span>';
            }
        }
        return "Haven't got Title";
    }
    public static function boat_width($value, $key)
    {
        $data = $value[$key];

        if ($data && isset($data["id"])) {
            $id = $data["id"];

            $object = YachtModel::find()
                ->innerJoin('yacht', 'yacht.yacht_model_id = yacht_model.xml_json_id AND yacht_model.xml_id = yacht.xml_id')
                ->where(['yacht.id' => $id])->one();
            return "" . $object->loa . ' m / ' . round($object->loa * 3.2808, 0) . " ft";
        }

        return '-';
    }

    public static function cabinAndBed($value, $key)
    {

        $data = $value[$key];
        if ($data && isset($data["id"])) {
            $id = $data["id"];

            $object = YachtDatas1::findOne($id);
            if ($object) {
                return $object->cabins . ' Cabins /' . $object->berths_total . ' Berths';
            }
        }
        return "";
    }

    public static function boat_url($value, $key, $wp_prefix)
    {
        $data = $value[$key];
        if ($data && isset($data["id"])) {
            $id = $data["id"];
            $from = date("Y-m-d", strtotime($data["date_from"]));
            $to   = date("Y-m-d", strtotime($data["date_to"]));
            $object = WpYacht::findOne(['id' => $id, 'wp_prefix' => $wp_prefix]);
            if ($object) {
                $post = Posts::findOne(["ID" => $object->wp_id]);
                return '/' . $post->post_type . '/' . $post->post_name . '?dateFrom=' . $from . '&dateTo=' . $to;
            }
        }

        return '/#';
    }
    public static function city_url($value, $key, $wp_prefix, $xmlId = 1)
    {
        if ($wp_prefix == 2){
            self::$foDest = 'hajoberles-uticelok';
        }
        $data = $value[$key];
        if ($data && isset($data["cityFrom"])) {
            $city = Cities::findOne(['name' => $data["cityFrom"]]);
            if ($city) {
                $objects = CityDestination::findAll(['city_id' => $city->id, 'wp_id' => $wp_prefix, "xml_id" => $xmlId]);

                if (is_array($objects) && count($objects) > 0) {
                    foreach ($objects as $object) {

                        $o = Posts::findOne(['ID' => $object->post_id]);
                        $obj = $o;
                        while (isset($o->post_parent) && $o->post_parent > 0) {
                            $o = Posts::findOne(['ID' => $o->post_parent]);
                        }
                        if ($o && $o->post_name == self::$foDest) {
                            $response = "[get_permalink id='".$object->post_id."']";
                            return  $response;
                        }
                    }
                }
            }
        }

        return '/#';
    }
}
