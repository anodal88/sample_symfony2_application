<?php

namespace aplicacion\BaseBundle\Form;
use \Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends AbstractType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       

        $builder->add('nombre','text',array('label' => 'form.nombre', 'translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'form.nombre')));
        $builder->add('apellidos','text',array('label' => 'form.nombre', 'translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'form.nombre')));
        $builder->add('ci','text',array('label' => 'form.nombre', 'translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'form.nombre')));       
        $builder->add('sexo', 'choice', array(
            'choices'   => array('m' => 'form.masculino', 'f' => 'form.femenino'),
            'required'  => true,'translation_domain' => 'FOSUserBundle',
            'attr'=>array('class'=>'form-control input-sm','placeholder'=>'form.nombre')
        ));
        $builder->add('telefono','text',array('label' => 'form.nombre', 'translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'form.nombre')));
        $builder->add('ext','text',array('label' => 'form.nombre', 'translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'form.nombre')));
//        $builder->add('foto', 'file', array('required' => false));
        $builder->add('jefe', 'entity', array('required'=>false,'label' => 'form.jefe','attr'=>array('class'=>'form-control input-sm','placeholder'=>'form.jefe'),
          'class' => 'BaseBundle:User',
            'translation_domain' => 'FOSUserBundle',
          'query_builder' => function(EntityRepository $er) {
              return $er->createQueryBuilder('u')
                  ->orderBy('u.username', 'ASC');
          },
            'empty_value' => 'form.jefe',
            'empty_data'  => -1
      ));
        
    }

    public function getParent()
    {
        return 'fos_user_profile';
    }

    public function getName()
    {
        return 'base_user_profile';
    }
    public function setUserId($id)
    {
        $this->userid=$id;
    }
    public function getUserId()
    {
        return $this->userid;
    }
}

