<?php
namespace console\controllers;

use Yii;

class IndexController extends \yii\console\Controller {

	public function actionIndex() {
		echo "Calling index\n";
		Yii::$app->filestore->index();
	}
}
