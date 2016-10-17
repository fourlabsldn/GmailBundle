<?php

namespace FL\GmailBundle\Services;

/**
 * Class Email
 * This class allows us to communicate with @see Google_Service_Gmail
 * @package FL\GmailBundle\Services
 */
class Email
{
    /**
     * @var \Google_Service_Gmail
     */
    private $service;

    /**
     * Email constructor.
     * @param \Google_Service_Gmail $service
     */
    public function __construct(\Google_Service_Gmail $service)
    {
        $this->service = $service;
    }

    /**
     * Get the user's list of emails.
     * @param string $userId
     * @param array $options
     * @return \Google_Service_Gmail_ListHistoryResponse
     */
    public function historyList(string $userId, array $options = []): \Google_Service_Gmail_ListHistoryResponse
    {
        return $this->service->users_history->listUsersHistory($userId, $options);
    }

    /**
     * Get the user's list of emails.
     * @param string $userId
     * @param array $options
     * @return \Google_Service_Gmail_ListMessagesResponse
     */
    public function list(string $userId, array $options = []): \Google_Service_Gmail_ListMessagesResponse
    {
        return $this->service->users_messages->listUsersMessages($userId, $options);
    }

    /**
     * Get email information given its ID.
     * @param string $userId
     * @param string $emailId
     * @param array $options
     * @return \Google_Service_Gmail_Message
     */
    public function get(string $userId, string $emailId, array $options = []): \Google_Service_Gmail_Message
    {
        return $this->service->users_messages->get($userId, $emailId, $options);
    }

    /**
     * Send an email and return the full Gmail Message stored by Google as a response.
     * @param string $userId
     * @param \Google_Service_Gmail_Message $message
     * @return \Google_Service_Gmail_Message
     */
    public function send(string $userId, \Google_Service_Gmail_Message $message): \Google_Service_Gmail_Message
    {
        return $this->service->users_messages->send($userId, $message);
    }

    /**
     * Delete an email given its ID.
     * @param string $userId
     * @param string $emailId
     * @return \Google_Service_Gmail_Message
     */
    public function trash(string $userId, string $emailId): \Google_Service_Gmail_Message
    {
        return $this->service->users_messages->trash($userId, $emailId);
    }

    /**
     * Un-delete an email given its ID.
     * @param string $userId
     * @param string $emailId
     * @return \Google_Service_Gmail_Message
     */
    public function untrash(string $userId, string $emailId): \Google_Service_Gmail_Message
    {
        return $this->service->users_messages->untrash($userId, $emailId);
    }

    /**
     * Get user's labels.
     * @param string $userId
     * @return \Google_Service_Gmail_ListLabelsResponse
     */
    public function getLabels(string $userId): \Google_Service_Gmail_ListLabelsResponse
    {
        return $this->service->users_labels->listUsersLabels($userId);
    }

    /**
     * Initialize Api Messages (e.g. add labels)
     *
     * Discard the notes from an array of GmailMessages.
     * Notes are messages that don't have 'from' or 'to' headers.
     * @internal
     * @param string $userId
     * @param \Google_Service_Gmail_Message[] $messages
     * @return \Google_Service_Gmail_Message[]
     */
    public function removeNotes(string $userId, array $messages): array
    {
        $filtered = [];

        foreach ($messages as $message) {
            $gmailMessage = $this->get($userId, $message->id);

            $isNote = !$this->hasHeader($gmailMessage, 'To') || !$this->hasHeader($gmailMessage, 'From');
            if (!$isNote) {
                $filtered[] = $gmailMessage;
            }
        }

        return $filtered;
    }

    /**
     * Return true if this Gmail Message has a header as specified in $header.
     * Because the order and amount of Gmail headers is not deterministic,
     * we need to loop through all the headers until we find the right one.
     * @param \Google_Service_Gmail_Message $message
     * @param string $headerName
     * @return bool
     */
    private function hasHeader(\Google_Service_Gmail_Message $message, string $headerName): bool
    {
        /** @var \Google_Service_Gmail_MessagePartHeader[] $headers */
        $headers = $message->getPayload()->getHeaders();
        foreach ($headers as $header) {
            if ($header->getName() === $headerName && $header->getValue() != "") {
                return true;
            }
        }
        return false;
    }
}
