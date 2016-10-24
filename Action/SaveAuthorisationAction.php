<?php

namespace FL\GmailBundle\Action;

use FL\GmailBundle\Token\TokenStorageInterface;
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
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * @var string
     */
    private $redirectRoute;

    /**
     * SaveAuthorisationAction constructor.
     * @param RouterInterface $router
     * @param TokenStorageInterface $storage
     * @param string $redirectRoute
     */
    public function __construct(
        RouterInterface $router,
        TokenStorageInterface $storage,
        string $redirectRoute
    ) {
        $this->router = $router;
        $this->storage = $storage;
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
