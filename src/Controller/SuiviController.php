<?php
namespace App\Controller;

use App\Entity\DossierApprenti;
use App\Entity\EtapeDossier;
use App\Entity\TypeEtape;
use App\Entity\Apprenti;
use App\Form\BordereauType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; //To define the route to access it
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted; //Pour vérifier si l'utilisateur est autorisé a accéder à la page
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException; //Erreur 403
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface; //Pour vérifier les droits
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; //Erreur 404
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\NotBlank;

class SuiviController extends Controller
{

    /**
     * On récupère les résultats de la recherche d'apprentis
     *
     */
    public function results_recherche(AuthorizationCheckerInterface $authChecker, Request $req) {
        //On récupère la recherche d'apprenti
        $search = $req->get('search');
        $etat = (int) $req->get('etat');
        if(empty($etat)) {
            $etat = 2;
        }
        if($etat == 1) {
            $etat_s = null;
        } elseif($etat == 2) {
            $etat_s = "En cours";
        } elseif($etat == 3) {
            $etat_s = "Abandonné";
        } elseif($etat == 4) {
            $etat_s = "Terminé";
        }

        if ($authChecker->isGranted('ROLE_MAITRE_APP')) {
            $id_maitre_app = $this->getUser()->getId();
            $liste = $this->getDoctrine()
                ->getRepository(Apprenti::class)->searchForMaitreApp($search, $id_maitre_app, $etat_s);
        }
        else {
            $liste = $this->getDoctrine()
                ->getRepository(Apprenti::class)->search($search, $etat_s);
        }
        return array($liste, $etat, $search);
    }

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

        $res = $this->results_recherche($authChecker, $req);
        $liste = $res[0];
        $etat = $res[1];
        $search = $res[2];

        return $this->render('suivi/liste.html.twig', array(
            'liste' => $liste, 'etat' => $etat, 'search' => $search
        ));
    }


    /**
     * Recherche des apprentis
     *
     * @Route("/liste/recherche/", name="recherche_liste")
     */
    public function recherche(AuthorizationCheckerInterface $authChecker, Request $req) {
        if($req->isXmlHttpRequest()) {
            if ($authChecker->isGranted('ROLE_APPRENTI')) {
                throw new AccessDeniedException();
            }

            $res = $this->results_recherche($authChecker, $req);
            $liste = $res[0];

            $apprenti_json = array();
            foreach ($liste as &$apprenti) {
                $entreprise = $apprenti->getDossier()->getEntreprise();
                if (empty($entreprise)) {
                    $entreprise = 'Pas encore saisi';
                } else {
                    $entreprise = $entreprise->getNom();
                }
                $mission = $apprenti->getDossier()->getsujetPropose();
                if (empty($mission)) {
                    $mission = 'Pas encore saisi';
                }
                $apprenti_json[] = array(
                    "id" => $apprenti->getCompte()->getId(),
                    "nom" => $apprenti->getCompte()->getNom(),
                    "prenom" => $apprenti->getCompte()->getPrenom(),
                    "entreprise" => $entreprise,
                    "mission" => $mission,
                    "etat_avancement" => $apprenti->getDossier()->getEtapeActuelle()->getTypeEtape()->getnomEtape(),
                    "lien" => $this->generateUrl(
                        'suivi',
                        array('id' => $apprenti->getCompte()->getId()),
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                );
            }
            return new JsonResponse(array('liste' => $apprenti_json));
        }
        throw new AccessDeniedException();
    }


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
            throw new NotFoundHttpException();
        }

        $dossier = $apprenti->getDossier()->getId();

        //Un maitre d'apprentissage ne peut voir le suivi que de ses apprenti (on bloque également l'accès au suivi des apprentis qui n'ont pas encore un maitre d'apprentissage
        if ($authChecker->isGranted('ROLE_MAITRE_APP')) {
            $ma_exist = !empty($apprenti->getDossier()->getMaitreApprentissage());
            if($ma_exist) {
                $ma = $apprenti->getDossier()->getMaitreApprentissage()->getCompte()->getId();
            }
            if (!$ma_exist || $ma != $this->getUser()->getId()) {
                throw new AccessDeniedException();
            }
        }
        return array($apprenti, $dossier);
    }

    /**
     * Suivi de apprenti correspondant à l'id
     *
     * @Route("/suivi/{id}", name="suivi", requirements={"id"="\d+"}) //requirements permet d'autoriser uniquement les nombres dans l'URL
     */
    public function suivi(AuthorizationCheckerInterface $authChecker, $id)
    {
        $res = $this->recup_dossier($authChecker, $id);
        $apprenti = $res[0];
        $dossier = $res[1];

        //On recupère toutes les étapes déjà complétée/en cours du dossier pour les afficher
        $etapes_dossier = $this->getDoctrine()
            ->getRepository(EtapeDossier::class)
            ->findBy(
                ['dossier' => $dossier], // Critere
                ['typeEtape' => 'ASC'] // Tri
            );

        //On récupère l'ID type étape de l'étape actuelle du dossier
        $etape_actuelle = $apprenti->getDossier()->getEtapeActuelle()->getTypeEtape();
        $position_etape_actuelle = $etape_actuelle->getpositionEtape();

//        if($position_etape_actuelle == 2 && $authChecker->isGranted('ROLE_MAITRE_APP')) {
//            $this->addFlash('warning', "Une fois le bordereau envoyé, veuillez valider l'étape \"" . $etape_actuelle->getNomEtape() . "\" en cliquant dessus.");
//        }

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
     * Remplissage du bordereau
     *
     * @Route("/suivi/{id}/remplir_bordereau/", name="remplir_bordereau", requirements={"id"="\d+"})
     */
    public function remplissage_bordereau(AuthorizationCheckerInterface $authChecker, $id, Request $request)
    {
        if($authChecker->isGranted('ROLE_MAITRE_APP') || $authChecker->isGranted('ROLE_IUT')) {
            $res = $this->recup_dossier($authChecker, $id);
            $apprenti = $res[0];
            $dossier_id = $res[1];

            $dossier = $apprenti->getDossier();

            //Si les infos de l'entreprise ont déjà été remplies
            if(!empty($dossier->getEntreprise()->getRaisonSociale())) {
                //Si le bordereau à déjà été remplis
                if(!empty($dossier->getSujetPropose()) && !$authChecker->isGranted('ROLE_IUT')) {
                    return $this->redirectToRoute('suivi', array('id' => $id));
                }
                else {
                    $form = $this->createForm(BordereauType::class, $dossier);

                    if ($request->isMethod('POST')) {

                        $form->handleRequest($request);
                        if ($form->isSubmitted() && $form->isValid()) {

                            if($authChecker->isGranted('ROLE_MAITRE_APP')) {
                                $this->get('app.emailservice')->notification_bordereau($apprenti);
                            }

                            $em = $this->getDoctrine()->getManager();

                            $etape_actuelle = $dossier->getEtapeActuelle();

                            $position_etape_actuelle = $etape_actuelle->getTypeEtape()->getPositionEtape();

                            if($position_etape_actuelle == 2) {
                                $etape_actuelle->setdateValidation(new \DateTime());

                                $type_etape_suivante = $em->getRepository(TypeEtape::class)->findOneByPositionEtape($position_etape_actuelle+1);
                                //On créer la nouvelle étape actuelle du dossier
                                $new_etape_dossier = new EtapeDossier();
                                $new_etape_dossier->setTypeEtape($type_etape_suivante);
                                $new_etape_dossier->setdateDebut(new \DateTime());
                                $new_etape_dossier->setDossier($dossier);

                                $em->persist($new_etape_dossier);

                                //On met a jour l'étape actuelle du dossier
                                $dossier->setEtapeActuelle($new_etape_dossier);
                                $em->flush();
                            }

                            $this->addFlash('success', 'Bordereau bien enregistré.');

                            $em->persist($dossier);

                            $em->flush();

                            return $this->redirectToRoute('consulter_bordereau', array('id' => $id));
                        }
                    }
                }
            } else {
                return $this->redirectToRoute('remplissage_entreprise', array('id' => $dossier->getEntreprise()->getId(), 'bordereau' => $id));
            }
            //Soit on viens d'arriver sur la page, soit le formulaire contient des données incorrectes
            return $this->render('suivi/bordereau.html.twig', array('form' => $form->createView(),
            ));
        }
        else {
            throw new AccessDeniedException();
        }

    }

    /**
     * Consultation du bordereau
     *
     * @Route("/suivi/{id}/bordereau/", name="consulter_bordereau", requirements={"id"="\d+"})
     */
    public function consulter_bordereau(AuthorizationCheckerInterface $authChecker, $id, Request $request)
    {
        $res = $this->recup_dossier($authChecker, $id);
        $apprenti = $res[0];
        $dossier_id = $res[1];

        $dossier = $apprenti->getDossier();

        //Si l'entreprise n'a pas encore été choisie
        if(!empty($dossier->getEntreprise())) {
            //Si le bordereau n'a pas encore été remplis
            if (empty($dossier->getSujetPropose()) || empty($dossier->getEntreprise()->getRaisonSociale()) || $dossier->getEtapeActuelle()->getTypeEtape()->getPositionEtape() == 2) {
                if($dossier->getEtat() != "Terminé") {
                    return $this->redirectToRoute('remplir_bordereau', array('id' => $id));
                }
                else {
                    return $this->redirectToRoute('suivi', array('id' => $id));
                }
            } else {
                $entreprise = $dossier->getEntreprise();
                $maitreapprentissage = $dossier->getMaitreApprentissage();

                if(empty($request->query->get('option'))) {
                    return $this->render('suivi/infos_bordereau.html.twig',
                        array('id' => $id,
                            'entreprise' => $entreprise,
                            'maitre' => $maitreapprentissage,
                            'dossier' => $dossier
                        ));
                }
                else {
                    $html = $this->renderView('bordereau/template_bordereau.html.twig', array(
                        'entreprise' => $entreprise, 'dossier' => $dossier, 'apprenti' => $apprenti
                    ));
                    $filename = sprintf('Bordereau-%s.pdf', date('Y-m-d'));

//                    return new Response($html);
                    return new Response(
                        $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                        200,
                        [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => sprintf('inline; filename="%s"', $filename),
                        ]
                    );
                }
            }
        }
        else {
            return $this->redirectToRoute('suivi', array('id' => $id));
        }
    }


    /**
     * Validation ou invalidation du bordereau
     *
     * @Route("/suivi/{id}/bordereau/validation/", name="validation_bordereau", requirements={"id"="\d+"})
     */
    public function validation_bordereau(AuthorizationCheckerInterface $authChecker, $id, Request $request) {
        $res = $this->recup_dossier($authChecker, $id);
        $apprenti = $res[0];
        $dossier_id = $res[1];

        $dossier = $apprenti->getDossier();

        if($dossier->getEtapeActuelle()->getTypeEtape()->getPositionEtape() != 3) {
            throw new AccessDeniedException();
        }
        $resp = $request->query->get('resp');

        if ($resp == "false") {
            $defaultData = array('message' => '');
            $form = $this->createFormBuilder($defaultData)
                ->add('raison', TextareaType::class, array(
                    'attr' => array('rows' => '10'),
                    'label' => "Raison de l'invalidation",
                    'constraints' => array(
                        new NotBlank())))
                ->add('Envoyer', SubmitType::class)
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $message = $data["raison"];

                $this->get('app.emailservice')->invalidation_bordereau($apprenti, $message);
                $this->addFlash('success', "Le maître d'apprentissage a été notifié de l'invalidation, il pourra apporter les modifications nécessaires au bordereau.");

                //Annuler étape
                $this->maj_etape_dossier($dossier, "invalider");

                return $this->redirectToRoute('suivi', array('id' => $id));
            }
            return $this->render('suivi/invalider_bordereau.html.twig',
                array('form' => $form->createView(),
                ));
        }
        else if ($resp == "true") {
            $this->maj_etape_dossier($dossier, "valider");

            $this->addFlash('success', "Bordereau validé avec succès.");
            return $this->redirectToRoute('suivi', array('id' => $id));
        }
        return $this->render('suivi/validation_bordereau.html.twig',
            array('user' => $apprenti->getCompte()));
    }


    /**
     * Mise à jour du dossier pour changer l'étape en cours
     *
     */
    public function maj_etape_dossier(DossierApprenti $dossier, $type)
    {
        $etape_actuelle = $dossier->getEtapeActuelle();


        $em = $this->getDoctrine()->getManager();

        //On identifie l'étape suivante qu'il faudra valider dans le dossier
        if ($type == "valider") {
            $etape_actuelle->setdateValidation(new \DateTime());
            $type_etape_new = $em->getRepository(TypeEtape::class)->findOneByPositionEtape(($etape_actuelle->getTypeEtape()->getPositionEtape()) + 1);
        } elseif ($type == "invalider") {
            $dossier->setetat('En cours');
            $type_etape_new = $em->getRepository(TypeEtape::class)->findOneByPositionEtape(($etape_actuelle->getTypeEtape()->getPositionEtape()) - 1);
        }
        if ($type_etape_new) {
            //On créer la nouvelle étape actuelle du dossier
            $new_etape_dossier = new EtapeDossier();
            $new_etape_dossier->setTypeEtape($type_etape_new);
            $new_etape_dossier->setdateDebut(new \DateTime());
            $new_etape_dossier->setDossier($dossier);

            // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $em->persist($new_etape_dossier);

            //On met a jour l'étape actuelle du dossier
            $dossier->setEtapeActuelle($new_etape_dossier);
        }
        else {
            $dossier->setetat('Terminé');
        }

        $em->flush();
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

                $this->maj_etape_dossier($dossier, "valider");
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
            if($type_etape->getPositionEtape() == 1) {
                $dossier->reset();
            }

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
     * @IsGranted("ROLE_IUT")
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

            if(!($authChecker->isGranted('ROLE_IUT'))) {
                return new AccessDeniedException();
            }

            $apprenti->getDossier()->setetat('Abandonné');

            $em->flush();

            return new JsonResponse(array('error' => "ok"));
        }
        throw new AccessDeniedException();
    }


    /**
     * Réactivation du dossier
     *
     * @Route("/suivi/reactivation/", name="reactivation")
     * @IsGranted("ROLE_IUT")
     */
    public function reactivation_dossier(AuthorizationCheckerInterface $authChecker, Request $req)
    {
        if($req->isXmlHttpRequest()) { //On vérifie que c'est bien une requête AJAX pour empêcher un accès direct a cette fonction

            $id = $req->get('id');

            $em = $this->getDoctrine()->getManager();
            $apprenti = $em->getRepository(Apprenti::class)->findOneByDossier($id);

            if (!$apprenti) {
                return new JsonResponse(array('error' => "Pas d'apprenti trouvé"));
            }

            $apprenti->getDossier()->setetat('En cours');

            $em->flush();

            return new JsonResponse(array('error' => "ok"));
        }
        throw new AccessDeniedException();
    }

    /**
     * Page de statistiques
     *
     * @Route("/statistiques/", name="statistiques")
     * @IsGranted("ROLE_IUT")
     */
    public function statistiques(AuthorizationCheckerInterface $authChecker, Request $req) {

        $search = $req->query->get('search');

        $em = $this->getDoctrine()->getManager();

        $liste_nom_etape = $em->getRepository(TypeEtape::class)->getListNomTypeEtape();
        $liste_nom_etape = array_column($liste_nom_etape, 'nomEtape');

        $abandons = $em->getRepository(EtapeDossier::class)->nombreAbandonsDossier();
        $tempsMoyenDossiers = $em->getRepository(EtapeDossier::class)->tempsMoyenDossier();
        $tempsMoyenEtapes = $em->getRepository(EtapeDossier::class)->tempsMoyenEtapes();

        $tauxAbandonEtape = $em->getRepository(EtapeDossier::class)->tauxAbandonEtapes();

        return $this->render('suivi/statistiques.html.twig',
            array('liste_nom_etape' => json_encode($liste_nom_etape),
                'tmpMoyenDossier' => $tempsMoyenDossiers,
                'nbAbandons' => $abandons,
                'tmpMoyenEtapes' => json_encode($tempsMoyenEtapes),
                'tauxAbandonEtape' => json_encode($tauxAbandonEtape)));
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
//        $iddossier = $apprenti->getDossier()->getId();
//
//        //Un maitre d'apprentissage ne peux voir le suivi que de ses apprenti
//        if ($authChecker->isGranted('ROLE_MAITRE_APP')) {
//            $ma = $apprenti->getDossier()->getMaitreApprentissage()->getId;
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
////        $id_type_etape_actuelle = $apprenti->getDossier()->getEtapeActuelle()->getTypeEtape()->getId();
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