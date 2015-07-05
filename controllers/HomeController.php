<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use MongoId;
use app\models\word\WordRecord;
use app\models\history\HistoryRecord;

class HomeController extends Controller
{
    public $layout = 'home';

	public function actionIndex()
	{
        $word    = $this->findWord();
        $conns   = $this->findConns($word);
        $history = $this->getHistory();

        $this->addHistory($word);

		return $this->render('index', compact('word', 'conns', 'history'));
	}

    private function findWord()
    {
        $wordRecord = $this->findWordById();
        if (!$wordRecord) {
            $wordRecord = $this->findWordByName();
        }
        return $wordRecord;
    }

    private function findWordById()
    {
        $id = Yii::$app->request->get('wid');
        return WordRecord::findOne($id);
    }

    private function findWordByName()
    {
        $lang = Yii::$app->request->get('lang', 'en');
        $name = Yii::$app->request->get('name');
        return WordRecord::findOne(compact('lang', 'name'));
    }

    private function findConns($word)
    {
        if (!isset($word['conns']))
            return [];

        $allConns = [];
        foreach ($word['conns'] as $connType => $conns) {
            foreach ($conns as $key => $conn) {
                $singleConn = WordRecord::findOne($conn['t_id']);
                if (!empty($singleConn)) {
                    $singleConn['conn_type'] = $connType;
                    $allConns[] = $singleConn;
                }
            }
        }
        return $allConns;
    }

    private function getHistory()
    {
        $history = HistoryRecord::find()
            ->orderBy('time DESC')
            ->limit(30)
            ->all();

        // Keep unique words
        $result = [];
        foreach ($history as $key => $value) {
            $result[$value->lang . '_' . $value->name] = $value;
        }

        return $result;
    }

    private function addHistory($word)
    {
        if (empty($word))
            return;

        $history = new HistoryRecord;
        $history->wid  = (string)$word['_id'];
        $history->lang = $word['lang'];
        $history->name = $word['name'];
        $history->time = date('Y/m/d H:i:s', time());
        $history->insert();
    }
}