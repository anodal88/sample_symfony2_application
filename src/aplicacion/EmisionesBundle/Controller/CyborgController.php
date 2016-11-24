<?php

namespace aplicacion\EmisionesBundle\Controller;
use aplicacion\EmisionesBundle\Entity\Tarjetacredito;
use aplicacion\EmisionesBundle\Entity\Pagodirecto;
use aplicacion\EmisionesBundle\Entity\DepefectivoTransferenciabancaria;
use aplicacion\EmisionesBundle\Entity\Emision;
use aplicacion\EmisionesBundle\Entity\Revision;
use aplicacion\EmisionesBundle\Entity\Anulacion;
use aplicacion\EmisionesBundle\Entity\Agente;
use aplicacion\EmisionesBundle\Entity\Ciudad;
use aplicacion\BaseBundle\Entity\Empresa;
use aplicacion\EmisionesBundle\Entity\Gds;
use aplicacion\EmisionesBundle\Entity\Estadoorden;
use aplicacion\EmisionesBundle\Entity\Usuariointerno;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;


/**
 * Aerolinea controller.
 *
 */
class CyborgController extends Controller
{
  public function LoginAction(Request $request)
  {
     
    $result=array();
    $data = $request->request->get('Credenciales');
    $em=  $this->getDoctrine()->getManager(); 
    $username ="";$pass="";
    if(isset($data['username']))
     {
        $username=$data['username'];
     }
     else
     {
       $result['respuesta']='Error';
       $result['detalle_respuesta']='Debe proporcionar el nombre de usuario.';
       return new Response(json_encode($result));  
     }
     
     if (isset($data['password'])) {
         $pass=$data['password'];
     }else
     {
       $result['respuesta']='Error';
       $result['detalle_respuesta']='Debe proporcionar la clave del usuario.';
       return new Response(json_encode($result));  
     }
     
    $user = $em->getRepository('BaseBundle:User')->findOneBy(array("username"=>$username));
    if ($user!=null) {
       if (!$user->isEnabled()) {
       $result['respuesta']='Error';
       $result['detalle_respuesta']='El usuario esta deshabilitado.';
       return new Response(json_encode($result));  
     } 
     if ($user->isExpired()) {
       $result['respuesta']='Error';
       $result['detalle_respuesta']='La cuenta del usuario ha expirado.';
       return new Response(json_encode($result));   
     }
     if ($user->isLocked()) {
       $result['respuesta']='Error';
       $result['detalle_respuesta']='El usuario esta bloqueado.';
       return new Response(json_encode($result));   
     } 
     $encoder = new MessageDigestPasswordEncoder('sha512');
    $passwordHashed = $encoder->encodePassword($pass, $user->getSalt());
    if ($passwordHashed== $user->getPassword()) {
       $result['respuesta']='Ok';
       $result['detalle_respuesta']='Credenciales satisfactoriamente verificadas.';
       return new Response(json_encode($result)); 
    }
    }
    $result['respuesta']='Error';
    $result['detalle_respuesta']='Error de credenciales.';
    return new Response(json_encode($result));        
  }
  public function SaveAction(Request $request)
  {  
       
         $result=array();
         $data = $request->request->get('Orden');

         $em=  $this->getDoctrine()->getManager();
         if(isset($data['tipoOrden']['emision']))
         {
            $emision=$data['tipoOrden']['emision'];
            $orden=new Emision();
            $orden->setReservaPnr($emision['reservaPnr']);
            $orden->setTarifaReserva($emision['tarifaReserva']);
         }
         elseif(isset($data['tipoOrden']['revision']))
         {
            $revision=$data['tipoOrden']['revision'];
            $orden=new Revision();
            $orden->setReservaPnr($revision['reservaPnr']);
            $orden->setTarifaReserva($revision['tarifaReserva']);
            $orden->setDatosBoleto($revision['datosBoleto']);
            $orden->setTarjet($revision['tarjet']);
         }
         elseif(isset($data['tipoOrden']['anulacion']))
         {
            $anulacion=$data['tipoOrden']['anulacion'];
            $orden=new Anulacion(); 
            $orden->setDatosBoleto($anulacion['datosBoleto']);
            $orden->setVtc($anulacion['vtc']);
            $orden->setTarjet($anulacion['tarjet']);
         }
         $orden->setNumeroOrden($data['numeroOrden']);
         $orden->setPrioridad($data['prioridad']);
         $orden->setNumPasajeros($data['numPasajeros']);
         $orden->setTipoBoleto($data['tipoBoleto']);
         $agente=$em->getRepository('EmisionesBundle:Agente')->find($data['agente']);
         if($agente instanceof  Agente)
         {
             $orden->setAgente($agente);
         }else
         {
            $result['respuesta']='Error';
            $result['detalle_respuesta']='No existe el agente o ha sido elimnado.';
            return new Response(json_encode($result));
         }
         $empresa=$em->getRepository('BaseBundle:Empresa')->find($data['empresa']);
         if($empresa instanceof Empresa)
         {
             $orden->setEmpresa($empresa);
         }else
         {
            $result['respuesta']='Error';
            $result['detalle_respuesta']='No existe la empresa o ha sido eliminada.';
            return new Response(json_encode($result));
         }
         $orden->setRecordGds($data['recordGds']);
         $orden->setTourcode($data['tourcode']);
         $orden->setFeeServicios($data['feeServicios']);
         $orden->setObservaciones($data['observaciones']);
         $orden->setAdjunto($data['adjunto']);//falta validar
         $gds=$em->getRepository('EmisionesBundle:Gds')->find($data['gds']);
         if($gds instanceof Gds)
         {$orden->setGds($gds);}
         else
         {
            $result['respuesta']='Error';
            $result['detalle_respuesta']='No existe el GDS seleccionado o ha sido eliminado por el administrador.';
            return new Response(json_encode($result));
         }
         $ciudad=$em->getRepository('EmisionesBundle:Ciudad')->find($data['ciudadDestino']);
         if($ciudad instanceof Ciudad)
         {$orden->setCiudadDestino($ciudad);}
         else
         {
            $result['respuesta']='Error';
            $result['detalle_respuesta']='No existe la ciudad destino seleccionada o ha sido eliminada por el administrador.';
            return new Response(json_encode($result));
         }
         $estado=$em->getRepository('EmisionesBundle:Estadoorden')->find(2);
         if($estado instanceof Estadoorden)
         {$orden->setEstado($estado);}
         else
         {
            $result['respuesta']='Error';
            $result['detalle_respuesta']='No existe estado pendiente para su orden, contacte al administrador del sistema.';
            return new Response(json_encode($result));
         }
         //Preguntar si existe el arreglo en forma de pago, sino existe sera error excepto en la anulacion
         if(isset($data['formasPago']))
         {
             $fps=$this->mergeFP($data['formasPago']);
            foreach ($fps as $fp)
            {
                $orden->addFormasPago($fp);                
            } 
         }
         else
         {
             if(!isset($data['tipoOrden']['anulacion']))
             {
                $result['respuesta']='Error';
                $result['detalle_respuesta']='Debe establecer la forma de pago.';
                return new Response(json_encode($result));
             }
         }
         
         $orden->setTiempoProceso($this->timeOfProcess($empresa, $orden));
         $orden->setFecha(new \DateTime());
         $counterinterno=$this->asignarOrden($orden);
         if($counterinterno instanceof Usuariointerno)
         {
             $orden->setUsuario($counterinterno);
             /*Setear la hora de asignacion*/
             $orden->setHoraAsignacion(new \DateTime());
         }
         $orden->setTipoPago($this->OrdenTipoPago($orden));
         $em->persist($orden);         
         $em->flush();
        // print_r(count($orden->getFormasPagos()));echo "<pre>";
        
         $result['respuesta']='Ok';
         $result['detalle_respuesta']='Su orden ha grabada satisfactoriamente.';
         return new Response(json_encode($result));
  }
  
  
  
  public function mergeFP($formasPago)
  {
      $result=array();
      if(isset($formasPago['tarjetasCredito'])){
        foreach ($formasPago['tarjetasCredito'] as $tc) 
        {
           $fptc=$this->createTC($tc);
           if($fptc['respuesta']=='Ok')
           {
             $result[]=  $fptc['detalle_respuesta'];  
           }
        }
      }
      if(isset($formasPago['pagosDirectos'])){
        foreach ($formasPago['pagosDirectos'] as $pd) 
        {
            $fppd=$this->createPD($pd);
            if($fppd['respuesta']=='Ok')
              {
                $result[]=  $fppd['detalle_respuesta'];  
              }        
        }
      }
      if(isset($formasPago['detbs'])){
        foreach ($formasPago['detbs'] as $detb) 
        {
            $fpdetb=$this->createDETB($detb);
            if($fpdetb['respuesta']=='Ok')
              {
                $result[]=  $fpdetb['detalle_respuesta'];  
              } 
        }
      }
    
      return $result;
  }
  
  
  public function createTC($tc)
  {
     // print_r($tc);echo"<pre>";
     $result=array(); 
     $em=  $this->getDoctrine()->getManager();
     $aerolinea=$em->getRepository('EmisionesBundle:Aerolinea')->find($tc['aerolinea']);
     if(!$aerolinea)
     {
        $result['respuesta']='Error';
        $result['detalle_respuesta'].=$result['detalle_respuesta'].' '.'No existe la aerolinea seleccionada en la forma de pago TC.';
        return $result;
     }
     $newtc=new Tarjetacredito();
     $newtc->setEmisorVtc($tc['emisorVtc']);
     $newtc->setAerolinea($aerolinea);
     $newtc->setBancoEmisorTarjeta($tc['bancoEmisorTarjeta']);
     $newtc->setTipoTarjeta($tc['tipoTarjeta']);
     $newtc->setNumeroTarjeta($tc['numeroTarjeta']);
     $newtc->setPropietario($tc['propietario']);
     $newtc->setVence(new \DateTime($tc['vence']));
     $newtc->setPin($tc['pin']);
     $newtc->setTipoPago($tc['tipoPago']);
     $newtc->setPlazo($tc['plazo']);
     $newtc->setTipoAutorizacion($tc['tipoAutorizacion']);
     $newtc->setNumeroAutorizacion($tc['numeroAutorizacion']);
     $newtc->setValorTarjeta($tc['valorTarjeta']);
     $newtc->setInteresTarjeta($tc['interesTarjeta']);
     $newtc->setValorTotal($tc['valorTotal']);
     $newtc->setPagoPasajeros($tc['pagoPasajeros']);
     $result['respuesta']='Ok';
     $result['detalle_respuesta']=$newtc;
     return  $result;
  }
  public function createPD($pd)
  {
      $result=array(); 
      $newpd=new Pagodirecto();
      $newpd->setTipoPago($pd['tipoPago']);
      $newpd->setValor($pd['valor']);
      $result['respuesta']='Ok';
      $result['detalle_respuesta']=$newpd;
      return  $result;
  }
  public function createDETB($detb)
  {      
       $result=array(); 
       $newdetb= new DepefectivoTransferenciabancaria();
       $newdetb->setBanco($detb['banco']);
       $newdetb->setNumeroDocumento($detb['numeroDocumento']);
       $newdetb->setValor($detb['valor']);
       $newdetb->setTransaccion($detb['transaccion']);
       $result['respuesta']='Ok';
       $result['detalle_respuesta']=$newdetb;
       return  $result;
  }
  /*
     * Funcion que determina el tiempo que demora procesar esta orden
     */
    public function timeOfProcess($empresa,$orden)
    {
        
        $config=  $empresa->getConfiguracionActiva();
        $tiempoTo=0;
        $cash=0;
        $planpiloto=0;
        $vtc=0;
        $destino=0;
        
        
        switch ($orden->getTipo()) {
            case 'Anulacion':
                    $tiempoTo+=$config->getTiempoAnulacion();
                break;
            case 'Emision':
                    $tiempoTo+=$config->getTiempoEmision();
                break;
            case 'Revision':
                    $tiempoTo+=$config->getTiempoRevision();
                break;

            default:
                break;
        }
      
        foreach ($orden->getFormasPagos() as $fp) {
            
            if(($fp instanceof Pagodirecto)|| ($fp instanceof DepefectivoTransferenciabancaria))
            {
                $cash+=$config->getTiempoFomaPagoCash();
            }
            if($fp instanceof Tarjetacredito)
            {
               
                if($orden->isPilotPlan()/*$fp->getAerolinea()->getAgenciasPlanPiloto()->contains($this->getAgente()->getAgencia())*/)
                {
                    $planpiloto+=$config->getTiempoFomaPagoPlanPiloto();
                }
                else
                {
                    $vtc+=$config->getTiempoFomaPagoVtc();
                }
            }
        }
        
        //Averiguando si la orden es local o remota
        if($empresa->getCiudad()== $orden->getCiudadDestino())
        {
            //Es local la orde porque se solicito a una empresa que esta en la misma ciudad 
            //que la ciudad destino de la orden
            $destino+=$config->getTiempoLocal();
            
        }
        else
        {
            //Orden para emision remota pues la ciudad de la empresa a la que se le solicito
            // es != a la ciudad destino de la orden
            $destino+=$config->getTiempoRemota();
        }
       // print_r("TiempoTipoOrden:".$tiempoTo."+"."TiempoCash:".$cash."+"."TiempoPlanPiloto:".$planpiloto."+"."Tiempovtc:".$vtc."+"."TiempoDestino:".$destino."+"."TiempoTotalPax:".$config->getTiempoPorPasajero()*$orden->getNumPasajeros());
       
        
       return ($tiempoTo+$cash+$planpiloto+$vtc+$destino)+($config->getTiempoPorPasajero()*$orden->getNumPasajeros());//dejarlo en segundos
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
              if(in_array($emision->getUsuario()->getId(), array_column($counters, 'id')))
              {
                 return $emision->getUsuario(); 
              }
          }
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
    public function OrdenTipoPago($orden)
    {
        
       $fps=  $orden->getFormasPagos();
       $cc=0;$ctc=0;
        foreach ($fps as $f) {
            if ($f instanceof Tarjetacredito) {
              $ctc++;  
            }
            else 
            {
               $cc++; 
            }
        }
        if ($cc>0&&$ctc>0) {
            return "Mixta";
        }else if($cc>0&&$ctc==0){return "Cash";}
        else if($cc==0&&$ctc>0){return "Tarjeta Cred";}
        else{return "";}
    }
}
