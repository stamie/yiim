<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cash".
 *
 * @property int $id
 * @property int $xml_id
 * @property string $create_date_time
 * @property string $from_date
 * @property string $json_value
 */
class Cash extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cash';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['xml_id', 'create_date_time', 'from_date', 'json_value'], 'required'],
            [['xml_id', 'duration'], 'integer'],
            [['create_date_time', 'update_date_time', 'from_date'], 'safe'],
            [['json_value'], 'string'],
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
            'create_date_time' => 'Create Date Time',
            'update_date_time' => 'Update Date Time',
            'from_date' => 'From Date',
            'json_value' => 'Json Value',
        ];
    }
}
