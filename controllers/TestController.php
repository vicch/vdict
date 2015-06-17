<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\mongodb\Query;

class TestController extends Controller
{
    public function actionAddword()
    {
        $word = [
            'lang' => 'en',
            'word' => 'implicit',
            'senses' => [
                'adj' => [
                    1 => [
                        'explain' => [
                            '[ implied | indirect ]',
                            'not expressly stated',
                        ],
                        'sents' => [
                            'an implicit agreement not to raise the touchy subject',
                            '…叫摩洛哥人不要继续一周前开始的军事行动的含蓄警告。>> ...an implicit warning to Moroccans not to continue the military actions they began a week ago.',
                        ],
                    ],
                    2 => [
                    ]
                ],
            ],
            'conns' => [
                'synonym' => [
                    'adj_1' => [
                        'en_inherent_adj_1',
                    ],
                    'en_absolute',
                ],
                'antonym' => [
                    'en_explicit',
                ],
            ],
        ];

        $collection = Yii::$app->mongodb->getCollection('word');
        $collection->insert($word);
    }

    public function actionShowword()
    {
        $query = new Query;
        $query->from('word')
              ->where(['word' => 'implicit']);
        $rows = $query->all();
        echo var_export($rows); die;
    }
}