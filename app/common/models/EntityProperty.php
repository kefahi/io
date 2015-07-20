<?php

namespace common\models;

use Yii;

class EntityProperty extends \yii\db\ActiveRecord
{
    public static function tableName() { return 'EntityProperty'; }

    public function rules()
    {
        return [
            [['name', 'value', 'entity_id'], 'required'],
            [['entity_id'], 'integer'],
            [['name', 'value'], 'string', 'max' => 255]
        ];
    }

    public function attributeLabels() {
        return [
            'name' => 'Name',
            'value' => 'Value',
            'entity_id' => 'Entity ID',
        ];
    }

    public function getEntity() {
        return $this->hasOne(Entity::className(), ['id' => 'entity_id']);
    }
}
