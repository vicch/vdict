<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\mongodb\Query;

class TestController extends Controller
{
    // public function actionAdd()
    // {
    //     $word = [
    //         'lang' => 'en',
    //         'name' => 'implicit',
    //         'snss' => [
    //             'adj_1' => [
    //                 'expl' => [
    //                     '\[ implied | indirect \]',
    //                     'not expressly stated',
    //                 ],
    //                 'snts' => [
    //                     'an \[ implicit \] agreement not to raise the touchy subject',
    //                     '…叫摩洛哥人不要继续一周前开始的军事行动的含蓄警告。>> ...an \[ implicit \] warning to Moroccans not to continue the military actions they began a week ago.',
    //                 ],
    //             ]
    //         ],
    //         'conns' => [
    //             'synonym' => [
    //                 [
    //                     'f_sns'  => 'adj_1',
    //                     't_lang' => 'en',
    //                     't_name' => 'inherent',
    //                     't_sns'  => 'adj_1',
    //                 ],
    //                 [
    //                     'f_sns'  => '',
    //                     't_lang' => 'en',
    //                     't_name' => 'indirect',
    //                     't_sns'  => '',
    //                 ],
    //             ],
    //             'antonym' => [
    //                 [
    //                     'f_sns'  => '',
    //                     't_lang' => 'en',
    //                     't_name' => 'explicit',
    //                     't_sns'  => '',
    //                 ]
    //             ],
    //         ],
    //         'atime' => '',
    //     ];

    //     $collection = Yii::$app->mongodb->getCollection('word');
    //     $collection->insert($word);
    // }

    // public function actionHistory()
    // {
    //     $lang = Yii::$app->request->get('lang');
    //     $name = Yii::$app->request->get('name');
    //     $time = date('Y/m/d H:i:s', time());
    //     $collection = Yii::$app->mongodb->getCollection('history');
    //     $collection->insert(compact('lang', 'name', 'time'));
    // }

    // public function actionShow()
    // {
    //     $query = new Query;
    //     $query->from('word')
    //           ->where(['word' => 'implicit']);
    //     $rows = $query->all();
    //     echo var_export($rows); die;
    // }
}