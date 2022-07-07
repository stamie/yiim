<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "price_measure".
 *
 * @property int $id
 * @property int|null $wp_id
 
 * @property int $xml_id
 * @property int $xml_json_id
 * @property int $is_active
 * @property string $name
 */
class PriceMeasure extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'price_measure';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'xml_id', 'xml_json_id', 'name'], 'required'],
            [['id', 'xml_id', 'xml_json_id', 'is_active'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'is_active' => 'Is Active',
            'name' => 'Name',
        ];
    }
}
