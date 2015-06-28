<?php
namespace app\models;

class Actor extends Entity {
    const TYPE = 'actor';

    public static function find() {
        return new EntityQuery(get_called_class(), ['where' => ['type' => self::TYPE]]);
    }

    public function beforeSave($insert) {
        $this->type = self::TYPE;
        return parent::beforeSave($insert);
    }
}
