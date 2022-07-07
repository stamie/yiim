<?php

namespace app\classes\yacht;

use Yii;
use app\models\Yacht;
use app\models\YachtModel;

class PhotoSync {

    const NEW_EXTENSION ='.webp';    
    protected $yachtId;
    protected $yacht;
    protected $url;

    // bankAccounts

    /**
     * 
     * Base functions 
     */

    public function __construct($ID, $url) {
        $this->yachtId = $ID;
        $this->url = $url;
        $this->yacht = Yacht::findOne($this->yachtId);
        if ($this->yacht){
            return $this;
        }

        return 0;

    }

    /**
     * 
     * Additional functions 
     */

    /**
     * 
     * Syncrons function
     */
     
    public function save ( int $xmlId, bool $resize = false, $is_main = 0 ) {
        var_dump($this);
        if ($this) {
            $extension = trim( substr ( $this->url, strrpos( $this->url, '.' ) ), '?w=1600' );
            var_dump($this->url);
            $extension = trim( $extension, '?w=400' );
            $extension = trim( $extension, '?w=900' );

            if ($extension == ".zip")
                return true;
            try {
                
            $img = file_get_contents($this->url);
            if ($img && $img!=""):
            $in = imagecreatefromstring($img); 
            //$in = fsockopen($this->url);
            if ($in){
                $yachtModel = YachtModel::findOne(['xml_id'=>$xmlId,'xml_json_id'=>$this->yacht->yacht_model_id]);
                if ($yachtModel && isset($yachtModel->name))
                    $yachtName = str_replace( [' ', '+', '.'], ['-','-',''], strtolower( $yachtModel->name ));
                else{
                    $yachtName = 'boat';
                    
                }
                while (strpos ( $yachtName, '--' )){
                    $yachtName = str_replace('--', '-', $yachtName);
                }
                $yachtName = trim( $yachtName, '-');
                $extension = trim( substr ( $this->url, strrpos( $this->url, '.' ) ), '?w=1600' );

                $extension = trim( $extension, '?w=400' );
                $extension = trim( $extension, '?w=600' );
                $extension = trim( $extension, '?w=900' );
            
                $name = 1; 
                if ($is_main==1)
                    $name = 'main';
                else if ($is_main==2)
                    $name = 'land_main';
                
                if ($is_main==1)
                    $yachtName = $yachtName.'-'.$this->yacht->build_year.'-';
                else
                    $yachtName = $yachtName.'-'.$this->yacht->build_year.'_';
                
                $outPath = Yii::$app->basePath.'/boat-'.$xmlId.'/new-'.$this->yachtId.'/';

                if (!file_exists($outPath)) {

                    @mkdir($outPath, 0777, 1);
                }
                if ($is_main<1){
                    while (file_exists($outPath.$yachtName.$name.self::NEW_EXTENSION)){
                        
                        $name ++;
                    }
                } else if(!file_exists($outPath.$yachtName.$name.self::NEW_EXTENSION)){
                    @unlink($outPath.$yachtName.$name.self::NEW_EXTENSION);
                }

                imagewebp($in, $outPath.$yachtName.$name.self::NEW_EXTENSION);
            }
        
           return true;
           endif;
           
        } catch (\Throwable $error) {
            var_dump($error);
            return false;
        } 
        }
        
        return false;

        
    }
/*
    $img = file_get_contents('http://www.site.com/image.jpg');

$im = imagecreatefromstring($img);

$width = imagesx($im);

$height = imagesy($im);

$newwidth = '120';

$newheight = '120';

$thumb = imagecreatetruecolor($newwidth, $newheight);

imagecopyresized($thumb, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

imagejpeg($thumb,'/images/myChosenName.jpg'); //save image as jpg

imagedestroy($thumb); 

imagedestroy($im);
*/

}

?>