<?php
namespace App\EventListener;

use App\Entity\MaitreApprentissage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Apprenti;
use Doctrine\ORM\EntityManager;

/**
 * Class LoginListener
 * @package App\EventListener
 */
class LoginListener
{

    /**
     * @var Session
     */
    protected $session;

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
     * LoginListener constructor.
     * @param Session $session
     * @param AuthorizationCheckerInterface $authChecker
     * @param TokenStorage $tokenStorage
     * @param Router $router
     * @param EntityManager $em
     */
    public function __construct(Session $session, AuthorizationCheckerInterface $authChecker, TokenStorage $tokenStorage, Router $router, EntityManager $em) {
        $this->session = $session;
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

        //L'apprenti et le maitre d'apprentissage ne peuvent rien faire tant qu'il n'ont pas complétés leurs informations
        if ($this->authChecker->isGranted ( 'IS_AUTHENTICATED_FULLY' )) {
            if($this->authChecker->isGranted('ROLE_APPRENTI') || $this->authChecker->isGranted('ROLE_MAITRE_APP')) {
                $id = $this->tokenStorage->getToken()->getUser()->getId();
                if($this->authChecker->isGranted('ROLE_APPRENTI')) {
                    $compte = $this->em
                        ->getRepository(Apprenti::class)
                        ->find($id);
                }
                if($this->authChecker->isGranted('ROLE_MAITRE_APP')) {
                    $compte = $this->em
                        ->getRepository(MaitreApprentissage::class)
                        ->find($id);
                }


                if ($compte->getTelephone() === null) {
                    $this->session->getFlashBag()->add('warning', 'Vous devez compléter vos informations avant de pouvoir accéder au site.');
                    $event->setResponse(new RedirectResponse($this->router->generate('edition_compte_perso')));
                }
            }
        }
    }
}