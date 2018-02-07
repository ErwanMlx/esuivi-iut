<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Apprenti;
use App\Entity\DossierApprenti;
use App\Entity\ResponsableCfa;
use App\Entity\ResponsableIut;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; //To define the route to access it
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;


class CompteController extends Controller
{

    /**
     * Suivi de apprenti corrspondant à l'id
     *
     * @Route("/ajout_compte", name="choix_ajout")
     */
    public function choix_ajout() {
        return $this->render('compte/choix_ajout.html.twig');
    }

    /**
     * Suivi de apprenti corrspondant à l'id
     *
     * @Route("/ajout_compte/{type}", name="ajout_compte", requirements={"type"="(apprenti|cfa)"})
     */
    public function ajout_compte(Request $request, $type)
    {
        if($type == "apprenti") {
            $title = "apprenti";
            $compte = new Apprenti();
        } else {
            $title = "CFA";
            $compte = new ResponsableCfa();
        }

        $form = $this->createFormBuilder($compte, array(
            'validation_groups' => array('ajout'),))
            ->add('nom',      TextType::class)
            ->add('prenom',     TextType::class)
            ->add('email',   EmailType::class)
            ->add('ajouter',      SubmitType::class)
            ->getForm()
        ;

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

                //!!! Provisoire mais a remplacer par l'id de l'user connecté
                $user = $this->getDoctrine()->getRepository(ResponsableIut::class)->find(1);
                $compte->setResponsableIut($user);

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
}