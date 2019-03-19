<?php
/**
 * Private Messaging plugin for Craft CMS 3.x
 *
 * Allows sending private messages between users
 *
 * @link      https://bluemantis.com/
 * @copyright Copyright (c) 2019 Blue Mantis
 */

namespace bluemantis\privatemessaging\assetbundles\PrivateMessaging;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Blue Mantis
 * @package   PrivateMessaging
 * @since     1.0.0
 */
class PrivateMessagingAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@bluemantis/privatemessaging/assetbundles/privatemessaging/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/PrivateMessaging.js',
        ];

        $this->css = [
            'css/PrivateMessaging.css',
        ];

        parent::init();
    }
}
