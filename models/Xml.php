<?php

namespace app\models;

use Yii;
 
/**
 * This is the model class for table "xml".
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 */
class Xml extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xml';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slug', 'name'], 'required'],
            [['slug', 'name'], 'string', 'max' => 250],
            [['class_name'], 'string', 'max' => 50],
            [['slug'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'slug' => 'Slug',
            'name' => 'Name',
            'class_name' => 'Class Name',
        ];
    }
}
