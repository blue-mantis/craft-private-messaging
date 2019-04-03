<?php
/**
 * Private Messaging plugin for Craft CMS 3.x
 *
 * Allows sending private messages between users
 *
 * @link      https://bluemantis.com/
 * @copyright Copyright (c) 2019 Blue Mantis
 */

namespace bluemantis\privatemessaging;

use bluemantis\privatemessaging\services\PrivateMessagingService as PrivateMessagingServiceService;
use bluemantis\privatemessaging\variables\PrivateMessagingVariable;
use bluemantis\privatemessaging\twigextensions\PrivateMessagingTwigExtension;
//use bluemantis\privatemessaging\models\Settings;
use bluemantis\privatemessaging\widgets\PrivateMessagingWidget as PrivateMessagingWidgetWidget;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\db\MigrationManager;

use yii\base\Event;

/**
 * Class PrivateMessaging
 *
 * @author    Blue Mantis
 * @package   PrivateMessaging
 * @since     1.0.0
 *
 * @property  PrivateMessagingServiceService $privateMessagingService
 */
class PrivateMessaging extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var PrivateMessaging
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.1';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Craft::$app->view->registerTwigExtension(new PrivateMessagingTwigExtension());

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                //$event->rules['send-message'] = 'private-messaging/messages/send';
                //$event->rules['delete-message'] = 'private-messaging/messages/delete';
            }
        );

        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = PrivateMessagingWidgetWidget::class;
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('privateMessaging', PrivateMessagingVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'private-messaging',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

   /**
     * Modified install method to as the default one doesn't install migrations properly
     *
     * @return null
     */
    public function install()
    {
        if ($this->beforeInstall() === false) {
            return false;
        }

        $migrator = $this->getMigrator();

        // Run the install migration, if there is one
        if (($migration = $this->createInstallMigration()) !== null) {
            try {
                $migrator->migrateUp($migration);
            } catch (MigrationException $e) {
                return false;
            }
        }

        // Mark all existing migrations as applied
        foreach ($migrator->getNewMigrations() as $name) {
            $migrator->migrateUp($this->instantiateMigration($name));
        }

        $this->isInstalled = true;

        $this->afterInstall();

        return null;
    }

    /**
     * Modified uninstall method to as the default one doesn't uninstall migrations properly
     *
     * @return null
     */
    public function uninstall()
    {
        $migrator = $this->getMigrator();

        if ($this->beforeUninstall() === false) {
            return false;
        }

        foreach ($this->getAppliedMigrations($migrator) as $name) {
            $migrator->migrateDown($this->instantiateMigration($name));
        }

        if (($migration = $this->createInstallMigration()) !== null) {
            try {
                $this->getMigrator()->migrateDown($migration);
            } catch (MigrationException $e) {
                return false;
            }
        }

        $this->afterUninstall();

        return null;
    }

    /**
     * Returns the migrations that are applied.
     *
     * @return array The list of installed migrations
     */
    public function getAppliedMigrations(MigrationManager $migrator): array
    {
        $migrations = [];

        // Ignore if the migrations folder doesn't exist
        if (!is_dir($migrator->migrationPath)) {
            return $migrations;
        }

        $history = $migrator->getMigrationHistory();
        $handle = opendir($migrator->migrationPath);

        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = $migrator->migrationPath . DIRECTORY_SEPARATOR . $file;

            if (preg_match('/^(m\d{6}_\d{6}_.*?)\.php$/', $file, $matches) && is_file($path) && isset($history[$matches[1]])) {
                $migrations[] = $matches[1];
            }
        }

        closedir($handle);
        sort($migrations);

        return $migrations;
    }

    // Protected Methods
    // =========================================================================

   /**
     * Instantiates and returns the plugin’s migration, if it exists.
     * Modified createInstallMigration method
     *
     * @return Migration|null The plugin’s migration
     */
    protected function instantiateMigration($name)
    {
        $migrator = $this->getMigrator();
        $class = $migrator->migrationNamespace . '\\' . $name;
        return new $class;
    }

    /**
     * @inheritdoc
     */
    //protected function createSettingsModel()
    //{
        //return new Settings();
    //}

    /**
     * @inheritdoc
     */
    //protected function settingsHtml(): string
    //{
        //return Craft::$app->view->renderTemplate(
            //'private-messaging/settings',
            //[
                //'settings' => $this->getSettings()
            //]
        //);
    //}
}
