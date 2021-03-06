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

use app\classes\booking\Booking;
use app\models\Cities;
use app\models\TablePrefix;
use app\models\Xml;
use app\models\PortsInCities;
use app\models\Cash;
use app\models\CashLog;
use app\models\YachtCash;
use app\models\Yacht;
use app\classes\booking\Cashing;
use app\models\YachtCategory;
use app\models\YachtDatas1;
use app\models\YachtDatas3;
use app\models\YachtModel;

class CashController extends Controller
{
    public function actionCasher($is_run = 0)
    {
        // ha CashLog-ban van olyan end date ami üres, s nem szabad figyelmenkívül hagynia, akkor 
        $allCashLogs = CashLog::find()->where('end_datetime is null')->all();
        $cashLog = new CashLog();
        $cashLog->start_datetime = date(\app\classes\Sync::$dateString);
        $cashLog->type = 'yacht cash';
        $cashLog->save();

        if ($is_run == 1) {
            //Összes null végű CashLog lezárása
            if (is_array($allCashLogs)) {
                foreach ($allCashLogs as $cashLogNeedClose) {
                    $d = date(\app\classes\Sync::$dateString);
                    $cashLogNeedClose->end_datetime = $d;
                    $cashLogNeedClose->ret_value = 'ERROR (UNDEFINIED)';
                    $cashLogNeedClose->save(0);
                }
            }
        } else {
            if (is_array($allCashLogs) && count($allCashLogs) > 0) {
                $d = date(\app\classes\Sync::$dateString);
                $cashLog->end_datetime = $d;
                $cashLog->ret_value = 'ERROR (RUN ANY JOBS)';
                $cashLog->save(0);
                return ExitCode::UNSPECIFIED_ERROR;
            }
        }

        $date_from = date('Y-m-d', strtotime('next Saturday'));
        for ($index = 0; $index < 52; $index++) {
            $return = $this->freeyachtstothecash($date_from);
            $date_from = strtotime($date_from) + (7 * Booking::DAY_MINUTE);
            $date_from = date('Y-m-d', $date_from);
            if (!$return) {
                $d = date(\app\classes\Sync::$dateString);
                $cashLog->end_datetime = $d;
                $cashLog->ret_value = 'ERROR (SYNCRON)';
                $cashLog->save(0);
                return ExitCode::UNSPECIFIED_ERROR;
            }
        }

        $d = date(\app\classes\Sync::$dateString);
        $cashLog->end_datetime = $d;
        $cashLog->ret_value = 'OK';
        $cashLog->save(0);

        return ExitCode::OK;
    }
    public function freeyachtstothecash($date_from)
    {
        $flexibility = 'on_day';
        $is_sale = 0;
        $orderBy = 0;
        $orderBy = 2;
        $ascOrDesc = 0;
        $ascOrDesc = 0;
        $ignoreOptions = '0';

        $page_num = 1;
        $lists = ['ignoreOptions' => $ignoreOptions, 'page_num' => $page_num];

        $booking = [];
        $exec = '';
        $return = 1;

        for ($index = 1; $index < 2; $index++) {
            $duration = 7 * $index;
            foreach (Xml::find()->all() as $xml) {
                $bookingClasses = 'app\classes\booking\\' . $xml->class_name . 'Booking';
                $booking1 = array();
                $booking1 = $bookingClasses::freeYachtsSearch($date_from, $duration, $flexibility, $lists, $xml->id, $is_sale, $orderBy, $ascOrDesc);
                $booking = $booking1; //array_merge($booking, $booking1); <-- fejlesztendő
                //var_dump($booking);
                if (!$booking)
                    return false;

                $exec = json_encode($booking);
                if ($exec) {
                    $cashModel = Cash::findOne(['from_date' => $date_from, 'duration' => $duration, 'xml_id' => $xml->id]);
                    if (!$cashModel) {
                        $cashModel = new Cash();
                        $cashModel->create_date_time = date(\app\classes\Sync::$dateString);
                    } else {
                        $cashModel->update_date_time = date(\app\classes\Sync::$dateString);
                    }
                    $cashModel->from_date        = $date_from;
                    $cashModel->xml_id           = $xml->id;
                    $cashModel->json_value       = $exec;
                    $cashModel->duration         = $duration;
                    $return = $return && $cashModel->save(0);
                }
            }
        }
        return $return;
    }

    public function actionYachtcasher()
    {
        $update_datetime = date("Y-m-d H:i:s");
        $date_from = date('Y-m-d');
        YachtCash::deleteAll("'$date_from' > date_from");
        $cashes = Cash::find()->where("'$date_from' <= from_date")->all();
        foreach ($cashes as $cash) {
            $exec = $cash->json_value;
            if ($exec && $exec != 'false') {
                $exec = json_decode($exec, true);
                if (isset($exec["list"])) {
                    $list = $exec["list"];
                    if ($list && is_array($list)) {
                        foreach ($list as $elem) {
                            $yachtCash = YachtCash::findOne(["yacht_id" => $elem["id"], 'date_from' => $cash->from_date]);
                            $yacht = Yacht::findOne($elem["id"]);

                            if (empty($yachtCash)) {
                                $yachtCash = new YachtCash();
                                $yachtCash->yacht_id    = $elem["id"];
                                $yachtCash->date_from   = $cash->from_date;
                                $yachtCash->json_value  = json_encode($elem);
                                $yachtCash->xml_id      = $cash->xml_id;
                                $yachtCash->location_id = $elem["location_id"];
                                if ($yacht) {
                                    $yachtModel            = YachtModel::findOne(["xml_json_id" => $yacht->yacht_model_id, "xml_id" => $yacht->xml_id]);
                                    if (!$yachtModel) {
                                        $yachtCash->model_id   = $yacht->yacht_model_id;
                                        $yachtCash->model      = 'boat1';
                                    }
                                    if ($yachtModel) {
                                        $yachtCash->model_id   = $yachtModel->xml_json_id;
                                        $yachtCash->model      = $yachtModel->name;
                                        $yachtCategory         = YachtCategory::findOne(['xml_id' => $yachtModel->xml_id, 'xml_json_id' => $yachtModel->category_xml_id]);
                                        $yachtCash->length     = $yachtModel->loa;
                                        $yachtCash->category   = $yachtCategory->name;
                                        $yachtCash->user_price = 0;
                                        $yachtCash->save();
                                    }
                                }
                            }
                            if ($yacht && $yachtCash && $yachtCash->user_price != floatval($elem["priceForUser"])) {
                                $yachtCash->user_price = floatval($elem["priceForUser"]);
                                $yachtCash->currency   = $elem["currency"];
                                $yacht1 = YachtDatas1::findOne($yacht->id);
                                if ($yacht1) {
                                    $yachtCash->beds = $yacht1->berths_total;
                                    $yachtCash->cabins = $yacht1->cabins;
                                }
                                $yacht3 = YachtDatas3::findOne($yacht->id);
                                if ($yacht3)
                                    $yachtCash->service_types = $yacht3->charter_type;
                                $yachtCash->capacity = $yacht->max_person;
                                $yachtCash->builder_year = $yacht->build_year;
                            }
                            if ($yachtCash) {
                                $yachtCash->update_datetime = $update_datetime;
                                $yachtCash->save(0);
                            }
                        }
                    }
                }
            }
        }
        YachtCash::deleteAll("'$update_datetime' > update_datetime");
        return ExitCode::OK;
    }
}
