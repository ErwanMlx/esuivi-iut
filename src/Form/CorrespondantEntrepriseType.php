<?php
namespace App\Form;

use App\Entity\CorrespondantEntreprise;
use App\Entity\Entreprise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CorrespondantEntrepriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom')
            ->add('prenom')
            ->add('fonction')
            ->add('telephone', TelType::class)
            ->add('email', EmailType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CorrespondantEntreprise::class,
//            'validation_groups' => array('ajout'),
        ));
    }
}
?>