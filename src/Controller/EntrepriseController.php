<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route; //To define the route to access it
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class EntrepriseController extends Controller
{
    /**
     * @Route("/entreprise/informations/", name="infos_entreprise")
     */
    public function infos_entreprise()
    {
        return $this->render('entreprise/entreprise.html.twig');
    }


}


