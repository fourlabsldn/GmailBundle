<?php

namespace FL\GmailBundle\Action;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthoriseGoogleAction
 * @package FL\GmailBundle\Action
 */
class AuthoriseGoogleAction
{
    /**
     * Unauthorised Google Client used to generate the auth url.
     * @var \Google_Client
     */
    private $unauthorisedClient;

    /**
     * AuthoriseGoogleAction constructor.
     * @param \Google_Client $unauthorisedClient
     */
    public function __construct(\Google_Client $unauthorisedClient)
    {
        $this->unauthorisedClient = $unauthorisedClient;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        return new RedirectResponse($this->unauthorisedClient->createAuthUrl());
    }
}
