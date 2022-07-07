<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "discount_item".
 *
 * @property int $id
 * @property int|null $wp_id
 * @property int $xml_id
 * @property int $xml_json_id
 * @property string $name
 * @property int $is_active
 */
class DiscountItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'discount_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'xml_id', 'xml_json_id', 'name'], 'required'],
            [['id', 'xml_id', 'xml_json_id', 'is_active', 'is_new'], 'integer'],
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
            'is_active' => 'Is Active',
        ];
    }
}
