<?php

namespace app\models;

use Yii;

class Datum extends \yii\db\ActiveRecord
{
    public static function tableName() { return 'Datum'; }

    public function rules() {
        return [
            [['entity_id', 'checksum', 'byte_size', 'type'], 'required'],
            [['entity_id', 'byte_size', 'type'], 'integer'],
            [['embedded'], 'string'],
            [['checksum', 'format', 'path'], 'string', 'max' => 255]
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'entity_id' => 'Entity ID',
            'checksum' => 'Checksum',
            'byte_size' => 'Byte Size',
            'embedded' => 'Embedded',
            'format' => 'Format',
            'path' => 'Path',
            'type' => 'Type',
        ];
    }

    public function getEntity() {
        return $this->hasOne(Entity::className(), ['id' => 'entity_id']);
    }
}
