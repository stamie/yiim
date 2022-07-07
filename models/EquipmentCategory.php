<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "equipment_category".
 *
 * @property int $id
 * @property int $xml_id
 * @property int $xml_json_id
 * @property string $name
 */
class EquipmentCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equipment_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'xml_id', 'xml_json_id', 'name'], 'required'],
            [[ 'xml_id', 'xml_json_id', 'is_active'], 'integer'],
            [['name'], 'string', 'max' => 100],
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
            'name' => 'Name',
            'is_active' => 'Is Active'
        ];
    }
}
