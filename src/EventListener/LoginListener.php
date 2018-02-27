<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Apprenti;
use Doctrine\ORM\EntityManager;

class LoginListener
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var SecurityContext
     */
    private $authChecker;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var Router $router
     */
    private $router;

    /**
     * @param AuthorizationCheckerInterface    $authChecker
     * @param Router             $router
     * @param ValidatorInterface $validator
     */
    public function __construct(AuthorizationCheckerInterface $authChecker, TokenStorage $tokenStorage, Router $router, EntityManager $em) {
        $this->authChecker = $authChecker;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event) {
        if ($event->getRequestType() !== HttpKernel::MASTER_REQUEST) {
            return;
        }

        $routeName = $event->getRequest()->get('_route');

        //Pour empecher une boucle infinie de redirection
        if ($event->isMasterRequest() and $routeName == 'edition_compte_perso') {
            return;
        }

        if ($this->authChecker->isGranted ( 'IS_AUTHENTICATED_FULLY' )) {
            if($this->authChecker->isGranted('ROLE_APPRENTI')) {
                $id = $this->tokenStorage->getToken()->getUser()->getId();
                $apprenti = $this->em
                    ->getRepository(Apprenti::class)
                    ->find($id);

                if ($apprenti->getTelephone() === null) {
                    $event->setResponse(new RedirectResponse($this->router->generate('edition_compte_perso')));
                }
            }
        }
    }
}