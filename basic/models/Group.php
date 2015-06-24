<?php
namespace app\models;

class Group extends Actor {
    const SUB_TYPE = 'group';

    public static function find() {
        return new EntityQuery(get_called_class(), ['where' => ['sub_type' => self::SUB_TYPE]]);
    }

    public function beforeSave($insert) {
        $this->sub_type = self::SUB_TYPE;
        return parent::beforeSave($insert);
    }
}
