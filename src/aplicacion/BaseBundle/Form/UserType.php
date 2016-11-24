<?php

namespace aplicacion\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Doctrine\ORM\EntityRepository;

class UserType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email','email',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control','placeholder'=>'form.email')))
                ->add('username','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control','placeholder'=>'form.username')))
            ->add('nombre','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control','placeholder'=>'registration.user.name')))
            ->add('apellidos','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control','placeholder'=>'registration.user.apellidos')))
            ->add('ci','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control','placeholder'=>'registration.user.ci')))
            ->add('sexo', 'choice', array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control'),
                    'choices'   => array(
                        'M'   => 'registration.user.gender.male',
                        'F' => 'registration.user.gender.female'
                        
                    )
                ))
            
            ->add('telefono','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control','placeholder'=>'registration.user.phone')))
            ->add('ext','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control','placeholder'=>'registration.user.ext')))
            //->add('grupos')
            ->add('jefe', 'entity', array('required' => false,'attr'=>array('class'=>'form-control','placeholder'=>'registration.user.boss'),
                        'class' => 'BaseBundle:User',
                            'translation_domain' => 'FOSUserBundle',
                        'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                                //->where('u.tipo'!= 'agente')
                                ->orderBy('u.username', 'ASC');
                        },

                    ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'aplicacion\BaseBundle\Entity\User'
        ));
    }
    public function getParent()
    {
        return 'fos_user_registration';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'aplicacion_basebundle_user';
    }
}
