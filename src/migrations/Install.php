<?php
/**
 * Private Messaging plugin for Craft CMS 3.x
 *
 * Allows sending private messages between users
 *
 * @link      https://bluemantis.com/
 * @copyright Copyright (c) 2019 Blue Mantis
 */

namespace bluemantis\privatemessaging\migrations;

use bluemantis\privatemessaging\PrivateMessaging;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * @author    Blue Mantis
 * @package   PrivateMessaging
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        //$migrator = $this->getMigrator();
        //foreach ($migrator->getNewMigrations() as $name) {
            //dd($name);
            ////$migrator->addMigrationHistory($name);
        //}

        return true;
    }

   /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%private_messaging}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%private_messaging}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                    'subject' => $this->string(255)->notNull()->defaultValue(''),
                    'body' => $this->text(),
                    'senderId' => $this->integer()->notNull(),
                    'recipientId' => $this->integer()->notNull(),
                    'isRead' => $this->boolean()->notNull()->defaultValue(false)
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(
            $this->db->getIndexName(
                '{{%private_messaging}}',
                'recipient_read',
                false
            ),
            '{{%private_messaging}}',
            ['recipientId','isRead'],
            false
        );
    }

    /**
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%private_messaging}}', 'siteId'),
            '{{%private_messaging}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

       $this->addForeignKey(
            $this->db->getForeignKeyName('{{%private_messaging}}', 'senderId'),
            '{{%private_messaging}}',
            'senderId',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

       $this->addForeignKey(
            $this->db->getForeignKeyName('{{%private_messaging}}', 'recipientId'),
            '{{%private_messaging}}',
            'recipientId',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists('{{%private_messaging}}');
    }
}
