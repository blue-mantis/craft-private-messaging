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
use bluemantis\privatemessaging\models\PrivateMessagingThreadsModel;
use bluemantis\privatemessaging\records\PrivateMessagingThreadsRecord;

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
     * @var integer
     */
    public $threadId;

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

    /**
     * @var PrivateMessagingThreadsRecord
     */
    public $thread;

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
            ['threadId', 'exist', 'targetClass' => PrivateMessagingThreadsRecord::class, 'targetAttribute' => ['recipientId' => 'id']],
        ];
    }

    /**
     * set sender object
     *
     * @param instance of craft\records\User
     * @return null
     */
    public function setSender(User $user){
      $this->sender = new UserElement($user->toArray($this->userColumns));
    }

    /**
     * set recipient object
     *
     * @param instance of craft\records\User
     * @return null
     */
    public function setRecipient(User $user){
      $this->recipient = new UserElement($user->toArray($this->userColumns));
    }

    /**
     * set thread object
     *
     * @param instance of PrivateMessagingThreadsRecord
     * @return null
     */
    public function setThread(PrivateMessagingThreadsRecord $thread){
      $this->thread = new PrivateMessagingThreadsModel($thread->toArray());
    }
}
