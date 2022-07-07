<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "standard_equipment".
 *
 * @property int $id
 * @property int|null $wp_id
 
 * @property int $xml_id
 * @property int $xml_json_id
 * @property string $name
 * @property int $is_active
 * @property int $quantity
 * @property int $equipment_id
 * @property int $yacht_id
 * @property string $
 */
class StandardEquipment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'standard_equipment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['xml_id', 'is_active', 'quantity', 'equipment_id', 'yacht_id'], 'integer'],
            [['xml_id', 'quantity', 'equipment_id', 'yacht_id'], 'required'],
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

            'name' => 'Name',
            'is_active' => 'Is Active',
            'quantity' => 'Quantity',
            'equipment_id' => 'Equipment ID',
            'yacht_id' => 'Yacht ID',

        ];
    }
    public static function sync(

        $xml_id,
        $yacht_id,
        $equipment_id,
        $quantity,
        $name
    ) {
        $standardEquipment = StandardEquipment::findOne([

            'xml_id' => $xml_id,
            'yacht_id' => $yacht_id,
            'equipment_id' => $equipment_id,
        ]);
        if ($standardEquipment) {
            $standardEquipment->is_active = 1;
            $standardEquipment->quantity = $quantity;
            $standardEquipment->name = $name;

            $standardEquipment->save(0);

            return $standardEquipment;
        }

        $standardEquipment = new StandardEquipment();
        $standardEquipment->equipment_id = $equipment_id;
        $standardEquipment->quantity = $quantity;
        $standardEquipment->name = $name;
        $standardEquipment->xml_id = $xml_id;
        $standardEquipment->yacht_id = $yacht_id;

        if ($standardEquipment->save()) {

            return $standardEquipment;
        }

        return 0;
    }

    public static function inactiveAllSeasonInYacht($yacht_id) {
        $standardEquipment = StandardEquipment::findOne([
            'yacht_id' => $yacht_id,
            'is_active' => 1,
        ]);
        while ($standardEquipment) {
            $standardEquipment->is_active = 0;
            $standardEquipment->save(0);
            $standardEquipment = StandardEquipment::findOne([
                'yacht_id' => $yacht_id,
                'is_active' => 1,
            ]);
        }
        return 1;
    }
}
