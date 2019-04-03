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
use bluemantis\privatemessaging\records\PrivateMessagingRecord;
use bluemantis\privatemessaging\models\PrivateMessagingModel;
use bluemantis\privatemessaging\models\PrivateMessagingThreadsModel;

/**
 * @author    Blue Mantis
 * @package   PrivateMessaging
 * @since     1.0.1
 */
class PrivateMessagingThreadsRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%private_messaging_threads}}';
    }

    protected function defineAttributes()
    {
      return array(
        'id' => AttributeType::Number,
        'excerpt' => AttributeType::String,
        'subject' => AttributeType::String,
        'siteId' => AttributeType::Number
      );
    }

    public function getMessages()
    {
      return $this->hasMany(PrivateMessagingRecord::class, ['threadId' => 'id']);
    }

    public function getModel()
    {
      $model = new PrivateMessagingThreadsModel($this->toArray());

      if($this->messages){
        foreach($this->messages as $message){
          $model->messages[] = new PrivateMessagingModel($message->toArray());
        }
      }

      return $model;
    }

    public function rules()
    {
        return [
            ['subject', 'string', 'max' => 255],
            ['excerpt', 'string', 'max' => 255],
            ['siteId', 'integer'],
        ];
    }
}
