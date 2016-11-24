<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CronTasksRunCommand
 *
 * @author soporte
 */
namespace aplicacion\EmisionesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\StringInput;
use aplicacion\EmisionesBundle\Entity\Anulacion;
use aplicacion\EmisionesBundle\Entity\Usuariointerno;

class AssignByTimeQueueCommand extends ContainerAwareCommand
{
    private $output;

    protected function configure()
    {
        $this
            ->setName('assign:bytimequeue')
            ->setDescription('Asigna las ordenes recibidas a los counters de acuerdo al
                tiempo que demorara el counter en procesar las ordenes pendientes de su cola,
                siempre asignado las ordenes a los counters que menos tiempo les lleve procesar
                su cola de solicitudes.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        //$output->writeln('<comment>Asignando ordenes a counters...</comment>');
        $this->output = $output;
        //Obteniendo el entity manager
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        //Obtener todas las empresas
        $empresas=$em->getRepository('BaseBundle:Empresa')->findAll();
        foreach ($empresas as $empresa) {
            //Preguntar si la empresa tiene alguna configuracion activa
            if($empresa->getConfiguracionActiva())
            {
                //Obtener la configuracion activa para setearle el ultimo counter que recibio ordenes
                $configuracionActiva=$empresa->getConfiguracionActiva();
                //obtener los counters de la empresa en cuestion sin organizar               
                $qb = $em->createQueryBuilder();
                $qb->select('u')->from("EmisionesBundle:Usuariointerno", 'u')->where($qb->expr()->like('u.roles', $qb->expr()->literal("%ROLE_COUNTER%")))
                    ->andWhere('u.enabled = :estado')
                    ->andWhere('u.empresa = :empresa')
                    ->setParameter('empresa', $empresa)
                    ->setParameter('estado', true);
                $counters=$qb->getQuery()->getResult();
                $ordenes=$em->getRepository('EmisionesBundle:Orden')->findBy(array('horaAsignacion'=>null,'usuario'=>null,'empresa'=>$empresa,'estado'=>2),array('prioridad' => 'DESC'));
                //Aqui empieza el algoritmo de asignacion
                $o=0;
                while($o<count($ordenes) && count($counters)>0)
                {
                    $orden=$ordenes[$o];
                    if($orden instanceof Anulacion)
                    {   //Darle la orden al counter que la emitio
                        $counterEmisor=$this->getCounterEmisor($orden);
                        if($counterEmisor instanceof Usuariointerno)
                        {
                            if(is_null( $orden->getUsuario()) && is_null($orden->getHoraAsignacion()))
                            {
                                $counterEmisor->addOrdene($orden);
                                $orden->setHoraAsignacion(new \DateTime());
                                $configuracionActiva->setLastCounter($counterEmisor);
                                $em->persist($orden);
                            }
                        }
                        else
                        {//Sino esta disponible le doy al que le toca por menor tiempo de cola
                            $counter=$this->getCurrentCounter($counters, $orden);
                            if($counter instanceof Usuariointerno)
                            {
                                if(is_null( $orden->getUsuario()) && is_null($orden->getHoraAsignacion()))
                                {
                                    $counter->addOrdene($orden);
                                    $orden->setHoraAsignacion(new \DateTime());
                                    $configuracionActiva->setLastCounter($counter);
                                    $em->persist($orden);
                                }
                            }
                        }
                    }
                    else
                    {
                        $counter=$this->getCurrentCounter($counters, $orden);
                        if($counter instanceof Usuariointerno)
                        {
                            if(is_null( $orden->getUsuario()) && is_null($orden->getHoraAsignacion()))
                            {
                                $counter->addOrdene($orden);
                                $orden->setHoraAsignacion(new \DateTime());
                                $configuracionActiva->setLastCounter($counter);
                                $em->persist($orden);
                            }
                        }
                    }
                    $o++;
                }

            }
        }
        $em->persist($configuracionActiva);
        $em->flush();
    }
    /*
     * Funcion que me da el counter con menos tiempo de cola
     * y que esta en un horario disponible para procesar ordenes
     */
    public function getCurrentCounter($counters,$orden)
    {
        usort ($counters, function (Usuariointerno $a,Usuariointerno $b) {
            if ($a->getTimeOfQueue() == $b->getTimeOfQueue()) {
                return 0;
            }
            return ($a->getTimeOfQueue() < $b->getTimeOfQueue()) ? -1 : 1;
        });
        for ($index = 0; $index < count($counters); $index++) {
            $counter=$counters[$index];

            if((new \DateTime($orden->getFecha()->format('H:i:s'))<= new \DateTime($counter->getInicioJornada()->format('H:i:s'))) && ($counter->getId()!= 701))
                {

                    if(
                        ((new \DateTime()<= new \DateTime($counter->getInicioAlmuerzo()->format('H:i:s'))) && (new \DateTime()>= new \DateTime($counter->getInicioJornada()->format('H:i:s'))))
                            ||
                        ((new \DateTime()<= new \DateTime($counter->getFinJornada()->format('H:i:s'))) && (new \DateTime()>= new \DateTime($counter->getFinAlmuerzo()->format('H:i:s'))))
                      )
                        {//Horario de la mannana u Horario de la Tarde                       
                            return $counter;
                        }
                }
                if((new \DateTime($orden->getFecha()->format('H:i:s')) <= new \DateTime($counter->getInicioAlmuerzo()->format('H:i:s'))) && (new \DateTime($orden->getFecha()->format('H:i:s')) > new \DateTime($counter->getInicioJornada()->format('H:i:s'))))
                {
                    return $counter;
                }
                if((new \DateTime($orden->getFecha()->format('H:i:s')) >= new \DateTime($counter->getFinAlmuerzo()->format('H:i:s')))&& (new \DateTime($orden->getFecha()->format('H:i:s')) <= new \DateTime($counter->getFinJornada()->format('H:i:s'))))
                {
                    return $counter;
                }
            }
        return null;
    }
    /*
     * Funcion que se invoca cuando la orden a asignar es una anulacion
     * y devuelve el agente que la proceso si el tiempo de cola del agente mas el 
     * tiempo de procesar la orden es valido
     */
    public function getCounterEmisor($orden)
    {
        //$this->output->writeln('<comment>Entro anulacion!</comment>');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $emision=$em->getRepository('EmisionesBundle:Emision')->findOneBy(array('numeroOrden'=>$orden->getTarjet()));
        if(!$emision)
        {
            return null;
        }
        $counter=$emision->getUsuario();
        if((new \DateTime($orden->getFecha()->format('H:i:s'))<= new \DateTime($counter->getInicioJornada()->format('H:i:s'))) && ($counter->getId()!= 701))
                {
                    if(
                        ((new \DateTime()<= new \DateTime($counter->getInicioAlmuerzo()->format('H:i:s'))) && (new \DateTime()>= new \DateTime($counter->getInicioJornada()->format('H:i:s'))))
                            ||
                        ((new \DateTime()<= new \DateTime($counter->getFinJornada()->format('H:i:s'))) && (new \DateTime()>= new \DateTime($counter->getFinAlmuerzo()->format('H:i:s'))))
                      )
                        {//Horario de la mannana u Horario de la Tarde
                            return $counter;
                        }
                }
                if((new \DateTime($orden->getFecha()->format('H:i:s')) <= new \DateTime($counter->getInicioAlmuerzo()->format('H:i:s'))) && (new \DateTime($orden->getFecha()->format('H:i:s')) > new \DateTime($counter->getInicioJornada()->format('H:i:s'))))
                {
                    return $counter;
                }
                if((new \DateTime($orden->getFecha()->format('H:i:s')) >= new \DateTime($counter->getFinAlmuerzo()->format('H:i:s')))&& (new \DateTime($orden->getFecha()->format('H:i:s')) <= new \DateTime($counter->getFinJornada()->format('H:i:s'))))
                {
                    return $counter;
                }         
         //Si llego aqui fue porque existe la emision pero el counter no esta habilitado
         return null;
         }
}

