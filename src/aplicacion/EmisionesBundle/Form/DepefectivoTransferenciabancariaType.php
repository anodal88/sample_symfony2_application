<?php

namespace aplicacion\EmisionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DepefectivoTransferenciabancariaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('banco')
            ->add('numeroDocumento')
            ->add('valor')
            ->add('transaccion')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'aplicacion\EmisionesBundle\Entity\DepefectivoTransferenciabancaria'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'aplicacion_emisionesbundle_depefectivotransferenciabancaria';
    }
}
