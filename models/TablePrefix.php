<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "table_prefix".
 *
 * @property int $id
 * @property string $prefix
 * @property string $url
 */
class TablePrefix extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'table_prefix';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['prefix', 'url'], 'required'],
            [['prefix'], 'string', 'max' => 50],
            [['url'], 'string', 'max' => 250],
            [['prefix'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'prefix' => 'Prefix',
            'url' => 'Url',
        ];
    }
}
