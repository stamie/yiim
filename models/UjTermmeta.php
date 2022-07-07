<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "uj_termmeta".
 *
 * @property int $meta_id
 * @property int $term_id
 * @property string|null $meta_key
 * @property string|null $meta_value
 */
class UjTermmeta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uj_termmeta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['term_id'], 'integer'],
            [['meta_value'], 'string'],
            [['meta_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'meta_id' => 'Meta ID',
            'term_id' => 'Term ID',
            'meta_key' => 'Meta Key',
            'meta_value' => 'Meta Value',
        ];
    }
}
