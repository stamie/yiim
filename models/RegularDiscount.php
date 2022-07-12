<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "regular_discount".
 *
 * @property int $id
 * @property int|null $wp_id
 * @property int $xml_id
 * @property int $is_active
 * @property int $season_id
 * @property int $discount_item_id
 * @property float $amount
 * @property string $type
 */
class RegularDiscount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'regular_discount';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'xml_id', 'is_active', 'season_id', 'discount_item_id', 'yacht_id'], 'integer'],
            [[ 'xml_id', 'season_id'/*, 'discount_item_id'*/, 'amount', 'type', 'yacht_id'], 'required'],
            [['amount'], 'number'],
            [['type'], 'string', 'max' => 100],
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
            'is_active' => 'Is Active',
            'season_id' => 'Season ID',
            'discount_item_id' => 'Discount Item ID',
            'amount' => 'Amount',
            'type' => 'Type',
        ];
    }

    public static function inactiveAllYatchtDiscounts($yacht_id)
    {
        $yacht = Yacht::findOne($yacht_id);
        if ($yacht) {
            RegularDiscount::updateAll(["is_active" => 0], ["yacht_id" => $yacht->xml_json_id, "xml_id" => $yacht->xml_id]);
            return 1;
        }
        return 0;
    }
    public static function sync(
        $xml_id,
        $yacht_id,
        $season_id,
        $discount_item_id_,
        $amount,
        $type
    ){
        $regularDiscount = RegularDiscount::findOne([
                                'xml_id' => $xml_id,
                                'yacht_id' => $yacht_id,
                                'season_id' => $season_id,
                                'discount_item_id' => $discount_item_id_,
                                'amount' => $amount,
                                'type' => $type,
                            ]);
        if ($regularDiscount){
            $regularDiscount->is_active = 1;
            $regularDiscount->save(0);
            return $regularDiscount;
        }
        $regularDiscount = new RegularDiscount();
        $regularDiscount->xml_id = $xml_id;
        $regularDiscount->season_id = $season_id;
        $regularDiscount->yacht_id = $yacht_id;
        $regularDiscount->discount_item_id = $discount_item_id_;
        $regularDiscount->amount = $amount;
        $regularDiscount->is_active = 1;
        $regularDiscount->type = $type;
        if ($regularDiscount->save()){
            return $regularDiscount;
        }
        return 0;
    }
    public static function inactiveAll(
        $xml_id, 
        $company_id
    ){
        $yachts = Yacht::findAll(['company_id' => $company_id]);
        foreach ($yachts as $yacht):
        $regularDiscount = RegularDiscount::findOne([
                                            'yacht_id' => $yacht->xml_json_id,                                     
                                            'xml_id' => $xml_id,
                                            'is_active' => 1,
                                        ]);
        while ($regularDiscount){
            $regularDiscount->is_active = 0;
            $regularDiscount->save(0);
            $regularDiscount = RegularDiscount::findOne([
                'yacht_id' => $yacht->xml_json_id,
                'xml_id' => $xml_id,
                'is_active' => 1,
            ]);
        }
        endforeach;
        return;
    }
}


