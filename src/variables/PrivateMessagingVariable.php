<?php
/**
 * Private Messaging plugin for Craft CMS 3.x
 *
 * Allows sending private messages between users
 *
 * @link      https://bluemantis.com/
 * @copyright Copyright (c) 2019 Blue Mantis
 */

namespace bluemantis\privatemessaging\variables;

use bluemantis\privatemessaging\PrivateMessaging;

use Craft;

/**
 * @author    Blue Mantis
 * @package   PrivateMessaging
 * @since     1.0.0
 */
class PrivateMessagingVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @return integer
     */
    public function unreadMessageCount()
    {
        return PrivateMessaging::$plugin->privateMessagingService->getUnreadMessageCount();
    }

    /**
     * @return array
     */
    public function messages()
    {
        return PrivateMessaging::$plugin->privateMessagingService->getMessages();
    }

    /**
     * @return array
     */
    public function threads()
    {
        return PrivateMessaging::$plugin->privateMessagingService->getThreads();
    }

    /**
     * @return integer
     */
    public function totalMessageCount()
    {
        return PrivateMessaging::$plugin->privateMessagingService->getTotalMessageCount();
    }
}
