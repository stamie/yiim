<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "yacht_datas1".
 *
 * @property int $id
 * @property int $yacht_model_id
 * @property float $draft
 * @property int $xml_id
 * @property int $cabins
 * @property int $cabins_crew
 * @property int $berths_cabin
 * @property int $berths_salon
 * @property int $berths_crew
 * @property int $berths_total
 * @property int $wc
 * @property int $wc_crew
 */
class YachtDatas1 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yacht_datas1';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'yacht_model_id', 'draft', 'xml_id', 'cabins', 'cabins_crew', 'berths_cabin', 'berths_salon', 'berths_crew', 'berths_total', 'wc', 'wc_crew'], 'required'],
            [['id', 'yacht_model_id', 'xml_id', 'cabins', 'cabins_crew', 'berths_cabin', 'berths_salon', 'berths_crew', 'berths_total', 'wc', 'wc_crew'], 'integer'],
            [['draft'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'yacht_model_id' => 'Yacht Model ID',
            'draft' => 'Draft',
            'xml_id' => 'Xml ID',
            'cabins' => 'Cabins',
            'cabins_crew' => 'Cabins Crew',
            'berths_cabin' => 'Berths Cabin',
            'berths_salon' => 'Berths Salon',
            'berths_crew' => 'Berths Crew',
            'berths_total' => 'Berths Total',
            'wc' => 'Wc',
            'wc_crew' => 'Wc Crew',
        ];
    }
}
