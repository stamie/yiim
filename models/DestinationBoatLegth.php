<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "destination_boat_legth".
 *
 * @property int $id
 * @property int $wp_id
 * @property int $destination_id
 * @property int $min_loa
 * @property int $max_loa
 */
class DestinationBoatLegth extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'destination_boat_legth';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wp_id', 'destination_id'], 'required'],
            [['wp_id', 'destination_id', 'min_loa', 'max_loa'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wp_id' => 'Wp ID',
            'destination_id' => 'Destination ID',
            'min_loa' => 'Min Loa',
            'max_loa' => 'Max Loa',
        ];
    }
}
