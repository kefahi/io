<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Entity;
use app\models\Group;

class HelloController extends Controller {
    public function actionIndex($message = 'hello world') {
			$entities = Entity::find()->all();
			foreach ($entities as $entity) {
					echo "$entity->id $entity->name " . get_class($entity) . "\n";
			}

			$group = Group::find()->limit(1)->one();
			#echo "$group->id $group->name " . get_class($group) . "\n";
      echo $message . "\n";
    }
}
