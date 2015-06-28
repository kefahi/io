<?php

namespace app\models;

use Yii;

class LinkProperty extends \yii\db\ActiveRecord
{
    public static function tableName() { return 'LinkProperty'; }

    public function rules() {
        return [
            [['name', 'value', 'link_id'], 'required'],
            [['link_id'], 'integer'],
            [['name', 'value'], 'string', 'max' => 255]
        ];
    }

    public function attributeLabels() {
        return [
            'name' => 'Name',
            'value' => 'Value',
            'link_id' => 'Link ID',
        ];
    }

    public function getLink() {
        return $this->hasOne(Link::className(), ['id' => 'link_id']);
    }
}
