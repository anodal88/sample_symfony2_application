<?php

namespace aplicacion\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmpresaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ruc','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Ruc')))
            ->add('direccion','textarea',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Direccion')))
            ->add('telefono','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Telefono')))
            ->add('razonsocial','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Razon Social')))
            ->add('email','email',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Email')))
            ->add('representante','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Representante')))            
//            ->add('logo','file',array('data_class'=>null,'required'=>false,'translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'small','placeholder'=>'form.picture','style'=>'/*visibility:hidden;position:absolute;top:0;left:0*/',
//                
//                'onchange'=>'/*document.getElementById("info").innerHTML=this.value*/'
//                )))
             ->add('logo','file',array('data_class'=>null,'required'=>false,'translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'small','placeholder'=>'form.picture','style'=>'visibility:hidden;position:absolute;top:0;left:0',
                'onchange'=>'document.getElementById("info").innerHTML=this.value;'
                )))
            //->add('ciudad')
            ->add('ciudad', 'entity', array('required' => true,'attr'=>array('class'=>'form-control input-sm','placeholder'=>'Seleccione Ciudad'),
                            'class' => 'EmisionesBundle:Ciudad',
                            'translation_domain' => 'FOSUserBundle',
                            'property'=>'nombre'
                    ))
            //->add('matriz')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'aplicacion\BaseBundle\Entity\Empresa'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'aplicacion_basebundle_empresa';
    }
}
