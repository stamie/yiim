<?php

namespace app\classes\booking;

use app\classes\Sync;
use app\models\Cash;
use app\models\Service;
use app\models\Yacht;
use app\models\YachtDatas1;
use app\models\YachtDatas3;
use app\models\YachtModel;
use app\models\Xml;
use app\models\YachtSeasonService;

class Booking extends Sync
{
    const RESULTS_PER_PAGE = 10;
    const DAY_MINUTE = 86400;
    const YEAR_DAY   = 365;
    const MAX_PAGE   = 2;
    const START_FROM = 4;
 /*   private static $equipmentsNausys = array(
        'air-conditioner' => [4],
        'watermaker' => [16],
        'generator' => [3],
        'electric-windlass' => [101704, 4263031, 485619, 113469],
        'outboard-motor' => [1213831, 1086052, 8769743, 8769744, 2854469, 118110, 14],
        'autopilot' => [17],
        'bowthruster' => [2],
        'electric-toilettes' => [107381],
        'furling-mainsail' => [0],
        //'Barbecue Grill' => [100500],
        'wifi' => [477829],
        'solar-panel' => [120913],
        'gps' => [24],
        'electric-winch' => [101704],
    );
    */
    private static $optionalExtras = array(
        'nausys' => array(
            'Cabin'         => array('Cabin conversion', 'Cabin kit', 'Conversion of two separate cabins into one', 'Extra charge for the superior cabin'),
            'Flotilla'      => array('Flotilla fee'),
            'Powered'       => array('Power cell'),
            'Berth'         => array('Annual berth', 'Daily berth / mooring', 'Daily berth in home port', 'Daily berth in the home port at the check-in / out day'),
            'Regatta'       => array('Comfort Package regatta Premium Plus', 'Comfort Package regatta Simple', 'Comfort package regatta', 'Damage waiver regatta', 'Deposit for regatta', 'Preparing the boat for the Regatta', 'Regatta', 'Regatta charge', 'Regatta package', 'Regatta surcharge', 'Service fee for the regatta'),
            'AllInclusive'  => array('All inclusive', 'All inclusive alcoholic package', 'All inclusive children', 'All inclusive domestic non alcoholic package', 'All inclusive domestic package', 'All inclusive package', 'All inclusive surcharge for two/more weeks'),
        ),
    );

    protected function search_boats_with_service_types(string $service_types, $Obj)
    {
        $Ids = isset($Obj["Ids"]) ? $Obj["Ids"] : null;
        if (is_array($Ids) && count($Ids) > 0) {
            $yachtDatas = [];
            $list = [];
            $ids = array();
            switch ($service_types) {
                case 'All':
                    return $Ids;
                case 'Bareboat':
                case 'Crewed':
                    //$sql = "SELECT id from yacht_datas3 where charter_type like upper('{$service_types}') and id {$list}";
                    $yachtDatas1 = YachtDatas3::find()->where(['id' => $Ids])->andWhere("charter_type like upper('$service_types')")->all();
                    $yachtDatas = array_merge($yachtDatas, $yachtDatas1);
                    break;
                case 'Cabin':
                case 'Flotilla':
                case 'Powered':
                case 'Berth':
                case 'All inclusive':
                    foreach (self::$optionalExtras as $xml => $optional) {
                        $Xml = Xml::findOne(['slug' => $xml]);
                        $services = Service::findAll(['xml_id' => $Xml->id, "name" => $optional[$service_types]]);
                        $yachts   = Yacht::findAll(['xml_id' => $Xml->id, 'id' => $Ids]);
                        $yachtXmlJsonIds =[];
                        foreach ($yachts as $yacht){
                            $yachtXmlJsonIds[] = $yacht->xml_json_id;
                        }
                        $serviceXmlJsonIds = [];
                        foreach ($services as $service) {
                            $serviceXmlJsonIds[] = $service->xml_json_id;
                        }
                        $yachtSeasonServices = YachtSeasonService::find()->where(['service_id' => $serviceXmlJsonIds,'yacht_id' => $yachtXmlJsonIds, 'xml'=> $Xml->id])->all();
                        $yachtXmlJsonIds = [];
                        foreach ($yachtSeasonServices as $yachtSeasonService) {
                            $yachtXmlJsonIds[] = $yachtSeasonService->yacht_id;
                        }
                        $yachtDatas1 = YachtDatas3::find()->where(['xml_json_id' => $yachtXmlJsonIds, 'xml_id' => $Xml->id])->all();
                        $yachtDatas = array_merge($yachtDatas, $yachtDatas1);
                    }
                    break;
                default:
                    return $Obj;
            }
            $Ids2 = [];
            if (is_array($yachtDatas)) {
                foreach ($yachtDatas as $id) {
                    $Ids2[] = $id->id;
                }
            }
            if (is_array($Obj["list"])) {
                foreach ($Obj["list"] as $obj) { //$obj);
                    if (is_array($Ids2) && in_array($obj['id'], $Ids2)) {
                        $list[] = $obj;
                    }
                }
            }
            return ["list" => $list, "Ids" => $Ids2];
        }
        return ["list" => [], "Ids" => []];
    }
    protected function discounts($discounts, $Obj)
    {
        $Ids = isset($Obj["Ids"]) ? $Obj["Ids"] : null;
        if (is_array($Ids) && count($Ids) > 0) {

            $yachtDatas = [];
            $list = [];
            $ids = array();
            foreach ($Obj['list'] as $key => $value) {
                $yacht = Yacht::findOne($value["id"]);
                if ($yacht && $yacht->xml_id == 1) {
                    $discountsOfYacht = $value['discounts'];
                    foreach ($discountsOfYacht as $discount) {
                        if (isset($discount['discountItemId'])) {
                            if (in_array($discount['discountItemId'], $discounts)) {
                                $yachtDatas[] = $yacht;
                                $list[] = $value;
                            }
                        } else if (isset($discount->discountItemId)) {
                            if (in_array($discount->discountItemId, $discounts)) {
                                $yachtDatas[] = $yacht;
                                $list[] = $value;
                            }
                        }
                    }
                }
            }
            $Ids2 = [];
            if (is_array($yachtDatas)) {
                foreach ($yachtDatas as $id) {
                    $Ids2[] = $id->id;
                }
            }
            return ["list" => $list, "Ids" => $Ids2];
        }
        return ["list" => [], "Ids" => []];
    }

    protected function findOnTheCash($date_from, $duration)
    {
        $return = [];
        $cash = Cash::findOne(['date_from' => $date_from]);
        if ($cash && $duration == 7) {
            $return = json_decode($cash->json_value, true);
            return $return;
        }
        return null;
    }
}
