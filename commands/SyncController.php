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
use app\models\TablePrefix;
use app\models\Xml;
use app\models\Company;
use app\models\SyncronLog;
use app\classes\Wpsync;
use app\models\CashLog;

class SyncController extends Controller
{

    private $parentLogId = 0;


    /**
     * 
     * ### Országok szinkronja (XXXXX)
     * 
     */
    public function actionPrefix($id)
    {

        $tablePrefix = TablePrefix::findOne($id);

        $prefix = '';
        $prId = 0;
        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;
        }

        return ExitCode::OK;
    }

    public function actionIndex()
    {
        $id = 1;
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Big Syncron');

        $this->parentLogId = $log->id;

        if ($this->actionCountry($id))
            echo ("REndben lezajlott az ország szinkron");

        if ($this->actionYachtbuilder($id))
            echo "REndben lezajlott a YachtBuilder szinkron";

        if ($this->actionEquipmentcategory($id))
            echo "REndben lezajlott az Equipmentcategory szinkron";

        if ($this->actionEnginebuilder($id))
            echo "REndben lezajlott az Enginebuilder szinkron";

        if ($this->actionEquipment($id))
            echo "REndben lezajlott az Equipment szinkron";

        if ($this->actionYachtcategory($id))
            echo "REndben lezajlott a Yachtcategory szinkron";

        if ($this->actionYachtmodel($id))
            echo "REndben lezajlott a Yachtmodel szinkron";

        if ($this->actionRegion($id))
            echo "REndben lezajlott a Region szinkron";

        if ($this->actionBase($id))
            echo "REndben lezajlott a Base szinkron";

        if ($this->actionPort($id))
            echo "REndben lezajlott a Port szinkron";

        if ($this->actionSteeringtype($id))
            echo "REndben lezajlott a Steeringtype szinkron";
        if ($this->actionSteeringtype($id))
            echo "REndben lezajlott a PriceMeasure szinkron";

        if ($this->actionSailtype($id))
            echo "REndben lezajlott a Service szinkron";

        if ($this->actionService($id))
            echo "REndben lezajlott a Service szinkron";

        if ($this->actionCompany($id))
            echo "REndben lezajlott a Company szinkron";

        if ($this->actionDiscountitem($id))
            echo "REndben lezajlott a Discount Itemek szinkronja";

        if ($this->actionSeason($id))
            echo "REndben lezajlott a Szezonok szinkronja";


        if ($this->actionYacht($id)) {
            echo "REndben lezajlott a Yacht szinkron";
            // $wpSyncController = new Wpsync();
            // $wpSyncController->savePosts($id); 


        } else echo "Yacht szinkron probléma";

        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));

        return ExitCode::OK;
    }
    public function actionLittlesync($id)
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Little Syncron');
        if ($this->actionYacht($id)) {
            echo "REndben lezajlott a Yacht szinkron";
            $wpSyncController = new Wpsync();
            $wpSyncController->savePosts($id);
        } else echo "Yacht szinkron probléma";

        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));

        return ExitCode::OK;
    }

    /**
     * 
     * ### PriceMeasure szinkronja (XXXXX)
     * 
     */
    public function actionPricemeasure($id, $exit = null)
    {

        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Price Measure Syncron', $this->parentLogId);



        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;

            $xmls = Xml::find()->all();

            foreach ($xmls as $xml) {
                $class = "app\classes\pricemeasure\\" . $xml->class_name . "PriceMeasure";

                $ret = $class::syncronise($prId);
                $return = $return && $ret;
            }
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }
    /**
     * 
     * ### Országok szinkronja (XXXXX)
     * 
     */
    public function actionCountry($id, $exit = null)
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Country Syncron', $this->parentLogId);

        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;

            $xmls = Xml::find()->all();

            foreach ($xmls as $xml) {
                $countryClass = "app\classes\country\\" . $xml->class_name . "Country";

                $countryRet = $countryClass::syncronise($prId);
                $return = $return && $countryRet;
            }
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }

    /**
     * 
     * ### Felszereltség kategória (equipment categories) szinkronja (XXXXX)
     * 
     */

    public function actionEquipmentcategory($id, $exit = null) //Fejlesztendő
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Equipment Category Syncron', $this->parentLogId);

        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'equipmentcategory';
        $className = 'EquipmentCategory';
        $dirName = 'equipmentCategory';
        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;
            $xmls = Xml::find()->all();
            foreach ($xmls as $xml) {

                $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
                $returnObj = $class::syncronise($prId);
                $return = $return && $returnObj;
            }
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }

    /**
     * 
     * ### Yacht építők szinkronja (XX)
     * 
     */
    public function actionYachtbuilder($id, $exit = null) //Fejlesztendő
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Yacht Builder Syncron', $this->parentLogId);
        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'yachtbuilder';
        $className = 'YachtBuilder';
        $dirName = 'yachtBuilder';
        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;
            $xmls = Xml::find()->all();
            foreach ($xmls as $xml) {
                $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
                $returnObj = $class::syncronise($prId);
                $return = $return && $returnObj;
            }
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }

    /**
     * 
     * ### Yacht motor gyártók szinkronja (XX)
     * 
     */
    public function actionEnginebuilder($id, $exit = null) //Fejlesztendő
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Engine Builder Syncron', $this->parentLogId);

        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'enginebuilder';
        $className = 'EngineBuilder';
        $dirName = 'engineBuilder';


        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;

            $xmls = Xml::find()->all();

            foreach ($xmls as $xml) {

                $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
                $returnObj = $class::syncronise($prId);
                $return = $return && $returnObj;
            }
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }

    /**
     * 
     * ### Felszereltség (equipment) szinkronja (XXXXX)
     * 
     */
    public function actionEquipment($id, $exit = null)
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Equipment Syncron', $this->parentLogId);

        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'equipment';
        $className = 'Equipment';
        $dirName = 'equipment';

        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;

            $xmls = Xml::find()->all();

            foreach ($xmls as $xml) {

                $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
                $returnObj = $class::syncronise($prId);
                $return = $return && $returnObj;
            }
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }

    /**
     *
     * ### Yacht kategóriák szinkronja (XX)
     * 
     */
    public function actionYachtcategory($id, $exit = null) //Fejlesztendő
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Yacht Category Syncron', $this->parentLogId);

        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'yachtcategory';
        $className = 'YachtCategory';
        $dirName = 'yachtCategory';


        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;

            $xmls = Xml::find()->all();

            foreach ($xmls as $xml) {

                $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
                $returnObj = $class::syncronise($prId);
                $return = $return && $returnObj;
            }
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }
    /**
     *
     * ### Yacht modelek szinkronja (XX)
     * 
     */
    public function actionYachtmodel($id, $exit = null) //Fejlesztendő
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Yacht Model Syncron', $this->parentLogId);
        $allCashLogs = CashLog::find()->where('end_datetime is null')->all();
        $cashLog = new CashLog();
        $cashLog->start_datetime = date('Y-m-d H:i:s');
        $cashLog->type = 'yacht cash';
        $cashLog->save();
        
        if (is_array($allCashLogs) && count($allCashLogs) > 0){
            $d = date('Y-m-d H:i:s');
            $cashLog->end_datetime = $d;
            $cashLog->ret_value = 'ERROR (RUN ANY JOBS)';
            $cashLog->save(0);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'yachtmodel';
        $className = 'YachtModel';
        $dirName = 'yachtModel';
        

        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;

            $xmls = Xml::find()->all();

            foreach ($xmls as $xml) {

                $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
                $returnObj = $class::syncronise($prId);
                $return = $return && $returnObj;
            }
        } 
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        if ($return){
            $d = date('Y-m-d H:i:s');
            $cashLog->end_datetime = $d;
            $cashLog->ret_value = 'OK';
            $cashLog->save(0);
            return ExitCode::OK;
        }
        $d = date('Y-m-d H:i:s');
        $cashLog->end_datetime = $d;
        $cashLog->ret_value = 'ERROR (SYNCRON)';
        $cashLog->save(0);

        return ExitCode::UNSPECIFIED_ERROR;
            
    }
    /**
     *
     * ### Yachtok szinkronja (XX)
     * 
     */
    public function actionYacht($id, $need_picture = 0, $exit = null) //Fejlesztendő
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Yacht Syncron', $this->parentLogId);

        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'yacht';
        $className = 'Yacht';
        $dirName = 'yacht';

        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;
            $xmls = Xml::find()->all();
            foreach ($xmls as $xml) {
                $companies = Company::findAll(["xml_id" => $xml->id]);
                if (is_array($companies)) {
                    foreach ($companies as $company) {
                        $returnObj = $this->actionYachtwithcompany($id, $company->id, $need_picture, 1);
                        $return = $return && $returnObj;
                    }
                }
            }
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }
    /**
     *
     * ### Yachtok szinkronja (XX)
     * 
     */
    public function actionYachtwithcompany($id, $companyId, $need_picture = 0, $exit = null)
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Yacht Syncron', $this->parentLogId);

        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'yacht';
        $className = 'Yacht';
        $dirName = 'yacht';

        $xmls = Xml::find()->all();

        foreach ($xmls as $xml) {

            $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
            $returnObj = $class::yachtSynronise($prId, $companyId, $need_picture);
            $return = $return && $returnObj;
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return ExitCode::OK;
    }

    /**
     *
     * ### Yachtok szinkronja (XX)
     * 
     */
    public function actionYachtsprices($exit = null) //Fejlesztendő
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Yacht Syncron Prices', $this->parentLogId);

        $return = true;
        $view = 'yacht';
        $className = 'Yacht';
        $dirName = 'yacht';

        $xmls = Xml::find()->all();

        foreach ($xmls as $xml) {
            $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
            $returnObj = $class::syncroniseYachtsAndPrices($xml->id);
            $return = $return && $returnObj;
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return ExitCode::OK;
        
    }
    /**
     *
     * ### Yachtok szinkronja (XX)
     * 
     */
    public function actionYachtstequipment($exit = null) //Fejlesztendő
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Yacht Syncron StandardEquipment', $this->parentLogId);

        $return = true;
        $view = 'yacht';
        $className = 'Yacht';
        $dirName = 'yacht';

        $xmls = Xml::find()->all();

        foreach ($xmls as $xml) {
            $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
            $returnObj = $class::syncroniseYachtsAndStandardEquipment($xml->id);
            $return = $return && $returnObj;
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return ExitCode::OK;
        
    }
    /*
    * ### Yachtok szinkronja (XX)
    * 
    */
    public function actionYachtadequipment($exit = null) //Fejlesztendő
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Yacht Syncron AdditionalEquipment', $this->parentLogId);

        $return = true;
        $view = 'yacht';
        $className = 'Yacht';
        $dirName = 'yacht';

        $xmls = Xml::find()->all();

        foreach ($xmls as $xml) {
            $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
            $returnObj = $class::syncroniseYachtsAndAdditionalEquipment($xml->id);
            $return = $return && $returnObj;
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return ExitCode::OK;
        
    }
    /*
    * ### Yachtok szinkronja (XX)
    * 
    */
    public function actionYachtcheckinperiod($exit = null) //Fejlesztendő
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Yacht Syncron CheckInPeriod', $this->parentLogId);

        $return = true;
        $view = 'yacht';
        $className = 'Yacht';
        $dirName = 'yacht';

        $xmls = Xml::find()->all();

        foreach ($xmls as $xml) {
            $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
            $returnObj = $class::syncroniseYachtsAndCheckInPeriod($xml->id);
            $return = $return && $returnObj;
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return ExitCode::OK;
        
    }
    /**
     *
     * ### Yachtok szinkronja (XX)
     * 
     */
    public function actionYachtservices($minId = 1, $exit = null) //Fejlesztendő
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Yacht Syncron', $this->parentLogId);

        $return = true;
        $view = 'yacht';
        $className = 'Yacht';
        $dirName = 'yacht';



        $xmls = Xml::find()->all();

        foreach ($xmls as $xml) {

            $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
            $returnObj = $class::syncroniseYachtsAndServices($xml->id, $minId);
            $return = $return && $returnObj;
        }

        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return ExitCode::OK;
        
    }
    /**
     *
     * ### Regiók szinkronja
     * 
     */
    public function actionRegion($id, $exit = null)
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Region Syncron', $this->parentLogId);

        $view = 'region';
        $className = 'Region';
        $dirName = 'region';



        $xmls = Xml::find()->all();
        $return = true;
        foreach ($xmls as $xml) {

            $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
            $returnObj = $class::syncronise($xml->id);
            $return = $return && $returnObj;
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }

    /**
     *
     * ### Base szinkronja
     * 
     */
    public function actionBase($id, $exit = null)
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Base Syncron', $this->parentLogId);

        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'base';
        $className = 'Base';
        $dirName = 'base';


        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;

            $xmls = Xml::find()->all();

            foreach ($xmls as $xml) {

                $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
                $returnObj = $class::syncronise($prId);
                $return = $return && $returnObj;
            }
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }

    /**
     *
     * ### Locations szinkronja
     * 
     */
    public function actionPort($id, $exit = null)
    {

        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Ports Syncron', $this->parentLogId);

        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'port';
        $className = 'Port';
        $dirName = 'port';


        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;

            $xmls = Xml::find()->all();

            foreach ($xmls as $xml) {

                $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
                $returnObj = $class::syncronise($prId);
                $return = $return && $returnObj;
            }
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }
    /**
     *
     * ### Sail típusok szinkronja
     * 
     */
    public function actionSailtype($id, $exit = null) //Fejlesztendő
    {

        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Sail Type Syncron', $this->parentLogId);
        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'sailtype';
        $className = 'SailType';
        $dirName = 'sailtype';


        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;

            $xmls = Xml::find()->all();

            foreach ($xmls as $xml) {

                $class = "app\classes\\$dirName\\" . $xml->class_name . $className;

                $returnObj = $class::syncronise($prId);
                $return = $return && $returnObj;
            }
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }
    /**
     *
     * ### Steering típusok szinkronja
     * 
     */
    public function actionSteeringtype($id, $exit = null) //Fejlesztendő
    {
        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Steering Type Syncron', $this->parentLogId);

        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'steeringtype';
        $className = 'SteeringType';
        $dirName = 'steeringtype';


        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;

            $xmls = Xml::find()->all();

            foreach ($xmls as $xml) {

                $class = "app\classes\\$dirName\\" . $xml->class_name . $className;

                $returnObj = $class::syncronise($prId);
                $return = $return && $returnObj;
            }
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }

    /**
     *
     * ### Service szinkronja
     * 
     */
    public function actionService($id, $exit = null) //Fejlesztendő
    {

        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Service Syncron', $this->parentLogId);
        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'service';
        $className = 'Service';
        $dirName = 'service';


        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;

            $xmls = Xml::find()->all();

            foreach ($xmls as $xml) {

                $class = "app\classes\\$dirName\\" . $xml->class_name . $className;

                $returnObj = $class::syncronise($prId);
                $return = $return && $returnObj;
            }
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }

    /**
     *
     * ### Company szinkronja
     * 
     */
    public function actionCompany($id, $exit = null) //Fejlesztendő
    {

        $startDate = date('Y-m-d H:i:s');
        $log = SyncronLog::log($startDate, 'Company Syncron', $this->parentLogId);
        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'company';
        $className = 'Company';
        $dirName = 'company';


        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;

            $xmls = Xml::find()->all();

            foreach ($xmls as $xml) {

                $class = "app\classes\\$dirName\\" . $xml->class_name . $className;

                $returnObj = $class::syncronise($prId);
                $return = $return && $returnObj;
            }
        }
        $end_date = date('Y-m-d H:i:s');
        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }

    /**
     *
     * ### DiscountItem szinkronja
     * 
     */
    public function actionDiscountitem($id, $exit = null)
    {
        $isAutomate = 1;

        $startDate = date('Y-m-d H:i:s');

        $parentString2 = 'Discount Item Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);


        $log = SyncronLog::log($startDate, $parentString2, $this->parentLogId, $isAutomate);



        $request = Yii::$app->request;
        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'discountitemsync';
        $className = 'DiscountItem';
        $dirName = 'discountItem';


        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;

            $xmls = Xml::find()->all();

            foreach ($xmls as $xml) {

                $class = "app\classes\\$dirName\\" . $xml->class_name . $className;

                $returnObj = $class::syncronise($prId);
                $return = $return && isset($returnObj);
            }
        }

        $end_date = date('Y-m-d H:i:s');

        $log->end($end_date, json_encode(array()));
        return (($exit) ? ExitCode::OK : 1);
    }

    /**
     *
     * ### Season szinkronja
     * 
     */
    public function actionSeason($id, $exit = null)
    {
        $isAutomate = 1;


        $startDate = date('Y-m-d H:i:s');

        $parentString2 = 'Session Syncron';
        $log = SyncronLog::log($startDate, $parentString2, $this->parentLogId, $isAutomate);


        $request = Yii::$app->request;
        $tablePrefix = TablePrefix::findOne($id);
        $prefix = '';
        $prId = 0;
        $return = true;
        $view = 'seasonsync';
        $className = 'Season';
        $dirName = 'season';


        if ($tablePrefix) {
            $prefix = $tablePrefix->prefix;
            $prId = $tablePrefix->id;

            $xmls = Xml::find()->all();

            foreach ($xmls as $xml) {

                $class = "app\classes\\$dirName\\" . $xml->class_name . $className;

                $returnObj = $class::syncronise($prId);
                $return = $return && isset($returnObj);
            }
        }

        $end_date = date('Y-m-d H:i:s');

        $error = json_encode([]);
        $log->end($end_date, $error);

        return (($exit) ? ExitCode::OK : 1);
    }
}
