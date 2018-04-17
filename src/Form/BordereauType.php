<?php
namespace App\Form;

use App\Entity\DossierApprenti;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BordereauType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('dateEmbauche', DateType::class, array(
            'widget' => 'single_text'))
            ->add('sujetPropose', TextType::class, array(
                'label' => 'Sujet Proposé'))
            ->add('descriptionDuSujet', TextareaType::class, array(
                'attr' => array('rows' => '10')))
            ->add('participationFinanciere', MoneyType::class)
            ->add('enregistrer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DossierApprenti::class,
        ));
    }
}
?>