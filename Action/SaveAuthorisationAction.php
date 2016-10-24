<?php

namespace FL\GmailBundle\Action;

use FL\GmailBundle\Event\SaveAuthorisationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var string
     */
    private $redirectRouteAfterTokenSaved;

    /**
     * SaveAuthorisationAction constructor.
     * @param RouterInterface $router
     * @param EventDispatcherInterface $dispatcher
     * @param string $redirectRouteAfterSaveAuthorisation
     */
    public function __construct(
        RouterInterface $router,
        EventDispatcherInterface $dispatcher,
        string $redirectRouteAfterSaveAuthorisation
    ) {
        $this->router = $router;
        $this->dispatcher = $dispatcher;
        $this->redirectRouteAfterTokenSaved = $redirectRouteAfterSaveAuthorisation;
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

        $this->dispatcher->dispatch(SaveAuthorisationEvent::EVENT_NAME, new SaveAuthorisationEvent($code));

        return new RedirectResponse(
            $this->router->generate($this->redirectRouteAfterTokenSaved),
            Response::HTTP_MOVED_PERMANENTLY
        );
    }
}
