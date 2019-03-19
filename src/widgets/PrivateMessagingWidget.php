<?php
/**
 * Private Messaging plugin for Craft CMS 3.x
 *
 * Allows sending private messages between users
 *
 * @link      https://bluemantis.com/
 * @copyright Copyright (c) 2019 Blue Mantis
 */

namespace bluemantis\privatemessaging\widgets;

use bluemantis\privatemessaging\PrivateMessaging;
use bluemantis\privatemessaging\assetbundles\privatemessagingwidgetwidget\PrivateMessagingWidgetWidgetAsset;

use Craft;
use craft\base\Widget;

/**
 * Private Messaging Widget
 *
 * @author    Blue Mantis
 * @package   PrivateMessaging
 * @since     1.0.0
 */
class PrivateMessagingWidget extends Widget
{

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $message = 'Hello, world.';

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('private-messaging', 'PrivateMessagingWidget');
    }

    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        return Craft::getAlias("@bluemantis/privatemessaging/assetbundles/privatemessagingwidgetwidget/dist/img/PrivateMessagingWidget-icon.svg");
    }

    /**
     * @inheritdoc
     */
    public static function maxColspan()
    {
        return null;
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge(
            $rules,
            [
                ['message', 'string'],
                ['message', 'default', 'value' => 'Hello, world.'],
            ]
        );
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'private-messaging/_components/widgets/PrivateMessagingWidget_settings',
            [
                'widget' => $this
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getBodyHtml()
    {
        Craft::$app->getView()->registerAssetBundle(PrivateMessagingWidgetWidgetAsset::class);

        return Craft::$app->getView()->renderTemplate(
            'private-messaging/_components/widgets/PrivateMessagingWidget_body',
            [
                'message' => $this->message
            ]
        );
    }
}
