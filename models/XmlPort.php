<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "xml_port".
 *
 * @property int $id
 * @property int $port_id
 * @property int $xml_id
 */
class XmlPort extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xml_port';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['port_id', 'xml_id'], 'required'],
            [['port_id', 'xml_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'port_id' => 'Port ID',
            'xml_id' => 'Xml ID',
        ];
    }
}
