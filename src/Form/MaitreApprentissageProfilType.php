<?php

namespace App\Form;

use App\Form\CompteType;
use App\Entity\User;
use App\Entity\MaitreApprentissage;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class MaitreApprentissageProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('compte', CompteType::class, array('label' => false))
            ->add('fonction', TextType::class)
            ->add('telephone', TelType::class)
            ->add('fax', TextType::class, array('required' => false))
            ->add('enregistrer', SubmitType::class);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => MaitreApprentissage::class,
        ));
    }

}
?>