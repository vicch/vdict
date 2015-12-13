<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\word\WordRecord;

class AjaxController extends Controller
{
    /**
     * Search word
     */
    public function actionSearch()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $name = Yii::$app->request->get('name');
            return WordRecord::find()
                ->select(['lang', 'name'])
                ->where(['like', 'name', $name])
                ->all();
        }
    }
}