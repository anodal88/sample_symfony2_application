<?php

namespace aplicacion\EmisionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TarjetacreditoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('emisorVtc')
            ->add('aerolinea')
            ->add('bancoEmisorTarjeta')
            ->add('tipoTarjeta')
            ->add('numeroTarjeta')
            ->add('propietario')
            ->add('vence')
            ->add('pin')
            ->add('tipoPago')
            ->add('plazo')
            ->add('tipoAutorizacion')
            ->add('numeroAutorizacion')
            ->add('valorTarjeta')
            ->add('interesTarjeta')
            ->add('valorTotal')
            ->add('pagoPasajeros')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'aplicacion\EmisionesBundle\Entity\Tarjetacredito'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'aplicacion_emisionesbundle_tarjetacredito';
    }
}
