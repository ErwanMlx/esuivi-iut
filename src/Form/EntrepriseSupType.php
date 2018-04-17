<?php

namespace App\Form;

use App\Form\CorrespondantEntrepriseType;
use App\Entity\Entreprise;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class EntrepriseSupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom',   TextType::class)
            ->add('raisonsociale',   TextType::class, array(
                'label' => "Raison sociale"))
            ->add('siret',   NumberType::class, array(
                'label' => "SIRET"))
            ->add('nombresalaries',   NumberType::class, array(
                'label' => "Nombre de salariés"))
            ->add('adresse',   TextType::class)
            ->add('code_postal',   NumberType::class, array(
                'attr' => array('maxlength' => 5)))
            ->add('ville',   TextType::class)
            ->add('telephone',   TelType::class, array(
                'attr' => array('maxlength' => 10)))
            ->add('fax',   TelType::class, array(
                'required'   => false))
            ->add('email',   EmailType::class)
            ->add('domaineactivite',   TextType::class, array(
                'label' => "Domaine d'activité"))
            ->add('CorrespondantEntreprise', CorrespondantEntrepriseType::class)
            ->add('enregistrer', SubmitType::class);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Entreprise::class,
        ));
    }

}
?>