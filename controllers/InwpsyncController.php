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

use AC\Column\User\Login;
use app\models\LoginForm;
use Yii;
use app\models\TablePrefix;
use app\models\Xml;
use app\models\Yacht;
use app\models\Posts;
use app\models\SyncronLog;
use app\classes\Wpsync;

class InwpsyncController extends \yii\web\Controller
{
const WPUSER = 'wpuser';
const RUNNER_ERROR = -2;
const AUTH_ERROR = -1;
const DONT_ERROR = 0;
const ERROR_BASE = 1;
const ERROR_COMPANY = 2;
const ERROR_COUNTRY = 3;
const ERROR_DISCOUNTITEM = 4;
const ERROR_ENGINEBUILDER = 5;
const ERROR_EQUIPMENT = 6;
const ERROR_EQUIPMENTCATEGORY = 7;
const ERROR_PORT = 8;
const ERROR_REGION = 9;
const ERROR_SAILTYPE = 10;
const ERROR_SEASON = 11;
const ERROR_SERVICE = 12;
const ERROR_STEERINGTYPE = 13;
const ERROR_YACHTBUILDER = 14;
const ERROR_YACHTCATEGORY = 15;
const ERROR_YACHTMODEL = 16;
const ERROR_YACHT = 17;
const ERROR_WPSYNC = 18;
const ERROR_PRICEMEASURE = 19;

/**
 * 
 * ### 
 * 
 */
    public function deleteOldYachtPosts() {
        foreach(TablePrefix::find()->all() as $id)
            Wpsync::deleteOldYachtPosts($id);
    }


    private function login($p){
        $model = new LoginForm();
        $model->username = self::WPUSER;
        $model->password = $p;
        $model->rememberMe = false;
        return $model->loginWithAjax();
    }

    public function actionBigSync($id, $p, $isAjax = true, $parentString = 'Big Syncron')
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        $startDate = date('Y-m-d H:i:s');

        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog)) {
            $log = SyncronLog::log($startDate, $parentString, 0, $isAutomate);
        
            $error = ['error' => self::AUTH_ERROR];
            
            if (!Yii::$app->user->isGuest) {
                $error = [];
            
                if (!$this->actionBase($id, $p, false, null, $log->id))
                $error[] = self::ERROR_BASE;
                if (!$this->actionCompany($id, $p, false, null, $log->id))
                $error[] = self::ERROR_COMPANY;
                if (!$this->actionCountry($id, $p, false, null, $log->id))
                $error[] = self::ERROR_COUNTRY;
                if (!$this->actionDiscountitem($id, $p, false, null, $log->id))
                $error[] = self::ERROR_DISCOUNTITEM;
                if (!$this->actionEnginebuilder($id, $p, false, null, $log->id))
                $error[] = self::ERROR_ENGINEBUILDER;
                if (!$this->actionEquipment($id, $p, false, null, $log->id))
                $error[] = self::ERROR_EQUIPMENT;
                if (!$this->actionEquipmentcategory($id, $p, false, null, $log->id))
                $error[] = self::ERROR_EQUIPMENTCATEGORY;
                if (!$this->actionPort($id, $p, false, null, $log->id))
                $error[] = self::ERROR_PORT;
                if (!$this->actionPricemeasure($id, $p, false, null, $log->id))
                $error[] = self::ERROR_PRICEMEASURE;
                if (!$this->actionRegion($id, $p, false, null, $log->id))
                $error[] = self::ERROR_REGION;
                if (!$this->actionSailtype($id, $p, false, null, $log->id))
                $error[] = self::ERROR_SAILTYPE;
                if (!$this->actionSeasonsync($id, $p, false, null, $log->id))
                $error[] = self::ERROR_SEASON;
                if (!$this->actionService($id, $p, false, null, $log->id))
                $error[] = self::ERROR_SERVICE;
                if (!$this->actionSteeringtype($id, $p, false, null, $log->id))
                $error[] = self::ERROR_STEERINGTYPE;
                if (!$this->actionYachtbuilder($id, $p, false, null, $log->id))
                $error[] = self::ERROR_YACHTBUILDER;
                if (!$this->actionYachtcategory($id, $p, false, null, $log->id))
                $error[] = self::ERROR_YACHTCATEGORY;
                if (!$this->actionYachtmodel($id, $p, false, null, $log->id))
                $error[] = self::ERROR_YACHTMODEL;

                
                $end_date = date('Y-m-d H:i:s');
                $log->end($end_date, json_encode(['error' => $error]));

                if ($isAjax) {

                    if (count($error) == 0) {
                        echo json_encode(['return' => true]);
                        return;
                    } else {
                        echo json_encode(['error' => $error, 'return' => false]);
                        return;
                    }
                } else {
                    return ['error' => $error];
                }
            }
            
            $end_date = date('Y-m-d H:i:s');
            $log->end($end_date, json_encode($error));

            if ($isAjax) {

                return json_encode($error);
            } else
                return $error;
            
        } else {
            $error = ['error' => [self::RUNNER_ERROR]];
            if ($isAjax) {

                return json_encode($error);
            } else
                return $error;
        }

    }
    public function actionLittlesync($id, $p, $isAjax = true, $parentString = 'Little Syncron')
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        
        $startDate = date('Y-m-d H:i:s');
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog)) {
            $log = SyncronLog::log($startDate, $parentString, 0, $isAutomate);
            //var_dump($log->id); exit;
            $error = ['error' => self::AUTH_ERROR];
            
            if (!Yii::$app->user->isGuest) {
                $error = [];
            
                if (!$this->actionYacht($id, $p, false, null, $log->id))
                $error[] = self::ERROR_YACHT;
                if (!$this->actionWpsync($id, $p, false, null, $log->id))
                $error[] = self::ERROR_WPSYNC;

                $end_date = date('Y-m-d H:i:s');
                $log->end($end_date, json_encode(['error' => $error]));

                if ($isAjax) {

                    if (count($error) == 0){
                        echo json_encode(['return' => true]);
                        return;
                    } else {
                        echo json_encode(['error' => $error]);
                        return;
                    }
                } else {
                    return ['error' => $error];
                }

            }

            $end_date = date('Y-m-d H:i:s');
            $log->end($end_date, json_encode($error));

            if ($isAjax) {
                return json_encode($error);
            } else
                return $error;
        } else {
            $error = ['error' => [self::RUNNER_ERROR]];
            if ($isAjax) {

                return json_encode($error);
            } else
                return $error;
        }
    }
/**
 * 
 * ### Országok szinkronja (XXXXX)
 * 
 */
    public function actionCountry($id, $p, $isAjax = true, $parentString = null, $parentId = 0) 
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        $startDate = date('Y-m-d H:i:s');
        $parentString2 = isset($parentString)?$parentString:'Country Syncron';
        
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
            $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
            $error = [];
            
            if (!Yii::$app->user->isGuest) {

                //$request = Yii::$app->request;
                //$id = $request->post('id');
                $tablePrefix = TablePrefix::findOne($id);
                $prefix = '';
                $prId = 0;
                $return = true;
                if ($tablePrefix) {
                    $prefix = $tablePrefix->prefix;
                    $prId = $tablePrefix->id;

                    $xmls = Xml::find()->all();
                    
                    foreach ($xmls as $xml){
                        $countryClass = "app\classes\country\\".$xml->class_name."Country";
                    
                        $countryRet = $countryClass::syncronise($prId);
                        $return = $return && isset($countryRet);
                    }
                }

                $end_date = date('Y-m-d H:i:s');
                if (!$return)
                    $error = ['error' => [self::ERROR_COUNTRY]];
                $log->end($end_date, json_encode($error), $parentId);

                if ($isAjax) {
                    echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
                    return;
                } else 
                    return $return;
            }
            $error = json_encode(['error' => [self::AUTH_ERROR]]);
            $end_date = date('Y-m-d H:i:s');
            $log->end($end_date, $error, $parentId);

            if ($isAjax) {
                    echo json_encode(['error' => 'hello']);
                    return;
                } else
                    return false;
            
        } else {
            $error = ['error' => [self::RUNNER_ERROR]];
            
            if ($isAjax) {

                echo json_encode($error);
                return;
            } else
                return $error;
        }

    }

/**
 * 
 * ### Felszereltség kategória (equipment categories) szinkronja (XXXXX)
 * 
 */

    public function actionEquipmentcategory($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Equipment Category Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
            $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
            
            if (!Yii::$app->user->isGuest) {
                $request = Yii::$app->request;
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
                    
                    foreach ($xmls as $xml){
                        
                        $class = "app\classes\\$dirName\\".$xml->class_name.$className;     
                        $returnObj = $class::syncronise($prId);
                        $return = $return && isset($returnObj);
                    }
                }

                $end_date = date('Y-m-d H:i:s');
            
                $error = $return ? json_encode([]) : json_encode(['error' =>[self::ERROR_EQUIPMENTCATEGORY]]);
                $log->end($end_date, $error, $parentId);

            if ($isAjax) { 
                    echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
                } else
                    return  $return;
            } else {
                $end_date = date('Y-m-d H:i:s');
            
                $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
                $log->end($end_date, $error, $parentId);

            if ($isAjax) {
                    echo json_encode(['error' => 'hello']);
                } else
                    return false;
            }
        } else {
            $error = ['error' => [self::RUNNER_ERROR]];
            
            if ($isAjax) {

                echo json_encode($error);
                return;
            } else
                return $error;
        }
    }    
    
/**
 * 
 * ### Yacht építők szinkronja (XX)
 * 
 */
    public function actionYachtbuilder($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Yacht Builder Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
            $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
            
            if (!Yii::$app->user->isGuest) {
                $request = Yii::$app->request;
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
                    
                    foreach ($xmls as $xml){
                        
                        $class = "app\classes\\$dirName\\".$xml->class_name.$className;     
                        $returnObj = $class::syncronise($prId);
                        $return = $return && isset($returnObj);
                    }
                }

                $end_date = date('Y-m-d H:i:s');
            
                $error = $return ? json_encode([]) : json_encode(['error' => [self::ERROR_YACHTBUILDER]]);
                $log->end($end_date, $error, $parentId);

            if ($isAjax) {
                    echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
                } else
                    return  $return;
            } else {
                $end_date = date('Y-m-d H:i:s');
            
                $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
                $log->end($end_date, $error, $parentId);

            if ($isAjax) {
                    echo json_encode(['error' => 'hello']);
                } else
                    return false;
            }

        } else {
            $error = ['error' => [self::RUNNER_ERROR]];
            
            if ($isAjax) {

                echo json_encode($error);
                return;
            } else
                return $error;
        }

    }   

/**
 * 
 * ### Yacht motor gyártók szinkronja (XX)
 * 
 */
    public function actionEnginebuilder($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Engine Builder Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
         $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
        
        if (!Yii::$app->user->isGuest) {
            $request = Yii::$app->request;
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
                
                foreach ($xmls as $xml){
                    
                    $class = "app\classes\\$dirName\\".$xml->class_name.$className;     
                    $returnObj = $class::syncronise($prId);
                    $return = $return && isset($returnObj);
                }
            }

            $end_date = date('Y-m-d H:i:s');
        
            $error = $return ? json_encode([]) : json_encode(['error' => [self::ERROR_ENGINEBUILDER]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
            } else {
                return  $return;
            }
        } else {
            $end_date = date('Y-m-d H:i:s');
            
            $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo json_encode(['error' => 'hello']); 
            } else {
                return false;
            }
        }
    } else {
        $error = ['error' => [self::RUNNER_ERROR]];
        if ($isAjax) {

            return json_encode($error);
        } else
            return $error;
    }

    }  

/**
 * 
 * ### Felszereltség (equipment) szinkronja (XXXXX)
 * 
 */
    public function actionEquipment($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Equipment Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
        $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
        
        if (!Yii::$app->user->isGuest) {
            $request = Yii::$app->request;
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
                
                foreach ($xmls as $xml){
                    
                    $class = "app\classes\\$dirName\\".$xml->class_name.$className;     
                    $returnObj = $class::syncronise($prId);
                    $return = $return && isset($returnObj);
                }
            }

            $end_date = date('Y-m-d H:i:s');
        
            $error = $return ? json_encode([]) : json_encode(['error' => [self::ERROR_EQUIPMENT]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
            } else {
                return  $return;
            }
        } else {
            $end_date = date('Y-m-d H:i:s');
        
            $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo json_encode(['error' => 'hello']);  
            } else
                return false;
        }
    } else {
        $error = ['error' => [self::RUNNER_ERROR]];
        if ($isAjax) {

            return json_encode($error);
        } else
            return $error;
    }

    }  

/**
 *
 * ### Yacht kategóriák szinkronja (XX)
 * 
 */   
    public function actionYachtcategory($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Yacht Category Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
        $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
        
        if (!Yii::$app->user->isGuest) {
            $request = Yii::$app->request;
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
                
                foreach ($xmls as $xml){
                    
                    $class = "app\classes\\$dirName\\".$xml->class_name.$className;     
                    $returnObj = $class::syncronise($prId);
                    $return = $return && isset($returnObj);
                }
            }

            $end_date = date('Y-m-d H:i:s');
        
            $error = $return ? json_encode([]) : json_encode(['error' => [self::ERROR_YACHTCATEGORY]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
            } else
                return  $return;
        } else {
            $end_date = date('Y-m-d H:i:s');
        
            $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo json_encode(['error' => 'hello']);  
            } else
                return false;
        }
    } else {
        $error = ['error' => [self::RUNNER_ERROR]];
        if ($isAjax) {

            return json_encode($error);
        } else
            return $error;
    }

    }   
/**
 *
 * ### Yacht modelek szinkronja (XX)
 * 
 */
    public function actionYachtmodel($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Yacht Model Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
        $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
        
        if (!Yii::$app->user->isGuest) {
            $request = Yii::$app->request;
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
                
                foreach ($xmls as $xml){
                    
                    $class = "app\classes\\$dirName\\".$xml->class_name.$className;     
                    $returnObj = $class::syncronise($prId);
                    $return = $return && isset($returnObj);
                }
            }

            $end_date = date('Y-m-d H:i:s');
        
            $error = $return ? json_encode([]) : json_encode(['error' => [self::ERROR_YACHTMODEL]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
                return;
            } else
                return  $return;
        } else {
            $end_date = date('Y-m-d H:i:s');
        
            $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo json_encode(['error' => 'hello']);  
            } else
                return false;
        }
    } else {
        $error = ['error' => [self::RUNNER_ERROR]];
        if ($isAjax) {

            return json_encode($error);
        } else
            return $error;
    }

    }   
/**
 *
 * ### Yachtok szinkronja (XX)
 * 
 */   
    public function actionYacht($id, $p, $isAjax = 1, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        if ($parentId != 0) {
            $isAutomate = 0;
        }
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Yacht Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
            $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);   
            
            if (!Yii::$app->user->isGuest) {
                //exit("cica");
                $request = Yii::$app->request;
                $tablePrefix = TablePrefix::findOne($id);
                $prefix = '';
                $prId = $id;
                $return = 1;
                $className = 'Yacht';
                $dirName = 'yacht';
                
                
                if ($tablePrefix) {

                    $prefix = $tablePrefix->prefix;
                   // $prId = $id;

                    $xmls = Xml::find()->all();
                    
                    foreach ($xmls as $xml){
                        
                        $class = "app\classes\\$dirName\\".$xml->class_name.$className;     
                        $returnObj = $class::syncronise($prId);
                        $return = $return && isset($returnObj);
                    }
                }

                $end_date = date('Y-m-d H:i:s');
          //  var_dump($return);
                $error = $return ? json_encode(['not_error']) : json_encode(['error' => [self::ERROR_YACHT]]);
                $log->end($end_date); //, $error, $parentId);

                if ($isAjax) {
                    return (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
                    return;
                } else
                    return  $return;
            } else {
            
                $end_date = date('Y-m-d H:i:s');
                $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
                $log->end($end_date, $error, $parentId);

                if ($isAjax) {
                    echo json_encode(['error' => $error]);  
                    return;
                } else
                    return false;
            } 
        } else {
            $error = ['error' => [self::RUNNER_ERROR]];
            $end_date = date('Y-m-d H:i:s');
            //$log->end($end_date, $error, $parentId);

            if ($isAjax) {

                echo json_encode(['error' => $error]);
            } else
                return $error;
        }
        return;
    }   

    /**
     *
     * ### Regiók szinkronja
     * 
     */   
    public function actionRegion($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Region Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
        $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
        
        if (!Yii::$app->user->isGuest) {
            $request = Yii::$app->request;
             $tablePrefix = TablePrefix::findOne($id);
            $prefix = '';
            $prId = 0;
            $return = true;
            $view = 'region';
            $className = 'Region';
            $dirName = 'region';
            
            
            if ($tablePrefix) {
                $prefix = $tablePrefix->prefix;
                $prId = $tablePrefix->id;

                $xmls = Xml::find()->all();
                
                foreach ($xmls as $xml){
                    
                    $class = "app\classes\\$dirName\\".$xml->class_name.$className;     
                    $returnObj = $class::syncronise($prId);
                    $return = $return && isset($returnObj);
                }
            }

            $end_date = date('Y-m-d H:i:s');
        
            $error = $return ? json_encode([]) : json_encode(['error' => [self::ERROR_REGION]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
            } else
                return  $return;
        } else {
            $end_date = date('Y-m-d H:i:s');
        
            $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo json_encode(['error' => 'hello']);  
            } else
                return false;
        }
    } else {
        $error = ['error' => [self::RUNNER_ERROR]];
        if ($isAjax) {

            return json_encode($error);
        } else
            return $error;
    }

    }   

    /**
     *
     * ### Base szinkronja
     * 
     */   
    public function actionBase($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Base Syncron';
     
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
        $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
        
        if (!Yii::$app->user->isGuest) {

            $request = Yii::$app->request;
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
                
                foreach ($xmls as $xml){
                    
                    $class = "app\classes\\$dirName\\".$xml->class_name.$className;     
                    $returnObj = $class::syncronise($prId);
                    $return = $return && isset($returnObj);
                }
            }

            $error = $return ? json_encode([]) : json_encode(['error' => [self::ERROR_BASE]]);
            $end_date = date('Y-m-d H:i:s');
            $log->end($end_date, $error);

            if ($isAjax) {
                    echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
            } else
                return  $return;

        } else {
            $end_date = date('Y-m-d H:i:s');
            $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
            $log->end($end_date, $error, $parentId);

            if ($isAjax) {
                    echo json_encode(['error' => 'hello']);
            } else
                return false;
        }
    } else {
        $error = ['error' => [self::RUNNER_ERROR]];
        if ($isAjax) {

            return json_encode($error);
        } else
            return $error;
    }

    }  
    
        /**
     *
     * ### Locations szinkronja
     * 
     */   
    public function actionPort($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Port Syncron';
     
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
        $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
        
        if (!Yii::$app->user->isGuest) {

            $request = Yii::$app->request;
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
                
                foreach ($xmls as $xml){
                    
                    $class = "app\classes\\$dirName\\".$xml->class_name.$className;     
                    $returnObj = $class::syncronise($prId);
                    $return = $return && isset($returnObj);
                }
            }

            $end_date = date('Y-m-d H:i:s');
        
            $error = $return ? json_encode([]) : json_encode(['error' => [self::ERROR_PORT]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
            } else
                return  $return;
        } else {
            $end_date = date('Y-m-d H:i:s');
        
            $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo json_encode(['error' => 'hello']);
            } else
                return false;
        }
    } else {
        $error = ['error' => [self::RUNNER_ERROR]];
        if ($isAjax) {

            return json_encode($error);
        } else
            return $error;
    }

    }  
    /**
     *
     * ### Steering típusok szinkronja
     * 
     */   
    public function actionSteeringtype($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Steering Type Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
        $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
        
        if (!Yii::$app->user->isGuest) {

            $request = Yii::$app->request;
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
                
                foreach ($xmls as $xml){
                    
                    $class = "app\classes\\$dirName\\".$xml->class_name.$className;   
                     
                    $returnObj = $class::syncronise($prId);
                    $return = $return && isset($returnObj);
                }
            }

            $end_date = date('Y-m-d H:i:s');
        
            $error = $return ? json_encode([]) : json_encode(['error' => [self::ERROR_STEERINGTYPE]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
            } else
                return  $return;
        } else {
            $end_date = date('Y-m-d H:i:s');
        
            $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo json_encode(['error' => 'hello']);
            } else
                return false;
        }
    } else {
        $error = ['error' => [self::RUNNER_ERROR]];
        if ($isAjax) {

            return json_encode($error);
        } else
            return $error;
    }

    }  
    /**
     *
     * ### Sail típusok szinkronja
     * 
     */   
    public function actionSailtype($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Sail Type Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
        $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
        
        if (!Yii::$app->user->isGuest) {

            $request = Yii::$app->request;
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
                
                foreach ($xmls as $xml){
                    
                    $class = "app\classes\\$dirName\\".$xml->class_name.$className;   
                     
                    $returnObj = $class::syncronise($prId);
                    $return = $return && isset($returnObj);
                }
            }

            $end_date = date('Y-m-d H:i:s');
        
            $error = $return ? json_encode([]) : json_encode(['error' => [self::ERROR_SAILTYPE]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
            } else
                return  $return;
        } else {
            $end_date = date('Y-m-d H:i:s');
        
            $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo json_encode(['error' => 'hello']);
            } else
                return false;
        }
    } else {
        $error = ['error' => [self::RUNNER_ERROR]];
        if ($isAjax) {

            return json_encode($error);
        } else
            return $error;
    }

    }  

    /**
     *
     * ### Service szinkronja
     * 
     */   
    public function actionService($id, $isAjax = false, $p="", $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Service Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
        $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
        
        if (!Yii::$app->user->isGuest) {

            $request = Yii::$app->request;
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
                
                foreach ($xmls as $xml){
                    
                    $class = "app\classes\\$dirName\\".$xml->class_name.$className;   
                     
                    $returnObj = $class::syncronise($prId);
                    $return = $return && isset($returnObj);
                }
            }

            $end_date = date('Y-m-d H:i:s');
        
            $error = $return ? json_encode([]) : json_encode(['error' => [self::ERROR_SERVICE]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
            } else
                return  $return;
        } else {
            $end_date = date('Y-m-d H:i:s');
        
            $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo json_encode(['error' => 'hello']);
            } else
                return false;
        }
    } else {
        $error = ['error' => [self::RUNNER_ERROR]];
        if ($isAjax) {

            return json_encode($error);
        } else
            return $error;
    }

    }  

    /**
     *
     * ### Company szinkronja
     * 
     */   
    public function actionCompany($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Company Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
        $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
        

        if (!Yii::$app->user->isGuest) {

            $request = Yii::$app->request;
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
                
                foreach ($xmls as $xml){
                    
                    $class = "app\classes\\$dirName\\".$xml->class_name.$className;   

                    $returnObj = $class::syncronise($prId);
                    $return = $return && isset($returnObj);
                }
            }

            $end_date = date('Y-m-d H:i:s');
        
            $error = $return ? json_encode([]) : json_encode(['error' => [self::ERROR_COMPANY]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
            } else
                return  $return;
        } else {
            $end_date = date('Y-m-d H:i:s');
        
            $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo json_encode(['error' => $error]);
            } else
                return false;
        }
    } else {
        $error = ['error' => [self::RUNNER_ERROR]];
        if ($isAjax) {

            return json_encode($error);
        } else
            return $error;
    }

    }  

    
    /**
     *
     * ### DiscountItem szinkronja
     * 
     */   
    public function actionDiscountitem($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Discount Item Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
        $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
        
        if (!Yii::$app->user->isGuest) {

            $request = Yii::$app->request;
             $tablePrefix = TablePrefix::findOne($id);
            $prefix = '';
            $prId = 0;
            $return = true;

            $className = 'DiscountItem';
            $dirName = 'discountItem';
            
            
            if ($tablePrefix) {
                $prefix = $tablePrefix->prefix;
                $prId = $tablePrefix->id;

                $xmls = Xml::find()->all();
                
                foreach ($xmls as $xml){
                    
                    $class = "app\classes\\$dirName\\".$xml->class_name.$className;   

                    $returnObj = $class::syncronise($prId);
                    $return = $return && isset($returnObj);
                }
            }

            $end_date = date('Y-m-d H:i:s');
        
            $error =  $return ? json_encode([]) : json_encode(['error' =>[self::ERROR_DISCOUNTITEM]]);
            $log->end($end_date, $error, $parentId);

            if ($isAjax) {
                echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
                return;
            } else
                return  $return;
        } else {
            $end_date = date('Y-m-d H:i:s');
        
            $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
            $log->end($end_date, $error, $parentId);

            if ($isAjax) {
                echo json_encode(['error' => $error]);
                return;
            } else
                return false;
            
      /*  } else {
            $error = ['error' => [self::RUNNER_ERROR]];
            if ($isAjax) {

                echo json_encode($error);
            } else
                return $error; */
            }
        }

    }  

    /**
     *
     * ### Season szinkronja
     * 
     */   
    public function actionSeason($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Session Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
            $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
            
            if (!Yii::$app->user->isGuest) {

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
                    
                    foreach ($xmls as $xml){
                        
                        $class = "app\classes\\$dirName\\".$xml->class_name.$className;   

                        $returnObj = $class::syncronise($prId);
                        $return = $return && isset($returnObj);
                    }
                }

                $end_date = date('Y-m-d H:i:s');
            
                $error = $return ? json_encode([]) : json_encode(['error' =>[self::ERROR_SEASON]]);
                $log->end($end_date, $error, $parentId);

                if ($isAjax) {
                    echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
                    return;
                } else
                    return  $return;
            } else {
                $end_date = date('Y-m-d H:i:s');
            
                $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
                $log->end($end_date, $error, $parentId);

                if ($isAjax) {
                    echo json_encode(['error' => 'hello']);
                } else
                    return false;
            
            } /*else {
            $error = ['error' => [self::RUNNER_ERROR]];
            if ($isAjax) {

                return json_encode($error);
            } else
                return $error;*/
        
        }
 
    }
    /**
     * 
     * ### PriceMeasure szinkronja (XXXXX)
     * 
     */
    public function actionPricemeasure($id, $p, $isAjax = true, $parentString = null, $parentId = 0) 
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        if ($parentId != 0) {
            $isAutomate = 0;
        }
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'Price Measure Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
            $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
            
            if (!Yii::$app->user->isGuest) {

            $tablePrefix = TablePrefix::findOne($id);
            $prefix = '';
            $prId = 0;
            $return = true;
            if ($tablePrefix) {
                $prefix = $tablePrefix->prefix;
                $prId = $tablePrefix->id;

                $xmls = Xml::find()->all();
                
                foreach ($xmls as $xml){
                    $class = "app\classes\pricemeasure\\".$xml->class_name."PriceMeasure";
                
                    $ret = $class::syncronise($prId);
                    $return = $return && $ret;
                }
            }
            $end_date = date('Y-m-d H:i:s');
            
            $error = $return ? json_encode([]) : json_encode(['error' =>[self::ERROR_PRICEMEASURE]]);
            $log->end($end_date, $error, $parentId);

            if ($isAjax) {
                echo (json_encode(['pr'=>$prefix, 'prId'=>$prId, 'return' => $return]));
                return;
            } else
                return  $return;
        } else {
            $end_date = date('Y-m-d H:i:s');
        
            $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
            $log->end($end_date, $error, $parentId);

        if ($isAjax) {
                echo json_encode(['error' => 'hello']);
            } else
                return false;
        }
    } else {
        $error = ['error' => [self::RUNNER_ERROR]];
        if ($isAjax) {
        
            echo json_encode($error);
        } else
            return $error;
    }

}
    public function actionWpsync($id, $p, $isAjax = true, $parentString = null, $parentId = 0)
    {
        $isAutomate = 1;
        if ( $isAjax ){
            $this->login($p);
            $isAutomate = 0;
        }
        
        if ($parentId != 0) {
            $isAutomate = 0;
        }
        $startDate = date('Y-m-d H:i:s');

        $parentString2 = isset($parentString)?$parentString:'WordPress Syncron';
        $lastLog = SyncronLog::findOne(['date_end' => null, 'parent_id' => 0]);
        
        if (empty($lastLog) || ($lastLog && $lastLog->id == $parentId)) {
            $log = SyncronLog::log($startDate, $parentString2, $parentId, $isAutomate);
            
            if (!Yii::$app->user->isGuest) {
                $date = date('Y-m-d H:i:s');

                $ret = Wpsync::savePosts($id);

                $tablePrefix = TablePrefix::findOne($id);
                $prefix = '';
                if ($tablePrefix)
                    $prefix = $tablePrefix->prefix;
                if ($ret){
                    
                    //exit('cica');
                    $end_date = date('Y-m-d H:i:s');
                    $error = json_encode([]);
                    $log->end($end_date, $error, $parentId);

                    if ($isAjax) {
                        echo json_encode(['pr'=>$prefix, 'prId'=>$id, 'return' => true]);
                    } else
                        return true;
                } else {
                    $end_date = date('Y-m-d H:i:s');
                    $error = json_encode([]);
                    
                    $log->end($end_date, $error, $parentId);
                    if ($isAjax) {
                        echo json_encode(['pr'=>$prefix, 'prId'=>$id, 'return' => true]);
                    } else
                        return false;

                }
            } else {
                $end_date = date('Y-m-d H:i:s');
                $error =  json_encode(['error' =>[self::AUTH_ERROR]]);
                $log->end($end_date, $error, $parentId);

                if ($isAjax) {
                    echo json_encode(['error' => 'hello']);
                } else
                    return false;
            }
        }
                    
                    

    }
}