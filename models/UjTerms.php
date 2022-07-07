<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "uj_terms".
 *
 * @property int $term_id
 * @property string $name
 * @property string $slug
 * @property int $term_group
 */
class UjTerms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uj_terms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['term_group'], 'integer'],
            [['name', 'slug'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'term_id' => 'Term ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'term_group' => 'Term Group',
        ];
    }
}
