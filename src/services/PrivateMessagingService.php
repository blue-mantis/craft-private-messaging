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
      $record = new PrivateMessagingRecord();
      $record->setAttributes($model->getAttributes());
      $record->isRead = 0;
      $record->senderId = Craft::$app->user->id;
      $record->siteId = Craft::$app->sites->getCurrentSite()->id;
      return $record->save() ? true : false;
    }

    /**
     * Get unread messages
     *
     * @param string $onlyUnread
     * @return array of bluemantis\privatemessaging\models\PrivateMessagingModel
     */
    public function getMessages($onlyUnread = false) {
      if (!Craft::$app->getUser()) return [];

      $conditions = [
        'recipientId' => Craft::$app->getUser()->id,
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
     * Delete message
     *
     * @param instance of bluemantis\privatemessaging\records\PrivateMessagingRecord
     * @return instance of bluemantis\privatemessaging\models\PrivateMessagingModel
     */
    protected function assignModel(PrivateMessagingRecord $message){
      $model = new PrivateMessagingModel($message->toArray());
      $model->setSender($message->sender);
      $model->setRecipient($message->recipient);
      return $model;
    }
}
