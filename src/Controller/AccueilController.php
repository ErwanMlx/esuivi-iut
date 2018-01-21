<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; //To define the route to access it
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccueilController extends Controller
{
    /**
     * @Route("/", name="home") //The route to access the next function
     */
    public function default()
    {
        $url_suivi = $this->generateUrl(
            'suivi'
        );
        return $this->render('accueil/accueil.html.twig', array(
            'url_suivi' => $url_suivi,
        ));
    }
}
?>