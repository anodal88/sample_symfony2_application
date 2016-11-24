<?php

namespace aplicacion\EmisionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class SupervisorCounterEditClaveType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('plainPassword','password',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Nueva Clave:')))
                
           /* ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label'=>' ','attr'=>array('style'=>'margin-top:-20px;','class'=>'form-control input-sm','placeholder'=>'form.password')),
                'second_options' => array('label'=>' ','attr'=>array('style'=>'margin-top:20px;','class'=>'form-control input-sm','placeholder'=>'form.password_confirmation')),
                'invalid_message' => 'fos_user.password.mismatch',
            ))*/
           
        ;
       
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'aplicacion\EmisionesBundle\Entity\Usuariointerno'
        ));
    }
//    public function getParent()
//    {
//        return 'fos_user_registration';
//    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'aplicacion_emisionesbundle_usuariointerno';
    }
}
