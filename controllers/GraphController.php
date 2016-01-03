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

            $i = 0;
            // Avoid duplications
            $existIds = [$id];

            // Attach 1st degree connections       
            foreach ($word['conns'] as $connType => $connGroup) {
                $colorName = Yii::$app->params['connStyles'][$connType];
                $color     = Yii::$app->params['colors'][$colorName];

                // $group = [
                //     'leaves' => [],
                //     'color'  => $color,
                // ];

                foreach ($connGroup as $id => $conn) {
                    $i++;
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
                    $existIds[] = $id;

                    // Attach 2nd degree connections
                    $connWord = WordRecord::findOne($id);
                    if (!empty($connWord)) {
                        $source = $i;

                        foreach ($connWord['conns'] as $connType2 => $connGroup2) {
                            $colorName2 = Yii::$app->params['connStyles'][$connType2];
                            $color2     = Yii::$app->params['colors'][$colorName2];

                            // $group = [
                            //     'leaves' => [],
                            //     'color'  => $color,
                            // ];

                            foreach ($connGroup2 as $id2 => $conn2) {
                                if (in_array($id2, $existIds)) {
                                    continue;
                                }
                                $i++;
                                $graph['nodes'][] = [
                                    'id'    => $id2,
                                    'name'  => $conn2['t_name'],
                                    'class' => $connType2,
                                    'color' => $color2,
                                ];
                                $graph['links'][] = [
                                    'source' => $source,
                                    'target' => $i,
                                    'class'  => $connType2,
                                ];
                                // $group['leaves'][] = $i;
                                $existIds[] = $id;
                            }
                        }
                    }
                }

                // $graph['groups'][] = $group;
            }

            return $graph;
        }
    }
}