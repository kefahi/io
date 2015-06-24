<?php

use yii\db\Schema;
use yii\db\Migration;

class m150623_043702_initial_setup extends Migration {
    public function safeUp() {
			$this->createTable('Entity', [
				'id'          => Schema::TYPE_PK,
				'name'        => Schema::TYPE_STRING . ' NOT NULL',
				'uri'         => Schema::TYPE_STRING . ' NOT NULL',
				'source'      => Schema::TYPE_STRING, # Details/pointer on the original source
 				'updated_at'  => Schema::TYPE_TIMESTAMP . ' NOT NULL',
 				'created_at'  => Schema::TYPE_TIMESTAMP . ' NOT NULL',
        'owner_id'    => Schema::TYPE_INTEGER . ' NOT NULL', # For Content only
        'author_id'   => Schema::TYPE_INTEGER . ' NOT NULL', # For Content only
				'description' => Schema::TYPE_STRING, 
				'public_key'  => Schema::TYPE_STRING, # For User only
				'credentials' => Schema::TYPE_STRING, # For User only
				'type'        => Schema::TYPE_STRING . ' NOT NULL', # actor, content, container 
				'sub_type'    => Schema::TYPE_STRING, # user/group, plain/rich/structured/message/wiki/binary, folder
			]);
			$this->addForeignKey('EntityOwner', 'Entity', 'owner_id', 'Entity', 'id');

			$this->createTable('EntityProperty', [
				'name' => Schema::TYPE_STRING . ' NOT NULL',
				'value' => Schema::TYPE_STRING . ' NOT NULL',
				'entity_id' => Schema::TYPE_INTEGER . ' NOT NULL',
			]);
 
			$this->addPrimaryKey('EntityPropertyPrimary', 'EntityProperty', ['entity_id', 'name']);
			$this->addForeignKey('EntityProperty_', 'EntityProperty', 'entity_id', 'Entity', 'id');

			$this->createTable('Datum', [
				'id'          => Schema::TYPE_PK,
				'entity_id' => Schema::TYPE_INTEGER . ' NOT NULL',
				'checksum' => Schema::TYPE_STRING . ' NOT NULL',
				'byte_size' => Schema::TYPE_INTEGER . ' NOT NULL',
				'embedded' => Schema::TYPE_TEXT,
				'format' => Schema::TYPE_STRING, # json,xml,markdown,pdf,sqlitedb,avro,images,video,audio,,binary,xhtml,
				'path' => Schema::TYPE_STRING,
				'type' => Schema::TYPE_INTEGER . ' NOT NULL', # 1: Embedded, 2: External
				]);
			$this->addForeignKey('DatumEntity', 'Datum', 'entity_id', 'Entity', 'id');

			$this->createTable('Link', [
				'id'          => Schema::TYPE_PK,
				'from_id' => Schema::TYPE_INTEGER . ' NOT NULL',
				'to_id' => Schema::TYPE_INTEGER . ' NOT NULL',
			]);
      $this->createIndex('LinkUnique', 'Link', ['from_id', 'to_id'], true);
			$this->addForeignKey('LinkFromEntity', 'Link', 'from_id', 'Entity', 'id');
			$this->addForeignKey('LinkToEntity', 'Link', 'to_id', 'Entity', 'id');
			
			$this->createTable('LinkProperty', [
				'name' => Schema::TYPE_STRING . ' NOT NULL',
				'value' => Schema::TYPE_STRING . ' NOT NULL',
				'link_id' => Schema::TYPE_INTEGER . ' NOT NULL',
			]);
			$this->addPrimaryKey('LinkPropertyPrimary', 'LinkProperty', ['link_id', 'name']);
			$this->addForeignKey('LinkProperty_', 'LinkProperty', 'link_id', 'Link', 'id');
			
			$this->createTable('Change', [
				'id'          => Schema::TYPE_PK,
				'entity_id' => Schema::TYPE_INTEGER . ' NOT NULL',
        'author_id'   => Schema::TYPE_INTEGER . ' NOT NULL',
 				'created_at'  => Schema::TYPE_TIMESTAMP . ' NOT NULL',
				'description' => Schema::TYPE_STRING, 
				'signature' => Schema::TYPE_BINARY . ' NOT NULL',
				'delta' => Schema::TYPE_BINARY . ' NOT NULL',
				'type' => Schema::TYPE_INTEGER . ' NOT NULL', # 0: Create, 1:Update
				
			]);
			$this->addForeignKey('ChangeEntity', 'Change', 'entity_id', 'Entity', 'id');
			$this->addForeignKey('ChangeAuthor', 'Change', 'author_id', 'Entity', 'id');
    }

    public function safeDown() {
			$this->dropTable('Change');
			$this->dropTable('LinkProperty');
			$this->dropTable('Link');
			$this->dropTable('Datum');
			$this->dropTable('EntityProperty');
			$this->dropTable('Entity');
    }
}

# View all constraints on MariaDb
# select TABLE_NAME as 'Table', COLUMN_NAME as 'Column', CONSTRAINT_NAME as 'Constraint', REFERENCED_TABLE_NAME as 'Table',REFERENCED_COLUMN_NAME as 'Columns',ORDINAL_POSITION as 'Position', POSITION_IN_UNIQUE_CONSTRAINT as 'Unique' from information_schema.KEY_COLUMN_USAGE WHERE CONSTRAINT_SCHEMA = 'io';
