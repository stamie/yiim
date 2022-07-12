<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "services_valid_for_bases".
 *
 * @property int $id
 * @property int|null $wp_id
 
 * @property int $xml_id
 * @property int $xml_json_id
 * @property int $service_id
 * @property int $base_id
 * @property int $is_active
 */
class ServicesValidForBases extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'services_valid_for_bases';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'xml_id', 'service_id', 'base_id', 'yacht_id', 'season_id'], 'required'],
            [['id', 'xml_id', 'service_id', 'base_id', 'is_active', 'yacht_id', 'season_id'], 'integer'],
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
            'service_id' => 'Service ID',
            'base_id' => 'Base ID',
            'is_active' => 'Is Active',
            'yacht_id' => 'Yacht Id',
            'season_id' => 'Season Id',
        ];
    }
    public static function saveModel(
        $xml_id,
        $service_id,
        $base_id,
        $is_active,
        $yacht_id,
        $season_id
    ) {
        $servicesValidForBases = ServicesValidForBases::findOne([
            'xml_id' => $xml_id,
            'service_id' => $service_id,
            'base_id' => $base_id,
            'yacht_id' => $yacht_id,
            'season_id' => $season_id,
        ]);
        if (!$servicesValidForBases){
            $servicesValidForBases = new ServicesValidForBases();
            $servicesValidForBases->xml_id = $xml_id;
            $servicesValidForBases->service_id = $service_id;
            $servicesValidForBases->base_id = $base_id;
            $servicesValidForBases->yacht_id = $yacht_id;
            $servicesValidForBases->season_id = $season_id;
            $servicesValidForBases->save();
        }
        $servicesValidForBases->is_active = $is_active;
        $servicesValidForBases->save(false);
        return $servicesValidForBases;

    }
}
