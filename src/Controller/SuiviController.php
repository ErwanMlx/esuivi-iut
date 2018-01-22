<?php
namespace App\Controller;

use App\Entity\TestsErwan;
use App\Repository\EtapeDossierRepository;
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
        //On récupère l'apprenti pour lequel on veux afficher le suivi
        $apprenti = $this->getDoctrine()
            ->getRepository(Apprenti::class)
            ->find($id);

        //On recupère toutes les étapes déjà complétée/en cours du dossier pour les afficher
        $etapes_dossier = $this->getDoctrine()
            ->getRepository(EtapeDossier::class)
            ->findBy(
                ['ID_Dossier' => $apprenti->getIDDossier()], // Critere
                ['ID_Type_Etape' => 'ASC'] // Tri
            );

        //On récupère l'ID type étape de l'étape actuelle du dossier
        $id_type_etape_actuelle = $apprenti->getIDDossier()->getIDEtapeActuelle()->getIDTypeEtape();

        //On récupère toutes les étapes pour un dossier
        $liste_etapes = $this->getDoctrine()
            ->getRepository(TypeEtape::class)
            ->findAll();

        if(!$apprenti) {
            throw $this->createNotFoundException('Pas d\'apprenti trouvé pour l\'ID ' . $id);
        }
        return $this->render('suivi/suiviDev.html.twig', array(
            'apprenti' => $apprenti, 'id' => $id, 'liste_etapes' => $liste_etapes, 'etapes_dossier' => $etapes_dossier, 'id_type_etape_actuelle' => $id_type_etape_actuelle,
        ));
    }
}
?>