<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "destination_berth".
 *
 * @property int $id
 * @property int $wp_id
 * @property int $destination_id
 * @property int $min_berth
 * @property int $max_berth
 */
class DestinationBerth extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'destination_berth';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wp_id', 'destination_id'], 'required'],
            [['wp_id', 'destination_id', 'min_berth', 'max_berth'], 'integer'],
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
            'min_berth' => 'Min Berth',
            'max_berth' => 'Max Berth',
        ];
    }
}
