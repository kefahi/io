<?php
namespace common\models;

use yii\db\ActiveQuery;

class EntityQuery extends ActiveQuery {
    public $type;
    public $sub_type;

    public function prepare($builder) {
        if ($this->type !== null) {
            $this->andWhere(['type' => $this->type]);
			if ($this->sub_type !== null) {
				$this->andWhere(['sub_type' => $this->sub_type]);
			}
	   }
        return parent::prepare($builder);
    }
}
