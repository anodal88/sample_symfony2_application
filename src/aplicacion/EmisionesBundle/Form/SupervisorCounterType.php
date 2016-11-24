<?php

namespace aplicacion\EmisionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Doctrine\ORM\EntityRepository;

class SupervisorCounterType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
         * Si es para registrar un counter se agrega los campos de contrasenna
         * cualquier otra funcionalidad no
         */
        if($options['intention']== 'registrar')
                {
                    $builder->add('plainPassword', 'repeated', array(
                        'type' => 'password',
                        'options' => array('translation_domain' => 'FOSUserBundle'),
                        'first_options' => array('label'=>'Clave ','attr'=>array('style'=>'','class'=>'form-control input-sm','placeholder'=>'form.password')),
                        'second_options' => array('label'=>'Confirmar Clave ','attr'=>array('style'=>'margin-top:15px;','class'=>'form-control input-sm','placeholder'=>'form.password_confirmation')),
                        'invalid_message' => 'fos_user.password.mismatch',
                    ));
                }
          /***************************************************************************************/      
              
        
        $builder
            ->add('foto','file',array('data_class'=>null,'required'=>false,'translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'small','placeholder'=>'form.picture','style'=>'visibility:hidden;position:absolute;top:0;left:0',
                'onchange'=>'document.getElementById("info").innerHTML=this.value'
                )))
            ->add('email','email',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'form.email')))
            ->add('username','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'form.username')))
            ->add('nombre','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'registration.user.name')))
            ->add('apellidos','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'registration.user.apellidos')))
            ->add('ci','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'registration.user.ci')))
            ->add('sexo', 'choice', array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm'),
                    'choices'   => array(
                        'M'   => 'registration.user.gender.male',
                        'F' => 'registration.user.gender.female'
                    )
                ))
            ->add('inicioJornada','time',array('widget' => 'single_text','translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm',)))
            ->add('finJornada','time',array('widget' => 'single_text','translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm')))
            ->add('inicioAlmuerzo','time',array('widget' => 'single_text','translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm ')))
            ->add('finAlmuerzo','time',array('widget' => 'single_text','translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm ')))
            ->add('celular','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'registration.user.movil')))
                
            ->add('telefono','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'registration.user.phone')))
            ->add('ext','text',array('required'=>false,'translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'registration.user.ext')))
            //->add('grupos')
            ->add('jefe', 'entity', array('required' => false,'attr'=>array('class'=>'form-control input-sm','placeholder'=>'registration.user.boss'),
                        'class' => 'EmisionesBundle:Usuariointerno',
                            'translation_domain' => 'FOSUserBundle',
                        'query_builder' => function(EntityRepository $er) use ($options) {
                            return $er->createQueryBuilder('u')
                                ->where('u.empresa = :empresa AND u.locked=:lock')
                                ->setParameter('empresa', $options['attr']['empresa'])
                                ->setParameter('lock', false)
                                ;
                        },
                    ))
//           ->add('empresa', 'entity', array('required' => true,'attr'=>array('class'=>'form-control input-sm','placeholder'=>'registration.user.enterprise'),
//                        'class' => 'BaseBundle:Empresa',
//                            'translation_domain' => 'FOSUserBundle'))
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
