<?php
/**
 * Private Messaging plugin for Craft CMS 3.x
 *
 * Allows sending private messages between users
 *
 * @link      https://bluemantis.com/
 * @copyright Copyright (c) 2019 Blue Mantis
 */

namespace bluemantis\privatemessaging\twigextensions;

use bluemantis\privatemessaging\PrivateMessaging;

use Craft;

/**
 * @author    Blue Mantis
 * @package   PrivateMessaging
 * @since     1.0.0
 */
class PrivateMessagingTwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Private Messaging';
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('getPrivateMessage', [$this, 'getPrivateMessage']),
        ];
    }

     /**
     * Get message from id
     *
     * @param int $id
     * @return object $message
     */
    public function getPrivateMessage($id) {
      $message = PrivateMessaging::$plugin->privateMessagingService->getMessage($id);
      return $message;
    }
}
