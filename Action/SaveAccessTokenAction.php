<?php

namespace FL\GmailBundle\Action;

use FL\GmailBundle\Token\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class SaveAccessTokenAction
 * Action that will process the auth code sent by Google in the request,
 * saving it in a file to make it available for the GoogleClient
 * service to obtain an auth token
 * @package FL\GmailBundle\Action
 */
class SaveAccessTokenAction
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * SaveAccessTokenAction constructor.
     * @param RouterInterface $router
     * @param TokenStorageInterface $storage
     */
    public function __construct(
        RouterInterface $router,
        TokenStorageInterface $storage
    ) {
        $this->router = $router;
        $this->storage = $storage;
    }

    /**
     * Process the auth code received from Google by adding it to
     * a file that can be later accessed by the Google Client service.
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        if (!$code = $request->query->get('code')) {
            throw new BadRequestHttpException("No authorisation code in request.");
        }

        $this->storage->persistAuthCode($code);

        return new RedirectResponse(
            $this->router->generate('app.email.gmail'),
            Response::HTTP_MOVED_PERMANENTLY
        );
    }
}
