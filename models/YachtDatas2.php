<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "yacht_datas2".
 *
 * @property int $id
 * @property int $xml_id
 * @property float|null $engine_power
 * @property int|null $engines
 * @property int|null $steering_type_id
 * @property int|null $sail_type_id
 * @property int|null $sail_renewed
 * @property int|null $genoa_type_id
 * @property int|null $genoa_renewed
 */
class YachtDatas2 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yacht_datas2';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'xml_id'], 'required'],
            [['id', 'xml_id', 'engines', 'steering_type_id', 'sail_type_id', 'sail_renewed', 'genoa_type_id', 'genoa_renewed'], 'integer'],
            [['engine_power'], 'number'],
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
            'engine_power' => 'Engine Power',
            'engines' => 'Engines',
            'steering_type_id' => 'Steering Type ID',
            'sail_type_id' => 'Sail Type ID',
            'sail_renewed' => 'Sail Renewed',
            'genoa_type_id' => 'Genoa Type ID',
            'genoa_renewed' => 'Genoa Renewed',
        ];
    }
}
