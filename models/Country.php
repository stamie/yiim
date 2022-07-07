<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property int $id
 * @property int|null $wp_id
 
 * @property int $xml_id
 * @property int $xml_json_id
 * @property string $code
 * @property string $name
 * @property int $is_active
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['xml_id', 'xml_json_id', 'is_active', 'is_new'], 'integer'],
            [['xml_id', 'xml_json_id', 'code', 'name'], 'required'],
            [['code'], 'string', 'max' => 5],
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
            'code' => 'Code',
            'name' => 'Name',
            'is_active' => 'Is Active',
        ];
    }
}
