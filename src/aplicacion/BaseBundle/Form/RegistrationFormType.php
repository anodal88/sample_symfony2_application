<?php

namespace aplicacion\BaseBundle\Form;
use \Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
      
        $builder->add('nombre');
       
        $builder->add('apellidos');
        $builder->add('ci');       
        $builder->add('sexo', 'choice', array(
            'choices'   => array('m' => 'form.masculino', 'f' => 'form.femenino'),
            'required'  => true,'translation_domain' => 'FOSUserBundle'
        ));
        $builder->add('jefe', 'entity', array('required' => false,
          'class' => 'BaseBundle:User',
            'translation_domain' => 'FOSUserBundle',
          'query_builder' => function(EntityRepository $er) {
              return $er->createQueryBuilder('u')
                  ->orderBy('u.username', 'ASC');
          },
            
      ));


        $builder->add('telefono');
        $builder->add('ext');
//        $builder->add('foto', 'file', array('required' => false));
        
        
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'base_user_registration';
    }
}

