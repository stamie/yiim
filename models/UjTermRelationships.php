<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "uj_term_relationships".
 *
 * @property int $object_id
 * @property int $term_taxonomy_id
 * @property int $term_order
 */
class UjTermRelationships extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uj_term_relationships';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['object_id', 'term_taxonomy_id'], 'required'],
            [['object_id', 'term_taxonomy_id', 'term_order'], 'integer'],
            [['object_id', 'term_taxonomy_id'], 'unique', 'targetAttribute' => ['object_id', 'term_taxonomy_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'object_id' => 'Object ID',
            'term_taxonomy_id' => 'Term Taxonomy ID',
            'term_order' => 'Term Order',
        ];
    }
}
