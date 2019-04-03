<?php
/**
 * Private Messaging plugin for Craft CMS 3.x
 *
 * Allows sending private messages between users
 *
 * @link      https://bluemantis.com/
 * @copyright Copyright (c) 2019 Blue Mantis
 */

namespace bluemantis\privatemessaging\services;

use bluemantis\privatemessaging\PrivateMessaging;
use bluemantis\privatemessaging\models\PrivateMessagingModel;
use bluemantis\privatemessaging\records\PrivateMessagingRecord;
use bluemantis\privatemessaging\records\PrivateMessagingThreadsRecord;

use Craft;
use craft\base\Component;

/**
 * @author    Blue Mantis
 * @package   PrivateMessaging
 * @since     1.0.0
 */
class PrivateMessagingService extends Component
{

    // Public Methods
    // =========================================================================

    /*
     * @return bool
     */
    public function saveMessage(PrivateMessagingModel $model)
    {
      $threadId = $model->threadId;

      if(!$threadId){
        $record = new PrivateMessagingThreadsRecord();
        $record->excerpt = $this->getExcerpt($model->body, 0, 100);
        $record->subject = $model->subject;
        $record->siteId = Craft::$app->sites->getCurrentSite()->id;
        $record->save();

        $threadId = $record->id;
      }

      $record = new PrivateMessagingRecord();
      $record->setAttributes($model->getAttributes());
      $record->isRead = 0;
      $record->senderId = Craft::$app->getUser()->getId();
      $record->siteId = Craft::$app->sites->getCurrentSite()->id;
      $record->threadId = $threadId;
      return $record->save() ? true : false;
    }

    /**
     * Get unread messages
     *
     * @param string $onlyUnread
     * @return array of bluemantis\privatemessaging\models\PrivateMessagingModel
     */
    public function getMessages($onlyUnread = false) {
      $this->getThreads();

      if (!Craft::$app->getUser()) return [];

      $conditions = [
        'recipientId' => Craft::$app->getUser()->getId(),
        'siteId' => Craft::$app->sites->getCurrentSite()->id];

      if ($onlyUnread) {
        $conditions['isRead'] = 0;
      }

      $records = PrivateMessagingRecord::find()
        ->with(['sender','recipient'])
        ->where($conditions)
        ->orderBy(['id' => SORT_DESC])
        ->all();

      $records = array_map([$this, 'assignModel'], $records);
      return $records;
    }

    /**
     * Get messages by thread
     *
     * @return array of bluemantis\privatemessaging\models\PrivateMessagingThreadsModel
     */
    public function getThreads() {
      if (!Craft::$app->getUser()) return [];

      $conditions = ['or',
        ['craft_private_messaging.recipientId' => Craft::$app->getUser()->getId()],
        ['craft_private_messaging.senderId' => Craft::$app->getUser()->getId()]
      ];

      $records = PrivateMessagingThreadsRecord::find()
        ->joinWith(['messages'])
        ->where(['craft_private_messaging_threads.siteId' => Craft::$app->sites->getCurrentSite()->id])
        ->andWhere($conditions)
        ->orderBy(['id' => SORT_DESC])
        ->all();


      $records = array_map([$this, 'assignModel'], $records);
      return $records;
    }

    /**
     * Getthread
     *
     * @return array of bluemantis\privatemessaging\models\PrivateMessagingThreadsModel
     */
    public function getThread($id) {
      if (!Craft::$app->getUser()) return [];

      $conditions = ['or',
        ['craft_private_messaging.recipientId' => Craft::$app->getUser()->getId()],
        ['craft_private_messaging.senderId' => Craft::$app->getUser()->getId()]
      ];

      $thread = PrivateMessagingThreadsRecord::find()
        ->joinWith(['messages'])
        ->where([
          'craft_private_messaging_threads.siteId' => Craft::$app->sites->getCurrentSite()->id,
          'craft_private_messaging_threads.id' => $id])
        ->andWhere($conditions)
        ->one();

      if(!$thread) return null;
      return $thread->getModel();
    }

    /**
     * Get message by id
     *
     * @param integer $id
     * @return instace of bluemantis\privatemessaging\models\PrivateMessagingModel
     */
    public function getMessage($id) {
      if (!Craft::$app->getUser()) return null;

      $message = PrivateMessagingRecord::find()
        ->with(['sender','recipient'])
        ->where([
          'recipientId' => Craft::$app->getUser()->id,
          'siteId' => Craft::$app->sites->getCurrentSite()->id,
          'id' => $id])
        ->one();

      if(!$message) return null;

      $this->markMessageAsRead($message);
      $message = $this->assignModel($message);
      return $message;
    }

    /**
     * Get total message count
     *
     * @return int
     */
    public function getTotalMessageCount() {
      if (!Craft::$app->getUser()) return 0;

      return PrivateMessagingRecord::find()
        ->where([
          'recipientId' => Craft::$app->getUser()->id,
          'siteId' => Craft::$app->sites->getCurrentSite()->id])
        ->count();
    }

    /**
     * Get unread message count
     *
     * @return int
     */
    public function getUnreadMessageCount() {
      if (!Craft::$app->getUser()) return 0;

      return PrivateMessagingRecord::find()
        ->where([
          'recipientId' => Craft::$app->getUser()->id,
          'siteId' => Craft::$app->sites->getCurrentSite()->id,
          'isRead' => 0])
        ->count();
    }

    /**
     * Delete message
     *
     * @param int $id
     * @return bool
     */
    public function deleteMessage($id) {
      $message = PrivateMessagingRecord::find()
        ->where([
          'recipientId' => Craft::$app->getUser()->id,
          'siteId' => Craft::$app->sites->getCurrentSite()->id,
          'id' => $id])
        ->one();

      if (!$message) return false;
      return $message->delete();
    }

    /**
     * Mark as read
     *
     * @param instance of bluemantis\privatemessaging\records\PrivateMessagingRecord
     * @return boolean
     */
    protected function markMessageAsRead(PrivateMessagingRecord $message){
      $message->isRead = 1;
            return $message->save();
    }

    /**
     * assign to model
     *
     * @param instance of bluemantis\privatemessaging\records\PrivateMessagingRecord
     * @return instance of bluemantis\privatemessaging\models\PrivateMessagingModel
     */
    protected function assignModel($record){
      return $record->getModel();
    }

    /**
     * Extract excerpt from string body
     *
     * @param string $str
     * @param integer $startPos
     * @param integer $maxLength
     * @return string
     */
    protected function getExcerpt($str, $startPos=0, $maxLength=100){
      if(strlen($str) > $maxLength) {
        $excerpt   = substr($str, $startPos, $maxLength-3);
        $lastSpace = strrpos($excerpt, ' ');
        $excerpt   = substr($excerpt, 0, $lastSpace);
        $excerpt  .= '...';
      } else {
        $excerpt = $str;
      }

      return $excerpt;
    }
}
