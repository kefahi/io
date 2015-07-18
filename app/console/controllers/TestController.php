<?php
namespace console\controllers;

use Yii;

class TestController extends \yii\console\Controller {

	public function actionIndex() {
		echo "Hello world\n";
		Yii::$app->filestore->hello('world');
	}
}
