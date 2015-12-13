<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\controllers\components\WordComponent;
use app\models\word\WordRecord;
use app\views\helpers\WordHelper;

class SenseController extends Controller
{
    /**
     * Add/update senses
     */
    public function actionSave()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $word = self::getRequestWord();
            if (!$word) {
                Yii::$app->response->setStatusCode(403, 'Word not exist.');
                return [];
            }

            $senseId   = str_replace('.', '_', Yii::$app->request->post('sns', ''));
            $expl      = self::textProcess(Yii::$app->request->post('expl', ''));
            $sentences = self::textProcess(Yii::$app->request->post('snts', ''));
            
            $wordSense = $word->snss;
            $newSense = isset($wordSense[$senseId]) ? false : true;
            $wordSense[$senseId] = [
                'expl' => $expl,
                'snts' => empty($sentences[0]) ? [] : $sentences,
            ];
            $word->snss = $wordSense;

            if ($word->update() !== false) {
                if ($newSense) {
                    return WordHelper::outSense($wordId, $senseId, $wordSense[$senseId]);
                } else {
                    return [];
                }
            } else {
                Yii::$app->response->setStatusCode(403, 'Add sense error.');
                return [];
            }
        }
    }

    public function actionDelete()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $word = self::getRequestWord();
            if (!$word) {
                Yii::$app->response->setStatusCode(403, 'Word not exist.');
                return [];
            }

            $senseId = str_replace('.', '_', Yii::$app->request->post('sns', ''));
            $wordSense = $word->snss;
            unset($wordSense[$senseId]);
            $word->snss = $wordSense;

            if ($word->update() === false)
                Yii::$app->response->setStatusCode(403, 'Add sense error.');
            return [];
        }
    }

    /**
     * Add multiple senses
     */
    public function actionAddmulti()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $word = self::getRequestWord();
            if (!$word) {
                Yii::$app->response->setStatusCode(403, 'Word not exist.');
                return [];
            }

            $newSenses = WordComponent::parseSenses();

            $senses = $word->snss;
            $senses = array_merge($senses, $newSenses);
            $word->snss = $senses;

            if ($word->update() === false)
                Yii::$app->response->setStatusCode(403, 'Add sense error.');
            return [];
        }
    }

    /**
     * Get word entity using word id in request params
     * return false if not found
     */
    public static function getRequestWord()
    {
        $id = Yii::$app->request->post('wid', '');
        $word = WordRecord::findOne($id);
        if (empty($word))
            return false;
        return $word;
    }

    /**
     * Break up textarea input and return as array
     */
    public static function textProcess($text)
    {
        $text = str_replace(['[', ']'], ['\\[', '\\]' ], $text);
        $textArr = explode("\n", $text);
        $lines = [];
        foreach ($textArr as $key => $line) {
            if (!empty($line))
                $lines[] = trim($line);
        }
        return $lines;
    }
}