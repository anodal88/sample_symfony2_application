<?php

namespace aplicacion\BaseBundle\Controller;

use aplicacion\BaseBundle\Entity\CronTask;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/crontasks")
 */
class CronTaskController extends Controller
{
   /**
     * @Route("/test", name="your_examplebundle_crontasks_test")
     */
    public function testAction()
    {
       
        $entity = new CronTask();

        $entity
            ->setNombre('Chequea el horario de los trabajadores')
            ->setDescripcion('Habilita/Deshabilita los counters de acuerdo a sus horarios definidos.')
            ->setIntervalo(180) // Run once every 5 minutes
            ->setActiva(true)
            ->setAsignacionOrdenes(true)
            ->setEmpresa($this->getUser()->getEmpresa())
            ->setComandos(array(
                'check:time'
            ));

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new Response('OK!');
    }
  
    
}
