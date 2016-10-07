<?php

namespace FL\GmailBundle\Services;

use FL\GmailBundle\Model\Collection\GmailDomain;
use FL\GmailBundle\Model\GmailUser;
use FL\GmailBundle\Model\GmailUserInterface;

/**
 * Class Directory
 * This class allows us to resolve userIds and emails in a domain,
 * by communicating with an instance of @see \Google_Service_Directory
 * @package FL\GmailBundle\Services
 */
class Directory
{
    const MODE_RESOLVE_PRIMARY_ONLY = 0;
    const MODE_RESOLVE_ALIASES_ONLY = 1;
    const MODE_RESOLVE_PRIMARY_PLUS_ALIASES = 2;

    /**
     * @var \Google_Service_Directory
     */
    private $directory;

    /**
     * @var GmailDomain[]
     */
    private $resolvedDomainsCache;

    /**
     * Directory constructor.
     * @param \Google_Service_Directory $directory
     */
    public function __construct(\Google_Service_Directory $directory)
    {
        $this->directory = $directory;
        $this->resolvedDomainsCache = [];
    }

    /**
     * @param string $domain
     * @return GmailDomain
     */
    private function resolveDomain(string $domain) : GmailDomain
    {
        $nextPage = null;
        $historyId = null;

        $gmailDomain = new GmailDomain($domain);

        do {
            $usersResponse = $this->directory->users->listUsers([
                'domain'=>$domain,
                'pageToken'=> $nextPage,
            ]);
            /** @var \Google_Service_Directory_User  $user*/
            foreach ($usersResponse->getUsers() as $user) {
                $primaryEmailAddress = $user->getPrimaryEmail();
                $gmailUser = new GmailUser();
                $gmailUser->setUserId($user->getId());
                $gmailUser->setPrimaryEmailAddress($primaryEmailAddress);
                foreach ($user->getEmails() as $email) { // primary email is in this list as well
                    if ($email['address'] !== $primaryEmailAddress) {
                        $gmailUser->addEmailAlias($email['address']);
                    }
                }
                $gmailDomain->addGmailUser($gmailUser);
            }
        } while (($nextPage = $usersResponse->getNextPageToken()));

        return $gmailDomain;
    }

    /**
     * @param string $domain
     */
    private function verifyDomainIsResolved(string $domain)
    {
        if (! array_key_exists($domain, $this->resolvedDomainsCache)) {
            $this->resolvedDomainsCache[$domain] = $this->resolveDomain($domain);
        }
    }

    /**
     * @param string $userId
     * @param string $domain (directory domain)
     * @param int $mode
     * @return string[] E.g.
     * [
     *  0 => "test@example.com",
     *  1 => "test_alias@example.com",
     * ]
     */
    public function resolveEmailsFromUserId(string $userId, string $domain, int $mode)
    {
        $this->verifyDomainIsResolved($domain);

        $emails = [];
        $gmailDomain = $this->resolvedDomainsCache[$domain];
        $gmailUser = $gmailDomain->findGmailUserById($userId);

        switch ($mode) {
            case self::MODE_RESOLVE_PRIMARY_ONLY:
                if ($gmailUser instanceof GmailUserInterface) {
                    $emails[] = $gmailUser->getPrimaryEmailAddress();
                }
                break;
            case self::MODE_RESOLVE_ALIASES_ONLY:
                if ($gmailUser instanceof GmailUserInterface) {
                    foreach ($gmailUser->getEmailAliases() as $emailAddress) {
                        $emails[] = $emailAddress;
                    }
                }
                break;
            case self::MODE_RESOLVE_PRIMARY_PLUS_ALIASES:
                if ($gmailUser instanceof GmailUserInterface) {
                    foreach ($gmailUser->getAllEmailAddresses() as $emailAddress) {
                        $emails[] = $emailAddress;
                    }
                }
                break;
            default:
                throw new \InvalidArgumentException();
        }

        return $emails;
    }

    /**
     * @param string $domain
     * @param int $mode
     * @return string[] E.g.
     * [
     *  0 => "test@example.com",
     *  1 => "test_alias@example.com",
     *  2 => "test2@example.com"
     * ]
     */
    public function resolveEmails(string $domain, int $mode)
    {
        $this->verifyDomainIsResolved($domain);

        $emails = [];
        $gmailDomain = $this->resolvedDomainsCache[$domain];
        $gmailUsers = $gmailDomain->getGmailUsers();

        /**
         * @var $gmailUser GmailUserInterface
         */
        foreach ($gmailUsers as $gmailUser) {
            $gmailUserEmails = $this->resolveEmailsFromUserId($gmailUser->getUserId(), $domain, $mode);
            foreach ($gmailUserEmails as $email) {
                $emails[] = $email;
            }
        }

        return $emails;
    }

    /**
     * @param string $email
     * @param string $domain (directory domain)
     * @param int $mode
     * @return string|null E.g. "12831283123123"
     */
    public function resolveUserIdFromEmail(string $email, string $domain, int $mode)
    {
        $this->verifyDomainIsResolved($domain);

        $userId = null;
        $gmailDomain = $this->resolvedDomainsCache[$domain];

        switch ($mode) {
            case self::MODE_RESOLVE_PRIMARY_ONLY:
                $gmailUser = $gmailDomain->findGmailUserByPrimaryEmail($email);
                break;
            case self::MODE_RESOLVE_ALIASES_ONLY:
                $gmailUser = $gmailDomain->findGmailUserByEmailAlias($email);
                break;
            case self::MODE_RESOLVE_PRIMARY_PLUS_ALIASES:
                $gmailUser = $gmailDomain->findGmailUserByEmail($email);
                break;
            default:
                throw new \InvalidArgumentException();
        }

        return $gmailUser ? $gmailUser->getUserId() : null;
    }

    /**
     * @param string $domain
     * @return string[] E.g.
     * [
     *  0 => "12831283123123",
     *  1 => "12831283123123",
     *  2 => "1045618888777"
     * ]
     */
    public function resolveUserIds(string $domain)
    {
        $this->verifyDomainIsResolved($domain);

        $userIds = [];
        $gmailDomain = $this->resolvedDomainsCache[$domain];
        $gmailUsers = $gmailDomain->getGmailUsers();

        /** @var $gmailUser GmailUserInterface */
        foreach ($gmailUsers as $gmailUser) {
            $userIds[] = $gmailUser->getUserId();
        }

        return $userIds;
    }

    /**
     * @param string $separator
     * @param string $domain
     * @param string $mode
     * @return string[] E.g. for ", " $separator
     * [
     *  "12831283123123" => "test@example.com, test_alias@example.com",
     *  "1045618888777" => "test2@example.com"
     * ]
     */
    public function resolveUserIdToInboxesArray(string $separator, string $domain, string $mode)
    {
        $this->verifyDomainIsResolved($domain);

        $userIds = $this->resolveUserIds($domain);
        $return = [];
        foreach ($userIds as $userId) {
            $return[$userId] = "";
            foreach ($this->resolveEmailsFromUserId($userId, $domain, $mode) as $email) {
                $return[$userId] .= $email . $separator;
            }
            $return[$userId] = rtrim($return[$userId], $separator);
        }

        return $return;
    }

    /**
     * @param string $separator
     * @param string $domain
     * @param string $mode
     * @return string[] E.g. for ", " $separator
     * [
     *  "test@example.com, test_alias@example.com" => "12831283123123",
     *  "test2@example.com" => "1045618888777"
     * ]
     */
    public function resolveInboxesToUserIdArray(string $separator, string $domain, string $mode)
    {
        $this->verifyDomainIsResolved($domain);

        return array_flip($this->resolveUserIdToInboxesArray($separator, $domain, $mode));
    }
}
