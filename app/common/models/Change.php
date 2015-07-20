<?php

namespace common\models;

use Yii;

class Change extends \yii\db\ActiveRecord
{
    public static function tableName() { return 'Change'; }

    public function rules() {
        return [
            [['entity_id', 'author_id', 'signature', 'delta', 'type'], 'required'],
            [['entity_id', 'author_id', 'type'], 'integer'],
            [['created_at'], 'safe'],
            [['signature', 'delta'], 'string'],
            [['description'], 'string', 'max' => 255]
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'entity_id' => 'Entity ID',
            'author_id' => 'Author ID',
            'created_at' => 'Created At',
            'description' => 'Description',
            'signature' => 'Signature',
            'delta' => 'Delta',
            'type' => 'Type',
        ];
    }

    public function getAuthor() {
        return $this->hasOne(Entity::className(), ['id' => 'author_id']);
    }

    public function getEntity() {
        return $this->hasOne(Entity::className(), ['id' => 'entity_id']);
    }

    public function beforeSave($insert)
    {
        if (!$this->created_at) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }
}
