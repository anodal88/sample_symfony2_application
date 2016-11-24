<?php

namespace aplicacion\EmisionesBundle\Controller;

use aplicacion\BaseBundle\Libs\Pdf\HTML2PDF;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Controller\RegistrationController;
use aplicacion\EmisionesBundle\Entity\Agente;
use aplicacion\EmisionesBundle\Entity\Usuariointerno;
use aplicacion\EmisionesBundle\Entity\Agencia;
use aplicacion\BaseBundle\Entity\Empresa;
use aplicacion\EmisionesBundle\Entity\Emision;
use aplicacion\EmisionesBundle\Entity\Revision;
use aplicacion\EmisionesBundle\Entity\Anulacion;
use aplicacion\BaseBundle\Entity\User AS BaseUser;
use aplicacion\EmisionesBundle\Form\RevisionType;
use aplicacion\EmisionesBundle\Form\AnulacionType;
use aplicacion\EmisionesBundle\Form\EmisionType;
use aplicacion\BaseBundle\Form\EmpresaType;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserInterface;
use aplicacion\BaseBundle\Entity\Configuracion;


/**
 * Usuariointerno controller.
 *
 */
class CounterController extends RegistrationController
{
    public function indexCounterDashboardAction()
    {
        $em=$this->getDoctrine()->getManager();
        $configuracionActiva=  $em->getRepository('BaseBundle:Configuracion')->findOneBy(array('empresa'=>  $this->getUser()->getEmpresa(),'activa'=>true));
        $estados= $em->getRepository('EmisionesBundle:Estadoorden')->findAll();
        return $this->render('EmisionesBundle:Counter/Cola:indexCola.html.twig', array(
           'estados'=>$estados,
           'configuracionactiva'=>$configuracionActiva
        ));
    }
    
    /*
     * Funcion que carga la cola de solicitudes de mayor a menor prioridad
     */
    public function loadColaCounterAction()
    {
        
        $peticion = $this->getRequest();
        $searchfilter = $peticion->request->get('search');
        $searchfilter=$searchfilter['value'];
       // print_r('entro');exit;
        $pstart=$peticion->request->get('start');
        $pend=$peticion->request->get('length');   
        $draw=$peticion->request->get('draw'); 
        $rango = $peticion->request->get('rangoOrdenes');
        $estado = $peticion->request->get('estado');
        $tipo = $peticion->request->get('tipo');
        $counter = $peticion->request->get('counter');
        $rango = explode('-', $rango);
        $start = $rango[0];
        $end = $rango[1];
        $start= new \DateTime($start);
        $end = new \DateTime($end);
        $em= $this->getDoctrine()->getManager();
        $counter=$em->getRepository('EmisionesBundle:Usuariointerno')->find($counter);        
        $ordenes = $em->getRepository('EmisionesBundle:Orden')->getBySortableGroupsQuery(array('empresa' =>$this->getUser()->getEmpresa()))->getResult();        
        $ordenes = $this->applyFilterCounter($ordenes, $counter->getId());        
        //Aplicar filtro de estado
        $ordenes=  $this->applyFilterState($ordenes, $estado);
        //Aplicar filtro de tipo
        $ordenes = $this->applyFilterType($ordenes, $tipo);
        //Aplicar filtro de fecha
        $ordenes = $this->applyFilterDate($ordenes, $start, $end);
       // si se aplica filtro de busqueda por el usuario
        if($searchfilter!='')
        {
         $ordenes=  $this->applyFilterSearch($ordenes, $searchfilter);
        }
       //En este punto tengo el total de ordenes que cumple con los filtros
        $result = array();
       // $result['iTotalRecords']=count($ordenes);
        $ordenes=  array_reverse($ordenes);
        //print_r(count($ordenes));exit;
        
        $result['draw']=$draw;
        $result['recordsTotal']=count($ordenes);
        $result['recordsFiltered']=count($ordenes);
        // print_r($pstart.'--'.$pend.'--'.count($ordenes));exit;
        $ordenes= $this->reduce($pstart, $pend, $ordenes);
       // print_r($pstart.'--'.$pend.'--'.count($ordenes));exit;
        $cont=0;
        $result['data'] = array();
        //print_r(count($ordenes));exit;
        for ($i = 0; $i < count($ordenes); $i++) 
        {
            //$fecha=$ordenes[$i]->getFecha();
            //if(($fecha>=$start) && ($fecha<=$end))
           // {      
                    $result['data'][$cont]['numero_orden']= $ordenes[$i]->getNumeroOrden();
                    $result['data'][$cont]['adjunto']= null;
                    if($ordenes[$i]->getAdjunto())
                    {
                    $result['data'][$cont]['adjunto']= $ordenes[$i]->getAdjunto();                 
                    }
                    $result['data'][$cont]['agente']= $ordenes[$i]->getAgente()->__toString();                   
                    
                    $result['data'][$cont]['avataragente']= $ordenes[$i]->getAgente()->getFoto();
                    $result['data'][$cont]['movil']= $ordenes[$i]->getAgente()->getCelular();
                    $result['data'][$cont]['telefono']= $ordenes[$i]->getAgente()->getTelefono().'-'.$ordenes[$i]->getAgente()->getExt();
                    $result['data'][$cont]['emailagente']= $ordenes[$i]->getAgente()->getEmail();
                    $result['data'][$cont]['agencia']= $ordenes[$i]->getAgente()->getAgencia()->__toString();
                    $result['data'][$cont]['emailagencia']= $ordenes[$i]->getAgente()->getAgencia()->getEmail();
                    $result['data'][$cont]['telefonoagencia']= $ordenes[$i]->getAgente()->getAgencia()->getTelefono();
                    $result['data'][$cont]['direccionagencia']= $ordenes[$i]->getAgente()->getAgencia()->getDireccion();
                  
                      $result['data'][$cont]['logoagencia']= $ordenes[$i]->getAgente()->getAgencia()->getLogo();  
                   
                    
                    if($ordenes[$i] instanceof Emision )
                    {
                        $result['data'][$cont]['procesar']=$this->generateUrl('counter_emision_edit', array('id' => $ordenes[$i]->getId()));
                        $result['data'][$cont]['mostrar']=$this->generateUrl('counter_emision_show', array('id' => $ordenes[$i]->getId()));
                    }
                    if($ordenes[$i] instanceof Revision )
                    {
                        $result['data'][$cont]['procesar']= $this->generateUrl('counter_revision_edit', array('id' => $ordenes[$i]->getId()));
                        $result['data'][$cont]['mostrar']=$this->generateUrl('counter_revision_show', array('id' => $ordenes[$i]->getId()));
                    }
                    if($ordenes[$i] instanceof Anulacion )
                    {
                        $result['data'][$cont]['procesar']= $this->generateUrl('counter_anulacion_edit', array('id' => $ordenes[$i]->getId()));
                        $result['data'][$cont]['mostrar']=$this->generateUrl('counter_anulacion_show', array('id' => $ordenes[$i]->getId()));
                    }
                    $result['data'][$cont]['pdf']= $this->generateUrl('counter_exportar_pdf', array());
                    $result['data'][$cont]['email']= $this->generateUrl('counter_render_email', array('entity'=>$ordenes[$i]->getId()));
                    if($ordenes[$i]->getObservaciones()!=null)
                    {
                       $result['data'][$cont]['observaciones']= $ordenes[$i]->getObservaciones(); 
                    }
                    else
                    {
                        $result['data'][$cont]['observaciones']= 'No existen observaciones...';
                    }
                    $result['data'][$cont]['fecha']= $ordenes[$i]->getFecha()->format('d M Y H:i:s');                    
                    $result['data'][$cont]['tipo']= $ordenes[$i]->getTipo();
                    $result['data'][$cont]['estado']= $ordenes[$i]->getEstado()->getNombre();
                    $result['data'][$cont]['gds']= $ordenes[$i]->getGds()->__toString();
                    $result['data'][$cont]['tboleto']= $ordenes[$i]->getTipoBoleto();
                    $result['data'][$cont]['prioridad']= $ordenes[$i]->getPrioridad();
                    $result['data'][$cont]['numero']= $ordenes[$i]->getNumeroOrden();
                    $result['data'][$cont]['tiempo']= $ordenes[$i]->timeSinceArrive(); 
                    if($ordenes[$i]->isTimeToFirsAlert())
                    {
                       $result['data'][$cont]['tiempo']= '<b class="text-green"><i class="ion ion-ios7-alarm"></i> '.$ordenes[$i]->timeSinceArrive().'</b>'; 
                    }
                    if($ordenes[$i]->isTimeToSecondAlert())
                    {
                       $result['data'][$cont]['tiempo']= '<b class="text-orange"><i class="ion ion-ios7-alarm"></i> '.$ordenes[$i]->timeSinceArrive().'</b>'; 
                    }
                    if($ordenes[$i]->isLimitHour())
                    {
                       $result['data'][$cont]['tiempo']= '<b style="color:red;"><i class="ion ion-ios7-alarm"></i> '.$ordenes[$i]->timeSinceArrive().'</b>'; 
                    }
                    if($ordenes[$i]->isOutOfTimeAlert())
                    {
                       $result['data'][$cont]['tiempo']= '<b class="text-black"><i class="ion ion-ios7-alarm"></i> '.$ordenes[$i]->timeSinceArrive().'</b>'; 
                    }
                    
                    $result['data'][$cont]['timetoproccess']= $ordenes[$i]->timeSinceArrive();
                   $cont++;
           // } 
            
        }
        
        
      // print_r($ordenes[0]->getPrioridad().'-'.$ordenes[1]->getPrioridad().'-'.$ordenes[2]->getPrioridad());exit;
      // print_r($result);exit;
        return new Response(json_encode($result));
    }
    public function applyFilterDate($ordenes,$start,$end)
    {
       $result=array();
       foreach ($ordenes as $o) {
            $fecha=$o->getFecha();
            if(($fecha>=$start) && ($fecha<=$end))
            {
                $result[]=  $o;
            }
       }
       return $result;
    }
    public function applyFilterSearch($ordenes,$filter)
    {
      
      $filter=strtoupper($filter);
       $result=array();
       foreach ($ordenes as $o) {
          
            if(preg_match('/'.$filter.'/',strtoupper($o->getTipo())) ||
               preg_match('/'.$filter.'/',strtoupper($o->getFecha()->format('d M Y H:i:s'))) ||
               preg_match('/'.$filter.'/',strtoupper($o->getAgente()->getAgencia()->getNombre())) ||
               preg_match('/'.$filter.'/',strtoupper($o->getAgente()->__toString())) ||     
               preg_match('/'.$filter.'/',strtoupper($o->timeSinceArrive())) ||
               preg_match('/'.$filter.'/',strtoupper($o->getNumeroOrden())) || 
               preg_match('/'.$filter.'/',strtoupper($o->getPrioridad())))     
                    
            {
                $result[]=  $o;
            }
                
           
       }
       return $result;
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
    
    public function renderReporteAction()
    {
        $em = $this->getDoctrine()->getManager();
        $estados=$em->getRepository('EmisionesBundle:Estadoorden')->findAll();
        $agentes=$em->getRepository('EmisionesBundle:Agente')->findAll();
        return $this->render('EmisionesBundle:Counter/Reportes:reporte.html.twig',array(
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
       $rango = explode('-', $rango);
       $start = $rango[0];
       $end = $rango[1];
       $start= new \DateTime($start);
       $end = new \DateTime($end);
       $em = $this->getDoctrine()->getManager();
       $ordenes=  $this->getUser()->getEmpresa()->getOrdenes();
       //Aplicar filtro de estado
        $ordenes=  $this->applyFilterState($ordenes, $estado);        
        //Aplicar filtro de agente
        $ordenes = $this->applyFilterAgente($ordenes, $agente);
       //Aplicar filtro de counter
        $ordenes = $this->applyFilterCounter($ordenes, $usuario);
        $tipos= array('Emision','Anulacion','Revision');
        
        $total=array();
        for ($x = 0; $x < count($tipos); $x++) {
            $cont=0;
            for ($z = 0; $z < count($ordenes); $z++) {
                if(($ordenes[$z]->getTipo()==$tipos[$x]) && (($ordenes[$z]->getFecha()>=$start) &&  ($ordenes[$z]->getFecha()<=$end)))
                {
                    $cont++;
                }
            }
            $total[]= array('name'=>$tipos[$x],'y'=>$cont);            
        }
       return new Response(json_encode($total));
   }
   public function applyFilterCounter($ordenes,$filtro)
   {
            $result=array();
           foreach ($ordenes as $item) {
              if($item->getUsuario() && $item->getUsuario()->getId()==$filtro) 
              {
                $result[]=  $item;
              }
           }
       return $result;
   }
   public function applyFilterAgente($ordenes,$filtro)
   {
       $result=array();
       if($filtro=='all')
       {
           foreach ($ordenes as $item) {
              if($item->getAgente()) 
              {
                $result[]=  $item;
              }
           }
       }
      else
       {
           foreach ($ordenes as $item) {
              if($item->getAgente() && $item->getAgente()->getId()==$filtro) 
              {
                $result[]=  $item;
              }
           }
       }
       
       return $result;
   }
    public function renderMailAction($entity)//se pasa para que tambien pueda generar el pdf en la vist del mail
    {
        
        $em = $this->getDoctrine()->getManager();
        $orden=$em->getRepository('EmisionesBundle:Orden')->find($entity);
        if(!$orden)
        {
            $this->get('session')->getFlashBag()->add('error', 'La orden no existe o ha sido eliminada, contacte al administrador del sistema!');
             return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard'));
        }
        return $this->render('EmisionesBundle:Counter/Mail:mail.html.twig', array(
            'entity'=>$orden
        ));
    } 
    public function makePdfAction()
    {
       
        $peticion = $this->getRequest();
        $style = $peticion->request->get('style');
        $content = $peticion->request->get('content');
        $title=$peticion->request->get('title');
        $nombre=$peticion->request->get('nombre').'.pdf';
       // if(!file_exists($this->container->getParameter('aplicacion.directorio.documentos.pdf').$nombre))
       // {
            
            //Genero el documento
            $content='
                    <!doctype html>
                    <html>
                    <head>        
                        '.$style.'
                    </head>
                    <body>'.
                        '<page_header>
                    <table class="page_header">
                        <tr>
                            <td style="width: 40%; text-align: left;">
                               <br>
                                    <img src="./bundles/base/img/logos/logomym.png" alt="Logo HTML2PDF" style="width:40%">
                                    <br>
                            </td>
                            <td style="width: 30%;">
                                <b style="font-size: 10px;float: right;">'.$title.'</b>                    
                            </td>
                             <td style="width: 30%;text-align: right;">
                                 <b style="font-size: 10px;text-align: right;">KOBRA Queue System v0.1</b>
                                 <hr style="width: 10%;"/><b style="font-size: 10px;text-align: right;">'.  date('d-M-Y H:i:s').'</b>

                            </td>


                        </tr>
                    </table>

                </page_header>
                <page_footer>
                    <table class="page_footer">
                        <tr>
                            <td style="width: 33%; text-align: left;">
                                <address style="margin-top: -5px;">
                                    <p style="font-style: oblique;font-size: 10px;text-decoration: underline;"><b>Direcci&oacute;n:</b>
                                    <span>Edificio Pinzón Piso 2,
                                        Av. Yánez Pinzón N26-243 y Av. Francisco de Orellana</span>
                                    <p ><b>Tel&eacute;fono:</b> 
                                    <span> +593 22983840</span>
                                    </p> 
                                    </p> 
                                </address>
                            </td>
                            <td style="width: 34%; text-align: center">
                                <span style="font-size: 10px;"> P&aacute;gina [[page_cu]] de [[page_nb]] </span>
                            </td>
                            <td style="width: 33%; text-align: right">
                                <a style="text-decoration-line:none;font-size: 10px;text-decoration: none; " href="http://www.mymtravel.com"> &copy; M&M Travel Group </a>
                            </td>
                        </tr>
                    </table>
                </page_footer>'.$content.
                    '</body>
                    </html>';

                     $path = $this->container->getParameter('aplicacion.directorio.documentos.pdf');
                   ob_start();        
                    echo '<page>'.$content.'</page>';
                    $content = ob_get_clean(); 
                    //Orientación / formato del pdf y el idioma.
                    $pdf = new HTML2PDF('P','A4','es'/*, array(10, 10, 10, 10) /*márgenes*/); //los márgenes también pueden definirse en <page> ver documentación
                    $pdf->WriteHTML($content);
                    $pdf->Output($path.$nombre, 'F'); //guardar archivo ( ¡ojo! si ya existe lo sobreescribe )
        //}
        
        return new Response($this->get('request')->getBasePath().'/uploads/pdf/'.$nombre);
    }
    function crearNombre($length)
    {
        if( ! isset($length) or ! is_numeric($length) ) $length=6;

        $str  = "0123456789abcdefghijklmnopqrstuvwxyz";
        $path = '';

        for($i=1 ; $i<$length ; $i++)
          $path .= $str{rand(0,strlen($str)-1)};

        return $path.'_'.date("d-m-Y_H-i-s").'.pdf';    
    }
    public function sendMailAction()
    {
        
        $peticion = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $orden=$em->getRepository('EmisionesBundle:Orden')->find($peticion->request->get('entity'));
        $to = $peticion->request->get('email_to');
        $cc = $peticion->request->get('email_cc');
        $bcc= $peticion->request->get('email_bcc');
        $subject=$peticion->request->get('email_subject');
        $from=$peticion->request->get('sender');
        $cuerpo=$peticion->request->get('email_message');
        $adjunto='';
        $allowed = array('png', 'jpg', 'gif','zip','doc','docx','pdf','jpeg','ppt'
            ,'rar','txt','xlsx','xls','csv');
        $path = $this->container->getParameter('aplicacion.directorio.documentos.adjuntos');
        if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0)
            {

                $extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
               // print_r($extension);exit;
                if(!in_array(strtolower($extension), $allowed)){
                       $this->get('session')->getFlashBag()->add('error', 'El fichero que usted adjunta no es permitido!');
                       return $this->render('EmisionesBundle:Counter/Email:mail.html.twig', array('entity'=>$orden));
                }

                if(move_uploaded_file($_FILES['upl']['tmp_name'], $path.$_FILES['upl']['name'])){
                        $adjunto=$path.$_FILES['upl']['name'];
                }
            }
          
        $message = \Swift_Message::newInstance()
        ->setFrom(array($from=>$from))   
        ->addReplyTo($from)
        ->setSubject($subject)
        ->setTo($to)
        ->setBody($cuerpo);
        
        if(isset($cc)&& $cc!='')
        {
           $message->setCc($cc);
        }
        if(isset($bcc)&& $bcc!='')
        {
           $message->setBcc($bcc);
        }
        if(isset($adjunto) && $adjunto !='')
        {
            $message->attach(\Swift_Attachment::fromPath($adjunto));
        }
        
       if( $this->get('mailer')->send($message))
       {
           //print_r('entro');exit;
           $this->get('session')->getFlashBag()->add('success', 'Su mensaje se ha enviado correctamente!');
           if(file_exists($adjunto))
           {
               unlink($adjunto);
           }
       }
       else
       {
           $this->get('session')->getFlashBag()->add('error', 'Ha ocurrido un error al enviar su mensaje!');
       }
       
        
        
   
    return $this->render('EmisionesBundle:Counter/Mail:mail.html.twig', array('entity'=>$orden));
       
    }
    
     /**
     * Finds and displays a Emision entity.
     *
     */
    public function showEmisionAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Emision')->find($id);

        if (!$entity) {
           $this->get('session')->getFlashBag()->add('error', 'La emision que usted desea abrir no existe o ha sido eliminada!');
                       return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard'));
        }

        return $this->render('EmisionesBundle:Counter/Emision:show.html.twig', array(
            'entity'      => $entity,
        ));
    }
     public function showAnulacionAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Anulacion')->find($id);
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'La anulacion que usted desea procesar no existe o ha sido eliminada!');
                       return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard'));
        }
  return $this->render('EmisionesBundle:Counter/Anulacion:show.html.twig', array(
            'entity'      => $entity
        ));
    }
     public function showRevisionAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Revision')->find($id);
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'La revision que usted desea procesar no existe o ha sido eliminada!');
                       return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard'));
        }
  return $this->render('EmisionesBundle:Counter/Revision:show.html.twig', array(
            'entity'      => $entity
        ));
    }

    /**
     * Displays a form to edit an existing Emision entity.
     *
     */
    public function editEmisionAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Emision')->find($id);
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'La emision que usted desea procesar no existe o ha sido eliminada!');
                       return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard'));
        }

        $editForm = $this->createEditEmisionForm($entity);
        //$deleteForm = $this->createDeleteForm($id);
	//print_r($entity->getFormasPagos()[0]->getTipo());exit;
        return $this->render('EmisionesBundle:Counter/Emision:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()            
        ));
    }
    public function editAnulacionAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Anulacion')->find($id);
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'La anulacion que usted desea procesar no existe o ha sido eliminada!');
                       return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard'));
        }

        $editForm = $this->createEditAnulacionForm($entity);
        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Counter/Anulacion:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()            
        ));
    }
    public function editRevisionAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Revision')->find($id);
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'La revision que usted desea procesar no existe o ha sido eliminada!');
                       return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard'));
        }

        $editForm = $this->createEditRevisionForm($entity);
        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Counter/Revision:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()            
        ));
    }

    /**
    * Creates a form to edit a Emision entity.
    *
    * @param Emision $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditEmisionForm(Emision $entity)
    {
        $form = $this->createForm(new EmisionType(), $entity, array(
            'action' => $this->generateUrl('counter_emision_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        return $form;
    }
    private function createEditAnulacionForm(Anulacion $entity)
    {
        $form = $this->createForm(new AnulacionType(), $entity, array(
            'action' => $this->generateUrl('counter_anulacion_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        return $form;
    }
    private function createEditRevisionForm(Revision $entity)
    {
        $form = $this->createForm(new RevisionType(), $entity, array(
            'action' => $this->generateUrl('counter_revision_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        return $form;
    }
    /**
     * Edits an existing Emision entity.
     *
     */
    public function updateEmisionAction(Request $request, $id)
    {
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Emision')->find($id);
        $estadoviejo=$entity->getEstado()->getNombre();
        $fechaoriginal=$entity->getFecha();
        if (!$entity) {
             $this->get('session')->getFlashBag()->add('error', 'La emision que usted desea procesar no existe o ha sido eliminada!');
             return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard'));
        }
        
        //$deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditEmisionForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) { 
            //Validar si el nuevo estado al que se va a setear la orden es procesada
            // verificar a que hora cayo y si la hora actual es > que las hora finaliza la atencion
            // y entonces seteao a true la procesada Emergencia
            if($estadoviejo== 'Pendiente' && $editForm->getData()->getEstado()->getNombre()=='Procesada')
            { 
               //Si la empresa tiene configuracion activa
                if($this->getUser()->getEmpresa()->getConfiguracionActiva()!= null)
                {
                    if(strtotime($fechaoriginal->format('Y-m-d H:i:s')) >= $this->getUser()->getEmpresa()->getConfiguracionActiva()->getFinHorarioAtencion()->format('U'))
                    {
                        //La orden cayo en horario de emergencia
                        $entity->setProcesadaEmergencia(true);
                    }
                }
               
            }
            
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Operacion realizada satisfactoriamente!');
            return $this->redirect($this->generateUrl('counter_emision_edit', array('id' => $id)));
        }
        else
        {
            $this->get('session')->getFlashBag()->add('error', 'Ha ocurrido un error, por favor revisar detalladamente los valores proporcionados!');
        }

        return $this->render('EmisionesBundle:Counter/Emision:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()            
        ));
    }
    public function updateAnulacionAction(Request $request, $id)
    {
       
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Anulacion')->find($id);
        $estadoviejo=$entity->getEstado()->getNombre();
        $fechaoriginal=$entity->getFecha();
        if (!$entity) {
             $this->get('session')->getFlashBag()->add('error', 'La anulacion que usted desea procesar no existe o ha sido eliminada!');
             return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard'));
        }
        
        //$deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditAnulacionForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) { 
            //Validar si el nuevo estado al que se va a setear la orden es procesada
            // verificar a que hora cayo y si la hora actual es > que las hora finaliza la atencion
            // y entonces seteao a true la procesada Emergencia
            if($estadoviejo== 'Pendiente' && $editForm->getData()->getEstado()->getNombre()=='Procesada')
            { 
               //Si la empresa tiene configuracion activa
                if($this->getUser()->getEmpresa()->getConfiguracionActiva()!= null)
                {
                    if(strtotime($fechaoriginal->format('Y-m-d H:i:s')) >= $this->getUser()->getEmpresa()->getConfiguracionActiva()->getFinHorarioAtencion()->format('U'))
                    {
                        //La orden cayo en horario de emergencia
                        $entity->setProcesadaEmergencia(true);
                    }
                }
               
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Operacion realizada satisfactoriamente!');
            return $this->redirect($this->generateUrl('counter_anulacion_edit', array('id' => $id)));
        }
        else
        {
            $this->get('session')->getFlashBag()->add('error', 'Ha ocurrido un error, por favor revise detalladamente los valores proporcionados!');
        }

        return $this->render('EmisionesBundle:Counter/Anulacion:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()            
        ));
    }
    public function updateRevisionAction(Request $request, $id)
    {
       
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Revision')->find($id);
        $estadoviejo=$entity->getEstado()->getNombre();
        $fechaoriginal=$entity->getFecha();
        if (!$entity) {
             $this->get('session')->getFlashBag()->add('error', 'La revision que usted desea procesar no existe o ha sido eliminada!');
             return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard'));
        }
       
        $editForm = $this->createEditRevisionForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) { 
            //Validar si el nuevo estado al que se va a setear la orden es procesada
            // verificar a que hora cayo y si la hora actual es > que las hora finaliza la atencion
            // y entonces seteao a true la procesada Emergencia
            if($estadoviejo== 'Pendiente' && $editForm->getData()->getEstado()->getNombre()=='Procesada')
            { 
               //Si la empresa tiene configuracion activa
                if($this->getUser()->getEmpresa()->getConfiguracionActiva()!= null)
                {
                    if(strtotime($fechaoriginal->format('Y-m-d H:i:s')) >= $this->getUser()->getEmpresa()->getConfiguracionActiva()->getFinHorarioAtencion()->format('U'))
                    {
                        //La orden cayo en horario de emergencia
                        $entity->setProcesadaEmergencia(true);
                    }
                }
               
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Operacion realizada satisfactoriamente!');
            return $this->redirect($this->generateUrl('counter_revision_edit', array('id' => $id)));
        }
        else
        {
            $this->get('session')->getFlashBag()->add('error', 'Ha ocurrido un error, por favor revise detalladamente los valores proporcionados!');
        }

        return $this->render('EmisionesBundle:Counter/Revision:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()            
        ));
    }
    public function indexFormasPagoAction($orden)
    {
        $em = $this->getDoctrine()->getManager();
        $orden=$em->getRepository('EmisionesBundle:Orden')->find($orden);
        if(!$orden)
        {
            $this->get('session')->getFlashBag()->add('error', 'La orden no existe o ha sido eliminada!');
            return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard', array()));
        }
        $entities = $em->getRepository('EmisionesBundle:Formapago')->findBy(array('orden'=>$orden));

        return $this->render('EmisionesBundle:Counter/FormasPago:indexFormasPago.html.twig', array(
            'entities' => $entities,
            'entity'=>$orden
        ));
    }
     public function showTarjetaCreditoAction($id,$orden)
    {
        $em = $this->getDoctrine()->getManager();
        $orden=$em->getRepository('EmisionesBundle:Orden')->find($orden);
        if(!$orden)
        {
            $this->get('session')->getFlashBag()->add('error', 'La orden no existe o ha sido eliminada!');
            return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard', array()));
        }
        $entity = $em->getRepository('EmisionesBundle:Tarjetacredito')->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'La forma de pago no existe o ha sido eliminada!');
            return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard', array()));
        }

        return $this->render('EmisionesBundle:Counter/FormasPago/TarjetaCredito:show.html.twig', array(
            'fp'      => $entity,
            'entity'=>$orden
        ));
    }
    public function showPagoDirectoAction($id,$orden)
    {
        $em = $this->getDoctrine()->getManager();
        $orden=$em->getRepository('EmisionesBundle:Orden')->find($orden);
        if(!$orden)
        {
            $this->get('session')->getFlashBag()->add('error', 'La orden no existe o ha sido eliminada!');
            return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard', array()));
        }
        $entity = $em->getRepository('EmisionesBundle:Pagodirecto')->find($id);

        if (!$entity) {
           $this->get('session')->getFlashBag()->add('error', 'La forma de pago no existe o ha sido eliminada!');
            return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard', array()));
        }
        return $this->render('EmisionesBundle:Counter/FormasPago/PagoDirecto:show.html.twig', array(
            'entity'      => $orden,
            'fp'=>$entity
        ));
    }
    public function showDETBAction($id,$orden)
    {
        $em = $this->getDoctrine()->getManager();
        $orden=$em->getRepository('EmisionesBundle:Orden')->find($orden);
        if(!$orden)
        {
            $this->get('session')->getFlashBag()->add('error', 'La orden no existe o ha sido eliminada!');
            return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard', array()));
        }
        $entity = $em->getRepository('EmisionesBundle:DepefectivoTransferenciabancaria')->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'La forma de pago no existe o ha sido eliminada!');
            return $this->redirect($this->generateUrl('EmisionesBundle_counter_dashboard', array()));
        }
        return $this->render('EmisionesBundle:Counter/FormasPago/DETB:show.html.twig', array(
            'entity'      => $orden,
            'fp'=>$entity
        ));
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
 
    
   /*
    * Funcion que aplica un flitro Tipo
    */     
   public function applyFilterType($ordenes,$filtro)
   {
       $result=array();
       if($filtro=='all')
       {
           $result= $ordenes;
       }
       else
       {
           
           foreach ($ordenes as $item) {
              if($item->getTipo()==$filtro) 
              {
                $result[]=  $item;
              }
           }
       }
       return $result;
   }
   public function applyFilterState($ordenes,$filtro)
   {
      
       $result=array();
       if($filtro=='all')
       {
          
           $result= $ordenes;
       }
       else
       {
           
           foreach ($ordenes as $item) {
              if($item->getEstado()->getNombre()==$filtro) 
              {
                $result[]=  $item;
              }
           }
       }
       //print_r(count($result));exit;
       return $result;
   }
  
   public function reasignarOrdenAction()
   {
        $peticion = $this->getRequest();
        $numeroOrden = $peticion->request->get('orden');
        //print_r($numeroOrden);exit;
        $em=$this->getDoctrine()->getManager();
        $orden=$em->getRepository('EmisionesBundle:Orden')->findOneBy(array('numeroOrden'=>$numeroOrden));
        $result=array();
        if(!$orden)
        {
            $result[0]='danger';
            $result[1]='La orden no existe o ha sido eliminada.';
           return new Response(json_encode($result)); 
        }
        $counter=$em->getRepository('EmisionesBundle:Usuariointerno')->find($orden->getUsuario()->getId());
        if(!$counter)
        {
            $result[0]='danger';
            $result[1]='El counter no existe o ha sido eliminado.';
           return new Response(json_encode($result)); 
        }
        $result[0]='success';
        $result[1]='Orden '.$numeroOrden.' reasignada a la cola de la empresa <b>'.$this->getUser()->getEmpresa()->getRazonsocial().'</b> satisfactoriamente';
        $orden->setUsuario(null);
        $em->persist($orden);
        $em->flush();
        //print_r($orden->getUsuario()->getId());exit;
        
        return new Response(json_encode($result));
   }
   public function asignarOrdenManualAction()
   {
        $peticion = $this->getRequest();
        $numeroOrden = $peticion->request->get('orden');
        $counter=$peticion->request->get('counter');
        //print_r($numeroOrden);exit;
        $em=$this->getDoctrine()->getManager();
        $orden=$em->getRepository('EmisionesBundle:Orden')->findOneBy(array('numeroOrden'=>$numeroOrden));
        $result=array();
        if(!$orden)
        {
            $result[0]='danger';
            $result[1]='La orden no existe o ha sido eliminada.';
           return new Response(json_encode($result)); 
        }
        $counter=$em->getRepository('EmisionesBundle:Usuariointerno')->find($counter);
        if(!$counter)
        {
            $result[0]='danger';
            $result[1]='El counter no existe o ha sido eliminado.';
           return new Response(json_encode($result)); 
        }
        $result[0]='success';
        $result[1]='Orden '.$numeroOrden.' asignada satisfactoriamente';
        $orden->setUsuario($counter);
        $em->persist($orden);
        $em->flush();
        
        //print_r(json_encode($result));exit;
        
        return new Response(json_encode($result));
   }
   
}
