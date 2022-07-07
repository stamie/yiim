<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wp_yacht".
 *
 * @property int $id
 * @property int|null $wp_id
 * @property int|null $wp_prefix
 * @property string|null $wp_name
 */
class WpYacht extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wp_yacht';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'wp_id', 'wp_prefix'], 'integer'],
            [['wp_name'], 'string', 'max' => 200],
            [['id', 'wp_prefix'], 'unique', 'targetAttribute' => ['id', 'wp_prefix']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wp_id' => 'Wp ID',
            'wp_prefix' => 'Wp Prefix',
            'wp_name' => 'Wp Name',
        ];
    }
}
