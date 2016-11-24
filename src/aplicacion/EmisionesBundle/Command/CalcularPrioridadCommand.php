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

class CalcularPrioridadCommand extends ContainerAwareCommand
{
    private $output;

    protected function configure()
    {
        $this
            ->setName('calc:priority')
            ->setDescription('Calcula las prioridades, de todas las ordenes cada un intervalo de tiempo.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Obteniendo el entity manager
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        //Obtener todas las empresas
        $empresas=$em->getRepository('BaseBundle:Empresa')->findAll();
        foreach ($empresas as $empresa) {
            if($empresa->getConfiguracionActiva())
            {
              //Obtener todas las ordenes de la empresa ordenada por prioridad
              $ordenes_tmp=$em->getRepository('EmisionesBundle:Orden')->findBy(array('empresa'=>$empresa,'estado'=>2),array('prioridad' => 'DESC'));
                //$ordenes_tmp = $em->getRepository('EmisionesBundle:Orden')->getBySortableGroupsQuery(array('empresa' =>$empresa))->getResult();              
               foreach ($ordenes_tmp as $o) {
                  //Si esta en su tiempo limite
                    if($o->isLimitHour() || $o->isOutOfTimeAlert())
                    {//Ordenes que estan en su time limit
                       $o->setPrioridad($ordenes_tmp[0]->getPrioridad()+1);
                       $em->persist($o);
                    }
                }
            }
        }
        // Flush database changes
        $em->flush();
    }
}


