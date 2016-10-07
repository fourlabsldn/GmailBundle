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
    private $client;

    /**
     * AuthoriseGoogleAction constructor.
     * @param \Google_Client $client
     */
    public function __construct(\Google_Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        return new RedirectResponse($this->client->createAuthUrl());
    }
}
