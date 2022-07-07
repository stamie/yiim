<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "posts_hasonlitas".
 *
 * @property int $uj
 * @property int $regi
 * @property string $uj_content
 * @property string $regi_content
 * @property string $post_type
 * @property string $post_name
 */
class PostsHasonlitas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts_hasonlitas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uj', 'post_type', 'post_name'], 'required'],
            [['uj', 'regi'], 'integer'],
            [['uj_content', 'regi_content'], 'string'],
            [['post_type'], 'string', 'max' => 20],
            [['post_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'uj' => 'Uj',
            'regi' => 'Regi',
            'uj_content' => 'Uj Content',
            'regi_content' => 'Regi Content',
            'post_type' => 'Post Type',
            'post_name' => 'Post Name',
        ];
    }
}
