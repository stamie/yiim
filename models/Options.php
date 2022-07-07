<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rentx_options".
 *
 * @property int $option_id
 * @property string $option_name
 * @property string $option_value
 * @property string $autoload
 */
class Options extends \yii\db\ActiveRecord
{
    public static $prefix = 'rentx_';


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
       
        return self::$prefix.'options';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['option_value'], 'required'],
            [['option_value'], 'string'],
            [['option_name'], 'string', 'max' => 191],
            [['autoload'], 'string', 'max' => 20],
            [['option_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'option_id' => 'Option ID',
            'option_name' => 'Option Name',
            'option_value' => 'Option Value',
            'autoload' => 'Autoload',
        ];
    }
}
