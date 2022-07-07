<?php

namespace app\models;
use app\models\PortsInCities;
use Yii;

/**
 * This is the model class for table "city_destination".
 *
 * @property int $id
 * @property int $wp_id
 * @property int $post_id
 * @property int $xml_id
 * @property int $city_id
 */
class CityDestination extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city_destination';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wp_id', 'post_id', 'xml_id', 'city_id'], 'required'],
            [['wp_id', 'post_id', 'xml_id', 'city_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wp_id' => 'Wp ID',
            'post_id' => 'Post ID',
            'xml_id' => 'Xml ID',
            'city_id' => 'City ID',
        ];
    }

    public function getPorts() {
        return PortsInCities::find()->where(['cities_id' => $this->city_id, 'xml_id' => $this->xml_id])->all();
    }

    public static function getAllPortsForAllCities($destId, $xml_id, $wp_prefix) {
        $array = [];
        $allCitiesDestination = CityDestination::find()->where(['post_id' => $destId, 'xml_id' => $xml_id, 'wp_id' => $wp_prefix])->all();
        
        if (is_array($allCitiesDestination)){
            foreach ($allCitiesDestination as $cityDestination) {
                $array = array_merge($array, $cityDestination->getPorts());
            }
        }
        $return = [];
        foreach ($array as $value) {
            $return[] = $value->xml_json_port_id;
        }
        return $return;
    }

    public static function getAllPortsForAllCitiesDestIds($destIds = array(), $xml_id, $wp_prefix) {
        $arrayDestIds = [];
        $array = [];
        if (is_array($destIds)){
            foreach ($destIds as $destId){
                $dest = Posts::findOne(['ID' => intval($destId)]);
                if ($dest){
                    $children = $dest->allChildren();
                    if (is_array($children) && count($children)>0)
                        $arrayDestIds = array_merge($arrayDestIds, $children);
                    $arrayDestIds[] = $destId; 
                }
            }
        }
        
        if (is_array($arrayDestIds)){
            foreach ($arrayDestIds as $destId){
                $array_ = self::getAllPortsForAllCities($destId, $xml_id, $wp_prefix);
                $array  = array_merge($array, $array_);
            }
        } 
        return $array;
    }
}
