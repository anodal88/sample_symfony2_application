<?php

namespace aplicacion\EmisionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use aplicacion\EmisionesBundle\Entity\Emision;
use aplicacion\EmisionesBundle\Entity\Revision;
use aplicacion\EmisionesBundle\Entity\Anulacion;
use Symfony\Component\HttpFoundation\Response;

class EmisionesController extends Controller
{
   public function indexAction()
   {
       $em=  $this->getDoctrine()->getManager();
       $peticion = $this->getRequest();
       $fecha = $peticion->request->get('fecha');
       $fs='';
      // print_r($fecha);exit;
          if($fecha != null)
          {              
              $fecha=new \DateTime($fecha);
             // $fs=$fecha;
              $fecha=$fecha->format('Y-m-d');
            
          }
          else
          {
              $fecha=new \DateTime("now");
              //$fs=$fecha;
              $fecha=$fecha->format('Y-m-d');
             
          }
         //buscar cual es el estado pendiente
          //$estado=$em->getRepository('EmisionesBundle:Estadoorden')->findBy(array('nombre'=>'Pendiente'));

          /*
           * Solo las ordenes de la empresa a la que corresponde el counter
           */
          $ordenes= $this->getUser()->getEmpresa()->getOrdenes();
      
          
          // Aplicando que sean del mismo dia
          $result=array();
          for ($i = 0; $i < count($ordenes); $i++) {
             
              if(($ordenes[$i]->getFecha()->format('Y-m-d') == $fecha)&& ($ordenes[$i]->getEstado()->getNombre()=='Pendiente'))
              {
                  
                  $result[$i]=$ordenes[$i];
                  
              }
          }
          
         
          return $this->render('EmisionesBundle:Ordenes:counter.html.twig',array('ordenes'=>$result,'fs'=>"$fecha"));
   }
   public function renderReporteAction()
   {
       $em = $this->getDoctrine()->getManager();
       $estados=$em->getRepository('EmisionesBundle:Estadoorden')->findAll();
       $agentes=$em->getRepository('EmisionesBundle:Agente')->findAll();
       return $this->render('EmisionesBundle:Ordenes:reporte.html.twig',array(
           'estados'=>$estados,
           'agentes'=>$agentes
           
           ));
   }
   public function makeReporteAction()
   {
       $peticion = $this->getRequest();
       $rango= $peticion->request->get('rangedate');
       $usuario=$peticion->request->get('usuario');
       $agente= $peticion->request->get('agente');
       $estado=$peticion->request->get('estado');
       $em = $this->getDoctrine()->getManager();
       $busqueda=array();
       if($estado!= -1)
       {
           $estado =$em->getRepository('EmisionesBundle:Estadoorden')->find($estado);
           
           $busqueda['estado']=$estado;
       }
       if($agente!=-1)
       {
           $agente=$em->getRepository('EmisionesBundle:Agente')->find($agente);
           $busqueda['agente']=$agente;
       }
      if($rango!= '')
      {
          $rango = split('-',$rango);
          $inicio=$rango[0];
          $fin=$rango[1];
      }
     if($usuario!='')
     {
         $usuario=$em->getRepository('BaseBundle:User')->find($usuario);
         $busqueda['usuario']=$usuario;
     }
       
     
       $ordenes=$em->getRepository('EmisionesBundle:Orden')->findBy($busqueda);//Total
      
       $result=array();
       $cont_emisiones=0;
       $cont_revisiones=0;
       $cont_anulaciones=0;
       foreach ($ordenes as $item) {
           if(isset($inicio)&& isset($fin))
           {
               if($item->getFecha()>= new \DateTime($inicio) && $item->getFecha()<= new \DateTime($fin))
               {
                    if($item instanceof Emision)
                    {
                        $cont_emisiones++;
                    }
                    if($item instanceof Revision)
                    {
                        $cont_revisiones++;
                    }
                    if($item instanceof Anulacion)
                    {
                        $cont_anulaciones++;
                    }
               }
               
              
           }
           else
           {
                if($item instanceof Emision)
                {
                    $cont_emisiones++;
                }
                if($item instanceof Revision)
                {
                    $cont_revisiones++;
                }
                if($item instanceof Anulacion)
                {
                    $cont_anulaciones++;
                }
          
           }
           
       }
    
       $result[0]['name']='Revisiones';$result[0]['value']=$cont_revisiones;
       $result[1]['name']='Anulaciones';$result[1]['value']=$cont_anulaciones;
       $result[2]['name']='Emisiones';$result[2]['value']=$cont_emisiones;
       
       return new Response(json_encode($result));
   }
   
  
}
