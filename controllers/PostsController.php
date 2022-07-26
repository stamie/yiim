<?php

namespace app\controllers;

use Yii;

class PostsController extends \yii\web\Controller
{
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            $this->redirect('/web/site/login');
        }
        $files = scandir('../models');
        foreach ($files as $key => $file) {
            if ($file == '.' || $file == '..' || !strpos($file, 'Posts')) {
                unset($files[$key]);
            }
            if ($file == 'Posts.php') {
                unset($files[$key]);
            }
        }
        $models = [];
        foreach ($files as $file) {
            $models[trim('app\models\\' . $file, '.php')] = trim('app\models\\' . $file, '.php');
        }
        return $this->render('index', ['models' => $models]);
    }

    public function actionSearch()
    {
        if (Yii::$app->user->isGuest) {
            $this->redirect('/web/site/login');
        }
        $request = Yii::$app->request;
        $mit = $request->post('mit');
        $mivel = $request->post('mivel');
        $postTipus = $request->post('tipus') ? $request->post('tipus') : 'destination';

        if ($mit == $mivel) {
            $this->redirect('index');
        }
        $postMiket = $mit::findAll(['post_type' => $postTipus]);
        $postMikel = [];
        foreach ($postMiket as $key => $postMit) {
            $postMikel[$key] = $mivel::findAll(['post_type' => $postTipus, 'post_name' => $postMit->post_name]);
            if (!$postMikel[$key])
                $postMikel[$key] = $mivel::findAll(['post_type' => $postTipus]);
        }
        return $this->render('search', ['postMiket' => $postMiket, 'postMikel' => $postMikel]);
    }

    public function actionReplace()
    {
        if (Yii::$app->user->isGuest) {
            $this->redirect('/web/site/login');
        }
        $request = Yii::$app->request;

        return $this->render('replace', []);
    }
}
