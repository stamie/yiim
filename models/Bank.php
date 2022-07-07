<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bank".
 *
 * @property int $id
 
 * @property int $xml_id
 * @property int $is_active
 * @property int $company_id
 * @property string $bank_name
 * @property string $bank_address
 * @property string $account_number
 * @property string|null $swift
 * @property string|null $iban
 */
class Bank extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bank';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'xml_id', 'company_id', 'bank_name', 'bank_address', 'account_number'], 'required'],
            [[ 'xml_id', 'is_active', 'company_id'], 'integer'],
            [['bank_name', 'bank_address', 'account_number', 'swift', 'iban'], 'string', 'max' => 100],
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
            'is_active' => 'Is Active',
            'company_id' => 'Company ID',
            'bank_name' => 'Bank Name',
            'bank_address' => 'Bank Address',
            'account_number' => 'Account Number',
            'swift' => 'Swift',
            'iban' => 'Iban',
        ];
    }
}
