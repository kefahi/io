<?php

namespace common\models;

use Yii;

class Link extends \yii\db\ActiveRecord
{
    public static function tableName() { return 'Link'; }

    public function rules()
    {
        return [
            [['from_id', 'to_id'], 'required'],
            [['from_id', 'to_id'], 'integer'],
            [['from_id', 'to_id'], 'unique', 'targetAttribute' => ['from_id', 'to_id'], 'message' => 'The combination of From ID and To ID has already been taken.']
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'from_id' => 'From ID',
            'to_id' => 'To ID',
        ];
    }

    public function getFrom() {
        return $this->hasOne(Entity::className(), ['id' => 'from_id']);
    }

    public function getTo() {
        return $this->hasOne(Entity::className(), ['id' => 'to_id']);
    }

    public function getLinkProperties() {
        return $this->hasMany(LinkProperty::className(), ['link_id' => 'id']);
    }
}
