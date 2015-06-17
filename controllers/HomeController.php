<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class HomeController extends Controller
{
    public $layout = 'home';

	public function actionIndex()
	{
		return $this->render('index');
	}
}