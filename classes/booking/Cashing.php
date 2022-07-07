<?php

namespace app\classes\booking;

use ACP\Sorting\Model\Comment\Author\UserMeta as AuthorUserMeta;
use app\classes\booking\NausysBooking as NausysBooking;
use app\models\Cities;
use Yii;
use app\models\Xml;
use app\models\Yacht;
use app\models\CityDestination;
use app\models\ClientLog;
use app\models\Country;
use app\models\PortsInCities;
use app\models\DestinationYachtCategory;
use app\models\Port;
use app\models\Usermeta;
use app\models\Users;
use app\models\Posts;
use app\models\Options;
use app\models\WpYacht;
use app\models\YachtDatas1;
use app\models\YachtModel;
use LiteSpeed\CDN\Quic;
use yii\helpers\Html;
use yii\db\Query;

class Cashing
{

    protected static $path ="";
    protected static $fileName = "";

    public function saveCashFile($text = '')
    {
        if (self::$path != '' && self::$fileName != '' && $text != '') {
            
            
            if (!is_dir(self::$path)) {
                $mkdir = @mkdir(self::$path, 0777, 1);
               
            }
            /*if (file_exists(self::$path . self::$fileName)) { 
                var_dump(@chmod(self::$path . self::$fileName, 7777));
            }
*/
            
            $file = fopen(self::$path . self::$fileName.'_new', 'w');
            
            
            if (is_dir(self::$path)) {
                if ($file) { 
                    
                    fwrite($file, $text);
                    fclose($file);
                    
                    if (file_exists(self::$path . self::$fileName))
                    @unlink(self::$path . self::$fileName);
                    
                    rename(self::$path . self::$fileName.'_new', self::$path . self::$fileName);
                    @chmod(self::$path . self::$fileName, 0777);
                    return 1;
                }
            }
        }
    }

    public function setPath($text) {

        self::$path = $text;

    }
    public function setFileName($text) {

        self::$fileName = $text;

    }


}
