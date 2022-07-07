<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "yacht_season_location".
 *
 * @property int $season_id
 * @property int $location_id
 * @property int $is_active
 * @property int $xml_id
 
 */
class YachtSeasonLocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yacht_season_location';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['season_id', 'location_id', 'xml_id'], 'required'],
            [['season_id', 'location_id', 'is_active', 'xml_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'season_id' => 'Season ID',
            'location_id' => 'Location ID',
            'is_active' => 'Is Active',
            'xml_id' => 'Xml ID',

        ];
    }
}
