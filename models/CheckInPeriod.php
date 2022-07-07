<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "check_in_period".
 *
 * @property int $id
 * @property int $wp_id
 * @property int $xml_id
 * @property int $yacht_id
 * @property string $date_from
 * @property string $date_to
 * @property int $minimal_reservation_duration
 * @property int $check_in_monday
 * @property int $check_in_tuesday
 * @property int $check_in_wednesday
 * @property int $check_in_thursday
 * @property int $check_in_friday
 * @property int $check_in_saturday
 * @property int $check_in_sunday
 * @property int $check_out_monday
 * @property int $check_out_tuesday
 * @property int $check_out_wednesday
 * @property int $check_out_thursday
 * @property int $check_out_friday
 * @property int $check_out_saturday
 * @property int $check_out_sunday
 */
class CheckInPeriod extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'check_in_period';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'xml_id', 'yacht_id', 'date_from', 'date_to', 'minimal_reservation_duration', 'check_in_monday', 'check_in_tuesday', 'check_in_wednesday', 'check_in_thursday', 'check_in_friday', 'check_in_saturday', 'check_in_sunday', 'check_out_monday', 'check_out_tuesday', 'check_out_wednesday', 'check_out_thursday', 'check_out_friday', 'check_out_saturday', 'check_out_sunday'], 'required'],
            [['xml_id', 'yacht_id', 'minimal_reservation_duration', 'check_in_monday', 'check_in_tuesday', 'check_in_wednesday', 'check_in_thursday', 'check_in_friday', 'check_in_saturday', 'check_in_sunday', 'check_out_monday', 'check_out_tuesday', 'check_out_wednesday', 'check_out_thursday', 'check_out_friday', 'check_out_saturday', 'check_out_sunday'], 'integer'],
            [['date_from', 'date_to'], 'safe'],
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
            'yacht_id' => 'Yacht ID',
            'date_from' => 'Date From',
            'date_to' => 'Date To',
            'minimal_reservation_duration' => 'Minimal Reservation Duration',
            'check_in_monday' => 'Check In Monday',
            'check_in_tuesday' => 'Check In Tuesday',
            'check_in_wednesday' => 'Check In Wednesday',
            'check_in_thursday' => 'Check In Thursday',
            'check_in_friday' => 'Check In Friday',
            'check_in_saturday' => 'Check In Saturday',
            'check_in_sunday' => 'Check In Sunday',
            'check_out_monday' => 'Check Out Monday',
            'check_out_tuesday' => 'Check Out Tuesday',
            'check_out_wednesday' => 'Check Out Wednesday',
            'check_out_thursday' => 'Check Out Thursday',
            'check_out_friday' => 'Check Out Friday',
            'check_out_saturday' => 'Check Out Saturday',
            'check_out_sunday' => 'Check Out Sunday',
        ];
    }

    public static function sync(

            $xml_id,
            $yacht_id,
            $date_from,
            $date_to,
            $minimal_reservation_duration,
            $check_in_monday,
            $check_in_tuesday,
            $check_in_wednesday,
            $check_in_thursday,
            $check_in_friday,
            $check_in_saturday,
            $check_in_sunday,
            $check_out_monday,
            $check_out_tuesday,
            $check_out_wednesday,
            $check_out_thursday,
            $check_out_friday,
            $check_out_saturday,
            $check_out_sunday

    ){
        $check = CheckInPeriod::findOne([
            'xml_id'   => $xml_id,
            'yacht_id' => $yacht_id,
            'date_from' => $date_from,
            'date_to' => $date_to,
        
        ]);
        if ($check){    
        
        $check->xml_id = $xml_id;
        $check->yacht_id = $yacht_id;
        $check->date_from = $date_from;
        $check->date_to = $date_to;
        $check->minimal_reservation_duration = $minimal_reservation_duration;
        $check->check_in_monday = $check_in_monday;
        $check->check_in_tuesday = $check_in_tuesday;
        $check->check_in_wednesday = $check_in_wednesday;
        $check->check_in_thursday = $check_in_thursday;
        $check->check_in_friday = $check_in_friday;
        $check->check_in_saturday = $check_in_saturday;
        $check->check_in_sunday = $check_in_sunday;
        $check->check_out_monday = $check_out_monday;
        $check->check_out_tuesday = $check_out_tuesday;
        $check->check_out_wednesday = $check_out_wednesday;
        $check->check_out_thursday = $check_out_thursday;
        $check->check_out_friday = $check_out_friday;
        $check->check_out_saturday = $check_out_saturday;
        $check->check_out_sunday = $check_out_sunday;

        $check->save(0);
        } else {
            $check = new CheckInPeriod();
            
            
            $check->xml_id = $xml_id;
            $check->yacht_id = $yacht_id;
            $check->date_from = $date_from;
            $check->date_to = $date_to;
            $check->minimal_reservation_duration = $minimal_reservation_duration;
            $check->check_in_monday = $check_in_monday;
            $check->check_in_tuesday = $check_in_tuesday;
            $check->check_in_wednesday = $check_in_wednesday;
            $check->check_in_thursday = $check_in_thursday;
            $check->check_in_friday = $check_in_friday;
            $check->check_in_saturday = $check_in_saturday;
            $check->check_in_sunday = $check_in_sunday;
            $check->check_out_monday = $check_out_monday;
            $check->check_out_tuesday = $check_out_tuesday;
            $check->check_out_wednesday = $check_out_wednesday;
            $check->check_out_thursday = $check_out_thursday;
            $check->check_out_friday = $check_out_friday;
            $check->check_out_saturday = $check_out_saturday;
            $check->check_out_sunday = $check_out_sunday;

            $bool = $check->save();

            if (!$bool){
                return null;
            }
        }
        return $check;
    }
}
