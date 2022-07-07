<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "destination_service_types".
 *
 * @property int $id
 * @property int $wp_id
 * @property int $destination_id
 * @property string $service_types
 */
class DestinationServiceTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'destination_service_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wp_id', 'destination_id', 'service_types'], 'required'],
            [['wp_id', 'destination_id'], 'integer'],
            [['service_types'], 'string', 'max' => 250],
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
            'service_types' => 'Service Types',
        ];
    }
}
