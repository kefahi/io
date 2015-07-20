<?php
namespace common\models;

class Person extends Actor {
    const SUB_TYPE = 'person';

    public static function find() {
        return new EntityQuery(get_called_class(), ['where' => ['sub_type' => self::SUB_TYPE]]);
    }

    public function beforeSave($insert) {
        $this->sub_type = self::SUB_TYPE;
        return parent::beforeSave($insert);
    }
}
