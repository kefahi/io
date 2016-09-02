<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Entity".
 *
 * @property integer $id
 * @property string $name
 * @property string $uri
 * @property string $source
 * @property string $updated_at
 * @property string $created_at
 * @property integer $owner_id
 * @property integer $author_id
 * @property string $description
 * @property string $public_key
 * @property string $credentials
 * @property integer $type
 *
 * @property Change[] $changes
 * @property Change[] $changes0
 * @property Datum[] $data
 * @property Entity $owner
 * @property Entity[] $entities
 * @property EntityProperty[] $entityProperties
 * @property Link[] $links
 * @property Link[] $links0
 */
class Entity extends \yii\db\ActiveRecord
{
    public static function tableName() { return 'Entity'; }

    public function rules() {
        return [
            [['name', 'uri', 'owner_id', 'author_id', 'type'], 'required'],
            [['updated_at', 'created_at'], 'safe'],
            [['owner_id', 'author_id', 'type'], 'integer'],
            [['name', 'uri', 'source', 'description', 'public_key', 'credentials'], 'string', 'max' => 255]
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('io', 'ID'),
            'name' => Yii::t('io', 'Name'),
            'uri' => Yii::t('io', 'Uri'),
            'source' => Yii::t('io', 'Source'),
            'updated_at' => Yii::t('io', 'Updated At'),
            'created_at' => Yii::t('io', 'Created At'),
            'owner_id' => Yii::t('io', 'Owner ID'),
            'author_id' => Yii::t('io', 'Author ID'),
            'description' => Yii::t('io', 'Description'),
            'public_key' => Yii::t('io', 'Public Key'),
            'credentials' => Yii::t('io', 'Credentials'),
            'type' => Yii::t('io', 'Type'),
        ];
    }

    # Changes authors ... we don't need this
    # public function getChanges() {
    #    return $this->hasMany(Change::className(), ['author_id' => 'id']);
    # }

    public function getChanges() {
        return $this->hasMany(Change::className(), ['entity_id' => 'id']);
    }

    public function getData() {
        return $this->hasMany(Datum::className(), ['entity_id' => 'id']);
    }

    public function getOwner() {
        return $this->hasOne(Entity::className(), ['id' => 'owner_id']);
    }

    public function getEntities() {
        return $this->hasMany(Entity::className(), ['owner_id' => 'id']);
    }

    public function getEntityProperties() {
        return $this->hasMany(EntityProperty::className(), ['entity_id' => 'id']);
    }

    public function getFromLinks() {
        return $this->hasMany(Link::className(), ['from_id' => 'id']);
    }

    public function getToLinks() {
        return $this->hasMany(Link::className(), ['to_id' => 'id']);
    }

	public static function instantiate($row) {
		switch ($row['type']) {
			case Actor::TYPE:
					return (isset($row['sub_type']) && Group::TYPE == $row['sub_type'])? new Group : new User();
			case Content::TYPE:
					return new Content();
			case Container::TYPE:
					return new Container();
			default:
				 return new self;
		}
	}

    public function beforeSave($insert)
    {
        if (!$this->created_at) {
            $this->created_at = date('Y-m-d H:i:s');
        } else {
            $this->updated_at = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

    public static function getEntityTypes()
    {
        return [Actor::TYPE, Content::TYPE, Container::TYPE];
    }
}
