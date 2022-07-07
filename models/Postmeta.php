<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rentx_postmeta".
 *
 * @property int $meta_id
 * @property int $post_id
 * @property string|null $meta_key
 * @property string|null $meta_value
 */
class Postmeta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    public static $prefix = '';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {


        if (isset($_GET['id'])) {
            $tablePrefix = TablePrefix::findOne($_GET['id']);
            if ($tablePrefix) {
                self::$prefix = $tablePrefix->prefix;
            }
            
        }
        if (isset($_POST['id'])) {
            $tablePrefix = TablePrefix::findOne($_POST['id']);
            if ($tablePrefix) {
                self::$prefix = $tablePrefix->prefix;
            }
            
        }
       
        return self::$prefix.'postmeta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['post_id'], 'integer'],
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
            'post_id' => 'Post ID',
            'meta_key' => 'Meta Key',
            'meta_value' => 'Meta Value',
        ];
    }
}
