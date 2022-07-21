<?php

namespace app\classes\discountItem;
use app\models\DestinationDiscountItem;
use app\classes\Sync;

class DiscountItemSync extends Sync{
    private static $model = 'app\models\DiscountItem';
    protected $name;
    /**
     * 
     * Base functions 
     */
    public function __construct($ID = null, $xmlId, $xmlJsonId, $name_, $isActive = 1)
    {
        parent::__construct($ID, $xmlId, $xmlJsonId, $isActive);
        $this->name = $name_;
    }
    /**
     * 
     * Syncrons function
     */
    public function sync () {
        if ($this) {
            $condition = [
                'xml_id' => $this->xml_id,
                'xml_json_id' => $this->xml_json_id,
            ];
            $object = self::$model::findOne($condition);
            if ($object){
                $object->is_active = 1;
                $object->save(0);
                return $object;
            } else {
                $object = new self::$model();
                $object->xml_id = $this->xml_id;
                $object->xml_json_id = $this->xml_json_id;
                $object->name = $this->name;
                $object->is_active = 1;
                if($object->save()) {

                    DestinationDiscountItem::refreshTable($object->id);
                    return $object;
                }
            }
        }
        return false;
    }
}
?>