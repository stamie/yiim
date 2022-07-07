<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "yacht_datas3".
 *
 * @property int $id
 * @property int $xml_id
 * @property int $xml_json_id
 * @property float $commission
 * @property float $deposit
 * @property float|null $max_discount
 * @property int $four_star_charter
 * @property int|null $internal_use
 * @property int|null $launched_year
 * @property int $needs_option_approval
 * @property int $can_make_booking_fixed
 * @property string|null $charter_type
 */
class YachtDatas3 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yacht_datas3';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'xml_id', 'xml_json_id', 'commission', 'deposit', 'needs_option_approval', 'can_make_booking_fixed'], 'required'],
            [['id', 'xml_id', 'xml_json_id', 'four_star_charter', 'internal_use', 'launched_year', 'needs_option_approval', 'can_make_booking_fixed'], 'integer'],
            [['commission', 'deposit', 'max_discount'], 'number'],
            [['charter_type'], 'string', 'max' => 200],
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
            'commission' => 'Commission',
            'deposit' => 'Deposit',
            'max_discount' => 'Max Discount',
            'four_star_charter' => 'Four Star Charter',
            'internal_use' => 'Internal Use',
            'launched_year' => 'Launched Year',
            'needs_option_approval' => 'Needs Option Approval',
            'can_make_booking_fixed' => 'Can Make Booking Fixed',
            'charter_type' => 'Charter Type',
        ];
    }
}
