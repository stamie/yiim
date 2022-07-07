<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cash_log".
 *
 * @property int $id
 * @property string $type
 * @property string $start_datetime
 * @property string $end_datetime
 * @property string|null $ret_value
 */
class CashLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cash_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'start_datetime'], 'required'],
            [['start_datetime', 'end_datetime'], 'safe'],
            [['type', 'ret_value'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'start_datetime' => 'Start Datetime',
            'end_datetime' => 'End Datetime',
            'ret_value' => 'Ret Value',
        ];
    }
}
