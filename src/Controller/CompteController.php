<?php

namespace App\Controller;

use App\Entity\Apprenti;
use App\Entity\DossierApprenti;
use App\Entity\EtapeDossier;
use App\Entity\User;
use App\Entity\TypeEtape;
use App\Form\ApprentiType;
use App\Form\CompteType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; //To define the route to access it
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class CompteController extends Controller
{

    /**
     * Affichage de la page de choix d'ajout de compte
     *
     * @Route("/ajout_compte", name="choix_ajout")
     */
    public function choix_ajout() {
        return $this->render('compte/choix_ajout.html.twig');
    }

    /**
     * Formulaire d'ajout de apprenti/cfa/iut
     *
     * @Route("/compte/ajout/{type}", name="ajout_compte", requirements={"type"="(apprenti|cfa|iut)"})
     */
    public function ajout_compte(Request $request, UserPasswordEncoderInterface $encoder, $type)
    {
        //!!! A modif avec gestion de compte pour vérif si un iut a le droit d'add un collègue
        $autorized = true;

        $user = new User();
        //On détermine quel type de compte on va créer
        if($type == "apprenti") {
            $title = "apprenti";
        } elseif($type == "cfa") {
            $title = "CFA";
        } elseif($type == "iut" && $autorized) {
            $title = "IUT";
        } else {
            return $this->render('message.html.twig', array(
                'typeMessage' => "Erreur", 'message' => "Vous n'êtes pas autorisé à accéder à cette page."
            ));
        }

        //On créer le formulaire
//        $form = $this->createFormBuilder($compte, array(
//            'validation_groups' => array('ajout'),))
//            ->add('nom',      TextType::class)
//            ->add('prenom',     TextType::class)
//            ->add('email',   EmailType::class)
//
//        ;

        $form = $this->createForm(CompteType::class, $user);

        //Dans le cas si on souhaite créer d'autres comptes administrateur
//        if($type == "iut") {
//            $form = $form->add('administrateur',   CheckboxType::class, array('required' => false));
//        }

        $form = $form->add('ajouter', SubmitType::class);

        // Si la requête est en POST (donc que le formulaire à été validé)
        if ($request->isMethod('POST')) {

            // À partir de maintenant, la variable $compte contient les valeurs entrées dans le formulaire par l'utilisateur
            $form->handleRequest($request);

            $email_exist = false;
            $userManager = $this->get('fos_user.user_manager');


            $exists = $userManager->findUserBy(array('email' => $user->getEmail()));
            if ($exists instanceof User) {
                $email_exist = true;            }

            //On vérifie s'il n'y a pas déjà un compte lié à cette adresse mail
//            $email_exist = $this->getDoctrine()->getRepository(Compte::class)->findByEmail($user->getEmail());
            // On vérifie que les valeurs entrées sont correctes
            if ($form->isSubmitted() && $form->isValid() && !$email_exist) {
                //On génère le mot de passe
//                $compte->setPassword(base64_encode(random_bytes(10)));

//                $userManager->updateUser($user);


                $plainPassword = 'password';
                $encoded = $encoder->encodePassword($user, $plainPassword);

                $user->setPassword($encoded);

                $user->setEnabled(true);

                if($type == "apprenti") {
                    $user->addRole('ROLE_APPRENTI');
                }
                elseif ($type == "cfa") {
                    $user->addRole('ROLE_CFA');
                }
                elseif ($type == "iut") {
                    $user->addRole('ROLE_IUT');
                }

                // On enregistre notre objet $compte dans la base de données,
                $em = $this->getDoctrine()->getManager();

                //On créer et rattache un dossier à l'apprenti
                if($type == "apprenti") {
                    $role = new Apprenti();

                    //!!! Provisoire mais a remplacer par l'id de l'user connecté

                    $user_connected = $this->getDoctrine()->getRepository(User::class)->find(1);
                    $role->setResponsableIut($user_connected);


                    $dossier = new DossierApprenti();
                    $role->setDossierApprenti($dossier);
                    $dossier->setEtat("En cours");
                    $em->persist($dossier);
                    $etape_dossier = new EtapeDossier();
                    $etape_dossier->setDossier($dossier);
                    $etape_dossier->setTypeEtape($this->getDoctrine()->getRepository(TypeEtape::class)->find(1));
                    $em->persist($etape_dossier);
                    $dossier->setEtapeActuelle($etape_dossier);
                }

                $em->persist($user);
                if($type == "apprenti") {
                    $role->setCompte($user);
                    $em->persist($role);
                }
                $em->flush();

                //On affiche un message de succès
                $this->addFlash('success', 'Compte bien enregistré.');

                // On redirige vers la même page pour donner la possibilité d'ajouter d'autres comptes
                return $this->redirectToRoute('ajout_compte', array('type' => $type));
            }
            //Si le mail est déjà utilisé on affiche a message d'erreur
            if($email_exist) {
                $form->get('email')->addError(new FormError('Un compte lié à cet email existe déjà.'));
            }
        }

        //Soit on viens d'arriver sur la page, soit le formulaire contient des données incorrectes
        return $this->render('compte/ajout.html.twig', array('title' => $title,
            'form' => $form->createView(),
        ));
    }

    /**
     * Edition du compte de l'apprenti
     *
     * @Route("/compte/edition/{id}", name="edition_compte", requirements={"id"="\d+"})
     */
    public function edition_compte(Request $request, $id) {
        $title = "Edition du compte";
        $apprenti = $this->getDoctrine()->getRepository(Apprenti::class)->find($id);

        //!!! Il faut vérifier que l'utilisateur connecté est un iut ou que le compte a modifier est bien celui de l'apprenti connecté
        $autorized = true;
        if(!$autorized) {
            return $this->render('message.html.twig', array(
                'typeMessage' => "Erreur", 'message' => "Vous n'êtes pas autorisé à accéder à cette page."
            ));
        }

        if(!$apprenti) {
            return $this->render('message.html.twig', array(
                'typeMessage' => "Apprenti non trouvé", 'message' => 'Pas d\'apprenti trouvé pour l\'ID ' . $id
            ));
        }

        $form = $this->createForm(ApprentiType::class, $apprenti);

        // Si la requête est en POST (donc que le formulaire à été validé)
        if ($request->isMethod('POST')) {

            //On copie l'email actuel du compte pour déterminer plus loin si l'utilisateur a changer le mail du compte
            $old_email = "".$apprenti->getCompte()->getEmail();
            // À partir de maintenant, la variable $compte contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            $email_ok = true;
            //On vérifie s'il n'y a pas déjà un compte lié à cette adresse mail
            if($old_email != $apprenti->getCompte()->getEmail()) {
                $email_exist = $this->getDoctrine()->getRepository(User::class)->findOneByEmail($apprenti->getCompte()->getEmail());
                if($email_exist) {
                    $email_ok = false;
                }
            }

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid() && $email_ok) {

                // On enregistre notre objet $compte dans la base de données,
                $em = $this->getDoctrine()->getManager();

                $em->flush();

                $this->addFlash('success', 'Modifications bien enregistrées.');

                // On redirige vers la même page pour donner la possibilité de modifier d'autres informations
                return $this->redirectToRoute('edition_compte', array('id' => $id));
            }
            if(!$email_ok) {
                $form->get('compte')->get('email')->addError(new FormError('Un compte lié à cet email existe déjà.'));
            }
        }



        return $this->render('compte/edition_compte.html.twig', array('title' => $title,
            'form' => $form->createView(),
        ));
    }

//    /**
//     * Modification de mot de passe
//     *
//     * @Route("/compte/mot_de_passe/modifier/{id}", name="edition_password", requirements={"id"="\d+"})
//     */
//    public function modifier_mot_de_passe(Request $request, $id) {
//
//    }

//    /**
//     * Affichage de la page de profil
//     *
//     * @Route("/profil/{type}/{id}", name="profil", requirements={"type"="(apprenti|cfa|iut)", "id"="\d+"})
//     */
//    public function profil($type, $id) {
//        $title = "Profil";
//        $autorized = true;
//        if($type == "apprenti") {
//            $compte = $this->getDoctrine()->getRepository(Apprenti::class)->find($id);
//        } elseif($type == "cfa") {
//            $compte = $this->getDoctrine()->getRepository(ResponsableCfa::class)->find($id);
//        } elseif($type == "iut" && $autorized) {
//            $compte = $this->getDoctrine()->getRepository(ResponsableIut::class)->find($id);
//        } else {
//            return $this->render('message.html.twig', array(
//                'typeMessage' => "Erreur", 'message' => "Vous n'êtes pas autorisé à accéder à cette page."
//            ));
//        }
//
//        if(!$compte) {
//            return $this->render('message.html.twig', array(
//                'typeMessage' => "Apprenti non trouvé", 'message' => 'Pas d\'apprenti trouvé pour l\'ID ' . $id
//            ));
//        }
//
//        return $this->render('compte/profil.html.twig', array('title' => $title,
//            'form' => $form->createView(),
//        ));
//    }
}