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
use \aplicacion\EmisionesBundle\Entity\Anulacion;
/*
 * Comando para Habilitar / Desahabilitar los counters de acuerdo a sus horarios
 */

class EnableUnableCountersCommand extends ContainerAwareCommand
{
    private $output;

    protected function configure()
    {
        $this
            ->setName('check:time')
            ->setDescription('Habilita/Deshabilita los counters de acuerdo a sus horarios definidos.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$output->writeln('<comment>Chequeando la Horarios Vs Hora Actual...</comment>');
       // $this->output = $output;
        //Obteniendo el entity manager
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        //Obtener todas las empresas
        $empresas=$em->getRepository('BaseBundle:Empresa')->findAll();
        $counters=array();
        foreach ($empresas as $empresa) {
            //Adicionar los counetrs habilitados de cada empresa al global
            // $output->writeln('<comment>'.count($empresa->getEnabledCounters()).'</comment>');
            $counters = array_merge($counters, $empresa->getUnlockedCounters());
        }
        //$output->writeln('<comment>'.count($counters).'</comment>');
        //Un ciclo para habilitar o deshabilitar los counters de acuerdo a su tiempo
        foreach ($counters as $c) {
            
                if((time() < $c->getInicioAlmuerzo()->format('U')) && (time()>=$c->getInicioJornada()->format('U')))
                {
                    //Horario de la mannana
                    if(!$c->isEnabled())
                    {
                        $c->setEnabled(true);
                    }

                }
               elseif((time()>=$c->getFinAlmuerzo()->format('U'))&& (time()< $c->getFinJornada()->format('U')))
                {
                    //Horario de la Tarde
                    if(!$c->isEnabled())
                    {
                        $c->setEnabled(true);
                    }
                }
                else
                {
                    //Fuera de horario laboral
                    if($c->isEnabled())
                    {
                        $c->setEnabled(false);
                    }
                }
            
            $em->persist($c);
        }
        
        // Flush database changes
        $em->flush();
        //$output->writeln('<comment>Terminado!</comment>');
    }
   
    
}


