<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route; //To define the route to access it
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class DossierController extends Controller
{
    /**
     * @Route("/bordereau/remplir", name="remplir_bordereau")
     */
    public function remplir_bordereau()
    {

    }

    /**
     * Page d'abandon de dossier
     *
     * @Route("/dossier/abandon", name="abandon")
     */
    public function abandon_dossier(AuthorizationCheckerInterface $authChecker, Request $req)
    {
        if($req->isXmlHttpRequest()) { //On vérifie que c'est bien une requête AJAX pour empêcher un accès direct a cette fonction

            $id = $req->get('id');

            if(!($authChecker->isGranted('ROLE_IUT') || $this->getUser()->getId()==$id)) {
                return new JsonResponse(array('error' => "Vous n'êtes pas autorisé a réaliser cette action"));
            }

            $em = $this->getDoctrine()->getManager();
            $dossier = $em->getRepository(DossierApprenti::class)->find($id);

            if (!$dossier) {
                return new JsonResponse(array('error' => "Pas de dossier trouvé"));
            }

            $dossier->setetat('Abandonné');

            $em->flush();

            return new JsonResponse(array('error' => "ok"));
        }
        throw new AccessDeniedException();
    }
}


