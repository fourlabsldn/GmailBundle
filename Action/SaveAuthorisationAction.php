<?php

namespace FL\GmailBundle\Action;

use FL\GmailBundle\Storage\CredentialsStorageInterface;
use FL\GmailBundle\Storage\HoldsCredentialsStorage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class SaveAuthorisationAction
 * @package FL\GmailBundle\Action
 */
class SaveAuthorisationAction
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CredentialsStorageInterface
     */
    private $storage;

    /**
     * @var string
     */
    private $redirectRoute;

    /**
     * SaveAuthorisationAction constructor.
     * @param RouterInterface $router
     * @param HoldsCredentialsStorage $holdsCredentialsStorage
     * @param string $redirectRoute
     */
    public function __construct(
        RouterInterface $router,
        HoldsCredentialsStorage $holdsCredentialsStorage,
        string $redirectRoute
    ) {
        $this->router = $router;
        $this->storage = $holdsCredentialsStorage->getCredentialsStorageService();
        $this->redirectRoute = $redirectRoute;
    }

    /**
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
            $this->router->generate($this->redirectRoute),
            Response::HTTP_MOVED_PERMANENTLY
        );
    }
}
