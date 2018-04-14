<?php

namespace App\Controller;

use App\Entity\CorrespondantEntreprise;
use App\Entity\MaitreApprentissage;
use App\Form\CompteType;
use Symfony\Component\Form\FormError;
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

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class EntrepriseController extends Controller
{
    /**
     * @Route("/entreprise/choix/", name="choix_entreprise")
     */
    public function choix_entreprise(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder)
    {
        $entreprises = $this->getDoctrine()->getRepository(Entreprise::class)->findAll();

        $ma = new MaitreApprentissage();
        $user = new User();
        $ma->setCompte($user);
        $entreprise = new Entreprise();
        $ma->setEntreprise($entreprise);
        $form = $this->createForm(MaitreApprentissageType::class, $ma);

        $selectionEntreprise = null;
        $selectionMaitre = null;
        $maitres = null;
        $error = false;
        $addMa = false;

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();

            $selectionEntreprise = $request->request->get('select_entreprise');
            $selectionMaitre = $request->request->get('select_maitre');

            if (!empty($selectionEntreprise && !empty($selectionMaitre)) /*&& !empty($selectionMaitre)*/) {
                if ($form->isSubmitted()) {

                    //Choix autre entreprise
                    if ($selectionEntreprise == 'Autre') {
//                        $this->addFlash('info', 'ICI');

                        $errorsEn = $validator->validate($ma->getEntreprise());
                        $errorsMa = $validator->validate($ma->getCompte(), null, array('ajout'));
//                        return new Response(count($errors) );
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

                                }
                            }
                            //Maitre d'apprentissage existant choisi
                            if ($selectionMaitre != 'Autre' && !empty($selectionMaitre)) {
                                $ma = $this->getDoctrine()->getRepository(MaitreApprentissage::class)->find($selectionMaitre);
                                if (!$ma) {
                                    $form->addError(new FormError('Maitre d\'apprentissage inexistant'));
                                    $error = true;
                                }
                            }
                        }
                    }
//
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
                    $em->flush();
                    return $this->redirectToRoute('suivi_perso');
                }
            }
        }
        return $this->render('entreprise/entreprise.html.twig', array('entreprises' => $entreprises, 'maitres' => $maitres, 'form' => $form->createView(), 'selectionEntreprise' => $selectionEntreprise, 'selectionMaitre' => $selectionMaitre));
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
}



