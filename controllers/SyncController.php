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
use Yii;
use app\models\TablePrefix;
use app\models\Xml;
use app\models\CashLog;
class SyncController extends \yii\web\Controller
{
    /**
     * 
     * ### Országok szinkronja (XXXXX)
     * 
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $id       = $request->get('id') ? $request->get('id') : null;
        if (!Yii::$app->user->isGuest) {
            $tablePrefix = TablePrefix::findOne($id);
            $prefix = '';
            $prId = 0;
            if ($tablePrefix) {
                $prefix = $tablePrefix->prefix;
                $prId = $tablePrefix->id;
            }
            return $this->render('index', ['pr' => $prefix, 'prId' => $prId]);
        } else {
            $this->redirect('/web/index.php');
        }
    }
    /*
     * 
     * ### Országok szinkronja (XXXXX)
     * 
     */
    public function actionCountry($id)
    {
        if (!Yii::$app->user->isGuest) {
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
                    $countryRet = $countryClass::syncronise();
                    $return = $return && $countryRet;
                }
            }
            return $this->render('country', ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }
    /**
     * 
     * ### Felszereltség kategória (equipment categories) szinkronja (XXXXX)
     * 
     */
    public function actionEquipmentcategory($id) //Fejlesztendő
    {
        if (!Yii::$app->user->isGuest) {
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
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }

    /**
     * 
     * ### Yacht építők szinkronja (XX)
     * 
     */
    public function actionYachtbuilder($id) //Fejlesztendő
    {
        if (!Yii::$app->user->isGuest) {
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
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }

    /**
     * 
     * ### Yacht motor gyártók szinkronja (XX)
     * 
     */
    public function actionEnginebuilder($id) //Fejlesztendő
    {
        if (!Yii::$app->user->isGuest) {

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
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }

    /**
     * 
     * ### Felszereltség (equipment) szinkronja (XXXXX)
     * 
     */
    public function actionEquipment($id)
    {
        if (!Yii::$app->user->isGuest) {
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
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }
    /**
     *
     * ### Yacht kategóriák szinkronja (XX)
     * 
     */
    public function actionYachtcategory($id) //Fejlesztendő
    {
        if (!Yii::$app->user->isGuest) {
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
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }
    /**
     * 
     * ### PriceMeasure szinkronja (XXXXX)
     * 
     */
    public function actionPricemeasure($id)
    {
        if (!Yii::$app->user->isGuest) {
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
                    $ret = $class::syncronise();
                    $return = $return && $ret;
                }
            }
            return $this->render('country', ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }

    /**
     *
     * ### Yacht modelek szinkronja (XX)
     * 
     */
    public function actionYachtmodel() //Fejlesztendő
    {
        $request = Yii::$app->request;
        $id = intval($request->get('id'));
        $cashLog = new CashLog();
        $cashLog->start_datetime = date(\app\classes\Sync::$dateString);
        $cashLog->type = 'yacht model cash';
        $cashLog->save();
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
                $returnObj = $class::syncronise();
                $return = $return && $returnObj;
            }
        }
        if ($return) {
            $d = date(\app\classes\Sync::$dateString);
            $cashLog->end_datetime = $d;
            $cashLog->ret_value = 'OK';
            $cashLog->save(0);
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        }
        $d = date(\app\classes\Sync::$dateString);
        $cashLog->end_datetime = $d;
        $cashLog->ret_value = 'ERROR (SYNCRON)';
        $cashLog->save(0);
        return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
    }
    /**
     *
     * ### Yachtok szinkronja (XX)
     * 
     */
    public function actionYacht($id) //Fejlesztendő
    {
        if (!Yii::$app->user->isGuest) {
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
                    $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }

    /**
     *
     * ### Regiók szinkronja
     * 
     */
    public function actionRegion($id)
    {
        if (!Yii::$app->user->isGuest) {

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
                foreach ($xmls as $xml) {
                    $class = "app\classes\\$dirName\\" . $xml->class_name . $className;
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }
    /**
     *
     * ### Base szinkronja
     * 
     */
    public function actionBase($id)
    {
        if (!Yii::$app->user->isGuest) {

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
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }
    /**
     *
     * ### Locations szinkronja
     * 
     */
    public function actionPort($id)
    {
        if (!Yii::$app->user->isGuest) {
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
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }
    /**
     *
     * ### Steering típusok szinkronja
     * 
     */
    public function actionSteeringtype($id) //Fejlesztendő
    {
        if (!Yii::$app->user->isGuest) {
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
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }
    /**
     *
     * ### Sail típusok szinkronja
     * 
     */
    public function actionSailtype($id) //Fejlesztendő
    {
        if (!Yii::$app->user->isGuest) {
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
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }

    /**
     *
     * ### Service szinkronja
     * 
     */
    public function actionService($id) //Fejlesztendő
    {
        if (!Yii::$app->user->isGuest) {

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
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }

    /**
     *
     * ### Company szinkronja
     * 
     */
    public function actionCompany($id) //Fejlesztendő
    {
        if (!Yii::$app->user->isGuest) {
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
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }
    /**
     *
     * ### DiscountItem szinkronja
     * 
     */
    public function actionDiscountitemsync($id) //Fejlesztendő
    {
        if (!Yii::$app->user->isGuest) {
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
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }
    /**
     *
     * ### Season szinkronja
     * 
     */
    public function actionSeasonsync($id) //Fejlesztendő
    {
        if (!Yii::$app->user->isGuest) {
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
                    $returnObj = $class::syncronise();
                    $return = $return && $returnObj;
                }
            }
            return $this->render($view, ['pr' => $prefix, 'prId' => $prId, 'return' => $return]);
        } else {
            $this->redirect('/web/index.php');
        }
    }
}
