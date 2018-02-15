<?php

namespace App\Form;

use App\Form\CompteType;
use App\Entity\Apprenti;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class ApprentiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('compte', CompteType::class, array('label' => false))
            ->add('telephone',   TelType::class, array(
                'attr' => array('maxlength' => 10)))
            ->add('adresse',   TextType::class)
            ->add('code_postal',   NumberType::class, array(
                'attr' => array('maxlength' => 5)))
            ->add('ville',   TextType::class)
            ->add('enregistrer', SubmitType::class);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Apprenti::class,
        ));
    }

}
?>