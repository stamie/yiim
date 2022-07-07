<?php

namespace app\models;

use Codeception\PHPUnit\ResultPrinter\HTML;
use Yii;

/**
 * This is the model class for table "destination_discount_item".
 *
 * @property int $id
 * @property int $discount_item_id
 * @property int $destination_id
 * @property int $wp_id
 */
class DestinationDiscountItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'destination_discount_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['discount_item_id', 'destination_id'], 'required'],
            [['discount_item_id', 'destination_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'discount_item_id' => 'Discount Item ID',
            'destination_id' => 'Destination ID',
            'wp_id' => 'Wp ID',
        ];
    }

    public static function refreshTable($discount_item_id) {

        $posts =  DestinationDiscountItem::findAll([]);
        $ids = array();

        foreach ($posts as $post){
            if (!in_array($post->destination_id, $ids)){
                $destination = new DestinationDiscountItem();
                $destination->setAttributes($post->getAttributes());
                $destination->discount_item_id = $discount_item_id;
                $destination->id = null;
                $destination->save();
                //var_dump($destination); exit;
                $ids[] = $post->destination_id;
            }
        }
    }
    public function getDiscounts($xml_id)
    {

        return DiscountItem::find()->where(['id' => $this->discount_item_id, 'xml_id' => $xml_id,])->all();
    }
    public static function getAllDiscountsForDestination($destId, $xml_id, $wp_prefix)
    {

        $array = [];
        $allDestinationYachtCategory = DestinationDiscountItem::find()->where(['destination_id' => $destId, 'wp_id' => $wp_prefix])->all();
        $dest = Posts::findOne(['ID' => $destId]);
        while ($dest) {
            if (is_array($allDestinationYachtCategory)) {
                foreach ($allDestinationYachtCategory as $destinationYachtCategory) {
                    $yachtCategories = $destinationYachtCategory->getDiscounts($xml_id);
                    $yachtCategories = is_array($yachtCategories) ? $yachtCategories : [];
                    $array = array_merge($array, $yachtCategories);
                }
            }
            $allDestinationYachtCategory = DestinationDiscountItem::find()->where(['destination_id' => $dest->post_parent, 'wp_id' => $wp_prefix])->all();
            $dest = Posts::findOne(['ID' => $dest->post_parent]);
        }
        $return = [];
        foreach ($array as $value) {
            $return[] = $value->xml_json_id;
        }

        return $return;
    }
}
