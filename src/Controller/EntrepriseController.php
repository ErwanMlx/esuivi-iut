<?php

namespace App\Controller;

use App\Entity\Apprenti;
use App\Entity\CorrespondantEntreprise;
use App\Entity\EtapeDossier;
use App\Entity\MaitreApprentissage;
use App\Entity\TypeEtape;
use App\Form\CompteType;
use App\Form\EntrepriseSupType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route; //To define the route to access it
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Entreprise;
use App\Entity\User;
use App\Form\EntrepriseType;
use App\Form\MaitreApprentissageType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class EntrepriseController extends Controller
{
    /**
     * @Route("/entreprise/choix/{id}", name="choix_entreprise", requirements={"id"="\d+"}, defaults={"id"=null})
     */
    public function choix_entreprise(AuthorizationCheckerInterface $authChecker, Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder, $id)
    {

        //Si ce n'est pas un apprenti qui accède à la page
        if (!$authChecker->isGranted('ROLE_APPRENTI') && !$authChecker->isGranted('ROLE_IUT')) {
            throw new AccessDeniedException();
        }

        //On récupère l'apprenti pour lequel on veut afficher le suivi
        if($authChecker->isGranted('ROLE_APPRENTI')) {
            $apprenti = $this->getDoctrine()
                ->getRepository(Apprenti::class)
                ->find($this->getUser()->getId());
        }
        elseif ($authChecker->isGranted('ROLE_IUT')) {
            $apprenti = $this->getDoctrine()
                ->getRepository(Apprenti::class)
                ->find($id);
        }


        if(!$apprenti) {
            throw new NotFoundHttpException();
        }
        //Si l'apprenti a déjà saisi les informations de son entreprise
//        if($authChecker->isGranted('ROLE_APPRENTI') && !empty($apprenti->getDossier()->getEntreprise())) {
//            throw new AccessDeniedException();
//        }

        $selectionEntreprise = null;
        $selectionMaitre = null;
        $maitres = null;
        $entreprise_old = null;
        $ma_old = null;
        $error = false;
        $addMa = false;
        $src = $request->get('src');


        $entreprises = $this->getDoctrine()->getRepository(Entreprise::class)->findAll();

        if(!empty($apprenti->getDossier()->getEntreprise())) {
            $ma_old = $apprenti->getDossier()->getMaitreApprentissage();
            $user = $apprenti->getDossier()->getMaitreApprentissage()->getCompte();
            $entreprise_old = $apprenti->getDossier()->getEntreprise();
            $selectionEntreprise = $entreprise_old->getId();
            $selectionMaitre = $user->getId();
            $maitres = $this->getDoctrine()->getRepository(MaitreApprentissage::class)->findByEntreprise($entreprise_old);
        }
        $ma = new MaitreApprentissage();
        $user = new User();
        $ma->setCompte($user);
        $entreprise = new Entreprise();
        $ma->setEntreprise($entreprise);

        $form = $this->createForm(MaitreApprentissageType::class, $ma);

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();

            $selectionEntreprise = $request->request->get('select_entreprise');
            $selectionMaitre = $request->request->get('select_maitre');

//            if (!empty($selectionEntreprise) && !empty($selectionMaitre)) {
            if ($form->isSubmitted()) {

                //Choix autre entreprise
                if ($selectionEntreprise == 'Autre') {

                    $errorsEn = $validator->validate($ma->getEntreprise(), null, array('ajout_entreprise'));
                    $errorsMa = $validator->validate($ma->getCompte(), null, array('ajout'));
                    if (count($errorsEn) == 0 && count($errorsMa) == 0) {
                        $addMa = true;
                        $em->persist($ma->getEntreprise());
                    } else {
                        foreach ($errorsEn as &$err) {
                            $input = $err->getPropertyPath();
                            if($input == "codePostal") {
                                $input = "code_postal";
                            }
                            $form->get('entreprise')->get($input)->addError(new FormError($err->getMessage()));
                        }
                        foreach ($errorsMa as &$err) {
                            $form->get('compte')->get($err->getPropertyPath())->addError(new FormError($err->getMessage()));
                        }
                        $error = true;
                    }
                }
                //Entreprise existante choisie
                if ($selectionEntreprise != 'Autre' && !empty($selectionEntreprise)) {
                    $entreprise = $this->getDoctrine()->getRepository(Entreprise::class)->find($selectionEntreprise);
                    $entreprise_old = $entreprise;
                    if (!$entreprise) {
                        $this->addFlash('warning', 'Entreprise inexistante');
                        $error = true;
                    } else {
                        $maitres = $this->getDoctrine()->getRepository(MaitreApprentissage::class)->findByEntreprise($entreprise);
                        //Choix autre maitre d'apprentissage
                        if ($selectionMaitre == 'Autre') {
                            $ma->setEntreprise($entreprise);
                            $errors = $validator->validate($ma->getCompte(), null, array('ajout'));

                            if (count($errors) == 0) {

                                $email = $this->getDoctrine()->getRepository(User::class)->findByEmail($ma->getCompte()->getEmail());

                                //Si le mail n'est pas déjà utilisé
                                if (!$email) {
                                    $addMa = true;
                                } else {
                                    $form->addError(new FormError('Adresse email déjà utilisée'));
                                    $error = true;
                                }
                            }
                            else {
                                foreach ($errors as &$err) {
                                    $form->get('compte')->get($err->getPropertyPath())->addError(new FormError($err->getMessage()));
                                }
                                $error = true;
                            }
                        }
                        //Maitre d'apprentissage existant choisi
                        if ($selectionMaitre != 'Autre' && !empty($selectionMaitre)) {
                            $ma = $this->getDoctrine()->getRepository(MaitreApprentissage::class)->find($selectionMaitre);
                            if (!$ma) {
                                $form->addError(new FormError('Maitre d\'apprentissage inexistant'));
                                $error = true;
                            }
                            $ma_old = &$ma;
                        }
                        if(empty($selectionEntreprise)) {
                            $this->addFlash('danger', "Veuillez sélectionner une entreprise.");
                            $error = true;
                        } else if(empty($selectionMaitre)) {
                            $this->addFlash('danger', "Veuillez sélectionner un maitre d'apprentissage.");
                            $error = true;
                        }

                    }
                }
            }
            if(!$error) {
                if($addMa) {
                    $ma->getCompte()->addRole("ROLE_MAITRE_APP");
                    $password = 'password';
                    $encoded = $encoder->encodePassword($user, $password);

                    $user->setPassword($encoded);

                    $user->setEnabled(true);
                    $em->persist($ma->getCompte());
                    $em->persist($ma);
                }
                $apprenti->getDossier()->setEntreprise($entreprise);
                $apprenti->getDossier()->setMaitreApprentissage($ma);

                if($apprenti->getDossier()->getEtapeActuelle()->getTypeEtape()->getPositionEtape() == 1) {
                    $etape_actuelle = $apprenti->getDossier()->getEtapeActuelle();
                    $etape_actuelle->setdateValidation(new \DateTime());
                    $etape_suivante = $this->getDoctrine()->getRepository(TypeEtape::class)->findOneByPositionEtape(2);
//                        $etape_suivante = $this->getDoctrine()->getRepository(TypeEtape::class)->findOneByPositionEtape($etape_actuelle->getTypeEtape()->getId()+1);

                    $new_etape_dossier = new EtapeDossier();
                    $new_etape_dossier->setTypeEtape($etape_suivante);
                    $new_etape_dossier->setdateDebut(new \DateTime());
                    $new_etape_dossier->setDossier($apprenti->getDossier());

                    $em->persist($new_etape_dossier);

                    //On met a jour l'étape actuelle du dossier
                    $apprenti->getDossier()->setEtapeActuelle($new_etape_dossier);
                }
                $em->flush();

                if(empty($id)) {
                    return $this->redirectToRoute('suivi_perso');
                } else {
                    if($src == "bordereau") {
                        return $this->redirectToRoute('consulter_bordereau', array('id' => $id));
                    }
                    else {
                        return $this->redirectToRoute('suivi', array('id' => $id));
                    }
                }

            }
//            }
        }
        return $this->render('entreprise/choix_entreprise.html.twig',
            array('id' => $id,
                'entreprises' => $entreprises,
                'maitres' => $maitres,
                'ma_old' => $ma_old,
                'entreprise_old' => $entreprise_old,
                'form' => $form->createView(),
                'selectionEntreprise' => $selectionEntreprise,
                'selectionMaitre' => $selectionMaitre,
                'src' => $src));
    }

    /**
     * Récupération des informations d'une entreprise et de la liste de ses maitres d'apprentissage
     *
     * @Route("/entreprise/choix/informations/", name="infos_entreprise")
     */
    public function infos_entreprise(AuthorizationCheckerInterface $authChecker, Request $req)
    {
        if($req->isXmlHttpRequest()) { //On vérifie que c'est bien une requête AJAX pour empêcher un accès direct a cette fonction
            $id_entreprise = $req->get('id_entreprise');
            $em = $this->getDoctrine()->getManager();
            $entreprise = $em->getRepository(Entreprise::class)->find($id_entreprise);

            $entreprise_json = array("id" => $entreprise->getId(),
                "nom" => $entreprise->getNom(),
                "siret" => $entreprise->getSiret(),
                "adresse" => $entreprise->getAdresse(),
                "ville" => $entreprise->getVille(),
                "cp" => $entreprise->getCodePostal(),
                "telephone" => $entreprise->getTelephone()
            );


            $liste_ma = $em->getRepository(MaitreApprentissage::class)->findByEntreprise($entreprise);
            $liste_json = array();
            foreach ($liste_ma as &$ma) {
                $liste_json[] = array("id" => $ma->getCompte()->getId(), "nom" => $ma->getCompte()->getNom(), "prenom" => $ma->getCompte()->getPrenom());
            }

            return new JsonResponse(array('entreprise' => $entreprise_json, 'liste_ma' => $liste_json));
        }
        throw new AccessDeniedException();
    }

    /**
     * Récupération des informations du maitre d'apprentissage selectionné
     *
     * @Route("/entreprise/choix/informations_ma/", name="infos_ma")
     */
    public function infos_maitre_app(AuthorizationCheckerInterface $authChecker, Request $req)
    {
        if($req->isXmlHttpRequest()) { //On vérifie que c'est bien une requête AJAX pour empêcher un accès direct a cette fonction
            $id_ma = $req->get('id_ma');
            $em = $this->getDoctrine()->getManager();

            $ma = $em->getRepository(MaitreApprentissage::class)->find($id_ma);

            $ma_json = array("id" => $ma->getCompte()->getId(),
                "nom" => $ma->getCompte()->getNom(),
                "prenom" => $ma->getCompte()->getPrenom(),
                "email" => $ma->getCompte()->getEmail(),
                "tel" => $ma->getTelephone(),
                "fonction" => $ma->getFonction()
            );
            return new JsonResponse(array('maitre_app' => $ma_json));
        }
        throw new AccessDeniedException();
    }

    /**
     * Récupération des informations du maitre d'apprentissage selectionné
     *
     * @Route("/entreprise/saisie/{id}", name="remplissage_entreprise", requirements={"id"="\d+"}, defaults={"id"=null})
     */
    public function remplissage_entreprise(AuthorizationCheckerInterface $authChecker, Request $request, $id) {
        if($authChecker->isGranted('ROLE_MAITRE_APP') || (!empty($id) && $authChecker->isGranted('ROLE_IUT'))) {
            $em = $this->getDoctrine()->getManager();
            if($authChecker->isGranted('ROLE_IUT')) {
                $entreprise = $em->getRepository(Entreprise::class)->find($id);
            }
            else {
                $ma = $em->getRepository(MaitreApprentissage::class)->find($this->getUser()->getId());
                $entreprise = $ma->getEntreprise();
            }

            $form = $this->createForm(EntrepriseSupType::class, $entreprise);

            if ($request->isMethod('POST')) {

                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $this->addFlash('success', 'Informations entreprise bien enregistrées.');

                    $em = $this->getDoctrine()->getManager();

                    $em->persist($entreprise->getCorrespondantEntreprise());
                    $em->persist($entreprise);

                    $em->flush();

                    $id_dossier = $request->query->get('bordereau');
                    if(!empty($id_dossier)) {
                        return $this->redirectToRoute('remplir_bordereau', array('id' => $id_dossier));
                    } else if($request->get('src') == "bordereau") {
                        return $this->redirectToRoute('consulter_bordereau', array('id' => $request->get('app')));
                    } else {
                        return $this->redirectToRoute('liste');
                    }
                }
            }

            return $this->render('entreprise/infos_entreprise.html.twig', array('form' => $form->createView(),
            ));
        }
        else {
            throw new AccessDeniedException();
        }
    }

}



