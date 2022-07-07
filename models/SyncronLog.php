<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "syncron_log".
 *
 * @property int $id
 * @property string $date_start
 * @property string $date_end
 * @property string $parent_string
 * @property string $errors
 */
class SyncronLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'syncron_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_start', 'parent_id'], 'required'],
            [['date_start', 'date_end'], 'safe'],
            [['errors'], 'string'],
            [['parent_string'], 'string', 'max' => 250],
            [['parent_id', 'is_automate'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_start' => 'Date Start',
            'date_end' => 'Date End',
            'parent_string' => 'Parent String',
            'errors' => 'Errors',
            'parent_id' => 'Parent ID',
            'is_automate' => 'Is Automate',
        ];
    }

    public static function log($dateStart, $parentString = 'Not Parent', $parentId = 0, $isAutomate = 1) {

        $log = new SyncronLog();
        $log->date_start = $dateStart;
        $log->parent_string = $parentString;
        $log->parent_id = $parentId;
        $log->is_automate = $isAutomate;
        $log->save();

        return $log;

    }

    public function end($endDate, $jsonString = null) {
        $jsonString2 = isset($jsonString)?$jsonString:json_encode([]);
        $this->date_end = $endDate;
        $this->errors = $jsonString2;
        $this->save(0);
    }
}
