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
use app\classes\booking\Cashing;


use function YoastSEO_Vendor\GuzzleHttp\json_decode;

class CashController extends Controller
{
    public function actionCasher($is_run = 0)
    {
        // ha CashLog-ban van olyan end date ami üres, s nem szabad figyelmenkívül hagynia, akkor 
        $allCashLogs = CashLog::find()->where('end_datetime is null')->all();
        $cashLog = new CashLog();
        $cashLog->start_datetime = date('Y-m-d H:i:s');
        $cashLog->type = 'yacht cash';
        $cashLog->save();
        
        if ($is_run == 1) {
            //Összes null végű CashLog lezárása
            if (is_array($allCashLogs)){
            foreach($allCashLogs as $cashLogNeedClose){
                $d = date('Y-m-d H:i:s');
                $cashLogNeedClose->end_datetime = $d;
                $cashLogNeedClose->ret_value = 'ERROR (UNDEFINIED)';
                $cashLogNeedClose->save(0);
            }}
        } else {
            if (is_array($allCashLogs) && count($allCashLogs) > 0){
                $d = date('Y-m-d H:i:s');
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
                $d = date('Y-m-d H:i:s');
                $cashLog->end_datetime = $d;
                $cashLog->ret_value = 'ERROR (SYNCRON)';
                $cashLog->save(0);
                return ExitCode::UNSPECIFIED_ERROR;
            }
        }
        
        $d = date('Y-m-d H:i:s');
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
                if(!$booking)
                    return false;

                $exec = json_encode($booking);
                if ($exec) {
                    $cashModel = Cash::findOne(['from_date' => $date_from, 'duration' => $duration, 'xml_id' => $xml->id]);
                    if (!$cashModel) {
                        $cashModel = new Cash();
                        $cashModel->create_date_time = date('Y-m-d H:i:s');
                    } else {
                        $cashModel->update_date_time = date('Y-m-d H:i:s');
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
}
