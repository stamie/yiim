<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "season".
 *
 * @property int $id
 * @property int|null $wp_id
 
 * @property int $xml_id
 * @property int $xml_json_id
 * @property string $season
 * @property int $is_active
 * @property int $charter_company_id
 * @property string $date_from
 * @property string $date_to
 * @property int $deafult_season
 */
class Season extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'season';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'xml_id', 'xml_json_id', 'is_active', 'charter_company_id', 'deafult_season'], 'integer'],
            [[ 'xml_id', 'xml_json_id', 'season', 'charter_company_id', 'date_from', 'date_to'], 'required'],
            [['date_from', 'date_to'], 'safe'],
            [['season'], 'string', 'max' => 100],
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
            'season' => 'Season',
            'is_active' => 'Is Active',
            'charter_company_id' => 'Charter Company ID',
            'date_from' => 'Date From',
            'date_to' => 'Date To',
            'deafult_season' => 'Deafult Season',
        ];
    }
}
