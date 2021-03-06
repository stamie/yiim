<?php

namespace app\classes\booking;

use ACP\Column\Comment\Date;
use app\classes\Nausys;
use app\models\BoatOption;
use app\models\CheckInPeriod;
use app\models\Yacht;
use app\models\Xml as XmlModel;
use app\models\DestinationYachtCategory;
use app\models\PortsInCities;
use app\models\Cities;
use app\models\YachtCategory;
use app\models\YachtDatas1;
use app\models\Cash;
use app\models\YachtCash;
use app\models\StandardEquipment;
use app\models\YachtModel;
use app\classes\yacht\NausysYacht;
use app\models\TablePrefix;
use Codeception\Util\Xml;
use yii\db\Query;
use LiteSpeed\Data;

class NausysBooking extends Booking
{
    private static $freeYachtsSearcUrl = 'http://ws.nausys.com/CBMS-external/rest/yachtReservation/v6/freeYachtsSearch';
    private static $freeYachtsUrl      = 'http://ws.nausys.com/CBMS-external/rest/yachtReservation/v6/freeYachts';
    private static $createClient       = 'http://ws.nausys.com/CBMS-external/rest/booking/v6/createInfo/';
    private static $createOption       = 'http://ws.nausys.com/CBMS-external/rest/booking/v6/createOption/';
    private static $refreshOption      = 'http://ws.nausys.com/CBMS-external/rest/yachtReservation/v6/reservations';
    private static function isChecked(int $from, CheckInPeriod $checkInPeriod)
    {
        $day = intval(getdate($from)['wday']);
        if ($day === 1 && $checkInPeriod->check_in_monday === 1) {
            return 1;
        }
        if ($day === 2 && $checkInPeriod->check_in_tuesday === 1) {
            return 1;
        }
        if ($day === 3 && $checkInPeriod->check_in_wednesday === 1) {
            return 1;
        }
        if ($day === 4 && $checkInPeriod->check_in_thursday === 1) {
            return 1;
        }
        if ($day === 5 && $checkInPeriod->check_in_friday === 1) {
            return 1;
        }
        if ($day === 6 && $checkInPeriod->check_in_saturday === 1) {
            return 1;
        }
        if ($day === 0 && $checkInPeriod->check_in_sunday === 1) {
            return 1;
        }
        return 0;
    }

    public static function nextFreePeriod($from, $to, $yachtId)
    {
        $date_from = strtotime($from);
        $date_to   = strtotime($to);
        $xml = XmlModel::find()->where(['slug' => 'nausys'])->one();
        $xml_id = $xml ? $xml->id : 0;
        $yacht = Yacht::findOne($yachtId);
        $next_from = $date_from;
        $next_to   = $date_to;
        if ($xml_id && $yacht) {
            $yachts = array($yacht->xml_json_id);

            for ($addDays = 1; $addDays < self::YEAR_DAY; $addDays++) {
                $next_from += self::DAY_MINUTE;
                $next_to   += self::DAY_MINUTE;
                $from1 = date("d.m.Y", $next_from);
                $to1 = date("d.m.Y", $next_to);
                $from2 = date("Y-m-d", $next_from);
                $checkInPeriods = CheckInPeriod::find()->where("xml_id = $xml_id")->andWhere("yacht_id = {$yacht->xml_json_id}")
                    ->andWhere("datediff('$from2', date_from)>0")->andWhere("datediff('$from2', date_to)<=0")->all();
                if (is_array($checkInPeriods)) {
                    foreach ($checkInPeriods as $checkInPeriod) {
                        if ($checkInPeriod && $d = self::isChecked($next_from, $checkInPeriod)) {
                            if ($d === 1) {
                                $obj = self::freeYachtsConnect($from1, $to1, $yachts);
                                if (isset($obj) && isset($obj->freeYachts) && is_array($obj->freeYachts) && count($obj->freeYachts) > 0) {
                                    return $obj;
                                }
                            }
                        }
                    }
                }
            }
        }
        return null;
    }

    public static function prevFreePeriod($from, $to, $yachtId)
    {
        $date_from = strtotime($from);
        $date_to   = strtotime($to);
        $xml = XmlModel::find()->where(['slug' => 'nausys'])->one();
        $xml_id = $xml ? $xml->id : 0;
        $yacht = Yacht::findOne($yachtId);
        $yachts = array($yacht->xml_json_id);
        $prev_from = $date_from;
        $prev_to   = $date_to;
        for ($addDays = 1; $addDays < self::YEAR_DAY; $addDays++) {
            $prev_from -= self::DAY_MINUTE;
            $prev_to   -= self::DAY_MINUTE;
            $from = date("d.m.Y", $prev_from);
            $to = date("d.m.Y", $prev_to);
            $from2 = date("Y-m-d", $prev_from);
            $checkInPeriods = CheckInPeriod::find()->where("xml_id = $xml_id")->andWhere("yacht_id = {$yacht->xml_json_id}")
                ->andWhere("datediff('$from2', date_from)>0")->andWhere("datediff('$from2', date_to)<=0")->all();
            if ($prev_from >= strtotime(date('Y-m-d'))) {
                if (is_array($checkInPeriods)) {
                    foreach ($checkInPeriods as $checkInPeriod) {
                        if ($checkInPeriod && $d = self::isChecked($prev_from, $checkInPeriod)) {
                            if ($d === 1) {
                                $obj = self::freeYachtsConnect($from, $to, $yachts);
                                if (isset($obj) && isset($obj->freeYachts) && is_array($obj->freeYachts) && count($obj->freeYachts) > 0) {
                                    return $obj;
                                }
                            }
                        }
                    }
                }
            } else {
                break;
            }
        }
        return null;
    }

    public static function freeYachtsConnect($from, $to, $yachtIds = array())
    {
        $cred    = new Nausys();
        $authAndPostFields = json_encode(
            [
                'credentials' =>
                $cred->getCredentials(),
                'periodFrom' => $from,
                'periodTo'   => $to,
                'yachts'     => $yachtIds,
                'ignoreOptions' => true,
            ]
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$freeYachtsUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $authAndPostFields);
        $header = array('Content-Type: application/json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $exec = curl_exec($ch);
        curl_close($ch);
        $exec = json_decode($exec);
        if ($exec->status == 'OK') {
            return $exec;
        }
        return null;
    }

    private static function convertYachtList($obj, $xml_id, $is_sale, $order = []) //Order nem biztos hogy kell
    {
        $list = [];
        $Ids  = [];
        if (is_array($obj)) {
            foreach ($obj as $yacht) {
                $theYacht = Yacht::findOne(['xml_id' => $xml_id, 'xml_json_id' => $yacht->yachtId]);
                if (!$theYacht) {
                    $returnId = NausysYacht::syncroniseOneYacht($yacht->yachtId);
                    if ($returnId) {
                        $theYacht = Yacht::findOne($returnId);
                        $tablePrefix = TablePrefix::find()->all();
                        foreach ($tablePrefix as $prefix) {
                            $wpch = curl_init('https://data.boattheglobe.ca/web/wpsync/newposts?id=' . $prefix->id);
                            curl_exec($wpch);
                            curl_close($wpch);
                        }
                    }
                }
                if ($theYacht) {
                    $userPrice = floatval($yacht->price->clientPrice);
                    $listPrice = floatval($yacht->price->priceListPrice);
                    $location = intval($yacht->locationFromId);
                    $city = PortsInCities::findOne(['xml_json_port_id' => $location, 'xml_id' => $xml_id]);
                    $cityNameFrom = '';
                    if ($city) {
                        $cityNameFrom = Cities::findOne($city->cities_id);
                        if ($cityNameFrom)
                            $cityNameFrom =  $cityNameFrom->name;
                        else
                            $cityNameFrom = '';
                    }
                    $location = intval($yacht->locationToId);
                    $city = PortsInCities::findOne(['xml_json_port_id' => $location, 'xml_id' => $xml_id]);
                    $cityNameTo = '';
                    if ($city) {
                        $cityNameTo = Cities::findOne($city->cities_id);
                        if ($cityNameTo)
                            $cityNameTo =  $cityNameTo->name;
                        else
                            $cityNameTo = '';
                    }
                    $yachtProperties = [
                        'id' => $theYacht->id,
                        'date_from' => $yacht->periodFrom,
                        'date_to' => $yacht->periodTo,
                        'priceForUser' => $yacht->price->clientPrice,
                        'listPrice' => $yacht->price->priceListPrice,
                        'currency' => $yacht->price->currency,
                        'cityFrom' => $cityNameFrom,
                        'cityTo'   => $cityNameTo,
                        'xml_id'   => $xml_id,
                        'location_id' => $location,
                        'discounts' => isset($yacht->price->discounts) ? $yacht->price->discounts : array(),
                        'deposit' => isset($yacht->price->depositAmount) ? $yacht->price->depositAmount : null,
                        'depositWhenInsuredAmount' => isset($yacht->price->depositWhenInsuredAmount) ? $yacht->price->depositWhenInsuredAmount : null,
                        'status'  => $yacht->status,
                    ];
                    if (!$is_sale || ($userPrice < $listPrice)) {
                        $list[] = $yachtProperties;
                        $Ids[] = $yachtProperties['id'];
                    }
                }
            }
        }
        return ['list' => $list, 'Ids' => $Ids];
    }

    private static function onDay($from, $to, $page = 1, $resultsPerPage = self::RESULTS_PER_PAGE)
    {
        $cred   = new Nausys();
        $ignoreOptions = false;
        $resultsPerPage2 = 0;
        if ($page == 1) {
            $resultsPerPage2 = count(Yacht::find()->all());
        } else {
            $resultsPerPage2 = ($page) * $resultsPerPage;
        }
        $authAndPostFields = json_encode(
            [
                'credentials'    => $cred->getCredentials(),
                'periodFrom'     => $from,
                'periodTo'       => $to,
                'resultsPerPage' => $resultsPerPage2,
                'resultsPage'    => 1,
                'ignoreOptions' => $ignoreOptions]
        );
        return $authAndPostFields;
    }
    private static function arrayInsert($original, $inserted, $pos)
    {
        $or = $original;
        $ins = [$inserted];
        array_splice($or, $pos, 0, $ins);
        return $or;
    }
    private static function priceInsert($objList, $obj, $ascOrDesc)
    {
        $objList2 = $objList;
        $objPrice = floatval($obj["priceForUser"]);
        for ($index = 0; $index < count($objList2); $index++) {
            $price = floatval($objList2[$index]["priceForUser"]);
            if ($ascOrDesc == 1 && $objPrice > $price) {
                $objList2 =  self::arrayInsert($objList2, $obj, ($index));
                return $objList2;
            } else if ($ascOrDesc == 0 && $objPrice < $price) {
                $objList2 =  self::arrayInsert($objList2, $obj, ($index));
                return $objList2;
            }
        }
        $objList2[] = $obj;
        return $objList2;
    }
    private static function arrayMerge($obj1, $obj2, $xml_id, $orderBy = 2, $ascOrDesc = 0)
    {
        $objList = $obj1['list'];
        $objIds = $obj1['Ids'];
        if (isset($obj2['list']) && is_array($obj2['list'])) {
            foreach ($obj2['list'] as $key => $yachtProperties) {
                if (isset($yachtProperties['id']) && is_array($objIds) && !in_array($yachtProperties['id'], $objIds)) {
                    if ($orderBy == 2 && count($objIds) > 0) {
                        //2 - price 3 - yacht length 4 - yacht cabins 5 - yacht build year
                        $objList = self::priceInsert($objList, $yachtProperties, $ascOrDesc);
                        $objIds[] = $yachtProperties['id'];
                    } else {
                        $objList[] = $yachtProperties;
                        $objIds[] = $yachtProperties['id'];
                    }
                }
            }
        }
        return ['list' => $objList, 'Ids' => $objIds];
    }
    static function freeYachtsSearch($date_from, $duration, $flexibility = "on_day", $attributes, $xml_id, $is_sale = 0, $orderBy = 2, $ascOrDesc = 0)
    {
        $date_to = strtotime($date_from) + (intval($duration) * self::DAY_MINUTE);
        $to = date("d.m.Y", $date_to);
        $from = date("d.m.Y", strtotime($date_from));
        $authAndPostFields = [];
        $page = 1;
        $resultsPerPage = count(Yacht::find()->all()) + 100000;
        $Obj = array('list' => [], 'Ids' => [], 'count' => null);
        $order = [];
        if ($orderBy > 2) {
            $order = [
                'orderby' => $orderBy,
                'desc'    => $ascOrDesc
            ];
        }
        $authAndPostFields[] = self::onDay($from, $to, $page, $resultsPerPage);
        foreach ($authAndPostFields as $auth) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$freeYachtsSearcUrl);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 400);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $auth);
            $header = array('Content-Type: application/json');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $exec = curl_exec($ch);
            curl_close($ch);
            if (!$exec) {
                return false;
            }
            $exec = json_decode($exec);
            $obj = array();
            if ($exec->status == 'OK') {
                $obj = $exec->freeYachtsInPeriod;
                $obj = self::convertYachtList($obj, $xml_id, $is_sale, $order);
                $Obj = self::arrayMerge($Obj, $obj, $xml_id, $orderBy, $ascOrDesc);
            } else {
                break;
            }
        }
        $count = is_array($Obj['Ids']) ? count($Obj['Ids']) : 0;
        return isset($Obj) ? $Obj : ['list' => [], 'count' => $count];
    }
    static function freeYachtsSearch2($date_from, $duration, $flexibility = "on_day", $attributes, $xml_id, $is_sale = 0, $orderBy = 2, $ascOrDesc = 0)
    {
        $ports   = (is_array($attributes['ports']) && count($attributes['ports']) > 0) ? $attributes['ports'] : null;
        $from = date("Y-m-d", strtotime($date_from));
        $authAndPostFields = [];
        $page = isset($attributes['page_num']) ? intval($attributes['page_num']) : 1;
        switch ($flexibility) {
            case 'on_day':
                $authAndPostFields[] = $from;
                break;
                /*    case "on_week":
                $authAndPostFields = self::onWeek($from, $to, $attributes, $orderBy, $ascOrDesc, $page);
                break;
            case "in_week":
                $authAndPostFields = self::inWeek($from, $to, $attributes, $orderBy, $ascOrDesc, $page);
                break;
            case "one_week":
                $authAndPostFields = self::oneWeek($from, $to, $attributes, $orderBy, $ascOrDesc, $page);
                break;
            case "two_weeks":
                $authAndPostFields = self::twoWeeks($from, $to, $attributes, $orderBy, $ascOrDesc, $page);
                break;
            case "in_month":
                $authAndPostFields = self::inMonth($from, $to, $attributes, $orderBy, $ascOrDesc, $page);
                break;
                */
            default:
                $authAndPostFields[] = $from;
                break;
        }
        $Obj = array('list' => [], 'Ids' => [], 'count' => null);
        foreach ($authAndPostFields as $auth) {
            $date_from_ = $auth;
            if (!$date_from_) {
                return ['list' => [], 'count' => null];
            }
            $date_from_ = date('Y-m-d', strtotime($date_from_));
            // 7 napos periodus
            $yachtCashes = YachtCash::find()->where(["xml_id" => $xml_id, "date_from" => $date_from_]);
            if (is_array($ports)) {
                $yachtCashes->andWhere(["location_id" => $ports]);
            }
            //Model szerinti sz??r??s ha van
            if (isset($attributes['args']['models']) && $attributes['args']['models'] != '-') {
                $yachtCashes->andWhere(["model" => $attributes['args']['models']]);
            }
            if (is_array($attributes['yacht_categories']) && count($attributes['yacht_categories']) > 0) {
                $yachtCategories = $attributes['yacht_categories'];
                $yachtCashes->andWhere(["category" => $yachtCategories]);
            }
            if (isset($attributes['args']['minBerth']) || isset($attributes['args']['maxBerth'])) {
                $minBerths = isset($attributes['args']['minBerth']) ? (intval($attributes['args']['minBerth'])) : 0;
                $maxBerths = isset($attributes['args']['maxBerth']) ? intval($attributes['args']['maxBerth']) : -1;
                $yachtCashes->andWhere("beds >= {$minBerths}");
                if ($maxBerths>-1){
                    $yachtCashes->andWhere("beds <= {$maxBerths}");
                }
            }
            if (isset($attributes['args']['minLength']) || isset($attributes['args']['maxLength'])) {
                $minLength = isset($attributes['args']['minLength']) ? (intval($attributes['args']['minLength'])) : 0;
                $maxLength = isset($attributes['args']['maxLength']) ? intval($attributes['args']['maxLength']) : -1;
                $yachtCashes->andWhere("length >= {$minLength}");
                if ($maxBerths>-1){
                    $yachtCashes->andWhere("length <= {$maxLength}");
                }
            }
            if (isset($attributes['args']['cabins'])) { //} && is_array($attributes['args']['cabins']) && count($attributes['args']['cabins']) > 0) {
                $where = "( 0 or ";
                foreach ($attributes['args']['cabins'] as $cabinNum) {
                    if ($cabinNum != '6+')
                        $where .= "cabins = {$cabinNum} or ";
                    else 
                        $where .= "cabins >= 6 or ";
                }
                $where = trim($where, "or ").")"; // var_dump($where);
                $yachtCashes->andWhere($where);
            }
            $returnCount = count($Obj['list']); // ($Obj);
            //Optional extras szerinti sz??r??s ha van
            // lefejlesztve NAUSYS Bareboat or creawed De a Cabin Flottilla Powered Berth All incluive nincs lefejlesztve!!!!
           /*
            if (isset($attributes['args']['selectedServiceTypes']) && $attributes['args']['selectedServiceTypes'] != "All") { // (isset($attributes['args']['selectedServiceTypes']);
                $Obj = parent::search_boats_with_service_types($attributes['args']['selectedServiceTypes'], $Obj);
            }
            */
            $Obj['count'] = $yachtCashes->count();
            $yachtCashes->limit(self::RESULTS_PER_PAGE)->offset(($page - 1) * self::RESULTS_PER_PAGE);
            switch ($orderBy) {
                case 2:
                    if (!$ascOrDesc)
                        $yachtCashes->orderBy(['user_price' => SORT_ASC]);
                    else
                        $yachtCashes->orderBy(['user_price' => SORT_DESC]);
                    break;
                case 3:
                    if (!$ascOrDesc)
                        $yachtCashes->orderBy(['length' => SORT_ASC]);
                    else
                        $yachtCashes->orderBy(['length' => SORT_DESC]);
                    break;
                case 4:
                    if (!$ascOrDesc)
                        $yachtCashes->orderBy(['cabins' => SORT_ASC]);
                    else
                        $yachtCashes->orderBy(['cabins' => SORT_DESC]);
                    break;
                case 5:
                    if (!$ascOrDesc)
                        $yachtCashes->orderBy(['builder_year' => SORT_ASC]);
                    else
                        $yachtCashes->orderBy(['builder_year' => SORT_DESC]);
                    break;
                case 6:
                    if (!$ascOrDesc)
                        $yachtCashes->orderBy(['beds' => SORT_ASC]);
                    else
                        $yachtCashes->orderBy(['beds' => SORT_DESC]);
                    break;
                case 7:
                    if (!$ascOrDesc)
                        $yachtCashes->orderBy(['capacity' => SORT_ASC]);
                    else
                        $yachtCashes->orderBy(['capacity' => SORT_DESC]);
                    break;
                default:
                    $yachtCashes->orderBy(['user_price' => SORT_ASC]);
                    break;
            }
            $yachtesFromCash = $yachtCashes->all();
            foreach ($yachtesFromCash as $value) {
                $Obj['list'][] = json_decode($value->json_value, true);
            }
        }
        $Obj['perPage'] = self::RESULTS_PER_PAGE;
        return isset($Obj) ? $Obj : ['list' => [], 'count' => null];
    }
    public static function yachtSearch($boat_id, $date_from, $date_to, $xml_id)
    {
        $yacht  =  Yacht::findOne(['xml_json_id' => $boat_id, 'xml_id' =>$xml_id]);
        if ($yacht){
            $yachtCash = YachtCash::findOne(['yacht_id' => $yacht->id, 'date_from' => $date_from]);
            if ($yachtCash){
                $return = json_decode($yachtCash->json_value, true);
                return $return;
            }
        }
        return array();
    }
    protected static function createClient($user_id, $client, $from, $to, $yachtId)
    {
        $cred    = new Nausys();
        $date_from = strtotime($from);
        $date_to   = strtotime($to);
        $to2 = date("d.m.Y", $date_to);
        $from2 = date("d.m.Y", $date_from);
        $authAndPostFields = json_encode(
            [
                'client'     => $client,
                'credentials' => $cred->getCredentials(),
                'yachtID'    => $yachtId,
                'periodFrom' => $from2,
                'periodTo'   => $to2,
                "onlinePayment" => "false"
            ]
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$createClient);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $authAndPostFields);
        $header = array('Content-Type: application/json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $exec = curl_exec($ch);
        curl_close($ch);
        $exec = json_decode($exec, true);
        if (isset($exec["status"]) && $exec["status"] == 'OK') {
            return $exec;
        }
        return null;
    }
    public static function createOption($user_id, $client, $from, $to, $yacht, $wp_prefix, $message = '', $ignoreOptions = '0')
    {
        $cred    = new Nausys();
        $ignoreO = ($ignoreOptions == '1') ? 1 : 0;
        $execArray = self::createClient($user_id, $client, $from, $to, $yacht->xml_json_id);
        if ($execArray) {
            $execArray["credentials"] = $cred->getCredentials();
            unset($execArray["status"]);
            $execArray["createWaitingOption"] = ($execArray["waitingForOption"] == true && $ignoreO) ? "true" : "false";
            $authAndPostFields = json_encode($execArray);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$createOption);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $authAndPostFields);
            $header = array('Content-Type: application/json');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $exec = curl_exec($ch);
            curl_close($ch);
            $exec = json_decode($exec, true);
            if (isset($exec["status"]) && $exec["status"] == 'OK') {
                return BoatOption::createOptionNausys($user_id, $client, $exec["reservationStatus"], $exec["id"], $from, $to, $yacht->id, $exec['priceListPrice'], $exec['clientPrice'], $exec['currency'], $wp_prefix, $message);
            }
        }
        BoatOption::createWrongOptionNausys($user_id, $client, $from, $to, $yacht->id, $wp_prefix);
        return null;
    }
    public static function refreshBooking(int $xml_id)
    {
        $cred = new Nausys();
        $date = date('Y-m-d', strtotime('-1 month'));
        $needRefreshes = BoatOption::find()->where("period_from > '{$date}' and xml_json_id is not null")->all();
        $needRefreshArray = [];
        foreach ($needRefreshes as $needRefresh) {
            $needRefreshArray[] = $needRefresh->xml_json_id;
        }
        $execArray = [];
        $execArray["credentials"] = $cred->getCredentials();
        $execArray["reservations"] = $needRefreshArray;
        $authAndPostFields = json_encode($execArray);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$refreshOption);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $authAndPostFields);
        $header = array('Content-Type: application/json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $exec = curl_exec($ch);
        curl_close($ch);
        $exec = json_decode($exec, true);
        if ($exec && isset($exec["status"]) && $exec["status"] == "OK") {
            foreach ($exec["reservations"] as $reservation) {
                $id                = intval($reservation["id"]);
                $reservationStatus = $reservation["reservationStatus"];
                $boatOption = BoatOption::findOne(["xml_json_id" => $id]);
                if ($boatOption && $boatOption->reservation_status !== $reservationStatus) {
                    $boatOption->reservation_status = $reservationStatus;
                    $boatOption->modify_date        = date(self::$dateString);
                    $boatOption->save(0);
                }
            }
            return 1;
        }
        return 0;
    }
    static function threeFreeYachtsSearch($xml_id, $yacht_category, $ports = null, $is_sale = 1)
    {
        $yachtCategoriesStrings  = ["Sailing yacht", "Motor yacht", "Motor boat", "Catamaran", "Luxury sailing yacht"];
        $attributes = [];
        $yachtCategories = YachtCategory::find()->where(["name" => $yachtCategoriesStrings])->all();
        foreach ($yachtCategories as $yachtCat) {
            if ($yachtCat->name == $yacht_category) {
                $attributes['yacht_categories'] = [$yachtCat->name];
                if (is_array($ports) && count($ports))
                    $attributes['ports'] = $ports;
                $duration = 7;
                $index = -1;
                $return = null;
                while ($index < 20) {
                    $index++;
                    $start_from = self::START_FROM + $index;
                    $date_from = date("Y-m-d", strtotime("next saturday"));
                    $date_from = strtotime($date_from) + (7 * $start_from * self::DAY_MINUTE);
                    $from = date("Y-m-d", $date_from);
                    $Obj = array('list' => [], 'Ids' => [], 'count' => null);
                    $count = 0;
                    $Obj = self::freeYachtsSearch2($from, $duration, "on_day", $attributes, $xml_id); //var_dump($Obj); // exit;
                    $count = (isset($Obj['list']) && is_array($Obj['list'])) ? count($Obj['list']) : 0;
                    var_dump($count); //exit;
                    $randomNumber = 0;
                    $return = null;
                    if ($count > 0) {
                        $randomNumber = rand(0, $count - 1);
                        $return = (isset($Obj) && isset($Obj['list']) && isset($Obj['list'][$randomNumber])) ? $Obj['list'][$randomNumber] : null;
                    }
                    if ($return)
                        return $return;
                }
            }
        }
        return null;
    }
}
