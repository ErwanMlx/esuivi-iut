<?php

namespace App\Form;

use App\Form\CompteType;
use App\Entity\User;
use App\Entity\MaitreApprentissage;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class MaitreApprentissageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('compte', CompteType::class, array('label' => false, 'required' => false))
            ->add('entreprise', EntrepriseType::class, array('label' => false, 'required' => false))
            ->add('enregistrer', SubmitType::class);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => MaitreApprentissage::class,
            'validation_groups' => false
        ));
    }

}
?>