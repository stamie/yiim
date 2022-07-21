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

use ACP\Sorting\Model\Comment\Author\UserMeta as AuthorUserMeta;
use app\classes\booking\NausysBooking as NausysBooking;
use Yii;
use app\models\Xml;
use app\models\Yacht;
use app\models\CityDestination;
use app\models\ClientLog;
use app\models\Country;
use app\models\DestinationBoatLegth;
use app\models\DestinationYachtCategory;
use app\models\DestinationDiscountItem;
use app\models\Port;
use app\models\Usermeta;
use app\models\Users;
use app\models\Posts;
use app\models\YachtCategory;
use app\classes\booking\Cashing;
use app\classes\booking\Booking;
use app\models\Cash;
use app\models\Cities;
use app\models\DestinationServiceTypes;
use app\models\PortsInCities;
use app\models\TablePrefix;


use function YoastSEO_Vendor\GuzzleHttp\json_decode;

class CashController extends \yii\web\Controller
{
    /**
     * 
     * ### 
     * 
     */
    public function actionFreeyachtstothecash()
    {
        $request = Yii::$app->request;
        $date_from = $request->post('date_from');
        $fileName  = '/' . str_replace('-', '_', $date_from) . '.json';
        $duration = 7;
        $flexibility = $request->post('flexibility') ? $request->post('flexibility') : 'on_day';
        $city = $request->post('city') ? $request->post('city') : null;
        $tablePrefixes = TablePrefix::findAll([1 => 1]); //var_dump($tablePrefixes);
        $dirNames = [];
        foreach ($tablePrefixes as $tablePrefix) {
            if ($city) {
                $dirNames[$tablePrefix->id] =  Yii::$app->getBasePath() . '/cash/cities/city_' . $city . '/prefix_' . $tablePrefix->id;
            }
        }

        $is_sale = 0;
        $orderBy = $request->post('order_by') ? intval($request->post('order_by')) : 0;
        $orderBy = ($orderBy > 1 && $orderBy < 8) ? $orderBy : 2;
        $ascOrDesc = $request->post('is_desc') ? intval($request->post('is_desc')) : 0;
        $ascOrDesc = ($ascOrDesc < 2 && $ascOrDesc > -1) ? $ascOrDesc : 0;
        $ignoreOptions = $request->post('ignoreOptions') ? $request->post('ignoreOptions') : '0';

        $page_num = isset($args['page_num']) ? intval($args['page_num']) : 1;
        $lists = ['ports' => [], 'ignoreOptions' => $ignoreOptions, 'page_num' => $page_num];

        $booking = [];
        $exec = '';
        $return = 1;
        foreach (Xml::find()->all() as $xml) {

            foreach ($tablePrefixes as $tablePrefix) {
                $wp_prefix = $tablePrefix->id;
                $lists['ports'] = array(); //array_merge($ports, $ports2);
                $ports = PortsInCities::findAll(['cities_id' => $city, 'wp_prefix_id' => $wp_prefix, 'xml_id' => $xml->id]);
                foreach ($ports as $port) {
                    $lists['ports'][] = $port->xml_json_port_id;
                }

                $bookingClasses = 'app\classes\booking\\' . $xml->class_name . 'Booking';
                $booking1 = array();
                $booking1 = $bookingClasses::freeYachtsSearch($date_from, $duration, $flexibility, $lists, $xml->id, $is_sale, $orderBy, $ascOrDesc);
                $booking = $booking1; //array_merge($booking, $booking1); <-- fejlesztendő
                $exec = json_encode($booking);
                $cashing = new Cashing();
                $cashing->setFileName($fileName);
                $cashing->setPath($dirNames[$wp_prefix]);


                $return2 = $cashing->saveCashFile($exec);
                if ($return2) {
                    $cashModel = new Cash();
                    $cashModel->create_date_time = date(\app\classes\Sync::$dateString);
                    $cashModel->cities_id        = $city;
                    $cashModel->wp_prefix_id     = $wp_prefix;
                    $cashModel->json_name        = $dirNames[$wp_prefix] . $fileName;
                    $return = $return && $cashModel->save();
                }
            }
        }
        return $return;
    }

    public function actionCashinger()
    {
        $url = Yii::$app->urlManager->createAbsoluteUrl('/cash/freeyachtstothecash'); var_dump($url);
        $cities = Cities::find()->all(); //var_dump($cities);
        foreach ($cities as $city) {
            $postFields = ['city' => $city->id];
            $date_from = date('Y-m-d', strtotime('next Saturday'));
            for ($index = 0; $index < 52; $index++) {
                $postFields['date_from'] = $date_from; 
                $ch = curl_init();
                $jsonPostFields = json_encode($postFields);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPostFields);

                $header = array('Content-Type: application/json');

                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                $exec = curl_exec($ch); var_dump($index);
                curl_close($ch);
                $date_from = strtotime($date_from) + (7 * Booking::DAY_MINUTE);
                $date_from = date('Y-m-d', $date_from);
            }
        }
        return 1;
    }
}
