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
use craft\records\User;
use craft\elements\User as UserElement;

/**
 * @author    Blue Mantis
 * @package   PrivateMessaging
 * @since     1.0.0
 */
class PrivateMessagingModel extends Model
{
    protected $userColumns = [
      'id',
      'username',
      'email',
      'firstName',
      'lastName',
      'photoId',
      'admin',
      'locked',
      'suspended',
      'pending',
      'lastLoginDate',
      'dateCreated',
      'dateUpdated'];

    // Public Properties
    // =========================================================================
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string
     */
    public $body;

    /**
     * @var integer
     */
    public $senderId;

    /**
     * @var integer
     */
    public $recipientId;

    /**
     * @var boolean
     */
    public $isRead;

    /**
     * @var integer
     */
    public $siteId;

    /**
     * @var uid
     */
    public $uid;

    /**
     * @var DateTime
     */
    public $dateCreated;

    /**
     * @var DateTime
     */
    public $dateUpdated;

    /**
     * @var User
     */
    public $sender;

    /**
     * @var User
     */
    public $recipient;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject', 'body', 'recipientId'], 'required'],
            ['subject', 'string', 'max' => 255],
            ['body', 'string'],
            ['recipientId', 'exist', 'targetClass' => User::class, 'targetAttribute' => ['recipientId' => 'id']],
            ['recipientId', 'integer', 'message' => 'You need to select recipient'],
        ];
    }

    /**
     * set sender object
     *
     * @param instance ofcraft\records\User
     * @return null
     */
    public function setSender(User $user){
      $this->sender = new UserElement($user->toArray($this->userColumns));
    }

    /**
     * set recipient object
     *
     * @param instance ofcraft\records\User
     * @return null
     */
    public function setRecipient(User $user){
      $this->recipient = new UserElement($user->toArray($this->userColumns));
    }
}
