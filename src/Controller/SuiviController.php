<?php
namespace App\Controller;

use App\Entity\DossierApprenti;
use App\Entity\ResponsableIut;
use App\Entity\TestsErwan;
use App\Entity\EtapeDossier;
use App\Entity\TypeEtape;
use App\Entity\Apprenti;
use App\Repository\EtapeDossierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; //To define the route to access it
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; //Erreur 404
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * Suivi de apprenti corrspondant à l'id
     *
     * @Route("/suivi/{id}", name="suivi_apprenti", requirements={"id"="\d+"}) //requirements permet d'autoriser uniquement les nombres dans l'URL
     */
    public function show($id)
    {
        $apprenti = null;
        $id_type_etape_actuelle = 3;
        $idDossier = 2;

        //On récupère l'apprenti pour lequel on veux afficher le suivi
        $apprenti = $this->getDoctrine()
            ->getRepository(Apprenti::class)
            ->find($id);

        if(!$apprenti) {
//            throw $this->createNotFoundException('Pas d\'apprenti trouvé pour l\'ID ' . $id);
            return $this->render('message.html.twig', array(
                'typeMessage' => "Apprenti non trouvé", 'message' => 'Pas d\'apprenti trouvé pour l\'ID ' . $id
            ));
        }

        $idDossier = $apprenti->getDossierApprenti()->getId();

        //On recupère toutes les étapes déjà complétée/en cours du dossier pour les afficher
        $etapes_dossier = $this->getDoctrine()
            ->getRepository(EtapeDossier::class)
            ->findBy(
                ['idDossier' => $idDossier], // Critere
                ['TypeEtape' => 'ASC'] // Tri
            );

        //On récupère l'ID type étape de l'étape actuelle du dossier
        $id_type_etape_actuelle = $apprenti->getDossierApprenti()->getEtapeActuelle()->getTypeEtape()->getId();

        //On récupère toutes les étapes pour un dossier
        $liste_etapes = $this->getDoctrine()
            ->getRepository(TypeEtape::class)
            ->findAll();

        //On récupère le nombre de type étape
        $nb_type_etapes = $this->getDoctrine()
            ->getRepository(TypeEtape::class)->getNbTypeEtape();

        return $this->render('suivi/suivi.html.twig', array(
            'apprenti' => $apprenti,
            'id' => $id,
            'liste_etapes' => $liste_etapes,
            'etapes_dossier' => $etapes_dossier,
            'id_type_etape_actuelle' => $id_type_etape_actuelle,
            'nb_type_etapes' => $nb_type_etapes,
        ));
    }

    /**
     * Validation d'une etape d'un dossier
     *
     * @Route("/suivi/valider_etape", name="valider_etape")
     */
    public function valider_etape(Request $req)
    {
        if($req->isXmlHttpRequest()) { //On vérifie que c'est bien une requête AJAX pour empêcher un accès direct a cette fonction

            $id = $req->get('id');
            $em = $this->getDoctrine()->getManager();
            $dossier = $em->getRepository(DossierApprenti::class)->find($id);

            if (!$dossier) {
//                throw $this->createNotFoundException(
//                    'Pas de dossier trouvé pour l\'id '.$id
//                );
                return new JsonResponse(array('error' => "Pas de dossier trouvé"));
            }

            //On enregistre la date de validation
            $etape_actuelle = $dossier->getEtapeActuelle();
            $etape_actuelle->setdateValidation(new \DateTime());
            //On détermine le validateur qui est autorisé a valider cette étape
            $typeValidateur = $dossier->getEtapeActuelle()->getTypeEtape()->gettypeValidateur();

//            $id_session = 0; //!!! A remplacer lorsque la gestion compte sera en place
//            if ($typeValidateur == "IUT") {
//                $etape_actuelle->setValidateurIut($this->getDoctrine()
//                    ->getRepository(ResponsableIut::class)
//                    ->find($id_session)
//                );
//            }
//            elseif ($typeValidateur == "CFA") {
//                $etape_actuelle->setValidateurCfa($this->getDoctrine()
//                    ->getRepository(ResponsableCfa::class)
//                    ->find($id_session));
//            }

            //On identifie l'étape suivante qu'il faudra valider dans le dossier
            $type_etape_suivante = $dossier->getEtapeActuelle()->getTypeEtape()->gettypeEtapeSuivante();

            if($type_etape_suivante) {
                //On créer la nouvelle étape actuelle du dossier
                $new_etape_dossier = new EtapeDossier();
                $new_etape_dossier->setTypeEtape($type_etape_suivante);
                $new_etape_dossier->setdateDebut(new \DateTime());
                $new_etape_dossier->setidDossier($id);

                // tell Doctrine you want to (eventually) save the Product (no queries yet)
                $em->persist($new_etape_dossier);

                //On met a jour l'étape actuelle du dossier
                $dossier->setEtapeActuelle($new_etape_dossier);
            }
            //Sinon il n'y a plus d'étape ensuite, donc le dossier est terminé
            else {
                $dossier->setetat('Terminé');
//                return new Response(
//                    '<html><body>Validation de l\'étape ' . $etape_actuelle->getTypeEtape()->getnomEtape() . ' du dossier ' . $id . ' le '. (new \DateTime())->format('Y-m-d H:i:s') .' par '.$typeValidateur. ' => Dossier terminé</body></html>'
//                );

            }
            $em->flush();

//            return new Response(
//                '<html><body>Validation de l\'étape ' . $etape_actuelle->getTypeEtape()->getnomEtape() . ' du dossier ' . $id . ' le '. (new \DateTime())->format('Y-m-d H:i:s') .' par '.$typeValidateur.'. ID type etape suivante : '.$type_etape_suivante->getnomEtape().' qui aura pour id '.
//                $new_etape_dossier->getId(). '. Nouvelle etape actuelle : '.$dossier->getEtapeActuelle()->getTypeEtape()->getnomEtape().'</body></html>'
//            );

            return new JsonResponse(array('error' => "ok"));
        }
        return $this->render('message.html.twig', array(
            'typeMessage' => "Erreur", 'message' => "Vous n'êtes pas autorisé à accéder à cette page."
        ));
    }

    /**
     * Annulation d'une etape d'un dossier
     *
     * @Route("/suivi/annuler_etape", name="annuler_etape")
     */
    public function annuler_etape(Request $req)
    {
        if($req->isXmlHttpRequest()) { //On vérifie que c'est bien une requête AJAX pour empêcher un accès direct a cette fonction

            $id_dossier = $req->get('id');
            $id_type_etape = $req->get('id_etape');

            $em = $this->getDoctrine()->getManager();
            $dossier = $em->getRepository(DossierApprenti::class)->find($id_dossier);

            $type_etape = $em->getRepository(TypeEtape::class)->find($id_type_etape);

            $dossier->setetat('En cours');

            //On créer la nouvelle étape actuelle du dossier
            $new_etape_dossier = new EtapeDossier();
            $new_etape_dossier->setTypeEtape($type_etape);
            $new_etape_dossier->setdateDebut(new \DateTime());
            $new_etape_dossier->setidDossier($id_dossier);

            $em->persist($new_etape_dossier);

            //On met a jour l'étape actuelle du dossier
            $dossier->setEtapeActuelle($new_etape_dossier);

            $em->flush();

            return new JsonResponse(array('error' => "ok"));
        }
        return $this->render('message.html.twig', array(
            'typeMessage' => "Erreur", 'message' => "Vous n'êtes pas autorisé à accéder à cette page."
        ));
    }

    /**
     * @Route("/liste/", name="liste")
     */
    public function liste()
    {
        $liste = $this->getDoctrine()
            ->getRepository(Apprenti::class)
            ->findAll();

        return $this->render('suivi/liste.html.twig', array(
            'liste' => $liste,
        ));
    }

}
?>