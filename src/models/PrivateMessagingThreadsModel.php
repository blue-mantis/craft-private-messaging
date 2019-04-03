<?php
/**
 * Private Messaging plugin for Craft CMS 3.x
 *
 * Allows sending private messages between users
 *
 * @link      https://bluemantis.com/
 * @copyright Copyright (c) 2019 Blue Mantis
 */

namespace bluemantis\privatemessaging\models;

use bluemantis\privatemessaging\PrivateMessaging;

use Craft;
use craft\base\Model;

/**
 * @author    Blue Mantis
 * @package   PrivateMessaging
 * @since     1.0.1
 */
class PrivateMessagingThreadsModel extends Model
{
    // Public Properties
    // =========================================================================
    /**
     * @var integer
     */
    public $id;

    /**
     * @var uid
     */
    public $uid;

    /**
     * @var string
     */
    public $excerpt;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var integer
     */
    public $siteId;

    /**
     * @var DateTime
     */
    public $dateCreated;

    /**
     * @var DateTime
     */
    public $dateUpdated;

    /**
     * @var array
     */
    public $messages = [];
}
