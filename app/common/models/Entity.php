<?php

namespace app\models;

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
            'id' => 'ID',
            'name' => 'Name',
            'uri' => 'Uri',
            'source' => 'Source',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'owner_id' => 'Owner ID',
            'author_id' => 'Author ID',
            'description' => 'Description',
            'public_key' => 'Public Key',
            'credentials' => 'Credentials',
            'type' => 'Type',
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
}
