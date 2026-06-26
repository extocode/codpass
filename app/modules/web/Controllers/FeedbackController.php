<?php

declare(strict_types=1);

namespace SP\Modules\Web\Controllers;

use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use SP\Core\Events\Event;
use SP\Core\Events\EventMessage;
use SP\Core\AppInfoInterface;
use SP\Core\Exceptions\SessionTimeout;
use SP\Core\Exceptions\SPException;
use SP\Http\JsonResponse;
use SP\Modules\Web\Controllers\Traits\JsonTrait;
use SP\Providers\Mail\MailParams;
use SP\Providers\Mail\MailProvider;

final class FeedbackController extends SimpleControllerBase
{
    use JsonTrait;

    private const FEEDBACK_LOG = CONFIG_PATH . DIRECTORY_SEPARATOR . 'feedback.log';
    private const MAX_TITLE_LENGTH = 120;
    private const MAX_MESSAGE_LENGTH = 4000;
    private const MAX_ATTACH_SIZE = 5242880; // 5 MB
    private const MAX_ATTACH_COUNT = 3;
    private const ALLOWED_ATTACH_MIME = [
        'image/png',
        'image/jpeg',
        'image/gif',
        'image/webp',
        'image/bmp',
        'application/pdf',
        'text/plain',
    ];

    /**
     * @var MailProvider|null
     */
    private $mailProvider;

    public function saveAction()
    {
        try {
            $this->checkPostSize();

            $this->checkSecurityToken($this->previousSk, $this->request);

            $title = trim((string)$this->request->analyzeString('title'));
            $message = trim((string)$this->request->analyzeUnsafeString('message'));

            if ($title === '' || $message === '') {
                throw new SPException(__u('Title and description required'), SPException::WARNING);
            }

            if (mb_strlen($title) > self::MAX_TITLE_LENGTH) {
                $title = mb_substr($title, 0, self::MAX_TITLE_LENGTH);
            }

            if (mb_strlen($message) > self::MAX_MESSAGE_LENGTH) {
                $message = mb_substr($message, 0, self::MAX_MESSAGE_LENGTH);
            }

            $attachments = $this->getValidatedAttachments();
            $attachmentNames = array_map(static fn($a) => $a['name'], $attachments);

            $userData = $this->session->getUserData();
            $userLogin = $userData->getLogin() ?: 'anonymous';
            $userEmail = method_exists($userData, 'getEmail') ? (string)$userData->getEmail() : '';
            $clientAddress = $this->request->getClientAddress();

            $this->appendToLog(
                $title,
                $message,
                $userLogin,
                (int)$userData->getId(),
                $clientAddress,
                $attachmentNames === [] ? null : implode(', ', $attachmentNames)
            );

            $mailSent = $this->sendFeedbackMail(
                $title,
                $message,
                $userLogin,
                $userEmail,
                $clientAddress,
                $attachments
            );

            $this->eventDispatcher->notifyEvent(
                'feedback.create',
                new Event($this, EventMessage::factory()
                    ->addDescription(__u('Feedback received'))
                    ->addDetail(__u('Title'), $title)
                    ->addDetail(__u('User'), $userLogin)
                    ->addDetail(__u('Attachments'), $attachmentNames === [] ? __u('None') : implode(', ', $attachmentNames))
                    ->addDetail(__u('Email sent'), $mailSent ? __u('Yes') : __u('No')))
            );

            if (!$mailSent) {
                return $this->returnJsonResponse(
                    JsonResponse::JSON_SUCCESS,
                    __u('Feedback saved. Email delivery is disabled or failed; entry written to log.')
                );
            }

            return $this->returnJsonResponse(
                JsonResponse::JSON_SUCCESS,
                __u('Feedback sent. Thank you!')
            );
        } catch (Exception $e) {
            processException($e);

            $this->eventDispatcher->notifyEvent('exception', new Event($e));

            return $this->returnJsonResponseException($e);
        }
    }

    private function appendToLog(
        string $title,
        string $message,
        string $userLogin,
        int $userId,
        string $clientAddress,
        ?string $attachmentName = null
    ): void {
        $entry = sprintf(
            "[%s] user=%s (id=%d) ip=%s%s  title=%s%s  attachment=%s%s%s%s---%s",
            date('Y-m-d H:i:s'),
            $userLogin,
            $userId,
            $clientAddress,
            PHP_EOL,
            $title,
            PHP_EOL,
            $attachmentName ?? '-',
            PHP_EOL,
            $message,
            PHP_EOL,
            PHP_EOL
        );

        if (file_put_contents(self::FEEDBACK_LOG, $entry, FILE_APPEND | LOCK_EX) === false) {
            throw new SPException(__u('Unable to persist feedback'), SPException::ERROR);
        }

        @chmod(self::FEEDBACK_LOG, 0600);
    }

    private function sendFeedbackMail(
        string $title,
        string $message,
        string $userLogin,
        string $userEmail,
        string $clientAddress,
        array $attachments = []
    ): bool {
        $recipient = trim($this->configData->getMailFeedback());

        if ($recipient === '' || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (!$this->configData->isFeedbackEnabled() || $this->mailProvider === null) {
            return false;
        }

        $server = trim($this->configData->getFeedbackServer());
        $from = trim($this->configData->getFeedbackFrom());

        if ($server === '' || $from === '') {
            return false;
        }

        try {
            $mailParams = new MailParams();
            $mailParams->server = $server;
            $mailParams->port = $this->configData->getFeedbackPort();
            $mailParams->security = $this->configData->getFeedbackSecurity();
            $mailParams->from = $from;
            $mailParams->mailAuthenabled = $this->configData->isFeedbackAuthenabled();

            if ($mailParams->mailAuthenabled) {
                $mailParams->user = $this->configData->getFeedbackUser();
                $mailParams->pass = $this->configData->getFeedbackPass();
            }

            $mailer = $this->mailProvider->getMailer($mailParams);
            $mailer->isHTML(true);
            $mailer->addAddress($recipient);
            $mailer->Subject = sprintf('%s - %s: %s', AppInfoInterface::APP_NAME, __('Feedback'), $title);

            $emailRow = $userEmail !== ''
                ? sprintf('<tr><td style="padding:4px 12px 4px 0;color:#666;white-space:nowrap;">%s</td><td style="padding:4px 0;">%s</td></tr>', __('Email'), htmlspecialchars($userEmail))
                : '';

            $attachmentHtml = '';
            $attachmentAlt = '';

            foreach ($attachments as $i => $attachment) {
                if (strpos($attachment['mime'], 'image/') === 0) {
                    $cid = 'feedback_attachment_' . $i;
                    $mailer->addEmbeddedImage(
                        $attachment['tmp_name'],
                        $cid,
                        $attachment['name'],
                        'base64',
                        $attachment['mime']
                    );
                    $attachmentHtml .= sprintf(
                        '<div style="margin-top:12px;"><img src="cid:%s" alt="%s" style="max-width:100%%;border:1px solid #ddd;border-radius:4px;"/></div>',
                        $cid,
                        htmlspecialchars($attachment['name'])
                    );
                } else {
                    $mailer->addAttachment(
                        $attachment['tmp_name'],
                        $attachment['name'],
                        'base64',
                        $attachment['mime']
                    );
                    $attachmentHtml .= sprintf(
                        '<div style="margin-top:12px;color:#666;font-size:13px;"><i>%s</i></div>',
                        htmlspecialchars($attachment['name'])
                    );
                }

                $attachmentAlt .= sprintf("\r\n%s: %s", __('Attachment'), $attachment['name']);
            }

            if ($attachmentHtml !== '') {
                $attachmentHtml = sprintf(
                    '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px;"><div style="font-size:15px;font-weight:bold;margin-bottom:8px;">%s</div>%s</div>',
                    htmlspecialchars(__('Attachments')),
                    $attachmentHtml
                );
            }

            $mailer->Body = sprintf(
                '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;font-size:14px;color:#222;margin:0;padding:0;">
<div style="max-width:600px;margin:32px auto;border:1px solid #ddd;border-radius:6px;overflow:hidden;">
  <div style="background:#2e3a4e;padding:18px 24px;">
    <div style="color:#aab4c2;font-size:11px;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;">%s &mdash; %s</div>
    <div style="color:#fff;font-size:18px;font-weight:bold;">%s</div>
  </div>
  <div style="padding:24px;">
    <table style="border-collapse:collapse;width:100%%;margin-bottom:24px;">
      <tr><td style="padding:4px 12px 4px 0;color:#666;white-space:nowrap;">%s</td><td style="padding:4px 0;">%s</td></tr>
      %s
      <tr><td style="padding:4px 12px 4px 0;color:#666;white-space:nowrap;">%s</td><td style="padding:4px 0;">%s</td></tr>
      <tr><td style="padding:4px 12px 4px 0;color:#666;white-space:nowrap;">%s</td><td style="padding:4px 0;">%s</td></tr>
    </table>
    <div style="border-top:1px solid #eee;padding-top:16px;">
      <div style="font-size:15px;font-weight:bold;margin-bottom:8px;">%s</div>
      <div style="white-space:pre-wrap;line-height:1.6;">%s</div>
    </div>
    %s
  </div>
</div>
</body></html>',
                htmlspecialchars(AppInfoInterface::APP_NAME),
                htmlspecialchars(__('Feedback')),
                htmlspecialchars($title),
                __('From'), htmlspecialchars($userLogin),
                $emailRow,
                __('IP'), htmlspecialchars($clientAddress),
                __('Date'), date('Y-m-d H:i:s'),
                htmlspecialchars($title),
                htmlspecialchars($message),
                $attachmentHtml
            );

            $mailer->AltBody = sprintf(
                "%s\r\n\r\n%s: %s\r\n%s%s: %s\r\n%s: %s\r\n\r\n%s\r\n%s%s",
                $title,
                __('From'), $userLogin,
                $userEmail !== '' ? sprintf("%s: %s\r\n", __('Email'), $userEmail) : '',
                __('IP'), $clientAddress,
                __('Date'), date('Y-m-d H:i:s'),
                $title,
                $message,
                $attachmentAlt
            );
            $mailer->send();

            return true;
        } catch (Exception $e) {
            processException($e);

            $this->eventDispatcher->notifyEvent('exception', new Event($e));

            return false;
        }
    }

    /**
     * Detects a request body that PHP discarded because it exceeded post_max_size.
     * In that case $_POST/$_FILES are empty, so without this the user would only see
     * a confusing CSRF/token error.
     *
     * @throws SPException
     */
    private function checkPostSize(): void
    {
        $contentLength = (int)$this->request->getServer('CONTENT_LENGTH');
        $postMax = $this->iniBytes((string)ini_get('post_max_size'));

        if ($contentLength > 0
            && $postMax > 0
            && $contentLength > $postMax
            && $_POST === []
        ) {
            throw new SPException(
                __u('Attachment too large'),
                SPException::WARNING,
                sprintf(__u('Total upload exceeds the server limit (%s)'), ini_get('post_max_size'))
            );
        }
    }

    /**
     * Converts a PHP ini shorthand byte value (e.g. "8M", "512K") to bytes.
     */
    private function iniBytes(string $value): int
    {
        $value = trim($value);

        if ($value === '') {
            return 0;
        }

        $number = (int)$value;
        $unit = strtolower($value[strlen($value) - 1]);

        return match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => $number,
        };
    }

    /**
     * Reads and validates the optional feedback attachments (max self::MAX_ATTACH_COUNT).
     *
     * @return array<int, array{tmp_name:string, name:string, mime:string, size:int}>
     * @throws SPException
     */
    private function getValidatedAttachments(): array
    {
        $files = $this->request->getFile('attachment');

        if ($files === null || !isset($files['tmp_name'])) {
            return [];
        }

        // Normalize single vs. multiple ($_FILES) structure into a flat list
        $candidates = [];

        if (is_array($files['tmp_name'])) {
            $count = count($files['tmp_name']);

            for ($i = 0; $i < $count; $i++) {
                $candidates[] = [
                    'tmp_name' => $files['tmp_name'][$i] ?? '',
                    'name' => $files['name'][$i] ?? '',
                    'size' => $files['size'][$i] ?? 0,
                    'error' => $files['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                ];
            }
        } else {
            $candidates[] = $files;
        }

        $attachments = [];

        foreach ($candidates as $file) {
            $attachment = $this->validateAttachment($file);

            if ($attachment !== null) {
                $attachments[] = $attachment;
            }

            if (count($attachments) >= self::MAX_ATTACH_COUNT) {
                break;
            }
        }

        return $attachments;
    }

    /**
     * Validates a single uploaded file entry.
     *
     * @return array{tmp_name:string, name:string, mime:string, size:int}|null null for empty slots
     * @throws SPException on invalid file
     */
    private function validateAttachment(array $file): ?array
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($error === UPLOAD_ERR_NO_FILE || ($file['tmp_name'] ?? '') === '' && $error === UPLOAD_ERR_OK) {
            return null;
        }

        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            throw new SPException(
                __u('Attachment too large'),
                SPException::WARNING,
                sprintf(__u('Maximum size: %d KB'), (int)(self::MAX_ATTACH_SIZE / 1024))
            );
        }

        if ($error !== UPLOAD_ERR_OK
            || ($file['tmp_name'] ?? '') === ''
            || !is_uploaded_file($file['tmp_name'])
        ) {
            throw new SPException(__u('Error while uploading the attachment'), SPException::WARNING);
        }

        $size = (int)($file['size'] ?? 0);

        if ($size <= 0 || $size > self::MAX_ATTACH_SIZE) {
            throw new SPException(
                __u('Attachment size exceeded'),
                SPException::WARNING,
                sprintf(__u('Maximum size: %d KB'), (int)(self::MAX_ATTACH_SIZE / 1024))
            );
        }

        $mime = $this->detectMimeType($file['tmp_name'], (string)($file['type'] ?? ''));

        if (!in_array($mime, self::ALLOWED_ATTACH_MIME, true)) {
            throw new SPException(
                __u('Attachment type not allowed'),
                SPException::WARNING,
                $mime
            );
        }

        return [
            'tmp_name' => $file['tmp_name'],
            'name' => $this->sanitizeFilename((string)($file['name'] ?? '')),
            'mime' => $mime,
            'size' => $size,
        ];
    }

    /**
     * Detects the real MIME type, falling back gracefully if the fileinfo
     * extension is unavailable, then to the client-provided type as a last resort.
     */
    private function detectMimeType(string $tmpName, string $clientType): string
    {
        if (class_exists('finfo')) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = (string)$finfo->file($tmpName);

            if ($mime !== '') {
                return $mime;
            }
        }

        if (function_exists('mime_content_type')) {
            $mime = (string)@mime_content_type($tmpName);

            if ($mime !== '') {
                return $mime;
            }
        }

        return $clientType;
    }

    /**
     * Reduces an uploaded filename to a safe basename. Never throws.
     */
    private function sanitizeFilename(string $name): string
    {
        $name = basename($name);
        $name = (string)preg_replace('/[^\p{L}\p{N}._\- ]+/u', '', $name);
        $name = trim($name);

        return $name === '' ? 'attachment' : $name;
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws SessionTimeout
     */
    protected function initialize(): void
    {
        $this->checks();

        if ($this->configData->isFeedbackEnabled()) {
            $this->mailProvider = $this->dic->get(MailProvider::class);
        }
    }
}
