<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Apprenti;
use App\Entity\DossierApprenti;
use App\Entity\ResponsableCfa;
use App\Entity\ResponsableIut;
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
     * @Route("/ajout_compte/{type}", name="ajout_compte", requirements={"type"="(apprenti|cfa|iut)"})
     */
    public function ajout_compte(Request $request, $type)
    {
        //!!! A modif avec gestion de compte pour vérif si un iut a le droit d'add un collègue
        $autorized = true;

        if($type == "apprenti") {
            $title = "apprenti";
            $compte = new Apprenti();
        } elseif($type == "cfa") {
            $title = "CFA";
            $compte = new ResponsableCfa();
        } elseif($type == "iut" && $autorized) {
            $title = "IUT";
            $compte = new ResponsableIut();
        } else {
            return $this->render('message.html.twig', array(
                'typeMessage' => "Erreur", 'message' => "Vous n'êtes pas autorisé à accéder à cette page."
            ));
        }

        $form = $this->createFormBuilder($compte, array(
            'validation_groups' => array('ajout'),))
            ->add('nom',      TextType::class)
            ->add('prenom',     TextType::class)
            ->add('email',   EmailType::class)

        ;

//        if($type == "iut") {
//            $form = $form->add('administrateur',   CheckboxType::class, array('required' => false));
//        }

        $form = $form->add('ajouter', SubmitType::class)->getForm();

        // Si la requête est en POST (donc que le formulaire à été validé)
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire

            // À partir de maintenant, la variable $compte contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            //On vérifie s'il n'y a pas déjà un compte lié à cette adresse mail
            $email_exist = $this->getDoctrine()->getRepository(Compte::class)->findByEmail($compte->getEmail());
            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid() && !$email_exist) {
                //On génère le mot de passe
                $compte->setPassword(base64_encode(random_bytes(10)));

                if($type == "apprenti" || $type == "cfa") {
                    //!!! Provisoire mais a remplacer par l'id de l'user connecté
                    $user = $this->getDoctrine()->getRepository(ResponsableIut::class)->find(1);
                    $compte->setResponsableIut($user);
                }

                // On enregistre notre objet $compte dans la base de données,
                $em = $this->getDoctrine()->getManager();

                //On créer et rattache un dossier à l'apprenti
                if($type == "apprenti") {
                    $dossier = new DossierApprenti();
                    $compte->setDossierApprenti($dossier);
                    $dossier->setEtat("En cours");
                    $em->persist($dossier);
                }

                $em->persist($compte);
                $em->flush();

                $this->addFlash('success', 'Compte bien enregistré.');

                // On redirige vers la même page pour donner la possibilité d'ajouter d'autres comptes
                return $this->redirectToRoute('ajout_compte', array('type' => $type));
            }
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
     * Suivi de apprenti corrspondant à l'id
     *
     * @Route("/edition_compte/{id}", name="edition_compte", requirements={"id"="\d+"})
     */
    public function edition_compte(Request $request, $id) {
        $title = "Edition du compte";
        $compte = $this->getDoctrine()->getRepository(Apprenti::class)->find($id);

        //!!! Il faut vérifier que l'utilisateur connecté est un iut ou que le compte a modifier est bien celui de l'apprenti connecté
        $autorized = true;
        if(!$autorized) {
            return $this->render('message.html.twig', array(
                'typeMessage' => "Erreur", 'message' => "Vous n'êtes pas autorisé à accéder à cette page."
            ));
        }

        if(!$compte) {
            return $this->render('message.html.twig', array(
                'typeMessage' => "Apprenti non trouvé", 'message' => 'Pas d\'apprenti trouvé pour l\'ID ' . $id
            ));
        }

        $form = $this->createFormBuilder($compte)
            ->add('nom',      TextType::class)
            ->add('prenom',     TextType::class)
            ->add('email',   EmailType::class)
            ->add('telephone',   TelType::class, array(
                'attr' => array('maxlength' => 10)))
            ->add('adresse',   TextType::class)
            ->add('code_postal',   NumberType::class, array(
                'attr' => array('maxlength' => 5)))
            ->add('ville',   TextType::class)
            ->add('enregistrer', SubmitType::class)
            ->getForm();

        // Si la requête est en POST (donc que le formulaire à été validé)
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire

            $old_email = "".$compte->getEmail();
            // À partir de maintenant, la variable $compte contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            $email_ok = true;
//            select c.email, a.id from compte c, apprenti a where a.email = c.email AND a.email = 'jakass@gmail.co.uk';
            //On vérifie s'il n'y a pas déjà un compte lié à cette adresse mail
            if($old_email != $compte->getEmail()) {
                $email_exist = $this->getDoctrine()->getRepository(Compte::class)->findOneByEmail($compte->getEmail());
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

                // On redirige vers la même page pour donner la possibilité d'ajouter d'autres comptes
                return $this->redirectToRoute('edition_compte', array('id' => $id));
            }
            if(!$email_ok) {
                $form->get('email')->addError(new FormError('Un compte lié à cet email existe déjà.'));
            }
        }

        return $this->render('compte/edition_compte.html.twig', array('title' => $title,
            'form' => $form->createView(),
        ));
    }
}