<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\word\WordRecord;

class GraphController extends Controller
{
    public function actionLoad()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $id = Yii::$app->request->post('wid', '');
            $word = WordRecord::findOne($id);

            if (empty($word)) {
                Yii::$app->response->setStatusCode(403, 'Word not found.');
                return [];
            }

            $graph = [
                'nodes'  => [
                    [
                        'name'  => $word['name'],
                        'class' => 'main',
                        'color' => Yii::$app->params['colors']['black'],
                    ]
                ],
                'links'  => [],
                'groups' => []
            ];

            $i = 1;
            foreach ($word['conns'] as $connType => $connGroup) {
                $colorName = Yii::$app->params['connStyles'][$connType];
                $color     = Yii::$app->params['colors'][$colorName];

                // $group = [
                //     'leaves' => [],
                //     'color'  => $color,
                // ];

                foreach ($connGroup as $id => $conn) {
                    $graph['nodes'][] = [
                        'id'    => $id,
                        'name'  => $conn['t_name'],
                        'class' => $connType,
                        'color' => $color,
                    ];
                    $graph['links'][] = [
                        'source' => 0,
                        'target' => $i,
                        'class'  => $connType,
                    ];
                    // $group['leaves'][] = $i;
                    $i++;
                }

                // $graph['groups'][] = $group;
            }

            return $graph;
        }
    }
}