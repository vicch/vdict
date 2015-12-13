<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\controllers\components\WordComponent;
use app\models\word\WordRecord;
use app\views\helpers\WordHelper;

class ConnectionController extends Controller
{
    public function actionAdd()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $params = self::extractConnParams();

            $fWord = WordRecord::findOne($params['fId']);
            if (empty($fWord)) {
                Yii::$app->response->setStatusCode(403, 'Word #1 not exist.');
                return [];
            }

            $tWord = WordRecord::findOne(['lang' => $params['tLang'], 'name' => $params['tName']]);
            // If the connected word does not exist yet, create it
            if (empty($tWord)) {
                $new = [
                    'lang' => $params['tLang'],
                    'name' => $params['tName'],
                ];
                $tWord = WordComponent::addWord($new);
                if ($tWord === false) {
                    Yii::$app->response->setStatusCode(403, 'Word #2 not exist and cannot be created.');
                    return [];
                }
            }
            $params['tId'] = (string)$tWord->_id;

            $fWordUpdate = self::saveWordConn($fWord, $params, 'f');
            $tWordUpdate = self::saveWordConn($tWord, $params, 't');

            if ($fWordUpdate !== false && $tWordUpdate !== false) {
                // Additional data for word rendering
                $tWord['conn_data'] = [
                    'f_sense' => $params['fSense'],
                    't_sense' => $params['tSense'],
                    'type'    => $params['type'],
                ];
                return WordHelper::outWordRight($tWord);
            } else {
                Yii::$app->response->setStatusCode(403, 'Connection add error.');
                return [];
            }
        }
    }

    public static function actionUpdate()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $params = self::extractConnParams();

            // Check if words exist
            $fWord = WordRecord::findOne($params['fId']);
            if (empty($fWord)) {
                Yii::$app->response->setStatusCode(403, 'Word #1 not exist.');
                return [];
            }
            $tWord = WordRecord::findOne($params['tId']);
            if (empty($tWord)) {
                Yii::$app->response->setStatusCode(403, 'Word #2 not exist.');
                return [];
            }

            $fWordUpdate = self::saveWordConn($fWord, $params, 'f');
            $tWordUpdate = self::saveWordConn($tWord, $params, 't');

            if ($fWordUpdate === false || $tWordUpdate === false)
                Yii::$app->response->setStatusCode(403, 'Connection update error.');
            return [];
        }
    }

    public static function actionDelete()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $params = self::extractConnParams();

            // Check if words exist
            $fWord = WordRecord::findOne($params['fId']);
            if (empty($fWord)) {
                Yii::$app->response->setStatusCode(403, 'Word #1 not exist.');
                return [];
            }
            $tWord = WordRecord::findOne($params['tId']);
            if (empty($tWord)) {
                Yii::$app->response->setStatusCode(403, 'Word #2 not exist.');
                return [];
            }

            $fWordConns = $fWord->conns;
            unset($fWordConns[$params['type']][$params['tId']]);
            if (empty($fWordConns[$params['type']]))
                unset($fWordConns[$params['type']]);
            $fWord->conns = $fWordConns;

            $tWordConns = $tWord->conns;
            unset($tWordConns[$params['revType']][$params['fId']]);
            if (empty($tWordConns[$params['revType']]))
                unset($tWordConns[$params['revType']]);
            $tWord->conns = $tWordConns;

            if ($fWord->update() === false || $tWord->update() === false)
                Yii::$app->response->setStatusCode(403, 'Connection delete error.');
            return [];
        }
    }

    /**
     * Extract params from add/update connection requests
     */
    public static function extractConnParams()
    {
        $params = [
            'fId'     => Yii::$app->request->post('fid', ''),
            'fLang'   => Yii::$app->request->post('flang', ''),
            'fName'   => Yii::$app->request->post('fname', ''),
            'fSense'  => str_replace('.', '_', Yii::$app->request->post('fsns', '')),
            'tId'     => Yii::$app->request->post('tid', ''),
            'tLang'   => Yii::$app->request->post('tlang', ''),
            'tName'   => Yii::$app->request->post('tname', ''),
            'tSense'  => str_replace('.', '_', Yii::$app->request->post('tsns', '')),
            'type'    => Yii::$app->request->post('type', ''),
            'revType' => Yii::$app->params['connTypes'][Yii::$app->request->post('type', '')],
        ];
        return $params;
    }

    /**
     * Save word's connection with given params
     */
    public static function saveWordConn($word, $params, $wordType)
    {
        $conns = $word->conns;
        if ($wordType == 'f') {
            $conns[$params['type']][$params['tId']] = [
                'f_sns'  => $params['fSense'],
                't_lang' => $params['tLang'],
                't_name' => $params['tName'],
                't_sns'  => $params['tSense'],
            ];
        } else {
            $conns[$params['revType']][$params['fId']] = [
                'f_sns'  => $params['tSense'],
                't_lang' => $params['fLang'],
                't_name' => $params['fName'],
                't_sns'  => $params['fSense'],
            ];
        }
        $word->conns = $conns;
        return $word->update();
    }
}