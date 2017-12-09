<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; //To define the route to access it
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SuiviController extends Controller
{
    /**
     * @Route("/suivi/") //The route to access the next function
     */
    public function number()
    {
        $nom = "John Doe";

        return $this->render('suivi/suivi.html.twig', array(
            'nom' => $nom,
        ));
    }
}
?>