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
use app\classes\booking\CashYachts;
use app\models\DestinationBerth;
use app\models\DestinationServiceTypes;

use function YoastSEO_Vendor\GuzzleHttp\json_decode;

class BookingController extends \yii\web\Controller
{
    /**
     * 
     * ### 
     * 
     */
    static private function boatlistToList($boatList)
    {
        $return = [];
        if (is_array($boatList) && count($boatList) > 0 && isset($boatList[0]->xml_json_id)) {
            foreach ($boatList as $boat) {
                $return[] = $boat->xml_json_id;
            }
        }
        return $return;
    }

    public function actionThisyacht()
    {
        $request = Yii::$app->request;
        $date_from = $request->get('date_from');
        $date_to   = $request->get('date_to');
        $boat_id   = $request->get('boat_id') ? $request->get('boat_id') : -1;
        $wp_prefix = $request->get('wp_prefix') ? $request->get('wp_prefix') : null;
        $booking = [];
        $yacht = Yacht::findOne($boat_id);
        if ($yacht && $xml = Xml::findOne($yacht->xml_id)) {
            if ($xml) {
                $bookingClasses = 'app\classes\booking\\' . $xml->class_name . 'Booking';
                $booking = array();
                $booking = $bookingClasses::yachtSearch($yacht->xml_json_id, $date_from, $date_to, $xml->id);
            } 
        }
        return json_encode($booking);
    }

    public function actionWherefreeyacht()
    {
        $request = Yii::$app->request;
        $date_from = $request->get('date_from');
        $date_to   = $request->get('date_to');
        $yachtId   = $request->get('id');
        $yacht = Yacht::findOne($yachtId);
        if ($yacht && $xml = Xml::findOne($yacht->xml_id)) {
            if ($xml) {
                $bookingClasses = 'app\classes\booking\\' . $xml->class_name . 'Booking';
                $nextFreePeriod = $bookingClasses::nextFreePeriod($date_from, $date_to, $yachtId);
                $prevFreePeriod = $bookingClasses::prevFreePeriod($date_from, $date_to, $yachtId);
                return json_encode(['nextFreePeriod' => $nextFreePeriod, 'prevFreePeriod' => $prevFreePeriod]);
            }
        }
        return [];
    }

    public function actionFreeyachts()
    {
        $request = Yii::$app->request;
        $date_from = $request->get('date_from');
        $duration = $request->get('duration');
        $flexibility = $request->get('flexibility') ? $request->get('flexibility') : 'on_day';
        $dest_ids = $request->get('dest_ids') ? $request->get('dest_ids') : null;
        $wp_prefix = $request->get('wp_prefix') ? $request->get('wp_prefix') : null;
        $booking = [];
        foreach (Xml::find()->all() as $xml) {
            $yachtCategories1 = DestinationYachtCategory::getAllYachtCategoriesForDestination($dest_ids, $xml->id, $wp_prefix);
            $ports = CityDestination::getAllPortsForAllCitiesDestIds($dest_ids, $xml->id, $wp_prefix);
            $list['ports'] = $ports;
            $list['yacht_categories'] = $yachtCategories1;
            $bookingClasses = 'app\classes\booking\\' . $xml->class_name . 'Booking';
            $booking = $bookingClasses::freeYachtsSearch($date_from, $duration, $flexibility, $list, $xml->id);
            if (is_array($dest_ids) && count($dest_ids) > 0 && count($ports) == 0) {
                $booking = [];
            }
        }
        return json_encode($booking);
    }

    public function actionAllfreeyachts()
    {
        $request = Yii::$app->request;
        $date_from = $request->get('date_from');
        $duration = $request->get('duration');
        $flexibility = $request->get('flexibility') ? $request->get('flexibility') : 'on_day';
        $ports = $request->get('ports') ? $request->get('ports') : array();
        $wp_prefix = $request->get('wp_prefix') ? $request->get('wp_prefix') : null;
        $is_sale = $request->get('is_sale') ? intval($request->get('is_sale')) : 0;
        $orderBy = $request->get('order_by') ? intval($request->get('order_by')) : 0;
        $orderBy = ($orderBy > 1 && $orderBy < 8) ? $orderBy : 2;
        $ascOrDesc = $request->get('is_desc') ? intval($request->get('is_desc')) : 0;
        $ascOrDesc = ($ascOrDesc < 2 && $ascOrDesc > -1) ? $ascOrDesc : 0;
        $ignoreOptions = $request->get('ignoreOptions') ? $request->get('ignoreOptions') : '0';
        $selectedCategories = $request->get('selectedCategories') ? $request->get('selectedCategories') : null;
        $dest_ids = $request->get('dest_ids') ? $request->get('dest_ids') : null;
        $cabins = $request->get('cabins') ? $request->get('cabins') :  null;
        $isset1 = $request->post('duration');
        $isset2 = $request->post('date_from');
        $feauteres = null;
        if (isset($isset1) || isset($isset2)) {
            $date_from = $request->post('date_from');
            $duration = $request->post('duration');
            $flexibility = $request->post('flexibility') ? $request->post('flexibility') : 'on_day';
            $dest_ids = $request->post('dest_ids') ? $request->post('dest_ids') : null;
            $ports = $request->post('ports') ? $request->post('ports') : array();
            $wp_prefix = $request->post('wp_prefix') ? $request->post('wp_prefix') : null;
            $is_sale = $request->post('is_sale') ? intval($request->post('is_sale')) : 0;
            $orderBy = $request->post('order_by') ? intval($request->post('order_by')) : 0;
            $orderBy = ($orderBy > 1 && $orderBy < 8) ? $orderBy : 2;
            $ascOrDesc = $request->post('is_desc') ? intval($request->post('is_desc')) : 0;
            $ascOrDesc = ($ascOrDesc < 2 && $ascOrDesc > -1) ? $ascOrDesc : 0;
            $ignoreOptions = $request->post('ignoreOptions') ? $request->post('ignoreOptions') : '0';
            $selectedCategories = $request->post('selectedCategories') ? $request->post('selectedCategories') : null;
            $feauteres = $request->post('feauteres') ? $request->post('feauteres') :  null;
            $cabins = $request->post('cabins') ? $request->post('cabins') :  null; 
            
        }
        $args = $request->post('args') ? $request->post('args') : array();
        $args['cabins'] = $cabins; 
        if (isset($args["order_by"])) {
            $orderBy   = intval($args["order_by"]);
            $ascOrDesc = isset($args["desc"]) ? intval($args["desc"]) : 0;
        }
        foreach ($ports as $key => $port) {
            $ports[$key] = intval(Port::findOne(intval($port))->xml_json_id);
        }
        $page_num = $request->post('page_num') ? intval($request->post('page_num')) : 1;
        $lists = ['ports' => [], 'ignoreOptions' => $ignoreOptions, 'page_num' => $page_num, 'feauteres' => $feauteres]; 
        $return = [];
        $booking = [];
        foreach (Xml::find()->all() as $xml) {
            $ports2 = array();
            if (is_array($dest_ids) && count($dest_ids) > 0) {
                $ports2 = CityDestination::getAllPortsForAllCitiesDestIds($dest_ids, $xml->id, $wp_prefix);
                if (is_array($ports2) && count($ports2) > 0) {
                    $lists['ports'] = array_merge($ports, $ports2);
                }
            } else {
                $ports2 = Port::findAll(["xml_id" => $xml->id]);
                foreach ($ports2 as $country) {
                    $lists['ports'][] = $country->xml_json_id;
                }
            }
            $bookingClasses = 'app\classes\booking\\' . $xml->class_name . 'Booking';
            $booking1 = array();
            $yachtCategories = null;
            $discountItems = null; 
            if (is_array($dest_ids)) {
                $ports = CityDestination::getAllPortsForAllCitiesDestIds($dest_ids, $xml->id, $wp_prefix); 
                $lists['ports'] = array_merge($ports, $lists['ports']);
            }
            if (is_array($dest_ids) && count($dest_ids) == 1) {
                ///dest_ids alapján a kizárt típusok (hajó)
                $notCategories = DestinationYachtCategory::getAllYachtCategoriesForDestination($dest_ids[0], $xml->id, $wp_prefix);
                if (is_array($notCategories) && count($notCategories) > 0) {
                    $yachtCategories = YachtCategory::find()->where(['xml_id' => $xml->id])->all();
                    $yacht_categories = [];
                    if (is_array($yachtCategories) && count($yachtCategories) > 0) {
                        foreach ($yachtCategories as $category) {
                            if (!in_array($category->xml_json_id, $notCategories))
                                $yacht_categories[] = $category->name;
                        }
                        $lists['yacht_categories'] = $yacht_categories;
                    }
                }
                ///dest_ids alapján beállított hajóhosszok
                $minMaxLengths = ['min_loa'   => 0, 'max_loa' => -1];
                $minMaxBerths  = ['min_berth' => 0, 'max_berth' => -1];
                $dest = Posts::findOne(['ID' => $dest_ids[0]]);
                while ($dest) {
                    $minMaxLength = DestinationBoatLegth::findOne(['destination_id' => $dest->ID, 'wp_id' => $wp_prefix]);
                    $minMaxBerth  = DestinationBerth::findOne(['destination_id' => $dest->ID, 'wp_id' => $wp_prefix]);
                    if ($minMaxLengths['min_loa'] < $minMaxLength->min_loa) {
                        $minMaxLengths['min_loa'] = $minMaxLength->min_loa;
                    }
                    if ($minMaxLengths['max_loa'] > $minMaxLength->max_loa) {
                        $minMaxLengths['max_loa'] = $minMaxLength->max_loa;
                    }
                    if ($minMaxBerths['min_berth'] < $minMaxBerth->min_berth) {
                        $minMaxBerths['min_berth'] = $minMaxBerth->min_berth;
                    }
                    if ($minMaxBerths['max_berth'] > $minMaxLength->max_berth) {
                        $minMaxBerths['max_berth'] = $minMaxLength->max_berth;
                    }
                    if (isset($dest->post_parent) && $dest->post_parent > 0)
                        $dest = Posts::findOne(['ID' => $dest->post_parent]);
                    else
                        $dest = null;
                }
                if ($minMaxLengths['min_loa'] > 0) {
                    $args['minLength'] = round($minMaxLengths['min_loa'] * 0.3048000);
                }
                if ($minMaxLengths['max_loa'] > 0) {
                    $args['maxLength'] = round($minMaxLengths['max_loa'] * 0.3048000);
                }
                if ($minMaxBerths['min_berth'] > 0) {
                    $args['minBerth'] = $minMaxBerths['min_berth'];
                }
                if ($minMaxBerths['max_berth'] > 0) {
                    $args['maxBerth'] = $minMaxBerths['max_berth'];
                }

                ///dest_ids alapján a kizárt akciók
                $notDiscounts = DestinationDiscountItem::getAllDiscountsForDestination($dest_ids[0], $xml->id, $wp_prefix);
                if (is_array($notDiscounts) && count($notDiscounts) > 0) {
                    $discountItems = YachtCategory::find()->where(['xml_id' => $xml->id])->all();
                    $discount_items = [];
                    if (is_array($discountItems) &&  count($discountItems) > 0) {
                        foreach ($discountItems as $item) {
                            if (!in_array($item->xml_json_id, $notDiscounts))
                                $discount_items[] = $item->xml_json_id;
                        }
                        $lists['discount_items'] = $discount_items;
                    }
                }
                ///dest_ids alapján beállított selectedServiceTypes
                //$args['selectedServiceTypes']
                $serviceType       = DestinationServiceTypes::findOne(['destination_id' => $dest_ids[0], 'wp_id' => $wp_prefix]);
                $post              = Posts::findOne(['ID' => $dest_ids[0]]);
                $parentDestination = Posts::findOne(['ID' => $post->post_parent]);
                while ($parentDestination && !$serviceType) {
                    $serviceType       = DestinationServiceTypes::findOne(['destination_id' => $parentDestination->ID, 'wp_id' => $wp_prefix]);
                    $post              = $parentDestination;
                    $parentDestination = Posts::findOne(['ID' => $post->post_parent]);
                }
                if ($serviceType)
                    $args['selectedServiceTypes'] = $serviceType->service_types;
            }
            if (is_array($selectedCategories)) {
                $yacht_categories = $selectedCategories;
                $lists['yacht_categories'] = $yacht_categories;
             }
            $lists['args'] = $args;
            if (is_array($dest_ids) && count($dest_ids) > 0 && is_array($lists['ports']) && count($lists['ports']) > 0) { //echo "hello";
                $booking1 = $bookingClasses::freeYachtsSearch2($date_from, $duration, $flexibility, $lists, $xml->id, $is_sale, $orderBy, $ascOrDesc);
               
                $booking = $booking1;
            } else if ((!is_array($dest_ids) || count($dest_ids) == 0) && is_array($lists['ports']) && count($lists['ports']) > 0) {
                $booking1 = $bookingClasses::freeYachtsSearch2($date_from, $duration, $flexibility, $lists, $xml->id, $is_sale, $orderBy, $ascOrDesc);
                $booking = $booking1;
            }
            //$booking = $booking1; //array_merge($booking, $booking1); <-- fejlesztendő
            $booking['yacht_categories'] = $lists['yacht_categories'];
            
        }
        return json_encode($booking);
    }

    public function actionFreeyachtswithports()
    {
        $request = Yii::$app->request;
        $date_from = $request->get('date_from');
        $duration = $request->get('duration');
        $flexibility = $request->get('flexibility') ? $request->get('flexibility') : 'on_day';
        $ports = $request->get('ports') ? $request->get('ports') : null;
        $dest_id = $request->get('dest_id') ? $request->get('dest_id') : null;
        $wp_prefix = $request->get('wp_prefix') ? $request->get('wp_prefix') : null;
        $lists = [];
        $return = [];
        $booking = [];
        foreach (Xml::find()->all() as $xml) {
            $yachtCategories = Yacht::find()->where(['id' => $lists])->andWhere(['xml_id' => $xml->id])->all();
            $yachtCategories = DestinationYachtCategory::getAllYachtCategoriesForDestination($dest_id, $xml->id, $wp_prefix);
            $list['ports'] = [];
            foreach ($ports as $port_id) {
                $port = Port::findOne($port_id);
                if ($port->xml_id == $xml->id) {
                    $list['ports'][] = intval($port->xml_json_id);
                }
            }
            $list['yacht_categories'] = $yachtCategories;
            $bookingClasses = 'app\classes\booking\\' . $xml->class_name . 'Booking';
            $booking = $bookingClasses::freeYachtsSearch2($date_from, $duration, $flexibility = "on_day", $list, $xml->id);
            if (isset($dest_id) &&  (empty($list['ports']) || count($list['ports']) == 0)) {
                $booking = [];
            }
        }
        return json_encode($booking);
    }

    private function isUser($user_id, $yacht_id, $date_from, $date_to, $random_string)
    {
        $ipAddress = Yii::$app->request->getUserIP();
        $where = ['user_id' => $user_id, 'yacht_id' => $yacht_id, 'date_from' => $date_from, 'date_to' => $date_to, 'random_string' => $random_string];
        $clientLog = ClientLog::find()->where($where)->one();
        if ($clientLog && $clientLog->is_used == 0) {
            $clientLog->is_used = 1;
            $clientLog->save(0);
            return 1;
        }
        return 0;
    }

    public function actionCreateoption()
    {
        if (!Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $date_from     = $request->post('date_from');
            $date_to       = $request->post('date_to');
            $yacht_id      = $request->post('yacht_id');
            $user_id       = $request->post('user_id');
            $random_string = $request->post('passw');
            $message       = $request->post('msg');
            $wp_prefix     = $request->post('id');
            $ignoreOptions = $request->post('ignoreOptions');
            $yacht     = Yacht::findOne($yacht_id);
            $clientUser      = Users::findOne(['ID' => $user_id]);
            $clientUsermetas = Usermeta::findAll(['user_id' => $user_id]);
            if ($clientUser && $clientUsermetas) {
                $email      = $clientUser->user_email;
                $first_name = Usermeta::findOne(['user_id' => $user_id, 'meta_key' => 'first_name']);
                $last_name  = Usermeta::findOne(['user_id' => $user_id, 'meta_key' => 'last_name']);
                $country    = Usermeta::findOne(['user_id' => $user_id, 'meta_key' => 'country']);
                $address    = Usermeta::findOne(['user_id' => $user_id, 'meta_key' => 'adress']);
                $city       = Usermeta::findOne(['user_id' => $user_id, 'meta_key' => 'city']);
                $zipCode    = Usermeta::findOne(['user_id' => $user_id, 'meta_key' => 'zip_code']);
                $phone_     = Usermeta::findOne(['user_id' => $user_id, 'meta_key' => 'phone_']);
                $countryId  = 100130;
                if ($country) {
                    $countryId = Country::findOne(['xml_id' => $yacht->xml_id, 'name' => $country]);
                    $countryId = isset($countryId) ? $countryId->xml_json_id : 100130;
                }
                $client =  [
                    "name" => isset($first_name) ? $first_name->meta_value : "",
                    "surname" => isset($last_name) ? $last_name->meta_value : "",
                    "company" => "false",
                    "vatNr" => "",
                    "address" =>  isset($address) ? $address->meta_value : "",
                    "zip" =>  isset($zipCode) ? $zipCode->meta_value : "",
                    "city" =>  isset($city) ? $city->meta_value : "",
                    "countryId" => $countryId,
                    "email" => $email,
                    "phone" => isset($phone_) ? $phone_->meta_value : "",
                    "mobile" => "",
                    "skype" => ""
                ];
                if (self::isUser($user_id, $yacht_id, $date_from, $date_to, $random_string) && $yacht && $date_from && $date_to) {
                    $return = NausysBooking::createOption($clientUser->ID, $client, $date_from, $date_to, $yacht, $wp_prefix, $message, $ignoreOptions);
                    if ($return) {
                        return ['status' => "OK", 'ref' => $return];
                    }
                }
            }
        }
        return ['status' => "Error"];
    }

    public function actionRefresh()
    {
        $xmls = Xml::find()->all();
        foreach ($xmls as $xml) {
            $className = 'app\classes\booking\\' . $xml->class_name . 'Booking';
            $return    = $className::refreshBooking($xml->id);
            if (!$return) {
                echo "['return':'error']";
                return 0;
            }
        }
        echo "['return':'no_error']";
        return 1;
    }
    public function actionThreefreeyachts()
    {
        $content = 'haha';
        $request = Yii::$app->request;
        $destId = $request->get('dest') ? intval($request->get('dest')) : null;
        $prefix = $request->get('id') ? intval($request->get('id')) : null;
        if ($destId && $prefix) {
            $fileName = "/three_$destId.html"; 
            $path     = Yii::$app->basePath . '/cash/landing_page/prefix_' . $prefix; 
            if (file_exists($path . $fileName)) {
                $file = fopen($path . $fileName, 'r');
                $content = fread($file, filesize($path . $fileName));
                fclose($file);
                echo $content;
                return;
            } else {
                $ret = CashYachts::threefreeyachtsToCash();
                if (file_exists($path . $fileName)) {
                    $file = fopen($path . $fileName, 'r');
                    $content = fread($file, filesize($path . $fileName));
                    fclose($file);
                    echo $content;
                    return;
                }
                return;
            }
        }
        echo $content;
        return;
    }

    public function actionThree()
    {
        $ret = CashYachts::threeFreeYachtsToCash();
        if ($ret)
            return 1;
        return 0;
    }
    public function actionThree2()
    {
        $request = Yii::$app->request;
        $prefix = $request->get('id') ? intval($request->get('id')) : null;
        $ret = CashYachts::threeFreeYachtsToCash2($prefix);
        if ($ret)
            return 1;
        return 0;
    }
}
