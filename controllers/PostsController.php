<?php
/**
 * 
 * Szinkronizációs kontroll
 * 
 * Készítette: Stampel Emese Ágota
 * Év: 2021
 * 
 */
namespace app\controllers;
use app\models\Posts;
use app\models\UjPosts;
use app\models\PostsHasonlitas;
class PostsController extends \yii\web\Controller
{
    const SEARCH_URL  = "https://boattheglobe.com";
    const REPLACE_URL = "https://boattheglobe.ca";

    public function actionPosts(){
        $condition = ["post_type" => "post", "post_status" => "publish"];
        $ujPosts = UjPosts::findAll($condition);
        return $this->ujPostsLoop($ujPosts);
    }
    public function actionPages(){
        $condition = ["post_type" => "page", "post_status" => "publish"];
        $ujPosts = UjPosts::findAll($condition);
        return $this->ujPostsLoop($ujPosts);
    }

    public function actionDest(){
        $condition = ["post_type" => "destination", "post_status" => "publish"];
        $ujPosts = UjPosts::findAll($condition);
        return $this->ujDestsLoop($ujPosts);
    }
    private function ujDestsLoop($ujPosts)
    {
        if ($ujPosts && is_array($ujPosts)){
            foreach ($ujPosts as $ujPost){
                $condition = ["post_type" => $ujPost->post_type, "post_status" => "publish", "post_name" => $ujPost->post_name, "ID" => $ujPost->ID];
                $regiPost = Posts::findOne($condition);
                if (isset($regiPost)) { 
                    $postsHasonlitas = new PostsHasonlitas();
                    $postsHasonlitas->regi_content = $regiPost->post_content;
                    $postsHasonlitas->uj_content   = $ujPost->post_content;
                    $postsHasonlitas->regi         = $regiPost->ID;
                    $postsHasonlitas->uj           = $ujPost->ID;
                    $postsHasonlitas->post_type    = $ujPost->post_type;
                    $postsHasonlitas->post_name    = $ujPost->post_name;
                    if(!$postsHasonlitas->save())
                    var_dump($postsHasonlitas->errors);
                    $regiPost->post_content = str_replace( self::SEARCH_URL, self::REPLACE_URL, $ujPost->post_content);
                    $regiPost->save(0);
                } else {
                    $regiPost = new Posts();
                    foreach ($ujPost->attributes as $name => $value){
                        $regiPost->$name = $value;
                    }
                    $regiPost->post_excerpt          = (isset($ujPost->post_excerpt) && $ujPost->post_excerpt!='')?$ujPost->post_excerpt:'no';
                    $regiPost->to_ping               = (isset($ujPost->to_ping) && $ujPost->to_ping!='')?$ujPost->to_ping:'no';
                    $regiPost->pinged                = (isset($ujPost->pinged) && $ujPost->pinged!='')?$ujPost->pinged:'no';
                    $regiPost->post_content_filtered = (isset($ujPost->post_content_filtered) && $ujPost->post_content_filtered!='')?$ujPost->post_content_filtered:'no';
                    $regiPost->post_title            = $ujPost->post_title;
                    $regiPost->post_content = str_replace( self::SEARCH_URL, self::REPLACE_URL, $ujPost->post_content);
                    $regiPost->ID = null;
                    if(!$regiPost->save()){
                        var_dump($regiPost->errors);
                        var_dump($ujPost->post_excerpt);
                    }
                    $regiPost->guid = str_replace(self::SEARCH_URL, self::REPLACE_URL, $regiPost->guid);
                    $regiPost->save(0);
                    echo $regiPost->ID.'; ';
                    $postsHasonlitas = new PostsHasonlitas();
                    $postsHasonlitas->uj_content   = $ujPost->post_content;
                    $postsHasonlitas->uj           = $ujPost->ID;
                    $postsHasonlitas->regi         = $regiPost->ID;
                    $postsHasonlitas->post_type    = $ujPost->post_type;
                    $postsHasonlitas->post_name    = $ujPost->post_name;
                    
                    if(!$postsHasonlitas->save())
                    var_dump($postsHasonlitas->errors);
                }
            }
            return 1;
        }
        return 0;
    }
    private function ujPostsLoop($ujPosts)
    {
        if ($ujPosts && is_array($ujPosts)){
            foreach ($ujPosts as $ujPost){
                $condition = ["post_type" => $ujPost->post_type, "post_status" => "publish", "post_name" => $ujPost->post_name];
                $regiPost = Posts::find()->where($condition)->one();
                if (isset($regiPost)) { 
                    $postsHasonlitas = new PostsHasonlitas();
                    $postsHasonlitas->regi_content = $regiPost->post_content;
                    $postsHasonlitas->uj_content   = $ujPost->post_content;
                    $postsHasonlitas->regi         = $regiPost->ID;
                    $postsHasonlitas->uj           = $ujPost->ID;
                    $postsHasonlitas->post_type    = $ujPost->post_type;
                    $postsHasonlitas->post_name    = $ujPost->post_name;
                    if(!$postsHasonlitas->save())
                    var_dump($postsHasonlitas->errors);
                    $regiPost->post_content = str_replace( self::SEARCH_URL, self::REPLACE_URL, $ujPost->post_content);
                    $regiPost->save(0);
                } else {
                    $regiPost = new Posts();
                    foreach ($ujPost->attributes as $name => $value){
                        $regiPost->$name = $value;
                    }
                    $regiPost->post_excerpt          = (isset($ujPost->post_excerpt) && $ujPost->post_excerpt!='')?$ujPost->post_excerpt:'no';
                    $regiPost->to_ping               = (isset($ujPost->to_ping) && $ujPost->to_ping!='')?$ujPost->to_ping:'no';
                    $regiPost->pinged                = (isset($ujPost->pinged) && $ujPost->pinged!='')?$ujPost->pinged:'no';
                    $regiPost->post_content_filtered = (isset($ujPost->post_content_filtered) && $ujPost->post_content_filtered!='')?$ujPost->post_content_filtered:'no';
                    $regiPost->post_title            = $ujPost->post_title;
                    $regiPost->post_content = str_replace( self::SEARCH_URL, self::REPLACE_URL, $ujPost->post_content);
                    $regiPost->ID = null;
                    if(!$regiPost->save()){
                        var_dump($regiPost->errors);
                        var_dump($ujPost->post_excerpt);
                    }
                    $regiPost->guid = self::REPLACE_URL."?".$regiPost->post_type."_id=".$regiPost->ID;
                    $regiPost->save(0);
                    
                    $postsHasonlitas = new PostsHasonlitas();
                    $postsHasonlitas->uj_content   = $ujPost->post_content;
                    $postsHasonlitas->uj           = $ujPost->ID;
                    $postsHasonlitas->regi         = $regiPost->ID;
                    $postsHasonlitas->post_type    = $ujPost->post_type;
                    $postsHasonlitas->post_name    = $ujPost->post_name;
                    
                    if(!$postsHasonlitas->save())
                    var_dump($postsHasonlitas->errors);
                }
            }
            return 1;
        }
        return 0;
    }
}

?>