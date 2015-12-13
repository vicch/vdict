<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\controllers\components\WordComponent;

class WordController extends Controller
{
    /**
     * Add word
     */
    public function actionAdd()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $data = [
                'lang' => Yii::$app->request->post('lang', 'en'),
                'name' => Yii::$app->request->post('name', null),
                'snss' => WordComponent::parseSenses(),
            ];
            $word = WordComponent::addWord($data);

            if ($word !== false) {
                return ['link' => '/home?wid=' . (string)$word->_id];
            } else {
                Yii::$app->response->setStatusCode(403, 'Add word error.');
                return [];
            }
        }
    }
}