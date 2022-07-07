<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "yacht_season".
 *
 * @property int $id
 * @property int|null $wp_id
 
 * @property int $xml_id
 * @property int $season_id
 * @property int $is_active
 * @property int $base_id
 * @property int $location_id
 * @property int $yacht_id
 */
class YachtSeason extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yacht_season';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'xml_id', 'season_id', 'is_active', 'base_id', 'location_id', 'yacht_id'], 'integer'],
            [[ 'xml_id', 'season_id', 'base_id', 'location_id', 'yacht_id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            

            'xml_id' => 'Xml ID',
            'season_id' => 'Xml Json ID',
            'is_active' => 'Is Active',
            'base_id' => 'Base ID',
            'location_id' => 'Location ID',
            'yacht_id' => 'Yacht ID',
        ];
    }

    public static function sync(
        
        $xml_id,
        $season_id,
        $base_id,
        $location_id,
        $yacht_id        
    ){
        $yachtSeason = YachtSeason::findOne([
                                            'season_id' => $season_id, //sessionId
                                            'xml_id' => $xml_id,
                                            'yacht_id' => $yacht_id,
                                        ]);
        if ($yachtSeason){
            $yachtSeason->is_active = 1;
            $yachtSeason->base_id = $base_id;
            $yachtSeason->location_id = $location_id;
            $yachtSeason->yacht_id = $yacht_id;

            $yachtSeason->save(0);
            
            return $yachtSeason;
        }

        $yachtSeason = new YachtSeason();
        $yachtSeason->season_id = $season_id;
        $yachtSeason->base_id = $base_id;
        $yachtSeason->location_id = $location_id;
        $yachtSeason->xml_id = $xml_id;
       
        $yachtSeason->yacht_id = $yacht_id;

        if ($yachtSeason->save()){

            return $yachtSeason;
        }


        return 0;
                                
    }

    public static function inactiveAllSeason(
        $wp_prefix,
        $xml_id, 
        $company_id

    ){
        $yachts = Yacht::findAll(['company_id' => $company_id]);
        foreach ($yachts as $yacht):
        $yachtSeason = YachtSeason::findOne([
                                            'yacht_id' => $yacht->xml_json_id,
                                            'xml_id' => $xml_id,
                                            'is_active' => 1,
                                        ]);
        while ($yachtSeason){

            $yachtSeason->is_active = 0;
            $yachtSeason->save(0);

            $yachtSeason = YachtSeason::findOne([
                'yacht_id' => $yacht->xml_json_id,
                'xml_id' => $xml_id,
                'is_active' => 1,
            ]);

        }
    endforeach;
        return;
    }
}
