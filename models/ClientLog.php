<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "client_log".
 *
 * @property int $id
 * @property int $user_id
 * @property string $logtime
 * @property string $random_string
 * @property int $yacht_id
 * @property string $date_from
 * @property string $date_to
 * @property int $is_used
 * @property string $ip_address
 */
class ClientLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'random_string', 'yacht_id', 'date_from', 'date_to', 'ip_address'], 'required'],
            [['user_id', 'yacht_id', 'is_used'], 'integer'],
            [['logtime', 'date_from', 'date_to'], 'safe'],
            [['random_string'], 'string'],
            [['ip_address'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'logtime' => 'Logtime',
            'random_string' => 'Random String',
            'yacht_id' => 'Yacht ID',
            'date_from' => 'Date From',
            'date_to' => 'Date To',
            'is_used' => 'Is Used',
            'ip_address' => 'Ip Address',
        ];
    }
}
