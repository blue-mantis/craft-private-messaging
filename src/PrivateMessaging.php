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
    public $schemaVersion = '1.0.0';

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

    // Protected Methods
    // =========================================================================

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
