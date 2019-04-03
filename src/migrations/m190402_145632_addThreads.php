<?php

namespace bluemantis\privatemessaging\migrations;

use Craft;
use craft\db\Migration;

/**
 * m190402_145632_addThreads migration.
 */
class m190402_145632_addThreads extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%private_messaging_threads}}');

        if ($tableSchema === null) {
            $this->createTable(
                '{{%private_messaging_threads}}',
                [
                    'id' => $this->primaryKey(),
                    'excerpt' => $this->integer()->notNull(),
                    'subject' => $this->string(255)->notNull()->defaultValue(''),
                    'siteId' => $this->integer()->notNull(),
                    'uid' => $this->uid(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                ]
            );
        }

        $this->addColumn('{{%private_messaging}}', 'threadId', $this->integer());
        $this->alterColumn('{{%private_messaging}}', 'subject', $this->string(255));
        $this->createForeignKeys();
    }

    /**
     * @inheritdoc
     */
    public function createForeignKeys()
    {
      $this->addForeignKey(
          $this->db->getForeignKeyName('{{%private_messaging_threads}}', 'siteId'),
          '{{%private_messaging_threads}}',
          'siteId',
          '{{%sites}}',
          'id',
          'CASCADE',
          'CASCADE'
      );

      $this->addForeignKey(
          $this->db->getForeignKeyName('{{%private_messaging}}', 'threadId'),
          '{{%private_messaging}}',
          'threadId',
          '{{%private_messaging_threads}}',
          'id',
          'CASCADE',
          'CASCADE'
      );
    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
      $this->dropForeignKey(
          $this->db->getForeignKeyName('{{%private_messaging}}', 'threadId'),
          '{{%private_messaging}}');
      $this->dropColumn('{{%private_messaging}}', 'threadId');
      $this->alterColumn('{{%private_messaging}}', 'subject', $this->string(255)->notNull()->defaultValue(''));
      $this->dropTableIfExists('{{%private_messaging_threads}}');
    }
}
