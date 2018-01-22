<?php
namespace App\Controller;

use App\Entity\TestsErwan;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; //To define the route to access it
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; //Erreur 404

class SuiviController extends Controller
{
    /**
     * @Route("/suivi/", name="suivi") //The route to access the next function
     */
    public function suivi()
    {
        $nom = "John Doe";

        return $this->render('suivi/suivi.html.twig', array(
            'nom' => $nom,
        ));
    }

    /**
     * @Route("/suiviDev/", name="suivi_dev") //The route to access the next function
     */
    public function suiviDev()
    {
        $nom = "John Doe";

        return $this->render('suivi/suiviDev.html.twig', array(
            'nom' => $nom,
        ));
    }

    /**
     * Suivi de apprenti corrspondant à l'id
     *
     * @Route("/suivi/{id}", name="suivi_apprenti", requirements={"id"="\d+"}) //requirements permet d'autoriser uniquement les nombres dans l'URL
     */
    public function show($id)
    {
        $nom = "John Doe";

        $tests_erwan = $this->getDoctrine()
            ->getRepository(TestsErwan::class)
            ->find($id);

//        if (!$tests_erwan) {
//            throw $this->createNotFoundException(
//                'No product found for id '.$id
//            );
//        }
//        return new Response('Check out this great product: '.$tests_erwan->getNom());

        if(!$tests_erwan) {
            throw $this->createNotFoundException('Pas d\'apprenti trouvé pour l\'ID '.$id);
        }
        return $this->render('suivi/suiviDev.html.twig', array(
            'tests_erwan' => $tests_erwan, 'id' => $id,
        ));
    }
}
?>