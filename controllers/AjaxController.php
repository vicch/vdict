<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\word\WordRecord;
use app\views\helpers\Word;

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

    /**
     * Add word
     */
    public function actionAddword()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $data = [
                'lang' => Yii::$app->request->post('lang', 'en'),
                'name' => Yii::$app->request->post('name', null),
            ];
            $word = self::addWord($data);

            if ($word !== false) {
                return ['link' => '/home?wid=' . (string)$word->_id];
            } else {
                Yii::$app->response->setStatusCode(403, 'Add word error.');
                return [];
            }
        }
    }

    public function actionSavesense()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $wordId = Yii::$app->request->post('wid', '');
            $word   = WordRecord::findOne($wordId);
            if (empty($word)) {
                Yii::$app->response->setStatusCode(403, 'Word not exist.');
                return [];
            }

            $senseId   = str_replace('.', '_', Yii::$app->request->post('sns', ''));
            $expl      = explode("\n", str_replace(['[', ']'], ['\\[', '\\]' ], Yii::$app->request->post('expl', '')));
            $sentences = explode("\n", str_replace(['[', ']'], ['\\[', '\\]' ], Yii::$app->request->post('snts', '')));
            
            $wordSense = $word->snss;
            $wordSense[$senseId] = [
                'expl' => $expl,
                'snts' => empty($sentences[0]) ? [] : $sentences,
            ];
            $word->snss = $wordSense;

            if ($word->update() !== false) {
                return Word::outSense($wordId, $senseId, $wordSense[$senseId]);
            } else {
                Yii::$app->response->setStatusCode(403, 'Add sense error.');
                return [];
            }
        }
    }

    public function actionDeletesense()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $wordId = Yii::$app->request->post('wid', '');
            $word   = WordRecord::findOne($wordId);
            if (empty($word)) {
                Yii::$app->response->setStatusCode(403, 'Word not exist.');
                return [];
            }

            $senseId = str_replace('.', '_', Yii::$app->request->post('sns', ''));
            $wordSense = $word->snss;
            unset($wordSense[$senseId]);
            $word->snss = $wordSense;

            if ($word->update() !== false) {
                return [];
            } else {
                Yii::$app->response->setStatusCode(403, 'Add sense error.');
                return [];
            }
        }
    }

    public function actionAddconn()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $fromId    = Yii::$app->request->post('fid', '');
            $fromLang  = Yii::$app->request->post('flang', '');
            $fromName  = Yii::$app->request->post('fname', '');
            $fromSense = Yii::$app->request->post('fsns', '');
            
            $connType   = Yii::$app->request->post('conn', '');
            $reConnType = Yii::$app->params['connTypes'][$connType];

            $toLang  = Yii::$app->request->post('tlang', '');
            $toName  = Yii::$app->request->post('tname', '');
            $toSense = Yii::$app->request->post('tsns', '');

            $fromWord = WordRecord::findOne($fromId);
            if (empty($fromWord)) {
                Yii::$app->response->setStatusCode(403, 'Word #1 not exist.');
                return [];
            }

            $toWord = WordRecord::findOne(['lang' => $toLang, 'name' => $toName]);
            if (empty($toWord)) {
                $data = [
                    'lang' => $toLang,
                    'name' => $toName,
                ];
                $toWord = self::addWord($data);
                if ($toWord === false) {
                    Yii::$app->response->setStatusCode(403, 'Word #2 not exist.');
                    return [];
                }
            }

            $fromWordConns = $fromWord->conns;
            $fromWordConns[$connType][] = [
                'f_sns'  => str_replace('.', '_', $fromSense),
                't_id'   => (string)$toWord->_id,
                't_lang' => $toLang,
                't_name' => $toName,
                't_sns'  => str_replace('.', '_', $toSense),
            ];
            $fromWord->conns = $fromWordConns;

            $toWordConns = $toWord->conns;
            $toWordConns[$reConnType][] = [
                'f_sns'  => str_replace('.', '_', $toSense),
                't_id'   => $fromId,
                't_lang' => $fromLang,
                't_name' => $fromName,
                't_sns'  => str_replace('.', '_', $fromSense),
            ];
            $toWord->conns = $toWordConns;

            if ($fromWord->update() !== false && $toWord->update() !== false) {
                $toWord['conn_type'] = $connType;
                return Word::outWordRight($toWord);
            } else {
                Yii::$app->response->setStatusCode(403, 'Connection add error.');
                return [];
            }
        }
    }

    private static function addWord($data)
    {
        $word = new WordRecord;
        $word->lang  = $data['lang'];
        $word->name  = $data['name'];
        $word->snss  = [];
        $word->conns = [];
        $word->atime = date('Y/m/d H:i:s', time());

        if ($word->insert() === true) {
            return $word;
        } else {
            return false;
        }
    }
}