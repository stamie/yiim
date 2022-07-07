<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property int|null $wp_id
 
 * @property int $xml_id
 * @property int $xml_json_id
 * @property string $name
 * @property int $is_active
 * @property string $address
 * @property string $city
 * @property string $zip
 * @property int $country_id
 * @property string $phone
 * @property string|null $fax
 * @property string|null $mobile
 * @property string $vatcode
 * @property string|null $web
 * @property string $email
 * @property int $pac
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['xml_id', 'xml_json_id', 'is_active', 'country_id', 'pac'], 'integer'],
            [['xml_id', 'xml_json_id', 'name', 'address', 'city', 'zip', 'country_id', 'phone', 'vatcode', 'email', 'pac'], 'required'],
            [['name', 'address', 'city', 'web'], 'string', 'max' => 100],
            [['zip', 'phone', 'fax', 'mobile', 'vatcode'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 125],
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
            'name' => 'Name',
            'is_active' => 'Is Active',
            'address' => 'Address',
            'city' => 'City',
            'zip' => 'Zip',
            'country_id' => 'Country ID',
            'phone' => 'Phone',
            'fax' => 'Fax',
            'mobile' => 'Mobile',
            'vatcode' => 'Vatcode',
            'web' => 'Web',
            'email' => 'Email',
            'pac' => 'Pac',
        ];
    }
}
