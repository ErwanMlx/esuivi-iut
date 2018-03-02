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


}


