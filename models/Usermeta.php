<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%usermeta}}".
 *
 * @property int $umeta_id
 * @property int $user_id
 * @property string|null $meta_key
 * @property string|null $meta_value
 */
class Usermeta extends \yii\db\ActiveRecord
{
    
    public static $prefix = '';
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
        return self::$prefix.'usermeta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
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
            'umeta_id' => 'Umeta ID',
            'user_id' => 'User ID',
            'meta_key' => 'Meta Key',
            'meta_value' => 'Meta Value',
        ];
    }
}
