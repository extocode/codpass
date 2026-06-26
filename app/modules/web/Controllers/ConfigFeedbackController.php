<?php

declare(strict_types=1);

namespace SP\Modules\Web\Controllers;

use Exception;
use SP\Core\Acl\Acl;
use SP\Core\Acl\UnauthorizedPageException;
use SP\Core\AppInfoInterface;
use SP\Core\Events\Event;
use SP\Core\Events\EventMessage;
use SP\Core\Exceptions\SessionTimeout;
use SP\Core\Exceptions\SPException;
use SP\Http\JsonResponse;
use SP\Modules\Web\Controllers\Traits\ConfigTrait;
use SP\Providers\Mail\MailParams;
use SP\Providers\Mail\MailProvider;

final class ConfigFeedbackController extends SimpleControllerBase
{
    use ConfigTrait;

    /**
     * @throws SPException
     */
    public function saveAction()
    {
        $this->checkSecurityToken($this->previousSk, $this->request);

        $configData = $this->config->getConfigData();
        $eventMessage = EventMessage::factory();

        $feedbackEnabled = $this->request->analyzeBool('feedback_enabled', false);
        $feedbackAuth = $this->request->analyzeBool('feedback_auth_enabled', false);
        $feedbackServer = $this->request->analyzeString('feedback_server');
        $feedbackPort = $this->request->analyzeInt('feedback_port', 25);
        $feedbackUser = $this->request->analyzeString('feedback_user');
        $feedbackPass = $this->request->analyzeEncrypted('feedback_pass');
        $feedbackSecurity = $this->request->analyzeString('feedback_security');
        $feedbackFrom = trim((string)$this->request->analyzeEmail('feedback_from', ''));
        $mailFeedback = trim((string)$this->request->analyzeEmail('mail_feedback', ''));

        if ($feedbackEnabled && (empty($feedbackServer) || empty($feedbackFrom) || empty($mailFeedback))) {
            return $this->returnJsonResponse(JsonResponse::JSON_ERROR, __u('Missing Feedback mail parameters'));
        }

        $configData->setFeedbackEnabled($feedbackEnabled);
        $configData->setFeedbackAuthenabled($feedbackAuth);
        $configData->setFeedbackServer($feedbackServer);
        $configData->setFeedbackPort($feedbackPort);
        $configData->setFeedbackSecurity($feedbackSecurity);
        $configData->setFeedbackFrom($feedbackFrom);
        $configData->setMailFeedback($mailFeedback);

        if ($feedbackAuth) {
            $configData->setFeedbackUser($feedbackUser);

            if ($feedbackPass !== '***') {
                $configData->setFeedbackPass($feedbackPass);
            }
        }

        $eventMessage->addDescription(__u('Feedback configuration updated'));

        return $this->saveConfig(
            $configData,
            $this->config,
            function () use ($eventMessage): void {
                $this->eventDispatcher->notifyEvent('save.config.feedback', new Event($this, $eventMessage));
            }
        );
    }

    /**
     * @throws SPException
     */
    public function checkAction()
    {
        $this->checkSecurityToken($this->previousSk, $this->request);

        $mailParams = new MailParams();
        $mailParams->server = $this->request->analyzeString('feedback_server');
        $mailParams->port = $this->request->analyzeInt('feedback_port', 25);
        $mailParams->security = $this->request->analyzeString('feedback_security');
        $mailParams->from = $this->request->analyzeEmail('feedback_from');
        $mailParams->mailAuthenabled = $this->request->analyzeBool('feedback_auth_enabled', false);
        $recipient = trim((string)$this->request->analyzeEmail('mail_feedback', ''));

        if (empty($mailParams->server) || empty($mailParams->from) || empty($recipient)) {
            return $this->returnJsonResponse(JsonResponse::JSON_ERROR, __u('Missing Feedback mail parameters'));
        }

        if ($mailParams->mailAuthenabled) {
            $mailParams->user = $this->request->analyzeString('feedback_user');
            $pass = $this->request->analyzeEncrypted('feedback_pass');
            $mailParams->pass = $pass === '***'
                ? $this->config->getConfigData()->getFeedbackPass()
                : $pass;
        }

        try {
            $mailer = $this->dic->get(MailProvider::class)->getMailer($mailParams);
            $mailer->isHTML();
            $mailer->addAddress($recipient);
            $mailer->Subject = sprintf('%s - %s', AppInfoInterface::APP_NAME, __('Feedback mail test'));
            $mailer->Body = __('This is a feedback mail test. If you received this, the Feedback SMTP configuration works.');
            $mailer->send();

            $this->eventDispatcher->notifyEvent(
                'send.mail.feedback.check',
                new Event($this, EventMessage::factory()
                    ->addDescription(__u('Email sent'))
                    ->addDetail(__u('Recipient'), $recipient))
            );

            return $this->returnJsonResponse(
                JsonResponse::JSON_SUCCESS,
                __u('Email sent'),
                [__u('Please, check your inbox')]
            );
        } catch (Exception $e) {
            processException($e);

            $this->eventDispatcher->notifyEvent('exception', new Event($e));

            return $this->returnJsonResponseException($e);
        }
    }

    /**
     * @throws SessionTimeout
     */
    protected function initialize()
    {
        try {
            $this->checks();
            $this->checkAccess(Acl::CONFIG_MAIL);
        } catch (UnauthorizedPageException $e) {
            $this->eventDispatcher->notifyEvent('exception', new Event($e));

            return $this->returnJsonResponseException($e);
        }

        return true;
    }
}
