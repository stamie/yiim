<?php

namespace app\models;

use app\models\YachtCategory;

use Yii;

/**
 * This is the model class for table "destination_yacht_category".
 *
 * @property int $id
 * @property int $yacht_category_id
 * @property int $destination_id
 * @property int $wp_id
 */
class DestinationYachtCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'destination_yacht_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['yacht_category_id', 'destination_id', 'wp_id'], 'required'],
            [['yacht_category_id', 'destination_id', 'wp_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'yacht_category_id' => 'Yacht Category ID',
            'destination_id' => 'Destination ID',
            'wp_id' => 'Wp ID',
        ];
    }
    public static function refreshTable($yacht_category_id, $wp_id)
    {

        $posts =  DestinationYachtCategory::findAll(['wp_id' => $wp_id]);
        $ids = array();

        foreach ($posts as $post) {
            if (!in_array($post->destination_id, $ids)) {
                $destination = new DestinationYachtCategory();
                $destination->setAttributes($post->getAttributes());
                $destination->yacht_category_id = $yacht_category_id;
                $destination->id = null;
                $destination->save();
                $ids[] = $post->destination_id;
            }
        }
    }

    public function getYachtCategories($xml_id)
    {

        return YachtCategory::find()->where(['id' => $this->yacht_category_id, 'xml_id' => $xml_id,])->all();
    }

    public static function getAllYachtCategoriesForDestination($destId, $xml_id, $wp_prefix)
    {

        $array = [];
        $allDestinationYachtCategory = DestinationYachtCategory::find()->where(['destination_id' => $destId, 'wp_id' => $wp_prefix])->all();
        $dest = Posts::findOne(['ID' => $destId]);
        while ($dest) {
            if (is_array($allDestinationYachtCategory)) {
                foreach ($allDestinationYachtCategory as $destinationYachtCategory) {
                    $yachtCategories = $destinationYachtCategory->getYachtCategories($xml_id);
                    $yachtCategories = is_array($yachtCategories) ? $yachtCategories : [];
                    $array = array_merge($array, $yachtCategories);
                }
            }
            $allDestinationYachtCategory = DestinationYachtCategory::find()->where(['destination_id' => $dest->post_parent, 'wp_id' => $wp_prefix])->all();
            $dest = Posts::findOne(['ID' => $dest->post_parent]);
        }
        $return = [];
        foreach ($array as $value) {
            $return[] = $value->xml_json_id;
        }

        return $return;
    }
}
