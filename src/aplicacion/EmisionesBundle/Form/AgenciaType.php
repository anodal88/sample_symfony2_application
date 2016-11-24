<?php

namespace aplicacion\EmisionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AgenciaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre','text',array('attr'=>array('class'=>'form-control input-sm','placeholder'=>'Nombre de agencia o Razon Social')))
            ->add('ruc','text',array('attr'=>array('class'=>'form-control input-sm','placeholder'=>'Ruc')))
            ->add('direccion','textarea',array('attr'=>array('class'=>'form-control input-sm','placeholder'=>'Direccion de la agencia...')))
            ->add('telefono','text',array('attr'=>array('class'=>'form-control input-sm','placeholder'=>'Telefono de la agencia')))
            ->add('ciudad','entity',array('class'=>'EmisionesBundle:Ciudad','attr'=>array('class'=>'form-control input-sm','placeholder'=>'')))
            ->add('email','email',array('attr'=>array('class'=>'form-control input-sm','placeholder'=>'Correo electronico de la agencia')))
            ->add('logo','file',array('data_class'=>null,'required'=>false,'translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'small','placeholder'=>'form.picture','style'=>'visibility:hidden;position:absolute;top:0;left:0',
                'onchange'=>'document.getElementById("info").innerHTML=this.value;'
                )))
            ->add('aerolineasPlanPiloto', 'entity', array('required' => false,'multiple'=>true,'attr'=>array('class'=>'form-control input-sm','placeholder'=>'Seleccione Aerolineas','style'=>'height:150px;'),
                        'class' => 'EmisionesBundle:Aerolinea',
                            'translation_domain' => 'FOSUserBundle'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'aplicacion\EmisionesBundle\Entity\Agencia'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'aplicacion_emisionesbundle_agencia';
    }
}
