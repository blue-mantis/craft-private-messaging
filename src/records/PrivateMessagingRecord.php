<?php
/**
 * Private Messaging plugin for Craft CMS 3.x
 *
 * Allows sending private messages between users
 *
 * @link      https://bluemantis.com/
 * @copyright Copyright (c) 2019 Blue Mantis
 */

namespace bluemantis\privatemessaging\records;

use bluemantis\privatemessaging\PrivateMessaging;

use Craft;
use craft\db\ActiveRecord;
use craft\records\User;

/**
 * @author    Blue Mantis
 * @package   PrivateMessaging
 * @since     1.0.0
 */
class PrivateMessagingRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%private_messaging}}';
    }

    protected function defineAttributes()
    {
      return array(
        'subject' => AttributeType::String,
        'body' => AttributeType::Mixed,
        'senderId' => AttributeType::Number,
        'recipientId' => AttributeType::Number,
        'isRead' => AttributeType::Bool,
        'siteId' => AttributeType::Number,
      );
    }

    public function getSender()
    {
      return $this->hasOne(User::class, ['id' => 'senderId']);
    }

    public function getRecipient()
    {
      return $this->hasOne(User::class, ['id' => 'recipientId']);
    }

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
}
