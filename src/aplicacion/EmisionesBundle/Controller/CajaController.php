<?php

namespace aplicacion\EmisionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use aplicacion\EmisionesBundle\Entity\Anulacion;
use aplicacion\EmisionesBundle\Entity\Revision;
use aplicacion\EmisionesBundle\Entity\Emision;
use aplicacion\EmisionesBundle\Entity\Tarjetacredito;
use aplicacion\EmisionesBundle\Entity\Agente;
/**
 * Aerolinea controller.
 *
 */
class CajaController extends Controller
{
  public function dashboardAction()
  {
       
       $em=  $this->getDoctrine()->getManager();
       $inicio=null;$fin=null;
       if($this->getRequest()->getMethod()=='POST')
       {
         $peticion = $this->getRequest();
         $inicio = $peticion->request->get('inicio');
         $fin = $peticion->request->get('fin');
         //print_r($inicio.'---'.$fin);exit;
         $sinconciliar=$em->getRepository('EmisionesBundle:Orden')->totalOrdenes($this->getUser()->getEmpresa()->getId(),3,'No Conciliada',$inicio,$fin);
         $pendientePago=$em->getRepository('EmisionesBundle:Orden')->totalOrdenes($this->getUser()->getEmpresa()->getId(),3,'Pendiente Pago',$inicio,$fin);
         $pagoConfirmado=$em->getRepository('EmisionesBundle:Orden')->totalOrdenes($this->getUser()->getEmpresa()->getId(),3,'Pago Confirmado',$inicio,$fin);
         $anuladas=$em->getRepository('EmisionesBundle:Orden')->totalOrdenes($this->getUser()->getEmpresa()->getId(),3,'Anulada',$inicio,$fin);
         $datapie=  $this->loadPie(new \DateTime($inicio), new \DateTime($fin));
         //print_r($datapie);exit;
       }
       else
       {
         $datapie=  $this->loadPie(new \DateTime('00:00:00'), new \DateTime('23:59:59'));
        // print_r($datapie);exit;
         $sinconciliar=$em->getRepository('EmisionesBundle:Orden')->totalOrdenes($this->getUser()->getEmpresa()->getId(),3,'No Conciliada');
         $pendientePago=$em->getRepository('EmisionesBundle:Orden')->totalOrdenes($this->getUser()->getEmpresa()->getId(),3,'Pendiente Pago');
         $pagoConfirmado=$em->getRepository('EmisionesBundle:Orden')->totalOrdenes($this->getUser()->getEmpresa()->getId(),3,'Pago Confirmado');
         $anuladas=$em->getRepository('EmisionesBundle:Orden')->totalOrdenes($this->getUser()->getEmpresa()->getId(),3,'Anulada');
       }
       $blackList=$em->getRepository('EmisionesBundle:Agencia')->blackList($this->getUser()->getEmpresa()->getId());
       
      return $this->render('EmisionesBundle:Caja:dashboard.html.twig', array(
          'sinconciliar'=>  $sinconciliar,'pendientepago'=> $pendientePago,
          'pagoconfirmado'=> $pagoConfirmado,'anuladas'=>$anuladas,'datapie'=>$datapie,
          'inicio'=>$inicio,'fin'=>$fin,
          'blacklist'=>$blackList
        ));
  }
  public function conciliarManualAction($id)
  {
      
      $em=  $this->getDoctrine()->getManager();
      $peticion = $this->getRequest();
      $entity=$em->getRepository('EmisionesBundle:Orden')->find($id);
      $existeanulacion=$em->getRepository('EmisionesBundle:Anulacion')->findOneBy(array('tarjet'=>$entity->getNumeroOrden()));
      $anulacionGenerada='';
        if(count($existeanulacion)>0)
        {
           $anulacionGenerada='Anulacion Generada con numer de Orden: '.$existeanulacion->getNumeroOrden();
        }
        else
        {
           $anulacionGenerada='No Generada';
        }
     
     
      if(!$entity)
      {
          $this->get('session')->getFlashBag()->add('error', 'La orden que usted desea conciliar no existe o ha sido eliminada!');
          return $this->redirect($this->generateUrl('EmisionesBundle_queue_manger')); 
      }
      
      if($this->getRequest()->getMethod()=='POST')
      {
          $aprobadoCaja = $peticion->request->get('aprobadoCaja');
          $detalleAprobacion = $peticion->request->get('detalleAprobacion');
          
          //$generarAnulacion = $peticion->request->get('generarAnulacion');
          $historial=$entity->getDetalleAprobacion();
          if(!empty($aprobadoCaja))
          {
              if(empty($detalleAprobacion))
              {
                  $this->get('session')->getFlashBag()->add('error', 'Debe detallar el motivo por el cual usted autoriza o no su desicion la venta!');
                        return $this->redirect($this->generateUrl('operador_conciliar_manual',array('id'=>$entity->getId())));  
                 
              }
              $entity->setDetalleAprobacion($historial.'<br>'.'<span class="text-blue">'.$this->getUser()->getUsername().'</span>'.'<span class="text-green">'.'('. date('l jS \of F Y h:i:s A').')</span>'.' : ('.$aprobadoCaja.') '.$detalleAprobacion);
              $entity->setAprobadoCaja($aprobadoCaja);  
              //Este blocque es para que despues que el supervisor modifique la orden el usuario de caja no la vea mas
              if($this->getUser()->hasRole('ROLE_SUPERVISOR_COBRANZA'))
              {
                  $entity->setModificadoSupervisorCobros(true);
              }
              //Este bloque permite generar el correo y la anulacion solo en caso de que no exista la nulacion para esta orden
              if($aprobadoCaja=='Anulada' && count($existeanulacion)<=0)
              {
                  $this->generateAnulacionAction($entity);
                  $entity->setAprobadoCaja('Anulada');
              }
              elseif ($aprobadoCaja=='Pendiente Pago' || $aprobadoCaja=='Pago Confirmado') {
                   $em->flush($entity);
                   $this->get('session')->getFlashBag()->add('success', 'Operacion culminada satisfactoriamente!');
              }
              
            
              return $this->redirect($this->generateUrl('EmisionesBundle_queue_manger'));
          }
          else
          {
              $this->get('session')->getFlashBag()->add('error', 'Debe seleccionar si aprueba o no la venta!');
              return $this->redirect($this->generateUrl('operador_conciliar_manual',array('id'=>$entity->getId()))); 
          }
         
      }
     
      return $this->render('EmisionesBundle:Caja:conciliarmanual.html.twig', array(
            'entity' => $entity,
            'anulacionGenerada'=>$anulacionGenerada
        ));
  }  
  public function generateAnulacionAction($entity)
  {
      $em=  $this->getDoctrine()->getManager();
      //$entity=$em->getRepository('EmisionesBundle:Orden')->find($idOrdenAanular);
      if(!$entity)
      {
          $this->get('session')->getFlashBag()->add('error', 'La orden que usted desea anular no existe o ha sido eliminada!');
          return $this->redirect($this->generateUrl('EmisionesBundle_queue_manger')); 
      }
      $anulacion=new Anulacion();
      $anulacion->setTipoPago("");
      $agente=$em->getRepository('EmisionesBundle:Agente')->findOneBy(array('username'=>'cobranza'));
      if($agente instanceof Agente)
      {
          $anulacion->setAgente($agente);
      }
      else
      {
          $this->get('session')->getFlashBag()->add('error', 'No existe el agente de cobranza para esta empresa, por favor crearlo!');
              return $this->redirect($this->generateUrl('EmisionesBundle_queue_manger')); 
      }

      $anulacion->setTiempoProceso($entity->getTiempoProceso());
      $anulacion->setEmpresa($entity->getEmpresa());
      $anulacion->setCiudadDestino($entity->getCiudadDestino());
      $anulacion->setComentario('Esto es una ANULACION generada automaticamente por el sistema de colas de MyM Travel Group, debido a un problema de pago asociado a la EMISION.');
      $anulacion->setDetalleAprobacion($entity->getDetalleAprobacion());
      $anulacion->setEstado($em->getRepository('EmisionesBundle:Estadoorden')->find(2));
      $anulacion->setNumeroOrden(time());
      $anulacion->setFecha(new \DateTime());
      $anulacion->setFeeServicios(0);
      $anulacion->setGds($entity->getGds());
      $anulacion->setTipoBoleto($entity->getTipoBoleto());
      $anulacion->setRecordGds($entity->getRecordGds());
      $anulacion->setTourcode($entity->getTourcode());
      $anulacion->setTarjet($entity->getNumeroOrden());
      $anulacion->setAprobadoCaja('Aprobada');
      $anulacion->setPrioridad(90000);
      $anulacion->setProcesadaEmergencia(false);
      $anulacion->setModificadoSupervisorCobros(true);
      $anulacion->setNumPasajeros($entity->getNumPasajeros());
      $anulacion->setDatosBoleto($entity->getComentario());
      $fps=$entity->getFormasPagos();
      $vtc='CASH';
      foreach ($fps as $value) {
          if($value instanceof Tarjetacredito)
          {
             $vtc='Por favor recuerde anular el VTC de la Emision'; 
          }
      }
      $anulacion->setVtc($vtc);
      $anulacion->setObservaciones('Esta es una anulacion generada por el usuario '.$this->getUser()->getUsername().' debido a prblemas de pago, anula a la orden de numero: '.$entity->getNumeroOrden());
      $counterinterno=$this->asignarOrden($anulacion);     
     if($counterinterno !=false)
     {
         $anulacion->setUsuario($counterinterno);
         /*Setear la hora de asignacion*/
         $anulacion->setHoraAsignacion(new \DateTime());
     }

      $em->persist($anulacion);
      
      $em->persist($entity);
      $em->flush();
      $this->get('session')->getFlashBag()->add('success', 'Operacion culminada satisfactoriamente!');
      $this->sendMail($entity->getId(), $anulacion->getId());
      //$this->get('session')->getFlashBag()->add('success', 'Se ha generado una anulacion para esta orden debido a que usted no aprobo la venta!');
  }
  public function sendMail($ordenoriginal_id,$anulacion_id)
    {
        $emisiones='emisiones@mymtravel.com;emisioncontabilidad@mymtravel.com;paulina.freire@mymtravel.com';
        $emergencia='emergencias@mymtravel.com;emisioncontabilidad@mymtravel.com;paulina.freire@mymtravel.com';
       
        $em = $this->getDoctrine()->getManager();
        $orden=$em->getRepository('EmisionesBundle:Orden')->find($ordenoriginal_id);
        if(!$orden)
        {
           $this->get('session')->getFlashBag()->add('error', 'La orden que usted desea anular no existe o ha sido eliminada!');
           return $this->redirect($this->generateUrl('EmisionesBundle_queue_manger'));  
        }
        $anulacion=$em->getRepository('EmisionesBundle:Anulacion')->find($anulacion_id);
         if(!$anulacion)
        {
           $this->get('session')->getFlashBag()->add('error', 'La que se genero no existe o ha sido eliminada!');
           return $this->redirect($this->generateUrl('EmisionesBundle_queue_manger'));  
        }
        $direcciones='';
        if(new \DateTime()< new \DateTime('19:00'))
        {
            $direcciones=  explode(';',  $this->getUser()->getEmail().';'.$emisiones.';'.$orden->getAgente()->getEmail().';'.$anulacion->getAgente()->getEmail());       
        }
        else
        {
            $direcciones=  explode(';', $this->getUser()->getEmail().';'.$emergencia.';'.$orden->getAgente()->getEmail().';'.$anulacion->getAgente()->getEmail());  
        }
        $subject='ORDEN CON #'.$orden->getNumeroOrden().' . NO PROCESADA POR PROBLEMAS CON EL PAGO.';
        $from='emisiones@mymtravel.com';
        $cuerpo='LA ORDEN #'.$orden->getNumeroOrden().'NO SE HA PROCESADO DEBIDO A QUE LA AGENCIA '.$orden->getAgente()->getAgencia().' QUE MANDO A PROCESAR ESTA ORDEN, HA PRESENTADO PROBLEMAS CON EL PROCESO DE PAGO.';
        $reciben= array();
             
            foreach ($direcciones as $key => $val) {
                if($val!='')
                {
                    $reciben[strtolower(str_replace(" ", "",$val))]=  strtolower(str_replace(" ", "",$val));
                }                               
            }
       $precuerpo=
           '
        SERVICIO         :  SOLICITUD '.strtoupper($anulacion->getTipo()).' WEB  BOLETOS INTERNACIONALES
        CIUDAD DESTINO EMISION : '.strtoupper($anulacion->getCiudadDestino()).'
        NUMERO DE ORDEN  :  '.  strtoupper($anulacion->getNumeroOrden()).'
        FECHA DE ENVIO   :  '.  strtoupper($anulacion->getFecha()->format('l jS F Y g:ia ')).'
        CEDULA / RUC     :  '.strtoupper($anulacion->getAgente()->getAgencia()->getRuc()).'
        AGENCIA          :  '.  strtoupper($anulacion->getAgente()->getAgencia()).'
        AGENTE           :  '.  strtoupper($anulacion->getAgente()->__toString()).'
        E-MAIL           :  '.  $anulacion->getAgente()->getEmail().'
        TELEFONOS        :  '.  strtoupper($anulacion->getAgente()->getTelefono()).'Ext:'.strtoupper($orden->getAgente()->getExt()).'CELULAR    :  '.strtoupper($orden->getAgente()->getCelular()).'
        ESTADO           :  '.  strtoupper($anulacion->getEstado()->getNombre()).'
        OPERADOR         :  '.  strtoupper($anulacion->getGds()->getNombre()).'
        RECORD           :  '.  strtoupper($anulacion->getRecordGds()).'
        FEE              :  '.strtoupper($anulacion->getFeeServicios()).'   
        OBSERVACIONES    :  '.strtoupper($anulacion->getObservaciones()). '
        -------------------------------------------------------------------------------
            ';
        $vtc=strtoupper($anulacion->getVtc());
        $precuerpo=$precuerpo.'
        NUMERO DE ORDEN X ANULAR :  '.strtoupper($anulacion->getTarjet()).'
        NUMERO DE BOLETOS Y NOMBRE DE PASAJEROS  :
        '.strtoupper($anulacion->getDatosBoleto()).'
        TIPO DE PAGO AL TKTT :  '.strtoupper($vtc);                       
      


        $message = \Swift_Message::newInstance()
        ->setFrom(array($from=>$from))   
        ->addReplyTo($from)
        ->setSubject($subject)
        ->setTo($reciben)        
        ->setBody($cuerpo.' 
            POR ESTE MOTIVO NUESTRO SISTEMA A GENERADO UNA ANULACION AUTOMATICA
            
                                DATOS DE LA ANULACION
                '.$precuerpo.'
                    Nota: Para obtener informacion comunicate mediante nuestro Pbx: 298 3800 
                    Extensiones: 2280-Bryan Robalino
                                 2281-Carla Cruz
                ');

       if( $this->get('mailer')->send($message))
       {
           $this->get('session')->getFlashBag()->add('success', 'Un correo de notificacion ha sido generado automaticamente a la agencia que presenta el problema con el pago!');
       }
       else
       {
           $this->get('session')->getFlashBag()->add('error', 'Ha ocurrido un error al enviar la notificacion a la agencia!');
       }
    return $this->redirect($this->generateUrl('EmisionesBundle_queue_manger')); 
    }
    
    public function indexAgenciasAction()
    {
        
        return $this->render('EmisionesBundle:Caja:indexAgencias.html.twig', array(
          
        ));
    }
    public function load_ajax_agenciasAction()
    {
        
        $peticion = $this->getRequest();
        $searchfilter = $peticion->request->get('search');
        $searchfilter=$searchfilter['value'];
        $pstart=$peticion->request->get('start');
        $pend=$peticion->request->get('length');   
        $draw=$peticion->request->get('draw');
        $empresaid = $peticion->request->get('empresa_id');       
     
        
        $em= $this->getDoctrine()->getManager();
        $empresa = $em->getRepository('BaseBundle:Empresa')->find($empresaid);
        $agencias= $em->getRepository('EmisionesBundle:Agencia')->findAll(); 

        if($searchfilter!='')
        {
         $agencias=  $this->applyFilterSearchAgencias($agencias, $searchfilter);
        }
        
        //En este punto tengo el total de ordenes que cumple con los filtros
        $result = array();       
        $result['draw']=$draw;
        $result['recordsTotal']=count($agencias);
        $result['recordsFiltered']=count($agencias);        
        $agencias= $this->reduce($pstart, $pend, $agencias);      
        $cont=0;
        $result['data'] = array();
        for ($i = 0; $i < count($agencias); $i++) 
        {     
                $result['data'][$cont]['agencia']= $agencias[$i];
                $result['data'][$cont]['id']= $agencias[$i]->getId();
                $result['data'][$cont]['nombre']= $agencias[$i]->__toString();
                $result['data'][$cont]['ruc']= $agencias[$i]->getRuc();
                $result['data'][$cont]['email']= $agencias[$i]->getEmail();
                $result['data'][$cont]['telefono']= $agencias[$i]->getTelefono();
                $result['data'][$cont]['ciudad']= $agencias[$i]->getCiudad()->__toString();
                if($empresa->__isMember($agencias[$i]))
                {
                    $result['data'][$cont]['member']= '<i class="fa fa-check-square-o"></i>';
                }
                else
                {
                   $result['data'][$cont]['member']= '<i class="fa fa-square-o"></i>';
                }
               $result['data'][$cont]['memberUnmember']= $this->generateUrl('supervisor_cobranza_member_unmember_agencia', array('id' => $agencias[$i]->getId(),'id_empresa'=>$empresa->getId(),'referencia'=>'supervisor_cobranza_index_agencias'));
               //$result['data'][$cont]['edit']= $this->generateUrl('supervisor_agencia_edit', array('id' => $agencias[$i]->getId()));                              
                $cont++;
        }
      
       //print_r($result);exit;
       return new Response(json_encode($result));
      
    }
    
    public function member_unmemberAction($id,$id_empresa,$referencia)
    {
        $em= $this->getDoctrine()->getManager();
        $agencia = $em->getRepository('EmisionesBundle:Agencia')->find($id);
        $empresa = $em->getRepository('BaseBundle:Empresa')->find($id_empresa);
        if(!$agencia)
        {
            $this->get('session')->getFlashBag()->add('error', 'La agencia no existe!');
        }
        if(!$empresa)
        {
            $this->get('session')->getFlashBag()->add('error', 'La empresa del supervisor no existe!');
        }
        if($empresa->__isMember($agencia))
        {
            $empresa->removeAgencia($agencia);
        }
        else
        {
            $empresa->addAgencia($agencia);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'Operacion realizada satisfactoriamente!');
        //print_r($referencia);exit;
        if(!is_null($referencia))
        {
            return $this->redirect($this->generateUrl($referencia));
        }
        return $this->redirect($this->generateUrl('supervisor_cobranza_index_agencias'));
    }
    public function reduce($inicio,$limite,$arreglo)
    {
       // print_r($inicio.'--'.$limite.'--'.count($arreglo));exit;
        $result=array();
        
        if($inicio+$limite<=count($arreglo))
        {
            
            $cont=0;
           for ($i = $inicio; $i < $inicio+$limite; $i++) {
               //print_r('asigno--');
               $result[$cont]=$arreglo[$i];
               $cont++;
            }  
        }
        else
        {
             $contj=0;
            for ($j = $inicio; $j < count($arreglo); $j++) {
                $result[$contj]=$arreglo[$j];
                $contj++;
            }
        }
         //print_r(count($result));exit;
        return $result;
        
    }
    public function loadPie($start, $end)
    {   
        
        $em = $this->getDoctrine()->getManager(); 
        $estados = array('No Conciliada','Pendiente Pago','Pago Confirmado','Anulada');
        $total=array();
        for ($x = 0; $x < count($estados); $x++) {
            $color='';
           switch ($estados[$x]) {
            case 'No Conciliada':
                $color= '#0073b7';
                break;
            case 'Pendiente Pago':
                $color= '#f39c12';
                break;
            case 'Pago Confirmado':
                 $color= '#00a65a';
                break;
            case 'Anulada':
                 $color= '#f56954';
                break;
            }
         $total[]= array('name'=>$estados[$x],'color'=>$color,'y'=>  floatval($em->getRepository('EmisionesBundle:Orden')->TotalEstadosConciliacion($start,$end,$estados[$x],3)));            
        }
        return json_encode( $total);
    }
     /*
     * filtro de busqueda para la tabla de agencias
     */
    public function applyFilterSearchAgencias($agencias,$filter)
    {
      
      $filter=strtoupper($filter);
       $result=array();
       foreach ($agencias as $a) {
          
            if(preg_match('/'.$filter.'/',strtoupper($a->getNombre())) ||
               preg_match('/'.$filter.'/',strtoupper($a->getRuc())) ||
               preg_match('/'.$filter.'/',strtoupper($a->getEmail())) ||
               preg_match('/'.$filter.'/',strtoupper($a->getTelefono())) ||     
               preg_match('/'.$filter.'/',strtoupper($a->getCiudad()->getNombre())))     
                    
            {
                $result[]=  $a;
            }
                
           
       }
       return $result;
    }
    public function asignarOrden($orden)
    {
        
        //Counters disponibles en la hora de la orden
        $counters=  $this->getDoctrine()->getManager()->getRepository('BaseBundle:Empresa')->getCountersOrderedByTimeOfQueue($orden->getEmpresa()->getId(),$orden->getFecha()->format('H:i:s'));
       
        if($orden instanceof Anulacion)
        {
          $emision=  $this->getDoctrine()->getManager()->getRepository('EmisionesBundle:Emision')->findOneBy(array('numeroOrden'=>$orden->getTarjet()));
          if($emision instanceof Emision && $emision->getUsuario() instanceof Usuariointerno)
          {
//              print_r("entro");exit;
              if(in_array($emision->getUsuario()->getId(), array_column($counters, 'id')))
              {
                 return $emision->getUsuario(); 
              }
          }
//           print_r("salio");exit;
        }
        foreach (array_column($counters, 'id') as $id_counter)
          {
              $counter=  $this->getDoctrine()->getManager()->getRepository('EmisionesBundle:Usuariointerno')->find($id_counter);
              if($orden->getFecha() < $counter->getInicioAlmuerzo() && $orden->getFecha()>= $counter->getInicioJornada())
              {//horario mannana del counter
                  return $counter; 
              }
              elseif($orden->getFecha() > $counter->getFinAlmuerzo() && $orden->getFecha() <= $counter->getFinJornada())
              {//horario de la tarde del counter
                  return $counter;
              }
          } 
       return false;
    }
  
}
