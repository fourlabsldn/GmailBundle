<?php

namespace FL\GmailBundle\Services;

use Psr\Http\Message\RequestInterface;

/**
 * This service allows us to communicate with @see Google_Service_Gmail.
 * It can send email for any user.
 */
class Email
{
    /**
     * @var GoogleServices
     */
    private $googleServices;

    /**
     * @param GoogleServices $googleClients
     */
    public function __construct(
        GoogleServices $googleClients
    ) {
        $this->googleServices = $googleClients;
    }

    /**
     * Get the user's list of emails.
     *
     * @param string $userId
     * @param array  $options
     *
     * @return \Google_Service_Gmail_ListHistoryResponse
     */
    public function historyList(string $userId, array $options = []): \Google_Service_Gmail_ListHistoryResponse
    {
        return $this->googleServices->getGoogleServiceGmailForUserId($userId)->users_history->listUsersHistory($userId, $options);
    }

    /**
     * Get the user's list of emails.
     *
     * @param string $userId
     * @param array  $options
     *
     * @return \Google_Service_Gmail_ListMessagesResponse
     */
    public function list(string $userId, array $options = []): \Google_Service_Gmail_ListMessagesResponse
    {
        return $this->googleServices->getGoogleServiceGmailForUserId($userId)->users_messages->listUsersMessages($userId, $options);
    }

    /**
     * Get email information given its ID.
     *
     * @param string $userId
     * @param string $emailId
     * @param array  $options
     *
     * @return \Google_Service_Gmail_Message|null
     *
     * @throws \Google_Service_Exception
     */
    public function get(string $userId, string $emailId, array $options = [])
    {
        try {
            return $this->googleServices->getGoogleServiceGmailForUserId($userId)->users_messages->get($userId, $emailId, $options);
        } catch (\Google_Service_Exception $exception) {
            // message does not exist
            if ($exception->getCode() === 404) {
                return;
            } else {
                throw $exception;
            }
        }
    }

    /**
     * Will return a mixed array, because some $emailIds might have thrown errors.
     * @see \Google_Service_Gmail_Resource_UsersMessages::get returns RequestInterface when its client is a batch client
     * @see https://developers.google.com/api-client-library/php/guide/batch
     *
     * @see https://developers.google.com/gmail/api/guides/batch#overview If you need to make more than 100 calls, use multiple batch requests.
     * @see https://developers.google.com/gmail/api/v1/reference/quota Sending batches larger than 50 requests is not recommended. (rate limiting)
     *
     * @param string $userId
     * @param array $emailIds
     * @param array $options
     *
     * @return \Google_Service_Gmail_Message[]|mixed[]|null
     */
    public function getBatch(string $userId, array $emailIds, array $options = [])
    {
        $gmailBatchService = $this->googleServices->getGoogleBatchServiceGmailForUserId($userId);
        $gmailBatchClient = $gmailBatchService->getClient();

        $batchResponses = [];
        foreach (array_chunk($emailIds, 45)  as $gmailIds) {
            $batchRequest = new \Google_Http_Batch($gmailBatchClient);
            foreach ($gmailIds as $gmailId) {
                /** @var RequestInterface $emailRequest */
                $emailRequest = $gmailBatchService->users_messages->get($userId, $gmailId, $options);
                $batchRequest->add($emailRequest);
            }
            $batchResponses = array_merge($batchResponses, $batchRequest->execute());
        }
        return $batchResponses;
    }

    /**
     * Notes are messages that don't have 'from' or 'to' headers.
     *
     * @param string $userId
     * @param string $emailId
     * @param array  $options
     *
     * @return \Google_Service_Gmail_Message|null
     */
    public function getIfNotNote(string $userId, string $emailId, array $options = [])
    {
        $fetchedApiMessage = $this->get($userId, $emailId, $options);

        $isNotApiMessage = !($fetchedApiMessage instanceof \Google_Service_Gmail_Message);
        if ($isNotApiMessage) {
            return;
        }

        $isApiMessageANote = !($this->hasHeader($fetchedApiMessage, 'To') && $this->hasHeader($fetchedApiMessage, 'From'));
        if ($isApiMessageANote) {
            return;
        }

        return $fetchedApiMessage;
    }

    /**
     * Send an email and return the full Gmail Message stored by Google as a response.
     *
     * @param string                        $userId
     * @param \Google_Service_Gmail_Message $message
     *
     * @return \Google_Service_Gmail_Message
     */
    public function send(string $userId, \Google_Service_Gmail_Message $message): \Google_Service_Gmail_Message
    {
        return $this->googleServices->getGoogleServiceGmailForUserId($userId)->users_messages->send($userId, $message);
    }

    /**
     * Send an email and return the full Gmail Message stored by Google as a response.
     *
     * @param string                        $email
     * @param \Google_Service_Gmail_Message $message
     *
     * @return \Google_Service_Gmail_Message
     */
    public function sendFromEmail(string $email, \Google_Service_Gmail_Message $message): \Google_Service_Gmail_Message
    {
        return $this->googleServices->getGoogleServiceGmailForEmail($email)->users_messages->send($email, $message);
    }

    /**
     * Delete an email given its ID.
     *
     * @param string $userId
     * @param string $emailId
     *
     * @return \Google_Service_Gmail_Message|null
     */
    public function trash(string $userId, string $emailId): \Google_Service_Gmail_Message
    {
        if ($this->get($userId, $emailId)) {
            return;
        }

        return $this->googleServices->getGoogleServiceGmailForUserId($userId)->users_messages->trash($userId, $emailId);
    }

    /**
     * Un-delete an email given its ID.
     *
     * @param string $userId
     * @param string $emailId
     *
     * @return \Google_Service_Gmail_Message|null
     */
    public function untrash(string $userId, string $emailId): \Google_Service_Gmail_Message
    {
        if ($this->get($userId, $emailId)) {
            return;
        }

        return $this->googleServices->getGoogleServiceGmailForUserId($userId)->users_messages->untrash($userId, $emailId);
    }

    /**
     * Get user's labels.
     *
     * @param string $userId
     *
     * @return \Google_Service_Gmail_ListLabelsResponse
     */
    public function getLabels(string $userId): \Google_Service_Gmail_ListLabelsResponse
    {
        return $this->googleServices->getGoogleServiceGmailForUserId($userId)->users_labels->listUsersLabels($userId);
    }

    /**
     * Return true if this Gmail Message has a header as specified in $header.
     * Because the order and amount of Gmail headers is not deterministic,
     * we need to loop through all the headers until we find the right one.
     *
     * @param \Google_Service_Gmail_Message $message
     * @param string                        $headerName
     *
     * @return bool
     */
    private function hasHeader(\Google_Service_Gmail_Message $message, string $headerName): bool
    {
        /** @var \Google_Service_Gmail_MessagePartHeader[] $headers */
        $headers = $message->getPayload()->getHeaders();
        foreach ($headers as $header) {
            if ($header->getName() === $headerName && $header->getValue() !== '') {
                return true;
            }
        }

        return false;
    }
}
