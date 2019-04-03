<?php
/**
 * Private Messaging plugin for Craft CMS 3.x
 *
 * Allows sending private messages between users
 *
 * @link      https://bluemantis.com/
 * @copyright Copyright (c) 2019 Blue Mantis
 */

namespace bluemantis\privatemessaging\controllers;

use bluemantis\privatemessaging\PrivateMessaging;
use bluemantis\privatemessaging\models\PrivateMessagingModel;
use bluemantis\privatemessaging\services\PrivateMessagingService;

use Craft;
use craft\web\Controller;



/**
 * @author    Blue Mantis
 * @package   PrivateMessaging
 * @since     1.0.0
 */
class MessagesController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = [];
    protected $message;
    protected $isAsync = false;

    /**
     * @param
     */
    public function __construct($id, $module, $config = [])
    {
      parent::__construct($id, $module, $config);
      $this->message = new PrivateMessagingModel();
      $this->isAsync = Craft::$app->getRequest()->getIsAjax();
    }

    /**
     * @return craft\web\Response
     */
    public function actionSend()
    {
      $this->requirePostRequest();

      $this->message = new PrivateMessagingModel();
      $this->message->subject = Craft::$app->request->post('subject');
      $this->message->body = Craft::$app->request->post('body');
      $this->message->recipientId = Craft::$app->request->post('recipientId');
      $this->message->threadId = Craft::$app->request->post('threadId');

      if(!$this->message->validate()){
        return $this->respondWithError();
      }

      PrivateMessaging::$plugin->privateMessagingService->saveMessage($this->message);
      return $this->respondWithSuccess();
    }

    /**
     * @return mixed
     */
    public function actionDelete()
    {
      $this->requirePostRequest();
      $id = Craft::$app->request->post('id');

      PrivateMessaging::$plugin->privateMessagingService->deleteMessage($id);
      return $this->redirectToPostedUrl();
    }

    /**
     * Generates error response based on the request type
     *
     * @return craft\web\Response
     */
    protected function respondWithError()
    {
      if($this->isAsync){
        return $this->asJson([
          'status' => false,
          'errors' => $this->message->getErrors()]);
      }

      Craft::$app->session->setFlash('errors', $this->message->getErrors());
      return $this->redirectToPostedUrl();
    }

    /**
     * Generates success response based on the request type
     *
     * @return craft\web\Response
     */
    protected function respondWithSuccess()
    {
      if($this->isAsync){
        return $this->asJson([
          'status' => true,
          'message' => $this->message]);
      }

      return $this->redirectToPostedUrl();
    }

}
