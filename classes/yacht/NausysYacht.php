<?php
namespace app\classes\yacht;
use Yii;
use app\classes\yacht\YachtSync;
use app\classes\Nausys;
use app\models\Xml;
use app\models\Company;
use app\models\Price;
use app\models\RegularDiscount;
use app\models\StandardEquipment;
use app\models\AdditionalEquipment;
use app\models\Yacht;
use app\models\YachtSeason;
use app\models\CheckInPeriod;
use app\models\YachtPriceLocation;
use app\models\YachtSeasonService;
use app\models\ServicesValidForBases;
use app\models\WpYacht;

class NausysYacht  extends YachtSync
{
    private static $resturl      = 'http://ws.nausys.com/CBMS-external/rest/catalogue/v6/yachts/';
    private static $restYachturl = 'http://ws.nausys.com/CBMS-external/rest/catalogue/v6/yacht/';
    private static $modelName    = 'app\classes\yacht\NausysYacht';
    private static $model        = 'app\models\Yacht';
    private static $objectname   = 'yachts'; //JSON-ban a második paraméter, a státusz után....
    private static $subModelsName = 'app\classes\yacht\PhotoSync';
    public function __construct(
        $ID = null,
       // $xmlId,
        $xmlJsonId,
        $name_,
        $isActive = 1,
        $company_id,
        $base_id,
        $location_id,
        $yachtModel_id,
        $draft,
        $cabins,
        $cabins_crew,
        $berths_cabin,
        $berths_salon,
        $berths_crew,
        $berths_total,
        $wc,
        $wc_crew,
        $build_year,
        $engines,
        $engine_power,
        $steering_type_id,
        $sail_type_id,
        $sail_renewed,
        $genoa_type_id,
        $genoa_renewed,
        $commission,
        $deposit,
        $max_discount,
        $four_star_charter,
        $charter_type,
        $propulsion_type,
        $internal_use,
        $launched_year,
        $needs_option_approval,
        $can_make_booking_fixed,
        $fuel_tank,
        $water_tank,
        $mast_length,
        $number_of_rudder_blades,
        $engine_builder_id,
        $hull_color,
        $third_partyInsurance_amount,
        $third_partyInsurance_currency,
        $max_person
    ) {
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml) {
            $xmlId = $xml->id;
        }
        
        parent::__construct(
            $ID,
            $xmlId,
            $xmlJsonId,
            $name_,
            $isActive,
            $company_id,
            $base_id,
            $location_id,
            $yachtModel_id,
            $draft,
            $cabins,
            $cabins_crew,
            $berths_cabin,
            $berths_salon,
            $berths_crew,
            $berths_total,
            $wc,
            $wc_crew,
            $build_year,
            $engines,
            $engine_power,
            $steering_type_id,
            $sail_type_id,
            $sail_renewed,
            $genoa_type_id,
            $genoa_renewed,
            $commission,
            $deposit,
            $max_discount,
            $four_star_charter,
            $charter_type,
            $propulsion_type,
            $internal_use,
            $launched_year,
            $needs_option_approval,
            $can_make_booking_fixed,
            $fuel_tank,
            $water_tank,
            $mast_length,
            $number_of_rudder_blades,
            $engine_builder_id,
            $hull_color,
            $third_partyInsurance_amount,
            $third_partyInsurance_currency,
            $max_person
        );
    }
    /**
     * 
     * Syncrons function
     */

    public static function syncronise()
    {
        $cred = new Nausys();
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml) {
            $xmlId = $xml->id;
        };
        $companies = Company::findAll(['xml_id' => $xmlId, 'is_active' => 1]);
        if (is_array($companies)) {
            $return = true;
            foreach ($companies as $company) {
                echo ($company->name);
                $return = $return && self::syncroniseWithCompany($company->id);
            }
        }
        return false;
    }
    /**
     * 
     * Syncrons With Company (cégek alapján szinkronizál)
     */
    public static function syncroniseWithCompany($company_id)
    {
        $cred = new Nausys();
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml) {
            $xmlId = $xml->id;
        };
        $companies = Company::findOne(['xml_id' => $xmlId, 'is_active' => 1, 'id' => $company_id]);
        $return = true;
        if (isset($companies)) {
            $company = $companies;
            self::inactiveRows(intval($xmlId), $company->xml_json_id);
            Price::inactiveAll($xmlId, $company->xml_json_id);
            YachtSeason::inactiveAllSeason($xmlId, $company->xml_json_id);
            RegularDiscount::inactiveAll($xmlId, $company->xml_json_id);
            echo ($company->name);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$resturl . $company->xml_json_id);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,  $cred->getJsonCredentials());
            $header = array('Content-Type: application/json');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $exec = curl_exec($ch);
            curl_close($ch);
            if ($exec) {
                $Obj = json_decode($exec);
                if ($Obj->status == "OK") {
                    $objName = self::$objectname;
                    $objectes = $Obj->$objName;
                    foreach ($objectes as $obj) {
                        $objObj = new self::$modelName(
                            null,
                          //  $xmlId,
                            intval($obj->id),
                            $obj->name,
                            1, //->textEN, 1,
                            $obj->companyId,
                            $obj->baseId,
                            $obj->locationId,
                            $obj->yachtModelId,
                            $obj->draft,
                            $obj->cabins,
                            $obj->cabinsCrew,
                            $obj->berthsCabin,
                            $obj->berthsSalon,
                            $obj->berthsCrew,
                            $obj->berthsTotal,
                            $obj->wc,
                            isset($obj->wcCrew) ? $obj->wcCrew : 0,
                            $obj->buildYear,
                            isset($obj->engines) ? $obj->engines : null,
                            isset($obj->enginePower) ? $obj->enginePower : null,
                            isset($obj->steeringTypeId) ? $obj->steeringTypeId : null,
                            isset($obj->sailTypeId) ? $obj->sailTypeId : null,
                            isset($obj->sailRenewed) ? $obj->sailRenewed : null,
                            isset($obj->genoaTypeId) ? $obj->genoaTypeId : null,
                            isset($obj->genoaRenewed) ? $obj->genoaRenewed : null,
                            $obj->commission,
                            $obj->deposit,
                            isset($obj->maxDiscount) ? $obj->maxDiscount : null,
                            $obj->fourStarCharter ? 1 : 0,
                            isset($obj->charterType) ? $obj->charterType : null,
                            isset($obj->propulsionType) ? $obj->propulsionType : null,
                            isset($obj->internalUse) ? $obj->internalUse : null,
                            isset($obj->launchedYear) ? $obj->launchedYear : null,
                            $obj->needsOptionApproval ? 1 : 0,
                            $obj->canMakeBookingFixed ? 1 : 0,
                            $obj->fuelTank,
                            $obj->waterTank,
                            isset($obj->mastLength) ? $obj->mastLength : null,
                            isset($obj->numberOfRudderBlades) ? $obj->numberOfRudderBlades : null,
                            isset($obj->engineBuilderId) ? $obj->engineBuilderId : null,
                            isset($obj->hullColor) ? $obj->hullColor : null,
                            isset($obj->thirdPartyInsuranceAmount) ? $obj->thirdPartyInsuranceAmount : null,
                            isset($obj->thirdPartyInsuranceCurrency) ? $obj->thirdPartyInsuranceCurrency : null,
                            isset($obj->maxPersons) ? $obj->maxPersons : null
                        );
                        $returnId = $objObj->sync();
                        if ($returnId) {
                            if (isset($obj->seasonSpecificData) && is_array($obj->seasonSpecificData)) {
                                foreach ($obj->seasonSpecificData as $season) {
                                    $yachtSeason = YachtSeason::sync(
                                        $xmlId,
                                        $season->seasonId,
                                        $season->baseId,
                                        $season->locationId,
                                        intval($obj->id),
                                    );
                                    if (empty($yachtSeason)) {
                                        return null;
                                    }
                                    if ($yachtSeason && isset($season->prices)) {
                                        foreach ($season->prices as $price) {
                                            $date_time = strtotime($price->dateFrom);
                                            $date_from = date('Y-m-d H:i:s', $date_time);
                                            $date_time = strtotime($price->dateTo);
                                            $date_to   = date('Y-m-d H:i:s', $date_time);
                                            $yachtPrice =  Price::sync(
                                                $yachtSeason->xml_id,
                                                $yachtSeason->season_id,
                                                $date_from,
                                                $date_to,
                                                floatval($price->price),
                                                $price->currency,
                                                $price->type,
                                                intval($obj->id)
                                            );
                                            if (!$yachtPrice) {
                                                return null;
                                            }
                                            YachtPriceLocation::inactiveAll($xmlId, $yachtPrice->id);
                                            if ($yachtPrice && is_array($price->locationsId)) {
                                                foreach ($price->locationsId as $location_id) {
                                                    $yachtPriceLocation = YachtPriceLocation::sync($xmlId, $yachtPrice->id, $location_id);
                                                    if (!$yachtPriceLocation) {
                                                        return null;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if ($yachtSeason && is_array($season->regularDiscounts)) {
                                        foreach ($season->regularDiscounts as $regularDiscount) {
                                            $discount = RegularDiscount::sync($xmlId, $obj->id, intval($season->seasonId), intval($regularDiscount->discountItemId), floatval($regularDiscount->amount), $regularDiscount->type);
                                        }
                                    }
                                    if ($yachtSeason && is_array($season->services)) {
                                        foreach ($season->services as $service) {
                                            $yachtSeasonService = YachtSeasonService::sync(
                                                $xmlId,
                                                $service->id, //xml_json_id
                                                $obj->id, //yacht_id
                                                $yachtSeason->season_id, //??
                                                $service->serviceId,
                                                $service->price,
                                                $service->currency,
                                                $service->priceMeasureId,
                                                $service->calculationType,
                                                $service->obligatory ? 1 : 0,
                                                is_array($service->description) && count($service->description) > 0 ? $service->description['textEN'] : '',
                                                $service->amount,
                                                isset($service->validForBases) ? 1 : 0,
                                                isset($service->amountIsPercentage) ? ($service->amountIsPercentage ? 1 : 0) : null,
                                                isset($service->percentageCalculationType) ? $service->percentageCalculationType : null,
                                                isset($service->validPeriodFrom) ? $service->validPeriodFrom : null,
                                                isset($service->validPeriodTo) ? $service->validPeriodTo : null,
                                                isset($service->validMinPax) ? $service->validMinPax : null,
                                                isset($service->validMaxPax) ? $service->validMaxPax : null,
                                                isset($service->minimumPrice) ? $service->minimumPrice : null
                                            );
                                            if ($yachtSeasonService && isset($service->validForBases) && is_array($service->validForBases)) {
                                                foreach ($service->validForBases as $base) {
                                                    ServicesValidForBases::saveModel(
                                                        $xmlId,
                                                        $service->serviceId,
                                                        $base,
                                                        1,
                                                        $obj->id,
                                                        $season->seasonId
                                                    );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if (isset($obj->standardYachtEquipment) && is_array($obj->standardYachtEquipment)) {
                                foreach ($obj->standardYachtEquipment as $standardYachtEquipment) {
                                    $comment = is_array($standardYachtEquipment->comment) && isset($standardYachtEquipment->comment['textEN']) ? $standardYachtEquipment->comment['textEN'] : '';
                                    $yachtEquipment = StandardEquipment::sync(
                                        $xmlId,
                                        $obj->id,
                                        $standardYachtEquipment->equipmentId,
                                        $standardYachtEquipment->quantity,
                                        $comment
                                    );
                                    if (!$yachtEquipment) {
                                        return null;
                                    }
                                }
                            }

                            if (isset($obj->additionalYachtEquipment) && is_array($obj->additionalYachtEquipment)) {
                                foreach ($obj->additionalYachtEquipment as $additionalYachtEquipment) {
                                    $comment = is_array($additionalYachtEquipment->comment) && isset($additionalYachtEquipment->comment['textEN']) ? $additionalYachtEquipment->comment['textEN'] : '';
                                    //exit("hello");
                                    $yachtEquipment = AdditionalEquipment::sync(
                                        $xmlId,
                                        $comment,
                                        $additionalYachtEquipment->quantity,
                                        $additionalYachtEquipment->price,
                                        $additionalYachtEquipment->currency,
                                        $obj->id,
                                        $additionalYachtEquipment->equipment_id,
                                        $additionalYachtEquipment->season_id,

                                        $additionalYachtEquipment->price_measure_id,
                                        $additionalYachtEquipment->calculation_type,
                                        $additionalYachtEquipment->condition_string,
                                        $additionalYachtEquipment->amount,
                                        $additionalYachtEquipment->amount_is_percentage,
                                        $additionalYachtEquipment->percentage_calculation_type,
                                        $additionalYachtEquipment->minimum_price, //
                                        $additionalYachtEquipment->id
                                    );
                                    if (!$yachtEquipment) {
                                        return null;
                                    }
                                }
                            }
                            //oneWayPeriods  // <-
                            //checkInPeriods // <- becsekkolási adatok
                            if (isset($obj->checkInPeriods) && is_array($obj->checkInPeriods)) {
                                foreach ($obj->checkInPeriods as $checkInPeriod) {
                                    //exit("hello");
                                    $check = CheckInPeriod::sync(
                                        $xmlId,
                                        $obj->id,
                                        date('Y-m-d', strtotime($checkInPeriod->dateFrom)),
                                        date('Y-m-d', strtotime($checkInPeriod->dateTo)),
                                        $checkInPeriod->minimalReservationDuration,
                                        $checkInPeriod->checkInMonday ? 1 : 0,
                                        $checkInPeriod->checkInTuesday ? 1 : 0,
                                        $checkInPeriod->checkInWednesday ? 1 : 0,
                                        $checkInPeriod->checkInThursday ? 1 : 0,
                                        $checkInPeriod->checkInFriday ? 1 : 0,
                                        $checkInPeriod->checkInSaturday ? 1 : 0,
                                        $checkInPeriod->checkInSunday ? 1 : 0,
                                        $checkInPeriod->checkOutMonday ? 1 : 0,
                                        $checkInPeriod->checkOutTuesday ? 1 : 0,
                                        $checkInPeriod->checkOutWednesday ? 1 : 0,
                                        $checkInPeriod->checkOutThursday ? 1 : 0,
                                        $checkInPeriod->checkOutFriday ? 1 : 0,
                                        $checkInPeriod->checkOutSaturday ? 1 : 0,
                                        $checkInPeriod->checkOutSunday ? 1 : 0
                                    );
                                    if (!$check) {
                                        return null;
                                    }
                                }
                            }
                        }
                        $subReturn = true;

                        // Main kép leszedése
                        if ($returnId && isset($obj->mainPictureUrl)) {
                            $photo = $obj->mainPictureUrl . "?w=900";
                            $photoObject = new PhotoSync($returnId, $photo);
                            $subReturn = $subReturn && $photoObject->save($xmlId, 0, 0);

                            $photo = $obj->mainPictureUrl . "?w=400";
                            $photoObject = new PhotoSync($returnId, $photo);

                            $subReturn = $subReturn && $photoObject->save($xmlId, 0, 1);
                        }

                        // Egyébb kép leszedése
                        if ($returnId && isset($obj->picturesURL)) {

                            foreach ($obj->picturesURL as $photo) {

                                $photo = $photo . "?w=900";
                                $photoObject = new PhotoSync($returnId, $photo);
                                $subReturn = $subReturn && $photoObject->save($xmlId, 0, 0);
                            }

                            $newPath = Yii::$app->basePath . '/boat-' . $xmlId . '/new-' . $returnId;
                            if (file_exists($newPath)) {
                                $oldPath = Yii::$app->basePath . '/boat-' . $xmlId . '/' . $returnId;
                                if (file_exists($oldPath)) {
                                    parent::rrmdir($oldPath);
                                }


                                @rename($newPath, $oldPath);
                                //exit;
                            }
                        }
                        $return = $return && isset($returnId) && $subReturn;
                    }
                }
            } else {

                exit("baj van az xml csat-al yacht-ban");
                $return = false;
            }
        }
        if ($return) {
            self::$model::archive();
            $return = 1;
        }
        return $return;
    }


    /**
     * 
     * Yacht Adatok Szinkronja (több yacht)
     */

    public static function syncroniseYachtsAndPrices($id)
    {

        echo "Árak kezdete";
        $return = false;
        $array = [0];
        $yacht = Yacht::find()->where(['is_active' => 1])->where(['xml_id' => $id])->andWhere(['not in', 'id', $array])->one();
        while ($yacht) {
            $array[] = $yacht->id;
            self::syncroniseYachtAndPrices($yacht->id);
            $yacht = Yacht::find()->where(['is_active' => 1])->where(['xml_id' => $id])->andWhere(['not in', 'id', $array])->one();
        }
        echo "Árak vége";

        return $return;
    }

    /**
     * 
     * Yacht Adatok Szinkronja (több yacht)
     */

    public static function syncroniseYachtsAndServices($id, $minId = 1)
    {

        echo "S Sercices kezdete";
        $return = false;
        $array = [0];
        $yacht = Yacht::find()->where(['is_active' => 1])->where(['xml_id' => $id])->andWhere(['not in', 'id', $array])->andWhere('id >= ' . $minId)->one();
        while ($yacht) {
            $array[] = $yacht->id;
            self::syncroniseYachtAndServices($yacht->id);
            $yacht = Yacht::find()->where(['is_active' => 1])->where(['xml_id' => $id])->andWhere(['not in', 'id', $array])->andWhere('id >= ' . $minId)->one();
        }
        echo "S Sercices vége";

        return $return;
    }
    /**
     * 
     * Yacht Adatok Szinkronja (több yacht)
     */

    public static function syncroniseYachtsAndStandardEquipment($id)
    {

        echo "SE Sercices kezdete";
        $return = false;
        $array = [0];
        $yacht = Yacht::find()->where(['is_active' => 1])->where(['xml_id' => $id])->andWhere(['not in', 'id', $array])->one();
        while ($yacht) {
            $array[] = $yacht->id;
            self::syncroniseYachtAndStandardEquipment($yacht->id);
            $yacht = Yacht::find()->where(['is_active' => 1])->where(['xml_id' => $id])->andWhere(['not in', 'id', $array])->one();
        }
        echo "SE Sercices vége";

        return $return;
    }

    /**
     * 
     * Yacht Adatok Szinkronja (több yacht)
     */

    public static function syncroniseYachtsAndAdditionalEquipment($id)
    {

        echo "AE Sercices kezdete";
        $return = false;
        $array = [0];
        $yacht = Yacht::find()->where(['is_active' => 1])->where(['xml_id' => $id])->andWhere(['not in', 'id', $array])->one();
        while ($yacht) {
            $array[] = $yacht->id;
            self::syncroniseYachtAndAdditionalEquipment($yacht->id);
            $yacht = Yacht::find()->where(['is_active' => 1])->where(['xml_id' => $id])->andWhere(['not in', 'id', $array])->one();
        }
        echo "AE Sercices vége";

        return $return;
    }
    /**
     * 
     * Yacht Adatok Szinkronja (több yacht)
     */

    public static function syncroniseYachtsAndCheckInPeriod($id)
    {

        echo "CIP Sercices kezdete";
        $return = false;
        $array = [0];
        $yacht = Yacht::find()->where(['is_active' => 1])->where(['xml_id' => $id])->andWhere(['not in', 'id', $array])->one();
        while ($yacht) {
            $array[] = $yacht->id;
            self::syncroniseYachtAndCheckInPeriod($yacht->id);
            $yacht = Yacht::find()->where(['is_active' => 1])->where(['xml_id' => $id])->andWhere(['not in', 'id', $array])->one();
        }
        echo "CIP Sercices vége";

        return $return;
    }
    public static function syncroniseOneYacht($xml_json_id)
    {
        $cred = new Nausys();
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml) {
            $xmlId = $xml->id;
        };
        $return = false;
        echo "Árak  és Regulár discount szinkronok elkezdődöttek;\n";
        $return = true;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$restYachturl . $xml_json_id);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  $cred->getJsonCredentials());
        $header = array('Content-Type: application/json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $exec = curl_exec($ch); // var_dump($exec);
        curl_close($ch);
        if ($exec) {
            $Obj = json_decode($exec);

            if ($Obj->status == "OK") {
                $objName = self::$objectname;
                $objectes = $Obj->$objName;
                foreach ($objectes as $obj) {
                    $returnId = self::oneYachtSynronise($obj, 1);
                    return $returnId;
                }
                
            } else {
                $return = false;
            }
        }

        return $return;
    }
    /**
     * 
     * Yacht Adatok Szinkronja (egy yacht)
     */

    public static function syncroniseYachtAndPrices($yacht_id)
    {
        $cred = new Nausys();
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml) {
            $xmlId = $xml->id;
        };
        $yacht = Yacht::findOne(['xml_id' => $xmlId, 'id' => $yacht_id]);
        echo "inaktívra állítja az összes hozzátartozó árat és kedvezményt;\n";
        Price::inactiveAllYachtPrices($yacht->id);
        RegularDiscount::inactiveAllYatchtDiscounts($yacht->id);
        echo "Az összes hozzátartozó árat és kedvezményt inaktívra állította;\n";
        $return = false;
        echo "Árak  és Regulár discount szinkronok elkezdődöttek;\n";
        if ($yacht) {
            $return = true;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$restYachturl . $yacht->xml_json_id);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,  $cred->getJsonCredentials());
            $header = array('Content-Type: application/json');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $exec = curl_exec($ch); // var_dump($exec);
            curl_close($ch);
            if ($exec) {
                $Obj = json_decode($exec);

                if ($Obj->status == "OK") {
                    $objName = self::$objectname;

                    $objectes = $Obj->$objName;

                    foreach ($objectes as $obj) {

                        $returnId = $yacht->id;
                        if ($returnId) {

                            //seasonSpecificData // <- ár syncron
                            //szezon és ár 
                            if (isset($obj->seasonSpecificData) && is_array($obj->seasonSpecificData)) {

                                foreach ($obj->seasonSpecificData as $season) {
                                    echo "Árak szinkron eleje (szezon szerint);";
                                    $yachtSeason = YachtSeason::sync(

                                        $xmlId,
                                        $season->seasonId,
                                        $season->baseId,
                                        $season->locationId,
                                        intval($obj->id),
                                    );
                                    if (empty($yachtSeason)) {
                                        return null;
                                    }
                                    if ($yachtSeason && isset($season->prices)) {

                                        foreach ($season->prices as $price) {

                                            $date_time = strtotime($price->dateFrom);
                                            $date_from = date('Y-m-d H:i:s', $date_time);
                                            $date_time = strtotime($price->dateTo);
                                            $date_to   = date('Y-m-d H:i:s', $date_time);
                                            $yachtPrice =  Price::sync(

                                                $yachtSeason->xml_id,
                                                $yachtSeason->season_id,
                                                $date_from,
                                                $date_to,
                                                floatval($price->price),
                                                $price->currency,
                                                $price->type,
                                                intval($obj->id)
                                            );

                                            if (!$yachtPrice) {
                                                return null;
                                            }


                                            if ($yachtPrice && is_array($price->locationsId)) {
                                                foreach ($price->locationsId as $location_id) {
                                                    // var_dump("bemegy"); exit;
                                                    $yachtPriceLocation = YachtPriceLocation::sync($xmlId, $yachtPrice->id, $location_id);
                                                    if (!$yachtPriceLocation) {
                                                        return null;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    echo "Árak szinkron vége (szezon szerint); ";
                                    echo "Regulár discount eleje (szezon szerint);";
                                    if ($yachtSeason && is_array($season->regularDiscounts)) {
                                        foreach ($season->regularDiscounts as $regularDiscount) {

                                            //if(empty($regularDiscount->discountItemId)) { return null;}
                                            $discount = RegularDiscount::sync($xmlId, $obj->id, intval($season->seasonId), intval($regularDiscount->discountItemId), floatval($regularDiscount->amount), $regularDiscount->type);
                                            /*   if (!$discount){
                                                    return null;
                                                } */
                                        }
                                    }
                                    echo "Regulár discount vége (szezon szerint);";
                                }
                            }
                        }
                    }
                } else {
                    $yacht->is_active = 0;
                    $yacht->save(0);
                }
            } else {

                exit("baj van az xml csat-al yacht-ban");
                $return = false;
            }
        }
        echo "Árak  és Regulár discount szinkronok vége; ";
        return $return;
    }
    /**
     * 
     * Yacht Adatok Szinkronja ()
     */

    public static function syncroniseYachtAndServices($yacht_id)
    {
        $cred = new Nausys();
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml) {
            $xmlId = $xml->id;
        };

        $yacht = Yacht::findOne(['xml_id' => $xmlId, 'id' => $yacht_id]);
        $return = false;
        if ($yacht) {
            $return = true;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$restYachturl . $yacht->xml_json_id);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,  $cred->getJsonCredentials());

            $header = array('Content-Type: application/json');

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $exec = curl_exec($ch);
            curl_close($ch);
            if ($exec) {
                $Obj = json_decode($exec);

                if ($Obj->status == "OK") {
                    $objName = self::$objectname;

                    $objectes = $Obj->$objName;

                    foreach ($objectes as $obj) {
                        echo "\n Belép a ciklusba\n";
                        $returnId = $yacht->id;
                        if ($returnId) {
                            YachtSeasonService::inactiveAllThisYacht($returnId);
                            //seasonSpecificData // <- ár syncron
                            //szezon és ár 
                            if (isset($obj->seasonSpecificData) && is_array($obj->seasonSpecificData)) {

                                foreach ($obj->seasonSpecificData as $season) {
                                    //var_dump($season); exit;
                                    $yachtSeason = YachtSeason::sync(
                                        $xmlId,
                                        $season->seasonId,
                                        $season->baseId,
                                        $season->locationId,
                                        intval($obj->id),
                                    );
                                    if (empty($yachtSeason)) {
                                        return null;
                                    }

                                    echo "services eleje; \n";
                                    if ($yachtSeason && is_array($season->services)) {
                                        foreach ($season->services as $service) {
                                            //var_dump($service->amount);exit;

                                            $yachtSeasonService = YachtSeasonService::sync(
                                                $xmlId,
                                                $service->id, //xml_json_id
                                                $obj->id, //yacht_id
                                                $yachtSeason->season_id, //??

                                                $service->serviceId,
                                                $service->price,
                                                $service->currency,
                                                $service->priceMeasureId,
                                                $service->calculationType,
                                                $service->obligatory ? 1 : 0,

                                                is_array($service->description) && count($service->description) > 0 ? $service->description['textEN'] : '',
                                                $service->amount,

                                                isset($service->validForBases) ? 1 : 0,

                                                isset($service->amountIsPercentage) ? ($service->amountIsPercentage ? 1 : 0) : null,
                                                isset($service->percentageCalculationType) ? $service->percentageCalculationType : null,
                                                isset($service->validPeriodFrom) ? $service->validPeriodFrom : null,
                                                isset($service->validPeriodTo) ? $service->validPeriodTo : null,
                                                isset($service->validMinPax) ? $service->validMinPax : null,
                                                isset($service->validMaxPax) ? $service->validMaxPax : null,
                                                isset($service->minimumPrice) ? $service->minimumPrice : null
                                            );
                                            if ($yachtSeasonService && isset($service->validForBases) && is_array($service->validForBases)) {
                                                foreach ($service->validForBases as $base) {
                                                    ServicesValidForBases::saveModel(
                                                        $xmlId,
                                                        $service->serviceId,
                                                        $base,
                                                        1,
                                                        $obj->id,
                                                        $season->seasonId
                                                    );
                                                }
                                            }
                                        }
                                    }
                                    echo "services vége; \n";
                                }
                            }
                        }
                        echo "\n Kilép a ciklusból\n";
                    }
                }
            } else {
                exit("baj van az xml csat-al yacht-ban");
                $return = false;
            }
        }
        echo "\n hajóId: " . $yacht_id . "\n";
        return $return;
    }


    /**
     * 
     * Yacht Adatok Szinkronja ()
     */

    public static function syncroniseYachtAndStandardEquipment($yacht_id)
    {
        $cred = new Nausys();
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml) {
            $xmlId = $xml->id;
        };

        $yacht = Yacht::findOne(['xml_id' => $xmlId, 'id' => $yacht_id]);
        $return = false;
        if ($yacht) {
            $return = true;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$restYachturl . $yacht->xml_json_id);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,  $cred->getJsonCredentials());

            $header = array('Content-Type: application/json');

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $exec = curl_exec($ch);
            curl_close($ch);
            if ($exec) {
                $Obj = json_decode($exec);

                if ($Obj->status == "OK") {
                    $objName = self::$objectname;

                    $objectes = $Obj->$objName;

                    foreach ($objectes as $obj) {
                        echo "\nBelépés a ciklusba\n";
                        $returnId = $yacht->id;
                        if ($returnId) {

                            StandardEquipment::inactiveAllSeasonInYacht($returnId);

                            if (isset($obj->standardYachtEquipment) && is_array($obj->standardYachtEquipment)) {
                                foreach ($obj->standardYachtEquipment as $standardYachtEquipment) {
                                    $comment = is_array($standardYachtEquipment->comment) && isset($standardYachtEquipment->comment['textEN']) ? $standardYachtEquipment->comment['textEN'] : '';
                                    $yachtEquipment = StandardEquipment::sync(
                                        $xmlId,
                                        $obj->id,
                                        $standardYachtEquipment->equipmentId,
                                        $standardYachtEquipment->quantity,
                                        $comment
                                    );
                                    if (!$yachtEquipment) {
                                        echo "\nStandard Equipment Hiba\n";
                                        var_dump($obj);
                                        return null;
                                    }
                                }
                            }
                        }

                        echo "\nKilépés a ciklusból\n";
                    }
                } else {
                    $yacht->is_active = 0;
                    $yacht->save(0);
                }
            } else {

                exit("baj van az xml csat-al yacht-ban");
                $return = false;
            }
        }

        echo "\n hajóId: " . $yacht_id . "\n";
        return $return;
    }

    /**
     * 
     * Yacht Adatok Szinkronja ()
     */

    public static function syncroniseYachtAndAdditionalEquipment($yacht_id)
    {
        $cred = new Nausys();
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml) {
            $xmlId = $xml->id;
        };

        $yacht = Yacht::findOne(['xml_id' => $xmlId, 'id' => $yacht_id]);
        $return = false;
        if ($yacht) {
            $return = true;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$restYachturl . $yacht->xml_json_id);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,  $cred->getJsonCredentials());

            $header = array('Content-Type: application/json');

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $exec = curl_exec($ch);
            curl_close($ch);
            if ($exec) {
                $Obj = json_decode($exec);

                if ($Obj->status == "OK") {
                    $objName = self::$objectname;

                    $objectes = $Obj->$objName;

                    foreach ($objectes as $obj) {
                        echo "\nBelépés a ciklusba\n";
                        $returnId = $yacht->id;
                        if ($returnId) {
                            if (isset($obj->seasonSpecificData) && is_array($obj->seasonSpecificData)) {
                                $seasonSpecificData = $obj->seasonSpecificData;
                                var_dump($seasonSpecificData);
                                if (isset($seasonSpecificData->additionalYachtEquipment) && is_array($seasonSpecificData->additionalYachtEquipment)) {
                                    foreach ($seasonSpecificData->additionalYachtEquipment as $additionalYachtEquipment) {
                                        $comment = is_array($additionalYachtEquipment->comment) && isset($additionalYachtEquipment->comment['textEN']) ? $additionalYachtEquipment->comment['textEN'] : '';
                                       // echo ("hello\n");
                                        $yachtEquipment = AdditionalEquipment::sync(
                                            $xmlId,
                                            $comment,
                                            $additionalYachtEquipment->quantity,
                                            $additionalYachtEquipment->price,
                                            $additionalYachtEquipment->currency,
                                            $seasonSpecificData->id,
                                            $additionalYachtEquipment->equipment_id,
                                            $additionalYachtEquipment->season_id,

                                            $additionalYachtEquipment->price_measure_id,
                                            $additionalYachtEquipment->calculation_type,
                                            $additionalYachtEquipment->condition_string,
                                            $additionalYachtEquipment->amount,
                                            $additionalYachtEquipment->amount_is_percentage,
                                            $additionalYachtEquipment->percentage_calculation_type,
                                            $additionalYachtEquipment->minimum_price, //
                                            $additionalYachtEquipment->id
                                        );
                                        if (!$yachtEquipment) {
                                            echo "\nAdditional Equipment hiba\n";
                                            var_dump($seasonSpecificData);
                                            return null;
                                        }
                                    }
                                }
                            }
                        }
                        echo "\nKilépés a ciklusból\n";
                    }
                } else {
                    $yacht->is_active = 0;
                    $yacht->save(0);
                }
            } else {

                exit("baj van az xml csat-al yacht-ban");
                $return = false;
            }
        }

        echo "\n hajóId: " . $yacht_id . "\n";
        return $return;
    }

    /**
     * 
     * Yacht Adatok Szinkronja ()
     */

    public static function syncroniseYachtAndCheckInPeriod($yacht_id)
    {
        $cred = new Nausys();
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml) {
            $xmlId = $xml->id;
        };

        $yacht = Yacht::findOne(['xml_id' => $xmlId, 'id' => $yacht_id]);
        $return = false;
        if ($yacht) {
            $return = true;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$restYachturl . $yacht->xml_json_id);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,  $cred->getJsonCredentials());

            $header = array('Content-Type: application/json');

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $exec = curl_exec($ch);
            curl_close($ch);
            if ($exec) {
                $Obj = json_decode($exec);

                if ($Obj->status == "OK") {
                    $objName = self::$objectname;

                    $objectes = $Obj->$objName;

                    foreach ($objectes as $obj) {
                        echo "\nBelépés a ciklusba\n";

                        $returnId = $yacht->id;
                        if ($returnId) {
                            if (isset($obj->checkInPeriods) && is_array($obj->checkInPeriods)) {
                                foreach ($obj->checkInPeriods as $checkInPeriod) {
                                    //exit("hello");
                                    $check = CheckInPeriod::sync(
                                        $xmlId,
                                        $obj->id,
                                        date('Y-m-d', strtotime($checkInPeriod->dateFrom)),
                                        date('Y-m-d', strtotime($checkInPeriod->dateTo)),
                                        $checkInPeriod->minimalReservationDuration,
                                        $checkInPeriod->checkInMonday ? 1 : 0,
                                        $checkInPeriod->checkInTuesday ? 1 : 0,
                                        $checkInPeriod->checkInWednesday ? 1 : 0,
                                        $checkInPeriod->checkInThursday ? 1 : 0,
                                        $checkInPeriod->checkInFriday ? 1 : 0,
                                        $checkInPeriod->checkInSaturday ? 1 : 0,
                                        $checkInPeriod->checkInSunday ? 1 : 0,
                                        $checkInPeriod->checkOutMonday ? 1 : 0,
                                        $checkInPeriod->checkOutTuesday ? 1 : 0,
                                        $checkInPeriod->checkOutWednesday ? 1 : 0,
                                        $checkInPeriod->checkOutThursday ? 1 : 0,
                                        $checkInPeriod->checkOutFriday ? 1 : 0,
                                        $checkInPeriod->checkOutSaturday ? 1 : 0,
                                        $checkInPeriod->checkOutSunday ? 1 : 0
                                    );
                                    if (!$check) {
                                        echo "\nCheckInPeriodHiba\n";
                                        var_dump($obj);
                                        return null;
                                    }
                                }
                            }
                        }
                        echo "\nKilépés a ciklusból\n";
                    }
                } else {
                    $yacht->is_active = 0;
                    $yacht->save(0);
                }
            } else {
                exit("baj van az xml csat-al yacht-ban");
                $return = false;
            }
        }
        echo "\n hajóId: " . $yacht_id . "\n";
        return $return;
    }
    /**********************************************************
     * Egy adott Yacht és képei szinkronja, specifikus adatok nélkül
     **********************************************************/
    public static function oneYachtSynronise($obj = null, $need_picture = 1)
    {
        $cred = new Nausys();
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml) {
            $xmlId = $xml->id;
        };
        $return = false;

        if ($obj) {
            $return = true;
            var_dump($obj);
            //  if (intval($obj->id)==101300){
            $objObj = new self::$modelName(
                null,
              //  $xmlId,
                intval($obj->id),
                $obj->name,
                1, //->textEN, 1,
                $obj->companyId,
                $obj->baseId,
                $obj->locationId,
                $obj->yachtModelId,
                $obj->draft,
                $obj->cabins,
                $obj->cabinsCrew,
                $obj->berthsCabin,
                $obj->berthsSalon,
                $obj->berthsCrew,
                $obj->berthsTotal,
                $obj->wc,
                isset($obj->wcCrew) ? $obj->wcCrew : 0,
                isset($obj->buildYear) ? $obj->buildYear : 0,
                isset($obj->engines) ? $obj->engines : null,
                isset($obj->enginePower) ? $obj->enginePower : null,
                isset($obj->steeringTypeId) ? $obj->steeringTypeId : null,
                isset($obj->sailTypeId) ? $obj->sailTypeId : null,
                isset($obj->sailRenewed) ? $obj->sailRenewed : null,
                isset($obj->genoaTypeId) ? $obj->genoaTypeId : null,
                isset($obj->genoaRenewed) ? $obj->genoaRenewed : null,
                isset($obj->commission) ? $obj->commission : 0,
                isset($obj->deposit) ? $obj->deposit : 0,
                isset($obj->maxDiscount) ? $obj->maxDiscount : null,
                $obj->fourStarCharter ? 1 : 0,
                isset($obj->charterType) ? $obj->charterType : null,
                isset($obj->propulsionType) ? $obj->propulsionType : null,
                isset($obj->internalUse) ? $obj->internalUse : null,
                isset($obj->launchedYear) ? $obj->launchedYear : null,
                $obj->needsOptionApproval ? 1 : 0,
                $obj->canMakeBookingFixed ? 1 : 0,
                isset($obj->fuelTank) ? $obj->fuelTank : 0,
                isset($obj->waterTank) ? $obj->waterTank : 0,
                isset($obj->mastLength) ? $obj->mastLength : null,
                isset($obj->numberOfRudderBlades) ? $obj->numberOfRudderBlades : null,
                isset($obj->engineBuilderId) ? $obj->engineBuilderId : null,
                isset($obj->hullColor) ? $obj->hullColor : null,
                isset($obj->thirdPartyInsuranceAmount) ? $obj->thirdPartyInsuranceAmount : null,
                isset($obj->thirdPartyInsuranceCurrency) ? $obj->thirdPartyInsuranceCurrency : null,
                isset($obj->maxPersons) ? $obj->maxPersons : null
            );

            $yacht_id = $objObj->sync();
            $returnId = $yacht_id;
            self::syncroniseYachtAndCheckInPeriod($yacht_id);
            self::syncroniseYachtAndStandardEquipment($yacht_id);
            self::syncroniseYachtAndServices($yacht_id);
            self::syncroniseYachtAndPrices($yacht_id);
            self::syncroniseYachtAndCheckInPeriod($yacht_id);
            self::syncroniseYachtAndAdditionalEquipment($yacht_id);
            self::syncroniseYachtAndStandardEquipment($yacht_id);

            var_dump($obj->id);
            var_dump($obj);

            $subReturn = true;
            if (isset($obj->mainPictureUrl))
                var_dump($obj->mainPictureUrl);

            $oldPath = Yii::$app->basePath . '/boat-' . $xmlId . '/' . $returnId;
            if (!file_exists($oldPath) || $need_picture == 1) {
                // Main kép leszedése
                if ($returnId && isset($obj->mainPictureUrl)) {
                    $photo = str_replace(' ', ' ', $obj->mainPictureUrl) . "?w=900";
                    $photoObject = new PhotoSync($returnId, $photo);
                    $subReturn = $subReturn && $photoObject->save($xmlId, 0, 0);
                    $photo = str_replace(' ', ' ', $obj->mainPictureUrl) . "?w=400";
                    $photoObject = new PhotoSync($returnId, $photo);
                    $subReturn = $subReturn && $photoObject->save($xmlId, 0, 1);
                    $photo = str_replace(' ', ' ', $obj->mainPictureUrl) . "?w=600";
                    $photoObject = new PhotoSync($returnId, $photo);
                    $subReturn = $subReturn && $photoObject->save($xmlId, 0, 2);
                } else if ($returnId && isset($obj->picturesURL)) {
                    foreach ($obj->picturesURL as $photo) {
                        var_dump($photo);
                        $photo =  str_replace(' ', ' ', $photo) . "?w=400";
                        $photoObject = new PhotoSync($returnId, $photo);
                        $subReturn = $subReturn && $photoObject->save($xmlId, 0, 1);

                        $photo =  str_replace(' ', ' ', $photo) . "?w=600";
                        $photoObject = new PhotoSync($returnId, $photo);
                        $subReturn = $subReturn && $photoObject->save($xmlId, 0, 2);
                        break;
                    }
                }

                // Egyébb kép leszedése
                if ($returnId && isset($obj->picturesURL)) {
                    //var_dump($obj->picturesURL);
                    $index = 0;
                    foreach ($obj->picturesURL as $photo) {
                        var_dump($photo);
                        if ($index > 9)
                            break;
                        $index++;
                        $photo =  str_replace(' ', ' ', $photo) . "?w=900";
                        $photoObject = new PhotoSync($returnId, $photo);
                        $subReturn = $subReturn && $photoObject->save($xmlId, 0, 0);
                    }
                    $newPath = Yii::$app->basePath . '/boat-' . $xmlId . '/new-' . $returnId;
                    if (file_exists($newPath)) {
                        $oldPath = Yii::$app->basePath . '/boat-' . $xmlId . '/' . $returnId;
                        if (file_exists($oldPath)) {
                            parent::rrmdir($oldPath);
                        }
                        @rename($newPath, $oldPath);
                    }
                }
            }
            $return = $returnId;
        }


        return $return;
    }
        /**********************************************************
     * Yachtok és képek szinkronja, specifikus adatok nélkül
     **********************************************************/

    public static function yachtSynronise2($companyId, $need_picture = 0)
    { 
        $cred = new Nausys();
        $xmlId = 0;
        
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml) {
            $xmlId = $xml->id;

        };
        $return = false;
        var_dump($companyId);
        $company = Company::findOne($companyId);
        if ($company) :
            $return = true;
            self::inactiveRows(intval($xmlId), $company->xml_json_id);
            echo "inactive rows; ";
            Price::inactiveAll($xmlId, $company->xml_json_id);
            echo "inactive price; ";

            //YachtPriceLocation::inactiveAll($prId, $xmlId, $company->xml_json_id);
            YachtSeason::inactiveAllSeason($xmlId, $company->xml_json_id);
            echo "inactive yachtSession; ";

            RegularDiscount::inactiveAll($xmlId, $company->xml_json_id);
            echo "inactive regularDiscounts; ";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$resturl . $company->xml_json_id);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,  $cred->getJsonCredentials());

            $header = array('Content-Type: application/json');

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $exec = curl_exec($ch);
            curl_close($ch);

            if ($exec) {
                $Obj = json_decode($exec);

                if ($Obj->status == "OK") {
                    $objName = self::$objectname;
                    $objectes = $Obj->$objName;
                    foreach ($objectes as $obj) {
                        var_dump($obj);
                        //  if (intval($obj->id)==101300){
                        $objObj = new self::$modelName(
                            null,
                         //   $xmlId,
                            intval($obj->id),
                            $obj->name,
                            1, //->textEN, 1,
                            $obj->companyId,
                            $obj->baseId,
                            $obj->locationId,
                            $obj->yachtModelId,
                            $obj->draft,
                            $obj->cabins,
                            $obj->cabinsCrew,
                            $obj->berthsCabin,
                            $obj->berthsSalon,
                            $obj->berthsCrew,
                            $obj->berthsTotal,
                            $obj->wc,
                            isset($obj->wcCrew) ? $obj->wcCrew : 0,
                            isset($obj->buildYear) ? $obj->buildYear : 0,
                            isset($obj->engines) ? $obj->engines : null,
                            isset($obj->enginePower) ? $obj->enginePower : null,
                            isset($obj->steeringTypeId) ? $obj->steeringTypeId : null,
                            isset($obj->sailTypeId) ? $obj->sailTypeId : null,
                            isset($obj->sailRenewed) ? $obj->sailRenewed : null,
                            isset($obj->genoaTypeId) ? $obj->genoaTypeId : null,
                            isset($obj->genoaRenewed) ? $obj->genoaRenewed : null,
                            isset($obj->commission) ? $obj->commission : 0,
                            isset($obj->deposit) ? $obj->deposit : 0,
                            isset($obj->maxDiscount) ? $obj->maxDiscount : null,
                            $obj->fourStarCharter ? 1 : 0,
                            isset($obj->charterType) ? $obj->charterType : null,
                            isset($obj->propulsionType) ? $obj->propulsionType : null,
                            isset($obj->internalUse) ? $obj->internalUse : null,
                            isset($obj->launchedYear) ? $obj->launchedYear : null,
                            $obj->needsOptionApproval ? 1 : 0,
                            $obj->canMakeBookingFixed ? 1 : 0,
                            isset($obj->fuelTank) ? $obj->fuelTank : 0,
                            isset($obj->waterTank) ? $obj->waterTank : 0,
                            isset($obj->mastLength) ? $obj->mastLength : null,
                            isset($obj->numberOfRudderBlades) ? $obj->numberOfRudderBlades : null,
                            isset($obj->engineBuilderId) ? $obj->engineBuilderId : null,
                            isset($obj->hullColor) ? $obj->hullColor : null,
                            isset($obj->thirdPartyInsuranceAmount) ? $obj->thirdPartyInsuranceAmount : null,
                            isset($obj->thirdPartyInsuranceCurrency) ? $obj->thirdPartyInsuranceCurrency : null,
                            isset($obj->maxPersons) ? $obj->maxPersons : null
                        );

                        $returnId = $objObj->sync();
                        var_dump($obj->id);
                        var_dump($obj);

                        $subReturn = true;
                        if (isset($obj->mainPictureUrl))
                            var_dump($obj->mainPictureUrl);

                        $oldPath = Yii::$app->basePath . '/boat-' . $xmlId . '/' . $returnId;
                        if (!file_exists($oldPath) || $need_picture == 1) {
                            // Main kép leszedése
                            if ($returnId && isset($obj->mainPictureUrl)) {
                                $photo = str_replace(' ', ' ', $obj->mainPictureUrl) . "?w=900";
                                $photoObject = new PhotoSync($returnId, $photo);
                                $subReturn = $subReturn && $photoObject->save($xmlId, 0, 0);
                                $photo = str_replace(' ', ' ', $obj->mainPictureUrl) . "?w=400";
                                $photoObject = new PhotoSync($returnId, $photo);
                                $subReturn = $subReturn && $photoObject->save($xmlId, 0, 1);
                                $photo = str_replace(' ', ' ', $obj->mainPictureUrl) . "?w=600";
                                $photoObject = new PhotoSync($returnId, $photo);
                                $subReturn = $subReturn && $photoObject->save($xmlId, 0, 2);
                            } else if ($returnId && isset($obj->picturesURL)) {
                                foreach ($obj->picturesURL as $photo) {
                                    var_dump($photo);
                                    $photo =  str_replace(' ', ' ', $photo) . "?w=400";
                                    $photoObject = new PhotoSync($returnId, $photo);
                                    $subReturn = $subReturn && $photoObject->save($xmlId, 0, 1);

                                    $photo =  str_replace(' ', ' ', $photo) . "?w=600";
                                    $photoObject = new PhotoSync($returnId, $photo);
                                    $subReturn = $subReturn && $photoObject->save($xmlId, 0, 2);
                                    break;
                                }
                            }

                            // Egyébb kép leszedése
                            if ($returnId && isset($obj->picturesURL)) {
                                //var_dump($obj->picturesURL);
                                $index = 0;
                                foreach ($obj->picturesURL as $photo) {
                                    var_dump($photo);
                                    if ($index > 9)
                                        break;
                                    $index++;
                                    $photo =  str_replace(' ', ' ', $photo) . "?w=900";
                                    $photoObject = new PhotoSync($returnId, $photo);
                                    $subReturn = $subReturn && $photoObject->save($xmlId, 0, 0);
                                }
                                $newPath = Yii::$app->basePath . '/boat-' . $xmlId . '/new-' . $returnId;
                                if (file_exists($newPath)) {
                                    $oldPath = Yii::$app->basePath . '/boat-' . $xmlId . '/' . $returnId;
                                    if (file_exists($oldPath)) {
                                        parent::rrmdir($oldPath);
                                    }
                                    @rename($newPath, $oldPath);
                                }
                            }
                        }
                        $return = $return && isset($returnId) && $subReturn;
                    }
                
                }
            
            } else {
                exit("baj van az xml csat-al yacht-ban");
                $return = false;
            }
        else :
            return false;
        endif;
        return $return;
    }
    /**********************************************************
     * Yachtok és képek szinkronja, specifikus adatok nélkül
     **********************************************************/

    public static function yachtSynronise($prId, $companyId, $need_picture = 0)
    {
        $cred = new Nausys();
        $xmlId = 0;
        $xml = Xml::findOne(array('slug' => 'nausys'));
        if ($xml) {
            $xmlId = $xml->id;
        };
        $return = false;
        var_dump($companyId);
        $company = Company::findOne($companyId);
        if ($company) :
            $return = true;
            self::inactiveRows(intval($xmlId), $company->xml_json_id);
            echo "inactive rows; ";
            Price::inactiveAll($prId, $xmlId, $company->xml_json_id);
            echo "inactive price; ";

            //YachtPriceLocation::inactiveAll($prId, $xmlId, $company->xml_json_id);
            YachtSeason::inactiveAllSeason($prId, $xmlId, $company->xml_json_id);
            echo "inactive yachtSession; ";

            RegularDiscount::inactiveAll($prId, $xmlId, $company->xml_json_id);
            echo "inactive regularDiscounts; ";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$resturl . $company->xml_json_id);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,  $cred->getJsonCredentials());

            $header = array('Content-Type: application/json');

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $exec = curl_exec($ch);
            curl_close($ch);

            if ($exec) {
                $Obj = json_decode($exec);

                if ($Obj->status == "OK") {
                    $objName = self::$objectname;
                    $objectes = $Obj->$objName;
                    foreach ($objectes as $obj) {
                        var_dump($obj);
                        //  if (intval($obj->id)==101300){
                        $objObj = new self::$modelName(
                            null,
                         //   $xmlId,
                            intval($obj->id),
                            $obj->name,
                            1, //->textEN, 1,
                            $obj->companyId,
                            $obj->baseId,
                            $obj->locationId,
                            $obj->yachtModelId,
                            $obj->draft,
                            $obj->cabins,
                            $obj->cabinsCrew,
                            $obj->berthsCabin,
                            $obj->berthsSalon,
                            $obj->berthsCrew,
                            $obj->berthsTotal,
                            $obj->wc,
                            isset($obj->wcCrew) ? $obj->wcCrew : 0,
                            isset($obj->buildYear) ? $obj->buildYear : 0,
                            isset($obj->engines) ? $obj->engines : null,
                            isset($obj->enginePower) ? $obj->enginePower : null,
                            isset($obj->steeringTypeId) ? $obj->steeringTypeId : null,
                            isset($obj->sailTypeId) ? $obj->sailTypeId : null,
                            isset($obj->sailRenewed) ? $obj->sailRenewed : null,
                            isset($obj->genoaTypeId) ? $obj->genoaTypeId : null,
                            isset($obj->genoaRenewed) ? $obj->genoaRenewed : null,
                            isset($obj->commission) ? $obj->commission : 0,
                            isset($obj->deposit) ? $obj->deposit : 0,
                            isset($obj->maxDiscount) ? $obj->maxDiscount : null,
                            $obj->fourStarCharter ? 1 : 0,
                            isset($obj->charterType) ? $obj->charterType : null,
                            isset($obj->propulsionType) ? $obj->propulsionType : null,
                            isset($obj->internalUse) ? $obj->internalUse : null,
                            isset($obj->launchedYear) ? $obj->launchedYear : null,
                            $obj->needsOptionApproval ? 1 : 0,
                            $obj->canMakeBookingFixed ? 1 : 0,
                            isset($obj->fuelTank) ? $obj->fuelTank : 0,
                            isset($obj->waterTank) ? $obj->waterTank : 0,
                            isset($obj->mastLength) ? $obj->mastLength : null,
                            isset($obj->numberOfRudderBlades) ? $obj->numberOfRudderBlades : null,
                            isset($obj->engineBuilderId) ? $obj->engineBuilderId : null,
                            isset($obj->hullColor) ? $obj->hullColor : null,
                            isset($obj->thirdPartyInsuranceAmount) ? $obj->thirdPartyInsuranceAmount : null,
                            isset($obj->thirdPartyInsuranceCurrency) ? $obj->thirdPartyInsuranceCurrency : null,
                            isset($obj->maxPersons) ? $obj->maxPersons : null
                        );

                        $returnId = $objObj->sync();
                        var_dump($obj->id);
                        var_dump($obj);

                        $subReturn = true;
                        if (isset($obj->mainPictureUrl))
                            var_dump($obj->mainPictureUrl);

                        $oldPath = Yii::$app->basePath . '/boat-' . $xmlId . '/' . $returnId;
                        if (!file_exists($oldPath) || $need_picture == 1) {
                            // Main kép leszedése
                            if ($returnId && isset($obj->mainPictureUrl)) {
                                $photo = str_replace(' ', ' ', $obj->mainPictureUrl) . "?w=900";
                                $photoObject = new PhotoSync($returnId, $photo);
                                $subReturn = $subReturn && $photoObject->save($xmlId, 0, 0);
                                $photo = str_replace(' ', ' ', $obj->mainPictureUrl) . "?w=400";
                                $photoObject = new PhotoSync($returnId, $photo);
                                $subReturn = $subReturn && $photoObject->save($xmlId, 0, 1);
                                $photo = str_replace(' ', ' ', $obj->mainPictureUrl) . "?w=600";
                                $photoObject = new PhotoSync($returnId, $photo);
                                $subReturn = $subReturn && $photoObject->save($xmlId, 0, 2);
                            } else if ($returnId && isset($obj->picturesURL)) {
                                foreach ($obj->picturesURL as $photo) {
                                    var_dump($photo);
                                    $photo =  str_replace(' ', ' ', $photo) . "?w=400";
                                    $photoObject = new PhotoSync($returnId, $photo);
                                    $subReturn = $subReturn && $photoObject->save($xmlId, 0, 1);

                                    $photo =  str_replace(' ', ' ', $photo) . "?w=600";
                                    $photoObject = new PhotoSync($returnId, $photo);
                                    $subReturn = $subReturn && $photoObject->save($xmlId, 0, 2);
                                    break;
                                }
                            }

                            // Egyébb kép leszedése
                            if ($returnId && isset($obj->picturesURL)) {
                                //var_dump($obj->picturesURL);
                                $index = 0;
                                foreach ($obj->picturesURL as $photo) {
                                    var_dump($photo);
                                    if ($index > 9)
                                        break;
                                    $index++;
                                    $photo =  str_replace(' ', ' ', $photo) . "?w=900";
                                    $photoObject = new PhotoSync($returnId, $photo);
                                    $subReturn = $subReturn && $photoObject->save($xmlId, 0, 0);
                                }
                                $newPath = Yii::$app->basePath . '/boat-' . $xmlId . '/new-' . $returnId;
                                if (file_exists($newPath)) {
                                    $oldPath = Yii::$app->basePath . '/boat-' . $xmlId . '/' . $returnId;
                                    if (file_exists($oldPath)) {
                                        parent::rrmdir($oldPath);
                                    }
                                    @rename($newPath, $oldPath);
                                }
                            }
                        }
                        $return = $return && isset($returnId) && $subReturn;
                    }
                }
            } else {
                exit("baj van az xml csat-al yacht-ban");
                $return = false;
            }
        else :
            return false;
        endif;
        return $return;
    }
    /**
     * 
     * Inactive All rows function
     */
    private static function inactiveRows(int $xml_id, $company_id)
    {
        $objName = self::$model;
        $object = $objName::findOne(['xml_id' => $xml_id, 'is_archive' => 0, 'is_active' => 1, 'company_id' =>  $company_id]);
        while ($object) {
            $object->is_active = 0;
            $object->is_new = 0;
            $object->save(0);
            $object = $objName::findOne(['xml_id' => $xml_id, 'is_archive' => 0, 'is_active' => 1, 'company_id' =>  $company_id]);
        }
        return true;
    }
    public static function deleteInactivRows(int $xml_id, $company_id)
    {
        $objName = self::$model;
        $object = $objName::findOne(['xml_id' => $xml_id, 'is_archive' => 0, 'is_active' => 0, 'company_id' =>  $company_id]);
        while ($object) {
            $object->is_active = 0;
            $object->is_archive = 1;
            $object->save(0);
            $wpYachts = WpYacht::findAll(['id' => $object->id]);
            foreach ($wpYachts as $wpYacht) {
                $ch = curl_init(Yii::$app->params['baseurl']."deleteoldposts?id={$wpYacht->wp_prefix}&wp_id={$wpYacht->wp_id}");
                $exec = curl_exec($ch);
            }
            WpYacht::deleteAll(['id' => $object->id]);
            $object = $objName::findOne(['xml_id' => $xml_id, 'is_archive' => 0, 'is_active' => 0, 'company_id' =>  $company_id]);
        }
        return true;
    }
}
