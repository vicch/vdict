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
                'lang'  => Yii::$app->request->post('lang', 'en'),
                'name'  => Yii::$app->request->post('name', null),
                'snss'  => self::parseSenses(Yii::$app->request->post('snss', '')),
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
            $expl      = self::textProcess(Yii::$app->request->post('expl', ''));
            $sentences = self::textProcess(Yii::$app->request->post('snts', ''));
            
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

            if ($word->update() === false)
                Yii::$app->response->setStatusCode(403, 'Add sense error.');
            return [];
        }
    }

    public function actionAddconn()
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
                $tWord = self::addWord($new);
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
                return Word::outWordRight($tWord);
            } else {
                Yii::$app->response->setStatusCode(403, 'Connection add error.');
                return [];
            }
        }
    }

    public static function actionUpdateconn()
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

    public static function actionDeleteconn()
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
    private static function extractConnParams()
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
    private static function saveWordConn($word, $params, $wordType)
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

    private static function addWord($data)
    {
        $word = new WordRecord;
        $word->lang  = $data['lang'];
        $word->name  = $data['name'];
        $word->snss  = isset($data['snss']) ? $data['snss'] : [];
        $word->conns = isset($data['conns']) ? $data['conns'] : [];
        $word->atime = date('Y/m/d H:i:s', time());

        if ($word->insert() === true) {
            return $word;
        } else {
            return false;
        }
    }

    /**
     * Break up textarea input and return as array
     */
    private static function textProcess($text)
    {
        $text = str_replace(['[', ']'], ['\\[', '\\]' ], $text);
        $lines = explode("\n", $text);
        foreach ($lines as $key => $line) {
            if (empty($line)) {
                unset($lines[$key]);
            }
        }
        return $lines;
    }

    /**
     * Parse senses input:
     *
     * <senseId>
     * <expl>
     * ...
     *     <sentence>
     *     ...
     *
     * <senseId>
     * ...
     */
    private static function parseSenses($text)
    {
        $senses = [];

        if (empty($text))
            return $senses;

        $text   = str_replace(['[', ']'], ['\\[', '\\]' ], $text);
        $groups = explode("\n\n", $text);

        foreach ($groups as $group) {
            $lines = explode("\n", $group);
            $senseId = str_replace('.', '_', array_shift($lines));
            $expl = [];
            $snts = [];
            foreach ($lines as $line) {
                // Sentences start with spaces, because Evernote does not support tabs
                // Good job, Evernote
                if (preg_match('/^[ ]+/', $line)) {
                    $snts[] = trim($line);
                } else {
                    $expl[] = trim($line);
                }
            }
            $senses[$senseId] = [
                'expl' => $expl,
                'snts' => $snts,
            ];
        }
        return $senses;
    }
}