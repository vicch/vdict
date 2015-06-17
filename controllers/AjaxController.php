<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class AjaxController extends Controller
{
    public function actionSearch()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $res = [];

            return $res;
        }
    }
}