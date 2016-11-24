<?php

namespace aplicacion\EmisionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use aplicacion\BaseBundle\Entity\Configuracion;
use \Doctrine\ORM\EntityRepository;
class SupervisorConfiguracionType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
                
        $builder
            ->add('nombre','text',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Nombre')))
            ->add('descripcion','textarea',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Resumen')))
            ->add('inicioHorarioAtencion','time',array('widget' => 'single_text','translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Inicio Horario Atencion')))
            ->add('finHorarioAtencion','time',array('widget' => 'single_text','translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Inicio Horario Atencion')))
            //->add('tiempoAsignacion','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Tiempo de Asignacion')))
            ->add('tiempoRespuestaPlanPiloto','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'SLA Plan Piloto')))
            ->add('tiempoRespuestaNormal','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'SLA ')))
            ->add('emailViaticos','email',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Email Viaticos')))
            ->add('emailVacaciones','email',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Email Vacaciones')))
            ->add('activa','checkbox',array('required'=>false))
            ->add('tiempoAnulacion','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Tiempo Anulacion ')))
            ->add('tiempoEmision','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Tiempo Emision ')))
            ->add('tiempoRevision','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Tiempo Revision')))
            ->add('tiempoFomaPagoCash','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Tiempo Forma Pago Cash')))
            ->add('tiempoFomaPagoPlanPiloto','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Tiempo Forma Pago Plan Piloto')))
            ->add('tiempoFomaPagoVtc','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Tiempo Forma Pago VTC ')))
            ->add('tiempoLocal','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Tiempo IATA Local ')))
            ->add('tiempoRemota','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Tiempo IATA Remoto ')))
            ->add('tiempoPorPasajero','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Tiempo x Pasajero ')))
            ->add('ponderacionPlanPiloto','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Ponderacion Plan Piloto ')))
            ->add('ponderacionNoPlanPiloto','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Ponderacion Ordenes Normales ')))
            ->add('ponderacionEmision','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Ponderacion Emision ')))
            ->add('ponderacionRevision','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Ponderacion Revision ')))
            ->add('ponderacionAnulacion','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Ponderacion Anulacion ')))
            //->add('lastCounter')
            ->add('lastCounter', 'entity', array('required' => false,'attr'=>array('class'=>'form-control input-sm','placeholder'=>'registration.user.boss'),
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
          ->add('ponderacionSVI','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Ponderacion SVI ')))
          ->add('ponderacionNOSVI','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Ponderacion NO SVI ')))                      
          ->add('tiempoPrimeraAlerta','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Tiempo Primera Alerta')))  
          ->add('tiempoSegundaAlerta','integer',array('translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Tiempo Segunda Alerta'))) 
          ->add('feeEmergencia','money',array('currency'=>'USD','translation_domain' => 'FOSUserBundle','attr'=>array('class'=>'form-control input-sm','placeholder'=>'Fee Emergencia')))                                                                   
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
