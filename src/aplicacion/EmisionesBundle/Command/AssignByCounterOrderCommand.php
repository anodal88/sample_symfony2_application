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

class AssignByCounterOrderCommand extends ContainerAwareCommand
{
    private $output;

    protected function configure()
    {
        $this
            ->setName('assign:byorder')
            ->setDescription('Asigna las ordenes recibidas a los counters en el mismo orden en 
                                que se encuentran los counetrs, siempre asignado la primera orden 
                                pendiente al siguente counter del ultimo que se le asigno una orden.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       // $output->writeln('<comment>Asignando ordenes a counters...</comment>');
        //$this->output = $output;
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
                //obtener los counters de la empresa en cuestion
                $counters = $empresa->getEnabledCounters();
                
                //Obteniendo las ordenes sin asignar de la empresa en cuestion
                //$ordenes=$em->getRepository('EmisionesBundle:Orden')->findBy(array('empresa' => $empresa,'usuario' => null));
                $ordenes_tmp = $em->getRepository('EmisionesBundle:Orden')->getBySortableGroupsQuery(array('empresa' =>$empresa))->getResult();
                $ordenes=array();//arreglo de ordenes ordenadas mayor a menor por la prioridad
                for ($x = count($ordenes_tmp)-1; $x >= 0; $x--) {
                    if(!$ordenes_tmp[$x]->getUsuario() && $ordenes_tmp[$x]->getEstado()->getNombre()=='Pendiente')
                    {
                       $ordenes[]= $ordenes_tmp[$x];
                       //$output->writeln('<comment>'.$ordenes_tmp[$x]->getPrioridad().'</comment>');
                    }
                }
                 $o=0;//indexOrdenes
                 while (($o<count($ordenes)) && ($this->getCurrentCounter($counters, $ordenes[$o])!=null))  
                    { 
                        if($ordenes[$o] instanceof Anulacion)
                            {//Darle la orden al counter que la emitio
                                //$output->writeln('<comment>Es anulacion!</comment>'); 
                                if($this->getCounterEmisor($ordenes[$o])!= null)
                                {//Si el counter esta disponible
                                   //$output->writeln('<comment>Encontro al counter!</comment>');
                                   $counter=$this->getCounterEmisor($ordenes[$o]);
                                }
                                else
                                {//Sino esta disponible le doy al que le toca por menor tiempo de cola
                                   $counter=$this->getCurrentCounter($counters, $ordenes[$o]); 
                                }
                               
                            }
                            else
                            {
                              //Sino es anulacion le doy al que le toca por tiempo de cola
                               $counter=$this->getCurrentCounter($counters, $ordenes[$o]); 
                            }
                            
                            $counter->addOrdene($ordenes[$o]);
                            if(!$ordenes[$o] instanceof Anulacion)
                            {//solo si no es anulacion para no arterar el orden
                                $configuracionActiva->setLastCounter($counter);
                            }
                            //Sino se ha asignado por primera vez
                            if(!$ordenes[$o]->getHoraAsignacion())
                            {
                                $ordenes[$o]->setHoraAsignacion(new \DateTime());
                                $em->persist($ordenes[$o]);
                            }                            
                            $em->persist($counter);
                            $o++;
                    }
            }
        }
       
        // Flush database changes
        $em->flush();
        //$output->writeln('<comment>Terminado!</comment>');
    }
     /*
     * Funcion que me da el counter en que se quedo
     * repartiendo ordenes
     */
    public function getCurrentCounter($counters,$orden)
    {
       
                if(!$orden->getEmpresa()->getConfiguracionActiva()->getLastCounter())
                {
                  
                  for ($i = 0; $i < count($counters); $i++) {
                      if((time() < $counters[$i]->getInicioAlmuerzo()->format('U')) && (time()>=$counters[$i]->getInicioJornada()->format('U')))
                            {
                                //Horario de la mannana
                                if((time()+$counters[$i]->getTimeOfQueue()+$orden->timeOfProcess()<= $counters[$i]->getInicioAlmuerzo()->format('U')))
                                {
                                    return $counters[$i];
                                }
                            }
                       if((time()>=$counters[$i]->getFinAlmuerzo()->format('U'))&& (time()< $counters[$i]->getFinJornada()->format('U')))
                            {
                                //Horario de la Tarde
                                
                                if((time()+$counters[$i]->getTimeOfQueue()+$orden->timeOfProcess() <= $counters[$i]->getFinJornada()->format('U')))
                                {
                                    return $counters[$i];

                                }
                            }
                  }
                  return null;
                 
                }
                else
                {
                    //Organizar la lista de counters de acuerdo al counter que se quedo por recibir
                    $indexLastCounter=  array_search($orden->getEmpresa()->getConfiguracionActiva()->getLastCounter(), $counters);                    
                    for ($j = $indexLastCounter+1; $j < count($counters); $j++) {
                        if((time() < $counters[$j]->getInicioAlmuerzo()->format('U')) && (time()>=$counters[$j]->getInicioJornada()->format('U')))
                            {
                                //Horario de la mannana
                                if((time()+$counters[$j]->getTimeOfQueue()+$orden->timeOfProcess()<= $counters[$j]->getInicioAlmuerzo()->format('U')))
                                {
                                    return $counters[$j];
                                }
                            }
                        if((time()>=$counters[$j]->getFinAlmuerzo()->format('U'))&& (time()< $counters[$j]->getFinJornada()->format('U')))
                            {
                            //$this->output->writeln('<comment>'.$counters[$j]->getId().'-'. $counters[$j]->getTimeOfQueue().'+'.$orden->timeOfProcess().'<='.$counters[$j]->getFinJornada()->format('U').'</comment>');
                                //Horario de la Tarde
                                if((time()+$counters[$j]->getTimeOfQueue()+$orden->timeOfProcess() <= $counters[$j]->getFinJornada()->format('U')))
                                {
                                    return $counters[$j];

                                }
                            }
                    }
                    
                    for ($k = 0; $k <= $indexLastCounter; $k++) {
                        if((time() < $counters[$k]->getInicioAlmuerzo()->format('U')) && (time()>=$counters[$k]->getInicioJornada()->format('U')))
                            {
                                //Horario de la mannana
                                if((time()+$counters[$k]->getTimeOfQueue()+$orden->timeOfProcess()<= $counters[$k]->getInicioAlmuerzo()->format('U')))
                                {
                                    return $counters[$k];
                                }
                            }
                            if((time()>=$counters[$k]->getFinAlmuerzo()->format('U'))&& (time()< $counters[$k]->getFinJornada()->format('U')))
                            {
                                //Horario de la Tarde
                                if((time()+$counters[$k]->getTimeOfQueue()+$orden->timeOfProcess() <= $counters[$k]->getFinJornada()->format('U')))
                                {
                                    return $counters[$k];

                                }
                            }
                    }
                    return null;
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
        
         $em = $this->getContainer()->get('doctrine.orm.entity_manager');
         $emision=$em->getRepository('EmisionesBundle:Emision')->findOneBy(array('numeroOrden'=>$orden->getTarjet()));
         
        
         if(!$emision)
         {
             //Laorden no fue emitida en MYM
             // $this->output->writeln('<comment> No hay la emision por ende no hay counter asociado</comment>');
             return null;
         }
         else if(($emision->getUsuario()) && ($emision->getUsuario()->isEnabled()))
         {
             $counter=$emision->getUsuario();
             //$this->output->writeln('<comment> Existe el counter y esta habilitado</comment>');
             if((time() < $counter->getInicioAlmuerzo()->format('U')) && (time()>=$counter->getInicioJornada()->format('U')))
                {
                    //Horario de la mannana
                    if((time()+$counter->getTimeOfQueue()+$orden->timeOfProcess()<= $counter->getInicioAlmuerzo()->format('U')))
                    {
                        return $counter;
                    }
                }
           if((time()>=$counter->getFinAlmuerzo()->format('U'))&& (time()< $counter->getFinJornada()->format('U')))
                {
                    //Horario de la Tarde
                    if((time()+$counter->getTimeOfQueue()+$orden->timeOfProcess() <= $counter->getFinJornada()->format('U')))
                    {
                        return $counter;

                    }
                }
         }
         //Si llego aqui fue porque existe la emision pero el counter no esta habilitado
         return null;
    }

}


