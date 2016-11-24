<?php

namespace aplicacion\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConfiguracionType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tiempoAsignacion')
            ->add('tiempoRespuestaPlanPiloto')
            ->add('tiempoRespuestaNormal')
            ->add('emailViaticos')
            ->add('emailVacaciones')
            ->add('activa')
            ->add('tiempoAnulacion')
            ->add('tiempoEmision')
            ->add('tiempoRevision')
            ->add('tiempoFomaPagoCash')
            ->add('tiempoFomaPagoPlanPiloto')
            ->add('tiempoFomaPagoVtc')
            ->add('tiempoLocal')
            ->add('tiempoRemota')
            ->add('tiempoPorPasajero')
            ->add('ponderacionPlanPiloto')
            ->add('ponderacionNoPlanPiloto')
            ->add('ponderacionEmision')
            ->add('ponderacionRevision')
            ->add('ponderacionAnulacion')
            ->add('lastCounter')
            ->add('empresa')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'aplicacion\BaseBundle\Entity\Configuracion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'aplicacion_basebundle_configuracion';
    }
}
