<?php
namespace App\Controller;

use App\Entity\DossierApprenti;
use App\Entity\EtapeDossier;
use App\Entity\TypeEtape;
use App\Entity\Apprenti;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; //To define the route to access it
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted; //Pour vérifier si l'utilisateur est autorisé a accéder à la page
use Symfony\Component\Security\Core\Exception\AccessDeniedException; //Erreur 403
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface; //Pour vérifier les droits
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; //Erreur 404
use Symfony\Component\HttpFoundation\JsonResponse;

class SuiviController extends Controller
{
    /**
     * Affichage de la liste des apprentis
     *
     * @Route("/liste/", name="liste")
     */
    public function liste(AuthorizationCheckerInterface $authChecker, Request $req)
    {
        if ($authChecker->isGranted('ROLE_APPRENTI')) {
            throw new AccessDeniedException();
        }

        //On récupère la recherche d'apprenti
        $search = $req->get('search');

        //Si il y en a une on cherche les apprentis correspondant
        if($search != null) {
            $liste = $this->getDoctrine()
                ->getRepository(Apprenti::class)->search($search);
        }
        //Sinon on affiche tous les apprentis
        else {
            $liste = $this->getDoctrine()
                ->getRepository(Apprenti::class)
                ->findAll();
        }

        return $this->render('suivi/liste.html.twig', array(
            'liste' => $liste,
        ));
    }


//    /**
//     * Recherche des apprentis
//     *
//     * @Route("/liste/recherche/", name="recherche_liste")
//     */
//    public function recherche(AuthorizationCheckerInterface $authChecker, Request $req) {
//        if($req->isXmlHttpRequest()) {
//            if ($authChecker->isGranted('ROLE_APPRENTI')) {
//                throw new AccessDeniedException();
//            }
//            $search = $req->get('search');
//        }
//        return $this->render('message.html.twig', array(
//            'typeMessage' => "Erreur", 'message' => "Vous n'êtes pas autorisé à accéder à cette page."
//        ));
//    }

    /**
     * Si l'utilisateur est un apprenti, on récupère son ID pour afficher son dossier
     *
     * @Route("/suivi/", name="suivi_perso")
     * @IsGranted("ROLE_APPRENTI")
     */
    public function suiviPerso(AuthorizationCheckerInterface $authChecker)
    {
        $id = $this->getUser()->getId();
        return $this->suivi($authChecker, $id);
    }


    /**
     * On récupère l'apprenti et son dossier si la personne qui souhaite y accéder y est autorisée
     *
     * @param AuthorizationCheckerInterface $authChecker
     * @param $id
     * @return array|Response
     */
    public function recup_dossier(AuthorizationCheckerInterface $authChecker, $id) {
        //Un apprenti ne peux pas voir le suivi d'un autre apprenti
        if ($authChecker->isGranted('ROLE_APPRENTI') && $this->getUser()->getId()!=$id) {
            throw new AccessDeniedException();
        }

        //On récupère l'apprenti pour lequel on veut afficher le suivi
        $apprenti = $this->getDoctrine()
            ->getRepository(Apprenti::class)
            ->find($id);

        if(!$apprenti) {
            return $this->render('message.html.twig', array(
                'typeMessage' => "Apprenti non trouvé", 'message' => 'Pas d\'apprenti trouvé pour l\'ID ' . $id
            ));
        }

        $dossier = $apprenti->getDossierApprenti()->getId();

        //Un maitre d'apprentissage ne peut voir le suivi que de ses apprenti
        if ($authChecker->isGranted('ROLE_MAITRE_APP')) {
            $ma = $dossier->getMaitreApprentissage()->getId;
            if ($ma != $this->getUser()->getId) {
                throw new AccessDeniedException();
            }
        }
        return array($apprenti, $dossier);
    }

    /**
     * Suivi de apprenti corrspondant à l'id
     *
     * @Route("/suivi/{id}", name="suivi", requirements={"id"="\d+"}) //requirements permet d'autoriser uniquement les nombres dans l'URL
     */
    public function suivi(AuthorizationCheckerInterface $authChecker, $id)
    {
        $res = $this->recup_dossier($authChecker, $id);
        $apprenti = $res[0];
        $dossier = $res[1];


//        select * from type_etape te LEFT JOIN etape_dossier ed ON (ed.id_type_etape = te.id) WHERE
//    (id_dossier=2 OR id_dossier IS NULL) AND (ed.date_debut = (SELECT MAX(ed2.date_debut) FROM etape_dossier ed2 WHERE ed2.id_type_etape=te.id AND ed2.id_dossier=2) OR ed.date_debut IS NULL) ORDER BY te.position_etape

        //On recupère toutes les étapes déjà complétée/en cours du dossier pour les afficher
        $etapes_dossier = $this->getDoctrine()
            ->getRepository(EtapeDossier::class)
            ->findBy(
                ['dossier' => $dossier], // Critere
                ['typeEtape' => 'ASC'] // Tri
            );

        //On récupère l'ID type étape de l'étape actuelle du dossier
        $position_etape_actuelle = $apprenti->getDossierApprenti()->getEtapeActuelle()->getTypeEtape()->getpositionEtape();

        //On récupère toutes les étapes pour un dossier
        $liste_etapes = $this->getDoctrine()
            ->getRepository(TypeEtape::class)
            ->findAll();

        //On récupère le nombre de type étape
        $nb_type_etapes = $this->getDoctrine()
            ->getRepository(TypeEtape::class)->getNbTypeEtape();

        return $this->render('suivi/suivi.html.twig', array(
            'apprenti' => $apprenti,
            'liste_etapes' => $liste_etapes,
            'etapes_dossier' => $etapes_dossier,
            'position_etape_actuelle' => $position_etape_actuelle,
            'nb_type_etapes' => $nb_type_etapes,
        ));
    }

    /**
     * Validation d'une etape d'un dossier
     *
     * @Route("/suivi/valider_etape/", name="valider_etape")
     */
    public function valider_etape(AuthorizationCheckerInterface $authChecker, Request $req)
    {
        if($req->isXmlHttpRequest()) { //On vérifie que c'est bien une requête AJAX pour empêcher un accès direct a cette fonction

            $id = $req->get('id');
            $id_etape = $req->get('id_etape');
            $em = $this->getDoctrine()->getManager();
            $dossier = $em->getRepository(DossierApprenti::class)->find($id);

            if (!$dossier) {
                return new JsonResponse(array('error' => "Pas de dossier trouvé"));
            }

            //On enregistre la date de validation
            $etape_actuelle = $dossier->getEtapeActuelle();

            //On vérifie que l'étape qu'on souhaite valider est bien l'étape actuelle (sécurité si on valide trop vite plusieurs étapes en même temps)
            if($etape_actuelle->getTypeEtape()->getId() == $id_etape) {

                $typeValidateur = $etape_actuelle->getTypeEtape()->getTypeValidateur();
                if(!$authChecker->isGranted('ROLE_IUT')) {
                    if (!$authChecker->isGranted($typeValidateur)) {
                        return new JsonResponse(array('error' => "Vous n'êtes pas autorisé a valider cette étape"));
                    }
                }

                $etape_actuelle->setdateValidation(new \DateTime());
                //On détermine le validateur qui est autorisé a valider cette étape
                $typeValidateur = $dossier->getEtapeActuelle()->getTypeEtape()->gettypeValidateur();


                //On identifie l'étape suivante qu'il faudra valider dans le dossier
                $type_etape_suivante = $em->getRepository(TypeEtape::class)->findOneByPositionEtape(($dossier->getEtapeActuelle()->getTypeEtape()->getId())+1);
                if ($type_etape_suivante) {
                    //On créer la nouvelle étape actuelle du dossier
                    $new_etape_dossier = new EtapeDossier();
                    $new_etape_dossier->setTypeEtape($type_etape_suivante);
                    $new_etape_dossier->setdateDebut(new \DateTime());
                    $new_etape_dossier->setDossier($dossier);

                    // tell Doctrine you want to (eventually) save the Product (no queries yet)
                    $em->persist($new_etape_dossier);

                    //On met a jour l'étape actuelle du dossier
                    $dossier->setEtapeActuelle($new_etape_dossier);
                } //Sinon il n'y a plus d'étape ensuite, donc le dossier est terminé
                else {
                    $dossier->setetat('Terminé');
                }
                $em->flush();
            }
            return new JsonResponse(array('error' => "ok"));
        }
        throw new AccessDeniedException();
    }

    /**
     * Annulation d'une etape d'un dossier
     *
     * @Route("/suivi/annuler_etape/", name="annuler_etape")
     */
    public function annuler_etape(AuthorizationCheckerInterface $authChecker, Request $req)
    {
        if($req->isXmlHttpRequest()) { //On vérifie que c'est bien une requête AJAX pour empêcher un accès direct a cette fonction
            if(!$authChecker->isGranted('ROLE_IUT')) {
                return new JsonResponse(array('error' => "Vous n'êtes pas autorisé a annuler cette étape"));
            }

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
            $new_etape_dossier->setDossier($dossier);

            $em->persist($new_etape_dossier);

            //On met a jour l'étape actuelle du dossier
            $dossier->setEtapeActuelle($new_etape_dossier);

            $em->flush();

            return new JsonResponse(array('error' => "ok"));
        }
        throw new AccessDeniedException();
    }

    /**
     * Historique
     *
     * @Route("/suivi/{id}/historique/", name="historique")
     */
    public function historique(AuthorizationCheckerInterface $authChecker, $id) {
        $res = $this->recup_dossier($authChecker, $id);
        $apprenti = $res[0];
        $dossier = $res[1];

        $etapes_dossier = $this->getDoctrine()
            ->getRepository(EtapeDossier::class)
            ->findBy(
                ['dossier' => $dossier], // Critere
                ['dateDebut' => 'DESC'] // Tri
            );

        return $this->render('suivi/historique.html.twig', array(
            'apprenti' => $apprenti,
            'etapes_dossier' => $etapes_dossier
        ));

    }

    /**
     * Abandon de dossier
     *
     * @Route("/suivi/abandon/", name="abandon")
     */
    public function abandon_dossier(AuthorizationCheckerInterface $authChecker, Request $req)
    {
        if($req->isXmlHttpRequest()) { //On vérifie que c'est bien une requête AJAX pour empêcher un accès direct a cette fonction

            $id = $req->get('id');

            $em = $this->getDoctrine()->getManager();
            $apprenti = $em->getRepository(Apprenti::class)->findOneByDossier($id);

            if (!$apprenti) {
                return new JsonResponse(array('error' => "Pas d'apprenti trouvé"));
            }

            if(!($authChecker->isGranted('ROLE_IUT') || $this->getUser()->getId()==$apprenti->getCompte()->getId())) {
                return new JsonResponse(array('error' => "Vous n'êtes pas autorisé a réaliser cette action"));
            }

            $apprenti->getDossierApprenti()->setetat('Abandonné');

            $em->flush();

            return new JsonResponse(array('error' => "ok"));
        }
        throw new AccessDeniedException();
    }

//    /**
//     * Abandon de dossier
//     *
//     * @Route("/suivi/reactivation/{id}", name="reactivation")
//     */
//    public function reactivation_dossier(AuthorizationCheckerInterface $authChecker, Request $req, $id)
//    {
//
//        $em = $this->getDoctrine()->getManager();
//        $apprenti = $em->getRepository(Apprenti::class)->findOneByDossier($id);
//
//        if (!$apprenti) {
//            return new JsonResponse(array('error' => "Pas d'apprenti trouvé"));
//        }
//
//        if(!($authChecker->isGranted('ROLE_IUT') || $this->getUser()->getId()==$apprenti->getCompte()->getId())) {
//            return new JsonResponse(array('error' => "Vous n'êtes pas autorisé a réaliser cette action"));
//        }
//
//        $apprenti->getDossierApprenti()->setetat('En cours');
//
//        $em->flush();
//
//        return new JsonResponse(array('error' => "ok"));
//    }

    /**
     * Page de statistiques
     *
     * @Route("/statistiques/", name="statistiques")
     * @IsGranted("ROLE_IUT")
     */
    public function statistiques(AuthorizationCheckerInterface $authChecker, Request $req) {

        $tezste = $this->getDoctrine()->getManager()->getRepository(EtapeDossier::class)->tempsMoyenDossier();



        //Exemple d'utilisation de Doctrine :
//        $em = $this->getDoctrine()->getManager();
//        $etape_dossier = $em->getRepository(EtapeDossier::class)->find($id_etape);
//        $etape_dossier = $em->getRepository(EtapeDossier::class)->findByDossier($id_dossier);
//        $etapes_dossier = $this->getDoctrine()
//            ->getRepository(EtapeDossier::class)
//            ->findBy(
//                ['dossier' => 1], // Critere
//                ['dateDebut' => 'ASC'] // Tri
//            );

        //Pour faire des requetes custom il faut utiliser le QueryBuilder de Doctrine : http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/query-builder.html
        //Le code est à mettre dans le Repository de l'entity (=classe correspondante), pour faire une requête custom sur l'entity EtapeDossier, il faut ajout le code dans une nouvelle fonction dans la classe EtapeDossierRepository dans le dossier Repository
        //Exemple de QueryBuilder dans ApprentiRepository avec utilisation dans la fontion liste
        $text1 = "En cours de ";
        $text2 = "développement";
        return new Response($text1 . "" . $text2);
    }
}







//    /* !!!!                TESTS                 !!!!*/
//    /**
//     * Suivi de apprenti corrspondant à l'id
//     *
//     * @Route("/test/{id}", name="test_suivi", requirements={"id"="\d+"}) //requirements permet d'autoriser uniquement les nombres dans l'URL
//     */
//    public function testsuivi(AuthorizationCheckerInterface $authChecker, $id)
//    {
//        //Un apprenti ne peux pas voir le suivi d'un autre apprenti
//        if ($authChecker->isGranted('ROLE_APPRENTI') && $this->getUser()->getId()!=$id) {
//            throw new AccessDeniedException();
//        }
//
//        //On récupère l'apprenti pour lequel on veux afficher le suivi
//        $apprenti = $this->getDoctrine()
//            ->getRepository(Apprenti::class)
//            ->find($id);
//
//        if(!$apprenti) {
//            return $this->render('message.html.twig', array(
//                'typeMessage' => "Apprenti non trouvé", 'message' => 'Pas d\'apprenti trouvé pour l\'ID ' . $id
//            ));
//        }
//
//        $iddossier = $apprenti->getDossierApprenti()->getId();
//
//        //Un maitre d'apprentissage ne peux voir le suivi que de ses apprenti
//        if ($authChecker->isGranted('ROLE_MAITRE_APP')) {
//            $ma = $apprenti->getDossierApprenti()->getMaitreApprentissage()->getId;
//            if ($ma != $this->getUser()->getId) {
//                throw new AccessDeniedException();
//            }
//        }
//
//
////        select * from type_etape te LEFT JOIN etape_dossier ed ON (ed.id_type_etape = te.id) WHERE id_dossier=2 OR id_dossier IS NULL ORDER BY te.position_etape
////        select * from type_etape te LEFT JOIN etape_dossier ed ON (ed.id_type_etape = te.id) WHERE
////    (id_dossier=2 OR id_dossier IS NULL) AND (ed.date_debut = (SELECT MAX(ed2.date_debut) FROM etape_dossier ed2 WHERE ed2.id_type_etape=te.id AND ed2.id_dossier=2) OR ed.date_debut IS NULL) ORDER BY te.position_etape
//        //On recupère toutes les étapes déjà complétée/en cours du dossier pour les afficher
//        $etapes_dossier = $this->getDoctrine()
//            ->getRepository(EtapeDossier::class)->findAllCurrent($iddossier);
//
////        $etapes_dossier = $this->getDoctrine()
////            ->getRepository(EtapeDossier::class)
////            ->findBy(
////                ['dossier' => $dossier], // Critere
////                ['typeEtape' => 'ASC'] // Tri
////            );
//
//        //On récupère l'ID type étape de l'étape actuelle du dossier
////        $id_type_etape_actuelle = $apprenti->getDossierApprenti()->getEtapeActuelle()->getTypeEtape()->getId();
////
////        //On récupère toutes les étapes pour un dossier
////        $liste_etapes = $this->getDoctrine()
////            ->getRepository(TypeEtape::class)
////            ->findAll();
////
////        //On récupère le nombre de type étape
////        $nb_type_etapes = $this->getDoctrine()
////            ->getRepository(TypeEtape::class)->getNbTypeEtape();
////
////        return $this->render('suivi/suivi.html.twig', array(
////            'apprenti' => $apprenti,
////            'id' => $id,
////            'liste_etapes' => $liste_etapes,
////            'etapes_dossier' => $etapes_dossier,
////            'id_type_etape_actuelle' => $id_type_etape_actuelle,
////            'nb_type_etapes' => $nb_type_etapes,
////        ));
//        return new Response(dump($etapes_dossier));
//    }
//}
?>