<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "boat_option".
 *
 * @property int $id
 * @property int $xml_id
 * @property int $xml_json_id
 * @property string $period_from
 * @property string $period_to
 * @property string $reservation_status
 * @property string $create_date
 * @property string|null $modify_date
 * @property int $user_id
 * @property string $last_name
 * @property string $first_name
 * @property string $country
 * @property string $address
 * @property string $country
 * 
 * @property string $phone_number
 * @property string $email
 * @property int $company
 * @property string|null $vat_number
 */
class BoatOption extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'boat_option';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['xml_id', 'prefix_id', 'period_from', 'period_to', 'reservation_status', 'create_date', 'user_id', 'last_name', 'first_name', 'country', 'phone_number', 'email', 'address', 'city', 'yacht_id'], 'required'],
            [['xml_id', 'prefix_id', 'xml_json_id', 'user_id', 'country', 'company', 'yacht_id', 'send_email'], 'integer'],
            [['period_from', 'period_to', 'create_date', 'xml_json_id', 'modify_date', 'user_price', 'list_price', 'currency', 'send_email', 'message'], 'safe'],
            [['reservation_status', 'vat_number', 'address', 'user_price', 'list_price'], 'string', 'max' => 250],
            [['last_name', 'zip_code'], 'string', 'max' => 50],
            [['first_name', 'phone_number', 'email'], 'string', 'max' => 100],
            [['currency'], 'string', 'max' => 20],
            [['city'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'xml_id' => 'Xml ID',
            'xml_json_id' => 'Xml Json ID',
            'period_from' => 'Period From',
            'period_to' => 'Period To',
            'reservation_status' => 'Reservation Status',
            'create_date' => 'Create Date',
            'modify_date' => 'Modify Date',
            'user_id' => 'User ID',
            'last_name' => 'Last Name',
            'first_name' => 'First Name',
            'country' => 'Country',
            'phone_number' => 'Phone Number',
            'email' => 'Email',
            'company' => 'Company',
            'vat_number' => 'Vat Number',
        ];
    }

    public static function createOptionNausys($user_id, $client, $satus, $xml_json_id, $period_from, $period_to, $yachtId, $listPrice, $userPrice, $currency, $wp_prefix, $message) {

        $xml = Xml::findOne(['slug'=>'nausys']);
        $option = new BoatOption();
        $option->first_name   = $client["name"];
        $option->last_name    = $client["surname"];
        $option->company      = ($client["company"]=="false")?0:1;
        $option->vat_number   = $client["vatNr"];
        $option->address      = $client["address"];
        $option->zip_code     = $client["zip"];
        $option->city         = $client["city"];
        $option->country      = intval($client["countryId"]);
        $option->xml_json_id  = $xml_json_id;
        $option->xml_id       = $xml->id;
        $option->yacht_id     = $yachtId;
        $option->email        = $client["email"];
        $option->phone_number = $client["phone"];
        $option->create_date  = date('Y-m-d H:i:s');
        $option->period_from  = $period_from;
        $option->period_to    = $period_to;
        $option->user_id      = $user_id;
        $option->list_price   = $listPrice;
        $option->user_price   = $userPrice;
        $option->currency     = $currency;
        $option->message      = $message;
        $option->prefix_id    = $wp_prefix;

        $option->reservation_status = $satus;
        if (!$option->save()){
            var_dump($option->errors); exit;
        }
        return $option->id;
    }
    public static function createWrongOptionNausys($user_id, $client, $period_from, $period_to, $yachtId, $wp_prefix) {
        $xml = Xml::findOne(['slug'=>'nausys']);
        $option = new BoatOption();
        $option->first_name   = $client["name"];
        $option->last_name    = $client["surname"];
        $option->company      = ($client["company"]=="false")?0:1;
        $option->vat_number   = $client["vatNr"];
        $option->address      = $client["address"];
        $option->zip_code     = $client["zip"];
        $option->city         = $client["city"];
        $option->country      = intval($client["countryId"]);
        $option->period_from  = $period_from;
        $option->period_to    = $period_to;
        $option->xml_id       = $xml->id;
        $option->yacht_id     = $yachtId;
        $option->user_id      = $user_id;
        
        $option->email        = $client["email"];
        $option->phone_number = $client["phone"];
        $option->create_date  = date('Y-m-d H:i:s');

        $option->reservation_status = 'OPTION_SEND_ERROR';
        $option->prefix_id    = $wp_prefix;
        if (!$option->save()){
            var_dump($option->errors); exit;
        }
    }
}
