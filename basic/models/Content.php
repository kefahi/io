<?php
namespace app\models;

class Content extends Entity {
    const TYPE = 'content';

    public static function find() {
        return new EntityQuery(get_called_class(), ['where' => ['type' => self::TYPE]]);
    }

    public function beforeSave($insert) {
        $this->type = self::TYPE;
        return parent::beforeSave($insert);
    }
}
