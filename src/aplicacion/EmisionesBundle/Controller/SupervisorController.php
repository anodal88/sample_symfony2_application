<?php

namespace aplicacion\EmisionesBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Controller\RegistrationController;
use aplicacion\EmisionesBundle\Entity\Agente;
use aplicacion\EmisionesBundle\Entity\Usuariointerno;
use aplicacion\EmisionesBundle\Entity\Agencia;
use aplicacion\BaseBundle\Entity\Empresa;
use aplicacion\BaseBundle\Entity\User AS BaseUser;
use aplicacion\EmisionesBundle\Form\SupervisorAgenteType;
use aplicacion\EmisionesBundle\Form\SupervisorCounterType;
use aplicacion\EmisionesBundle\Form\AgenteType;
use aplicacion\EmisionesBundle\Form\AgenciaType;
use aplicacion\EmisionesBundle\Form\SupervisorAgenteEditClaveType;
use aplicacion\EmisionesBundle\Form\SupervisorCounterEditClaveType;
use aplicacion\EmisionesBundle\Form\SupervisorConfiguracionType;
use aplicacion\BaseBundle\Form\EmpresaType;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserInterface;
use aplicacion\BaseBundle\Entity\Configuracion;
use aplicacion\EmisionesBundle\Entity\Anulacion;
use aplicacion\EmisionesBundle\Entity\Revision;
use aplicacion\EmisionesBundle\Entity\Emision;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Column\BlankColumn;
use APY\DataGridBundle\Grid\Column\NumberColumn;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Export\CSVExport;
use aplicacion\EmisionesBundle\Entity\Tarjetacredito;


/**
 * Usuariointerno controller.
 *
 */
class SupervisorController extends RegistrationController
{

   public function asignarManualAction($id)
   {
         $em=  $this->getDoctrine()->getManager();
         $peticion = $this->getRequest();
         $counter = $peticion->request->get('counters');
         $orden=$em->getRepository('EmisionesBundle:Orden')->find($id);
         $qb = $em->createQueryBuilder();
                $qb->select('u')->from("EmisionesBundle:Usuariointerno", 'u')->where($qb->expr()->like('u.roles', $qb->expr()->literal("%ROLE_COUNTER%")))
                    ->andWhere('u.enabled = :estado')
                    ->andWhere('u.empresa = :empresa')
                    ->setParameter('empresa', $this->getUser()->getEmpresa())
                    ->setParameter('estado', true);
         $enabledCounters=$qb->getQuery()->getResult();
       
         if(!$orden)
         {
             $this->get('session')->getFlashBag()->add('error', 'La orden deseada no existe o ha sido eliminada!');
             return $this->redirect($this->generateUrl('EmisionesBundle_queue_manger'));
         }
         
         if(count($enabledCounters)==0)
         {
            $this->get('session')->getFlashBag()->add('error', 'No existen counters habilitados para asignarle la presente orden!');
             return $this->redirect($this->generateUrl('EmisionesBundle_queue_manger')); 
         }
         if ($peticion->getMethod() == 'POST') {
                if($counter==-1 || is_null($counter))
                {
                    $this->get('session')->getFlashBag()->add('error', 'El counter seleccionado no es valido para la asignacion de la orden!');
                    return $this->redirect($this->generateUrl('supervisor_asignar_manual', array('id'=>$id,'counters'=>$enabledCounters,'orden'=>$orden)));
                }
                else
                {
                    $counter=$em->getRepository('EmisionesBundle:Usuariointerno')->find($counter);
                    if(!$counter || $counter->isEnabled()==false)
                    {
                        $this->get('session')->getFlashBag()->add('error', 'El counter seleccionado no existe o esta deshabilitado!');
                        return $this->redirect($this->generateUrl('supervisor_asignar_manual', array('id'=>$id,'counters'=>$enabledCounters,'orden'=>$orden)));
                    }
                    $orden->setUsuario($counter);
                    /*agregado para tener en cuenta a que hora se reasigno la orden*/
                    $orden->setHoraAsignacion(new \DateTime());
                    /*agregado para tener en cuenta a que hora se reasigno la orden*/
                    $em->persist($orden);
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('success', 'Operacion realizada satisfactoriamente!');
                    return $this->redirect($this->generateUrl('EmisionesBundle_queue_manger'));
                }
            }
         
       return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AsignarManual:asignarmanual.html.twig',
               array(
                   'id'=>$id,'counters'=>$enabledCounters,'orden'=>$orden
                   ));
   }
   public function indexSupervisorAction()
   {
       return $this->render('EmisionesBundle:SupervisorAdministrarAgentes:supervisoremisiones.html.twig',array());
   }
   public function indexAction()
    {
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarAgentes:index.html.twig', array(
           
        ));
    }
   public function indexDaemonsAction()
    {
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/Daemons:index.html.twig', array(
           
        ));
    }
   public function indexCountersAction()
    {
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarCounters:index.html.twig', array(
           
        ));
    }
    public function loadPieAction()
    {
        $peticion = $this->getRequest();
        $rango = $peticion->request->get('rango');
        $rango = explode('-', $rango);
        $start = $rango[0];
        $end = $rango[1];
        $start=new \DateTime($start);       
        $end=new \DateTime($end);      
        $em = $this->getDoctrine()->getManager();       
        
        $estados = $em->getRepository('EmisionesBundle:Estadoorden')->findAll();        
                
        $total=array();
        for ($x = 0; $x < count($estados); $x++) {
           
            $total[]= array('name'=>$estados[$x]->__toString(),'y'=>  floatval($em->getRepository('EmisionesBundle:Orden')->TotalEstados($start,$end,$estados[$x]->getId())));            
        }
        
        return new Response(json_encode( $total));
    }
   public function loadTimeAverageAction()
    {
        $peticion = $this->getRequest();
        $emergencia=$peticion->request->get('emergencia');
        if($emergencia=='noemergencias')
        {$emergencia=false;}else{$emergencia=true;}        
        $rango = $peticion->request->get('rango');
        $rango = explode('-', $rango);
        $start = $rango[0];
        $end = $rango[1];
        $start=new \DateTime($start);       
        $end=new \DateTime($end);      
        $em = $this->getDoctrine()->getManager(); 
        $result=$em->getRepository('EmisionesBundle:Orden')->globalAverage(false,$this->getUser()->getEmpresa()->getId(),$emergencia,$start,$end);
        $total=array();
        $gce=0;$gcr=0;$gca=0;
        for ($i=0;$i<count($result);$i++)
        {
            $total['categories'][$i]=$result[$i]['nombre'].' '.$result[$i]['apellidos'];
            $tmpE[$i]=round(floatval($result[$i]['TAE']),1);
            $tmpR[$i]=round(floatval($result[$i]['TAR']),1);
            $tmpA[$i]=round(floatval($result[$i]['TAA']),1);
            $ce=0;$cr=0;$ca=0;
            if($tmpE[$i]>0)
            {$ce++;$gce++;}
            if($tmpR[$i]>0)
            {$cr++;$gcr++;}
            if($tmpA[$i]>0)
            {$ca++;$gca++;}
            if(($ce+$cr+$ca)==0)
            {
               $tmpAvg[$i]=round(($tmpE[$i]+$tmpR[$i]+$tmpA[$i])/3,1);
            }
            else
            {
                $tmpAvg[$i]=round(($tmpE[$i]+$tmpR[$i]+$tmpA[$i])/($ce+$cr+$ca),1);
            }
        }
 
        $total['series'][0]['type']='column';
        $total['series'][0]['name']='Emision';
        $total['series'][0]['data']=$tmpE;
        
        $total['series'][1]['type']='column';
        $total['series'][1]['name']='Revision';
        $total['series'][1]['data']=$tmpR;
        
        $total['series'][2]['type']='column';
        $total['series'][2]['name']='Anulacion';
        $total['series'][2]['data']=$tmpA;
    
        $total['series'][3]['type']='spline';
        $total['series'][3]['name']='Average';
        $total['series'][3]['data']=$tmpAvg;
       //datos para el mini pie
        $promPie[0]['name']='Emision';
        if($gce!=0)
        {
         $promPie[0]['y']=  round(array_sum($tmpE)/$gce,1);
        }else
        {
         $promPie[0]['y']=  round(array_sum($tmpE)/count($tmpE),1); 
        }
        $promPie[1]['name']='Revision';
        if($gcr!=0)
        {
         $promPie[1]['y']=  round(array_sum($tmpR)/$gcr,1);
        }else
        {
         $promPie[1]['y']=  round(array_sum($tmpR)/count($tmpR),1); 
        }
        $promPie[2]['name']='Anulacion';
        if($gca!=0)
        {
         $promPie[2]['y']= round(array_sum($tmpA)/$gca,1);
        }else
        {
         $promPie[2]['y']= round(array_sum($tmpA)/count($tmpA),1); 
        }
        $total['series'][4]['type']='pie';
        $total['series'][4]['name']='Promedio Total';
        $total['series'][4]['data']= $promPie;
        //config mini pie
        $total['series'][4]['center']=array(0=>100,1=>80);
        $total['series'][4]['size']=100;
        $total['series'][4]['showInLegend']=false;
        $total['series'][4]['dataLabels']=array('enabled'=>false);
        return new Response(json_encode($total));
    }
    public function loadBarAction()
    {
        $peticion = $this->getRequest();
        $rango = $peticion->request->get('rangobar');
        $rango = explode('-', $rango);
        $start = $rango[0];
        $end = $rango[1];
        $start=new \DateTime($start);       
        $end=new \DateTime($end);      
        $em = $this->getDoctrine()->getManager();       
        
        $counters=  $this->getUser()->getEmpresa()->getCounters();
        $availableCounters=array();
        $categories = array();//Todos los counter de la empresa que se vana mostrar
        for ($x = 0; $x < count($counters); $x++) {
            if(!$counters[$x]->isLocked())
            {
                $categories[]=$counters[$x]->__toString();
                $availableCounters[]=$counters[$x];
            }
        }
        $total=array();
        $total['categories']=$categories;
        $estados=$em->getRepository('EmisionesBundle:Estadoorden')->findAll();
        for ($i = 0; $i < count($estados); $i++) {
            $temp=array();
            for ($j = 0; $j < count($availableCounters); $j++) {
                $temp[]= floatval($em->getRepository('EmisionesBundle:Orden')->TotalEstados($start,$end,$estados[$i]->getId(),$availableCounters[$j]->getId()));
            }
           $total['datos'][]= array('name'=>$estados[$i]->__toString(),'data'=>$temp);
        }
      
        return new Response(json_encode( $total));
    }
    /*
     * Funcion para maximizar la visata del reporte porcentaje vs estados
     */
    public function maximizarPorcentajeVsEstadosAction()
    {
       return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/Reportes:porcentajeVsEstados.html.twig'); 
    }
    /*
     * Funcion para maximizar la vista del reporte de estados vs counters
     */
    public function maximizarEstadosVsCountersAction()
    {
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/Reportes:estadosVsCounters.html.twig'); 
    }
    /*
     * Funcion para pintar las emisiones
     */
    public function allOrdersAction()
    {
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/Ordenes:allOrders.html.twig'); 
    }
    /*
     * Funcion para maximizar la vista del reporte tipos vs counters
     */
    public function maximizarTiposVsCountersAction()
    {
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/Reportes:tiposVsCounters.html.twig'); 
    }
     /*
     * Funcion para maximizar la vista del reporte time average
     */
    public function maximizarTimeAverageAction()
    {
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/Reportes:promediotiempoemision.html.twig'); 
    }


    public function loadLineAction()
    {
        $peticion = $this->getRequest();
        $rango = $peticion->request->get('rangoline');
        $rango = explode('-', $rango);
        $start = $rango[0];
        $end = $rango[1];
        $start=new \DateTime($start);       
        $end=new \DateTime($end);      
        $em = $this->getDoctrine()->getManager(); 
        $counters=  $this->getUser()->getEmpresa()->getCounters();
        $availableCounters=array();
        $categories = array();//Todos los counter de la empresa que se vana mostrar
        for ($x = 0; $x < count($counters); $x++) {
            if(!$counters[$x]->isLocked())
            {
                $categories[]=$counters[$x]->__toString();
                $availableCounters[]=$counters[$x];
            }
        }
        $total=array();
        $total['categories']=$categories;

       $childs=array(0=>'Emision',1=>'Revision',2=>'Anulacion');//buscar la amenra de cargarlo dinamico
        for ($i = 0; $i < count($childs); $i++) {
            $temp=array();            
            for ($j = 0; $j < count($availableCounters); $j++) {
                  $temp[]= floatval($em->getRepository('EmisionesBundle:'.$childs[$i] )->TotalTipo($start,$end,null,$availableCounters[$j]->getId()));
                }
                $total['datos'][]= array('name'=>$childs[$i],'data'=>$temp);
            }
          
        return new Response(json_encode( $total));
    }
    
    public function indexAgenciasAction()
    {
        
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarAgencias:indexAgencias.html.twig', array(
          
        ));
    }
     public function indexPlanPilotoAction()
    {
        $em=  $this->getDoctrine()->getManager();
        
        $agencias=  $this->getUser()->getEmpresa()->getAgencias();
        $aerolineas=$em->getRepository('EmisionesBundle:Aerolinea')->findAll(array(),array('id' => 'ASC'));
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/PlanesPilotos:index.html.twig', array(
          'aerolineas'=>$aerolineas,
          'agencias'=>$agencias
        ));
    }
  
    
    public function indexColaAction()
    {
        $em=$this->getDoctrine()->getManager();
        $estados= $em->getRepository('EmisionesBundle:Estadoorden')->findAll();
        $counters=  $this->getUser()->getEmpresa()->getCounters();
        $configuracionActiva=  $em->getRepository('BaseBundle:Configuracion')->findOneBy(array('empresa'=>  $this->getUser()->getEmpresa(),'activa'=>true));
        $enabledCounters=$this->getUser()->getEmpresa()->getEnabledCounters();
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/Cola:indexCola.html.twig', array(
           'estados'=>$estados,
           'counters'=>$counters,
           'enabledCounters'=>$enabledCounters,
           'configuracionactiva'=>$configuracionActiva
        ));
    }
    public function indexConfiguracionesAction()
    {
        //$em=$this->getDoctrine()->getManager();
        //$estados= $em->getRepository('EmisionesBundle:Estadoorden')->findAll();
        //$counters=  $this->getUser()->getEmpresa()->getCounters();
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarConfiguraciones:index.html.twig', array(
           //'estados'=>$estados,
           //'counters'=>$counters
        ));
    }
    
    private function createEditForm(Agente $entity,$empresa_id)
    {
        //print_r('aki etuvo');exit;
        $form = $this->createForm(new SupervisorAgenteType(), $entity, array(
            'action' => $this->generateUrl('supervisor_agente_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'intention'=>'editar',
            'attr'=>array('empresa'=>$empresa_id)
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    private function createEditConfiguracionForm(Configuracion $entity,$empresa_id)
    {
        //print_r('aki etuvo');exit;
        $form = $this->createForm(new SupervisorConfiguracionType(), $entity, array(
            'action' => $this->generateUrl('supervisor_configuracion_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'intention'=>'editar',
            'attr'=>array('empresa'=>$empresa_id)
        ));
        return $form;
    }
    private function createEditCounterForm(Usuariointerno $entity,$empresa_id)
    {
        //print_r('aki etuvo');exit;
        $form = $this->createForm(new SupervisorCounterType(), $entity, array(
            'action' => $this->generateUrl('supervisor_counter_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'intention'=>'editar',
            'attr'=>array('empresa'=>$empresa_id)
        ));
        return $form;
    }
    private function createEditAgenciaForm(Agencia $entity)
    {
      
        $form = $this->createForm(new AgenciaType(), $entity, array(
            'action' => $this->generateUrl('supervisor_agencia_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        return $form;
    }
    private function createEditEmpresaForm(Empresa $entity)
    {
      
        $form = $this->createForm(new EmpresaType(), $entity, array(
            'action' => $this->generateUrl('supervisor_empresa_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        return $form;
    }
    private function createEditClaveForm(Agente $entity)
    {
        //print_r('aki etuvo');exit;
        $form = $this->createForm(new SupervisorAgenteEditClaveType(), $entity, array(
            'action' => $this->generateUrl('supervisor_agente_update_clave', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        
        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    private function createEditClaveCounterForm(Usuariointerno $entity)
    {
        //print_r('aki etuvo');exit;
        $form = $this->createForm(new SupervisorCounterEditClaveType(), $entity, array(
            'action' => $this->generateUrl('supervisor_counter_update_clave', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        
        return $form;
    }
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Agente')->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'El agente que usted desea editar no existe!');
             return $this->redirect($this->generateUrl('supervisor_index_agentes'));
        }
        /*
         * Obtener el id de la empresa del user firmado para que solo cargue
         * las agencias asociadas y no el resto de las agencias que no estan asociadas 
         */
        $empresa_id=  $this->getUser()->getEmpresa()->getId();
        $editForm = $this->createEditForm($entity,$empresa_id);
        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarAgentes:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        ));
    }
    public function editCounterAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Usuariointerno')->find($id);
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'El counter que usted desea editar no existe!');
             return $this->redirect($this->generateUrl('supervisor_index_counters'));
        }
        $empresa_id=  $this->getUser()->getEmpresa()->getId();
        $editForm = $this->createEditCounterForm($entity,$empresa_id);
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarCounters:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    public function editAgenciaAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Agencia')->find($id);

        if (!$entity) {
             $this->get('session')->getFlashBag()->add('error', 'La agencia que usted desea editar no existe o no esta asociada a su empresa!');
             return $this->redirect($this->generateUrl('supervisor_index_agencias'));
        }
        
        $editForm = $this->createEditAgenciaForm($entity);
        

        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarAgencias:editAgencia.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()            
        ));
    }
    public function editEmpresaAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BaseBundle:Empresa')->find($id);

        if (!$entity) {
             $this->get('session')->getFlashBag()->add('error', 'Su empresa acaba de ser elimida!');
             return $this->redirect($this->generateUrl('index'));
        }
        $editForm = $this->createEditEmpresaForm($entity);
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarEmpresa:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()            
        ));
    }
    public function editConfiguracionAction($id)
    {
		
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BaseBundle:Configuracion')->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'La configuracion que usted desea editar no existe!');
             return $this->redirect($this->generateUrl('supervisor_index_configuraciones'));
        }
        $empresa_id=  $this->getUser()->getEmpresa()->getId();
        $editForm = $this->createEditConfiguracionForm($entity,$empresa_id);


        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarConfiguraciones:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
           
        ));
    }
    public function removeConfiguracionAction($id)
    {
        //$this->getRequest()->attributes->get('_controller') controlador
        //$this->getRequest()->attributes->get('_route') ruta
        //$this->getRequest()->attributes->get('_route_params') parametros de la ruta array()
        //$this->getRequest()->server->get('REMOTE_ADDR') IP del usuario
        //$this->getRequest()->server->get('REQUEST_TIME') Fecha y hora del servidor al que se hizo la peticion
        
       
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BaseBundle:Configuracion')->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'La configuracion que usted desea eliminar no existe o ha sido eliminada por otro usuario!');
             return $this->redirect($this->generateUrl('supervisor_index_configuraciones'));
        }
        $entityname=$entity->getNombre();
        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'La configuracion'.$entityname.' ha sido eliminada con exito!');
        return $this->redirect($this->generateUrl('supervisor_index_configuraciones'));
    }
    private function createCreateForm(Agente $entity,$empresa_id)
    {
        $form = $this->createForm(new SupervisorAgenteType(), $entity, array(
           // 'action' => $this->generateUrl('agente_create'),
            'method' => 'POST',
            'intention'=>'registrar',
            'attr'=>array('empresa'=>$empresa_id)
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }
    private function createCreateCounterForm(Usuariointerno $entity,$empresa_id)
    {
        $form = $this->createForm(new SupervisorCounterType(), $entity, array(
           // 'action' => $this->generateUrl('agente_create'),
            'method' => 'POST',
            'intention'=>'registrar',
            'attr'=>array('empresa'=>$empresa_id)
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }
    private function createCreateAgenciaForm(Agencia $entity)
    {
        $form = $this->createForm(new AgenciaType(), $entity, array(
           // 'action' => $this->generateUrl('agente_create'),
            'method' => 'POST',
        ));

        return $form;
    }
    private function createCreateConfiguracionForm(Configuracion $entity,$empresa_id)
    {
        $form = $this->createForm(new SupervisorConfiguracionType(), $entity, array(
           // 'action' => $this->generateUrl('agente_create'),
            'method' => 'POST',
            'attr'=>array('empresa'=>$empresa_id)
        ));

        return $form;
    }
    public function newAction()
    {
        $entity = new Agente();
        /*
         * Obtener la empresa para pasarla al formulario y solo caragr la agencias que coprresponden al usuario firmado
         */
        $empresa_id=  $this->getUser()->getEmpresa()->getId();
        $form   = $this->createCreateForm($entity,$empresa_id);
        
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarAgentes:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    public function newCounterAction()
    {
        $entity = new Usuariointerno();
        /*
         * Obtener la empresa para pasarla al formulario y solo cargar los jefes
         */
        $empresa_id=  $this->getUser()->getEmpresa()->getId();
        $form   = $this->createCreateCounterForm($entity,$empresa_id);
        
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarCounters:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    public function newAgenciaAction()
    {
        $entity = new Agencia();
        $form   = $this->createCreateAgenciaForm($entity);

        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarAgencias:newAgencia.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    public function newConfiguracionAction()
    {
        $entity = new Configuracion();
        $empresa_id= $this->getUser()->getEmpresa()->getId();
        $form   = $this->createCreateConfiguracionForm($entity,$empresa_id);
        
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarConfiguraciones:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    
    public function registerAgenciaAction(Request $request)
    {
        $allowed = array('png', 'jpg', 'gif','jpeg');
        $em = $this->getDoctrine()->getManager();
        $path = $this->container->getParameter('aplicacion.directorio.imagenes.agencias');
        $entity = new Agencia();
        $form = $this->createCreateAgenciaForm($entity);
        $form->handleRequest($request);
        $empresa =  $this->getUser()->getEmpresa();
        if ($form->isValid()) {
           if(isset($_FILES['aplicacion_emisionesbundle_agencia']['name']['logo']) && $_FILES['aplicacion_emisionesbundle_agencia']['error']['logo'] == 0)
            {
           
                $extension = pathinfo($_FILES['aplicacion_emisionesbundle_agencia']['name']['logo'], PATHINFO_EXTENSION);
                //print_r($extension);exit;
                $foto=  $this->crearNombre(10,$extension);
                if(!in_array(strtolower($extension), $allowed)){
                       $this->get('session')->getFlashBag()->add('error', 'El fichero que usted adjunta no es permitido!');
                       return $this->redirect($this->generateUrl('supervisor_new_agencia',array()));
                }
                

                if(move_uploaded_file($_FILES['aplicacion_emisionesbundle_agencia']['tmp_name']['logo'], $path.$foto)){
                     //print_r('entro aki');exit;    
                    $entity->setLogo($foto);
                }
            
            }
           
            $em->persist($entity);
            $empresa->addAgencia($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'La agencia ha sido agregada y asociada s su empresa satisfactoriamente!');
            return $this->redirect($this->generateUrl('supervisor_new_agencia', array()));
        }
        else
        {
            $this->get('session')->getFlashBag()->add('error', 'Ha ocurrido un error al enviar los datos de la agencia, por favor revise cuidadosamente los valores proporcionados en cada unos de los campos requeridos!');
        }
        
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarAgencias:newAgencia.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    
    public function registerAction(Request $request)
    {   
        $allowed = array('png', 'jpg', 'gif','jpeg');
        $path = $this->container->getParameter('aplicacion.directorio.imagenes.usuarios');
        $em = $this->getDoctrine()->getManager();
        $dispatcher = $this->get('event_dispatcher');
        $entity = new Agente();
        $entity->setEnabled(true);
     

        $event = new GetResponseUserEvent($entity, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);
        
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        /*
        * Pasar el ide de la empresa para que solo cargie las agencias asociadas a
        * la empresa del supervisor
        */
        $empresa_id=  $this->getUser()->getEmpresa()->getId();
        $form = $this->createCreateForm($entity,$empresa_id);
        $form->handleRequest($request);
   
        if ($form->isValid()) {  
            $event = new FormEvent($form, $request);            
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
            
                 /*Agregar la foto a la carpeta de imagenes*/
        if(isset($_FILES['aplicacion_emisionesbundle_agente']['name']['foto']) && $_FILES['aplicacion_emisionesbundle_agente']['error']['foto'] == 0)
            {
                
                $extension = pathinfo($_FILES['aplicacion_emisionesbundle_agente']['name']['foto'], PATHINFO_EXTENSION);
                //print_r($extension);exit;
                $foto=  $this->crearNombre(10,$extension);
                if(!in_array(strtolower($extension), $allowed)){
                       $this->get('session')->getFlashBag()->add('error', 'El fichero que usted adjunta no es permitido!');
                       return $this->redirect($this->generateUrl('supervisor_new_agente'));
                }

                if(move_uploaded_file($_FILES['aplicacion_emisionesbundle_agente']['tmp_name']['foto'], $path.$foto)){
                        $entity->setFoto($foto);
                }
            }
           
            $entity->addRole('ROLE_AGENTE_EXTERNO');
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'El agente ha sido registrado satisfactoriamente, se ha enviado un link de activacion a su email!');
            return $this->redirect($this->generateUrl('supervisor_new_agente'));
        }
        else
        {
            $this->get('session')->getFlashBag()->add('error', 'Ha ocurrido un error al enviar los datos del agente, por favor revise cuidadosamente los valores proporcionados en cada unos de los campos requeridos!');
            
        }
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarAgentes:new.html.twig', array(
                'form' => $form->createView(),
            ));
        
    }
    public function registerCounterAction(Request $request)
    {   
        //print_r($_FILES);exit;
        $allowed = array('png', 'jpg', 'gif','jpeg');
        $path = $this->container->getParameter('aplicacion.directorio.imagenes.usuarios');
        $em = $this->getDoctrine()->getManager();
        $dispatcher = $this->get('event_dispatcher');
        $entity = new Usuariointerno();
        $entity->setEnabled(true);
     

        $event = new GetResponseUserEvent($entity, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);
        
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        /*
        * Pasar el ide de la empresa para que solo cargie las agencias asociadas a
        * la empresa del supervisor
        */
        $empresa_id=  $this->getUser()->getEmpresa()->getId();
        $form = $this->createCreateCounterForm($entity,$empresa_id);
        $form->handleRequest($request);
   
        if ($form->isValid()) {  
            $event = new FormEvent($form, $request);            
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
            
                 /*Agregar la foto a la carpeta de imagenes*/
        if(isset($_FILES['aplicacion_emisionesbundle_usuariointerno']['name']['foto']) && $_FILES['aplicacion_emisionesbundle_usuariointerno']['error']['foto'] == 0)
            {
                
                $extension = pathinfo($_FILES['aplicacion_emisionesbundle_usuariointerno']['name']['foto'], PATHINFO_EXTENSION);
                //print_r($extension);exit;
                $foto=  $this->crearNombre(10,$extension);
                if(!in_array(strtolower($extension), $allowed)){
                       $this->get('session')->getFlashBag()->add('error', 'El fichero que usted adjunta no es permitido!');
                       return $this->redirect($this->generateUrl('supervisor_new_counter'));
                }

                if(move_uploaded_file($_FILES['aplicacion_emisionesbundle_usuariointerno']['tmp_name']['foto'], $path.$foto)){
                        $entity->setFoto($foto);
                }
            }
            $entity->addRole('ROLE_USUARIO_INTERNO');
            $entity->addRole('ROLE_COUNTER');
            $entity->setEmpresa($this->getUser()->getEmpresa());
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'El counter ha sido registrado satisfactoriamente, se ha enviado un link de activacion a su email!');
            return $this->redirect($this->generateUrl('supervisor_new_counter'));
        }
        else
        {
           $this->get('session')->getFlashBag()->add('error', 'Ha ocurrido un error por favor revise cada uno de los datos porporcionados detalladamente!');
        }

        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarCounters:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    public function registerConfiguracionAction(Request $request)
    {   
        
        $em = $this->getDoctrine()->getManager();
       
        $entity = new Configuracion();
  
        /*
        * Pasar el ide de la empresa para que solo cargue
         * los counter de esta empresa
        */
        $empresa_id=  $this->getUser()->getEmpresa()->getId();
        $form = $this->createCreateConfiguracionForm($entity,$empresa_id);
        $form->handleRequest($request);
        $configuraciones=$em->getRepository('BaseBundle:Configuracion')->findBy(array('empresa'=>  $this->getUser()->getEmpresa()));
        if ($form->isValid()) {  
            if($form->getData()->getActiva())
            {
                foreach ($configuraciones as $c) {
                        $c->setActiva(false);
                        $em->persist($c);
                }
            }
            $entity->setEmpresa($this->getUser()->getEmpresa());
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'La configuracion ha sido creada satisfactoriamente!');       
        }
        else
        {
            $this->get('session')->getFlashBag()->add('error', 'Existen errores en algunos de los datos proporcionados, por favor revise detalladamente!');       
        }

        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarConfiguraciones:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    public function editClaveAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Agente')->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'El agente seleccionado para modificar su clave no existe!');
                       return $this->redirect($this->generateUrl('supervisor_index_agentes'));
        }

        $editForm = $this->createEditClaveForm($entity);
        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarAgentes:changePassword.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        ));
    }
    public function editClaveCounterAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Usuariointerno')->find($id);
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'El counter seleccionado para modificar suclave no existe!');
                       return $this->redirect($this->generateUrl('supervisor_index_counters'));
        }

        $editForm = $this->createEditClaveCounterForm($entity);
        
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarCounters:changePassword.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        ));
    }
    function crearNombre($length,$ext)
    {
        if( ! isset($length) or ! is_numeric($length) ) $length=6;

        $str  = "0123456789abcdefghijklmnopqrstuvwxyz";
        $path = '';

        for($i=1 ; $i<$length ; $i++)
          $path .= $str{rand(0,strlen($str)-1)};

        return $path.'_'.date("d-m-Y_H-i-s").'.'.$ext;    
    }
    public function updateAction(Request $request, $id)
    {
        
        $allowed = array('png', 'jpg', 'gif','jpeg');
        $path = $this->container->getParameter('aplicacion.directorio.imagenes.usuarios');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Agente')->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'El agente que desea modificar no existe!');
                       return $this->redirect($this->generateUrl('supervisor_index_agentes'));
        }
        $avatarold=$entity->getFoto();//respaldando el avatar antiguo
       /*
        * Pasar el ide de la empresa para que solo cargie las agencias asociadas a
        * la empresa del supervisor
        */
        $empresa_id=  $this->getUser()->getEmpresa()->getId();
        $editForm = $this->createEditForm($entity,$empresa_id);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            /*Agregar la foto a la carpeta de imagenes*/
        if(isset($_FILES['aplicacion_emisionesbundle_agente']['name']['foto']) && $_FILES['aplicacion_emisionesbundle_agente']['error']['foto'] == 0)
            {
            
                $extension = pathinfo($_FILES['aplicacion_emisionesbundle_agente']['name']['foto'], PATHINFO_EXTENSION);
                //print_r($extension);exit;
                $foto=  $this->crearNombre(10,$extension);
                if(!in_array(strtolower($extension), $allowed)){
                       $this->get('session')->getFlashBag()->add('error', 'El fichero que usted adjunta no es permitido!');
                       return $this->redirect($this->generateUrl('supervisor_agente_edit',array('id'=>$entity->getId())));
                }
                if($avatarold!='')
                {   /*Eliminando el avatar viejo*/
                    
                    if(is_file($path.$avatarold))
                    {
                        unlink($path.$avatarold);
                    }
                }

                if(move_uploaded_file($_FILES['aplicacion_emisionesbundle_agente']['tmp_name']['foto'], $path.$foto)){
                        $entity->setFoto($foto);
                }
            
            }
            else
            {
                $entity->setFoto($avatarold);
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Su perfil ha sido actualizado!");
            return $this->redirect($this->generateUrl('supervisor_agente_edit', array('id' => $id)));
        }
        else
        {
           $this->get('session')->getFlashBag()->add('error', "Ha ocurrido un error, por favor revise detalladamente los valores proporcionados!"); 
        }

        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarAgentes:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
           // 'delete_form' => $deleteForm->createView(),
        ));
    }
    public function updateEmpresaAction(Request $request, $id)
    {
       
        $allowed = array('png', 'jpg', 'gif','jpeg');
        $path = $this->container->getParameter('aplicacion.directorio.imagenes.empresas');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BaseBundle:Empresa')->find($id);
       
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'La empresa que desea modificar no existe!');
                       return $this->redirect($this->generateUrl('index'));
        }
        $avatarold=$entity->getLogo();//respaldando el avatar antiguo
         //print_r($avatarold);
        $editForm = $this->createEditEmpresaForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {//print_r('valido');exit;
            /*Agregar la foto a la carpeta de imagenes*/
        if(isset($_FILES['aplicacion_basebundle_empresa']['name']['logo']) && $_FILES['aplicacion_basebundle_empresa']['error']['logo'] == 0)
            {
            
                $extension = pathinfo($_FILES['aplicacion_basebundle_empresa']['name']['logo'], PATHINFO_EXTENSION);
                //print_r($extension);exit;
                $foto=  $this->crearNombre(10,$extension);
                if(!in_array(strtolower($extension), $allowed)){
                       $this->get('session')->getFlashBag()->add('error', 'El fichero que usted adjunta no es permitido!');
                       return $this->redirect($this->generateUrl('supervisor_empresa_edit',array('id'=>$entity->getId())));
                }
                

                if(move_uploaded_file($_FILES['aplicacion_basebundle_empresa']['tmp_name']['logo'], $path.$foto)){
                        $entity->setLogo($foto);
                        if($avatarold!='')
                        {   /*Eliminando el avatar viejo*/
                            if(is_file($path.$avatarold))
                            {
                                unlink($path.$avatarold);
                            }
                        }
                }
            
            }
            else
            {
                $entity->setLogo($avatarold);
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Los datos de su empresa han sido actualizados satisfactoriamente!");
            return $this->redirect($this->generateUrl('supervisor_empresa_edit', array('id' => $id)));
        }
        else
        {
            $entity->setLogo($avatarold);
            $this->get('session')->getFlashBag()->add('error', "Ha ocurrido un error,por favor revise detalladamente los valores proporcionados!");
        }

        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarEmpresa:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
           // 'delete_form' => $deleteForm->createView(),
        ));
    }
    public function updateConfiguracionAction(Request $request, $id)
    {
       
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BaseBundle:Configuracion')->find($id);
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'La configuracion que usted desea modificar no existe');
            return $this->redirect($this->generateUrl('supervisor_index_configuraciones'));
        }
        $empresa_id=  $this->getUser()->getEmpresa()->getId();
        $editForm = $this->createEditConfiguracionForm($entity, $empresa_id);
        $editForm->handleRequest($request);
        $configuraciones=$em->getRepository('BaseBundle:Configuracion')->findBy(array('empresa'=>$this->getUser()->getEmpresa()));
        if ($editForm->isValid()) {
            if($editForm->getData()->getActiva())
            {
                foreach ($configuraciones as $c) {
                    if($c!=$entity)
                    {
                        $c->setActiva(false);
                        $em->persist($c);
                    }
                }
            }
            
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "La operacion ha sido satisfactoria");
            return $this->redirect($this->generateUrl('supervisor_configuracion_edit', array('id' => $id)));
        }
        else
        {
           $this->get('session')->getFlashBag()->add('error', "Ha ocurrido un error, por favor revise detalladamente los valores pproporcionados!"); 
        }

        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarConfiguraciones:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),         
        ));
    }
    public function updateCounterAction(Request $request, $id)
    {
        
        $allowed = array('png', 'jpg', 'gif','jpeg');
        $path = $this->container->getParameter('aplicacion.directorio.imagenes.usuarios');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Usuariointerno')->find($id);
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'El counter que usted desea modificar no existe');
            return $this->redirect($this->generateUrl('supervisor_index_counters'));
        }
        $avatarold=$entity->getFoto();//respaldando el avatar antiguo
       /*
        * Pasar el id de la empresa para que solo cargie las agencias asociadas a
        * la empresa del supervisor
        */
        $empresa_id=  $this->getUser()->getEmpresa()->getId();
        $editForm = $this->createEditCounterForm($entity,$empresa_id);
        $editForm->handleRequest($request);
        //print_r($_FILES);exit;
        if ($editForm->isValid()) {
        	if(!(($editForm->getData()->getInicioAlmuerzo()>= $editForm->getData()->getInicioJornada())&& ($editForm->getData()->getInicioAlmuerzo()<= $editForm->getData()->getFinJornada())))
            {
                $this->get('session')->getFlashBag()->add('error', 'El inicio del almuerzo debe ser un horario comprendido dentro de la jornada laboral.');
                return $this->redirect($this->generateUrl('supervisor_counter_edit',array('id'=>$entity->getId())));
            }
            if(!(($editForm->getData()->getFinAlmuerzo()>= $editForm->getData()->getInicioAlmuerzo())&& ($editForm->getData()->getFinAlmuerzo()<= $editForm->getData()->getFinJornada())))
            {
                $this->get('session')->getFlashBag()->add('error', 'El fin del almuerzo debe ser un horario comprendido dentro de la jornada laboral y mayor o igual que el inicio del almuerzo.');
                return $this->redirect($this->generateUrl('supervisor_counter_edit',array('id'=>$entity->getId())));
            }
            /*Agregar la foto a la carpeta de imagenes*/
        if(isset($_FILES['aplicacion_emisionesbundle_usuariointerno']['name']['foto']) && $_FILES['aplicacion_emisionesbundle_usuariointerno']['error']['foto'] == 0)
            {
            
                $extension = pathinfo($_FILES['aplicacion_emisionesbundle_usuariointerno']['name']['foto'], PATHINFO_EXTENSION);
               // print_r($extension);exit;
                $foto=  $this->crearNombre(10,$extension);
                if(!in_array(strtolower($extension), $allowed)){
                       $this->get('session')->getFlashBag()->add('error', 'El fichero que usted adjunta no es permitido!');
                       return $this->redirect($this->generateUrl('supervisor_counter_edit',array('id'=>$entity->getId())));
                }
                if($avatarold!='')
                {   /*Eliminando el avatar viejo*/
                    
                    if(is_file($path.$avatarold))
                    {
                        unlink($path.$avatarold);
                    }
                }

                if(move_uploaded_file($_FILES['aplicacion_emisionesbundle_usuariointerno']['tmp_name']['foto'], $path.$foto)){
                        $entity->setFoto($foto);
                }
            
            }
            else
            {
                $entity->setFoto($avatarold);
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "La operacion ha sido satisfactoria");
            return $this->redirect($this->generateUrl('supervisor_counter_edit', array('id' => $id)));
        }
        else
        {
            $this->get('session')->getFlashBag()->add('error', 'Ha ocurrido un error por favor revise detalladamente los valores proporcionados!');
        }

        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarCounters:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
           // 'delete_form' => $deleteForm->createView(),
        ));
    }
    public function updateClaveAction(Request $request, $id)
    {
        
       
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Agente')->find($id);
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'El agente seleccionado para modificar suclave no existe!');
                       return $this->redirect($this->generateUrl('supervisor_index_agentes'));
        }
      
       
        $editForm = $this->createEditClaveForm($entity);
        $editForm->handleRequest($request);//revisar aki

        if ($editForm->isValid()) {
            /*Agregar la foto a la carpeta de imagenes*/
     
            $entity->setPassword( $editForm->getData()->getPlainPassword());
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "La clave ha sido cambiada satisfctoriamente!");
            return $this->redirect($this->generateUrl('supervisor_agente_edit_clave', array('id' => $id)));
        }

        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarAgentes:changePassword.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
           // 'delete_form' => $deleteForm->createView(),
        ));
    }
    public function updateClaveCounterAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Usuariointerno')->find($id);
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'El counter seleccionado para modificar suclave no existe!');
                       return $this->redirect($this->generateUrl('supervisor_index_counters'));
        }
        $editForm = $this->createEditClaveCounterForm($entity);
        $editForm->handleRequest($request);//revisar aki

        if ($editForm->isValid()) {
            
            $entity->setPassword( $editForm->getData()->getPlainPassword());
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "La clave ha sido cambiada satisfctoriamente!");
            return $this->redirect($this->generateUrl('supervisor_counter_edit_clave', array('id' => $id)));
        }

        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarCounters:changePassword.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
           
        ));
    }
    public function member_unmemberAction($id,$id_empresa)
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
        return $this->redirect($this->generateUrl('supervisor_index_agencias'));
    }
  
    public function lock_unlockAction($id)
    {
        $em= $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('EmisionesBundle:Agente')->find($id);
        if(!$usuario)
        {
            $this->get('session')->getFlashBag()->add('error', 'El agente no existe!');
            return $this->redirect($this->generateUrl('supervisor_index_agentes'));
        }
        $usuario->setLocked(!$usuario->isLocked());
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'Operacion realizada satisfactoriamente!');
        return $this->redirect($this->generateUrl('supervisor_index_agentes'));
    }
    public function lock_unlockCounterAction($id)
    {
        $em= $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('EmisionesBundle:Usuariointerno')->find($id);
        if(!$usuario)
        {
            $this->get('session')->getFlashBag()->add('error', 'El counter no existe!');
            return $this->redirect($this->generateUrl('supervisor_index_counters'));
        }
        if(!$usuario->isLocked())
        {
           $usuario->setLocked(true);
           $usuario->setEnabled(false);
        }
        else
        {
           $usuario->setLocked(false); 
        }
        
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'Operacion realizada satisfactoriamente!');
        return $this->redirect($this->generateUrl('supervisor_index_counters'));
    }
    public function unable_enableAction($id)
    {
        $em= $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('EmisionesBundle:Agente')->find($id);
        if(!$usuario)
        {
            $this->get('session')->getFlashBag()->add('error', 'El agente no existe!');
            return $this->redirect($this->generateUrl('supervisor_index_agentes'));
        }
        $usuario->setEnabled(!$usuario->isEnabled());
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'Operacion realizada satisfactoriamente!');
        return $this->redirect($this->generateUrl('supervisor_index_agentes'));
    }
    public function activar_desactivarConfiguracionAction($id)
    {
        $em= $this->getDoctrine()->getManager();
        $conf = $em->getRepository('BaseBundle:Configuracion')->find($id);
        if(!$conf)
        {
            $this->get('session')->getFlashBag()->add('error', 'La configuracion no existe o ha sido eliminada!');
            return $this->redirect($this->generateUrl('supervisor_index_configuraciones'));
        }
        if(!$conf->getActiva())
        {
            $configuraciones = $em->getRepository('BaseBundle:Configuracion')->findBy(array('empresa'=>  $this->getUser()->getEmpresa()));
            foreach ($configuraciones as $c) {
                if($c==$conf)
                {
                    $c->setActiva(true);
                }
                else
                {
                    $c->setActiva(false);
                }
                $em->persist($c);
            }
            $em->flush();
             $this->get('session')->getFlashBag()->add('success', 'Operacion realizada satisfactoriamente!');
            return $this->redirect($this->generateUrl('supervisor_index_configuraciones'));
        }
        
        
        $this->get('session')->getFlashBag()->add('info', 'La configuracion seleccionada ya esta activada!');
        return $this->redirect($this->generateUrl('supervisor_index_configuraciones'));
    }
    public function activar_desactivarDaemonAction($id)
    {
        $em= $this->getDoctrine()->getManager();
        $daemon = $em->getRepository('BaseBundle:CronTask')->find($id);
        if(!$daemon)
        {
            $this->get('session')->getFlashBag()->add('error', 'El demonio de asignacion no existe o ha sido eliminada!');
            return $this->redirect($this->generateUrl('supervisor_index_daemons'));
        }
        if(!$daemon->getActiva())
        {
            $daemons = $em->getRepository('BaseBundle:CronTask')->findBy(array('empresa'=>  $this->getUser()->getEmpresa(),'asignacionOrdenes'=>true));
            foreach ($daemons as $d) {
                if($d==$daemon)
                {
                    $d->setActiva(true);
                }
                else
                {
                    $d->setActiva(false);
                }
                $em->persist($d);
            }
            $em->flush();
             $this->get('session')->getFlashBag()->add('success', 'Operacion realizada satisfactoriamente!');
            return $this->redirect($this->generateUrl('supervisor_index_daemons'));
        }
        
        
        $this->get('session')->getFlashBag()->add('info', 'La configuracion seleccionada ya esta activada!');
        return $this->redirect($this->generateUrl('supervisor_index_daemons'));
    }
    public function activar_desactivarPlanPilotoAction($agencia,$aerolinea)
    {
        $em= $this->getDoctrine()->getManager();
        $agencia=$em->getRepository('EmisionesBundle:Agencia')->find($agencia);
        if(!$agencia)
        {
            $this->get('session')->getFlashBag()->add('error', 'La agencia seleccionada no existe o ha sido eliminada!');
            return $this->redirect($this->generateUrl('supervisor_index_plan_piloto'));
        }
        $aerolinea=$em->getRepository('EmisionesBundle:Aerolinea')->find($aerolinea);
       if(!$aerolinea)
        {
            $this->get('session')->getFlashBag()->add('error', 'La aerolinea seleccionada no existe o ha sido eliminada!');
            return $this->redirect($this->generateUrl('supervisor_index_plan_piloto'));
        }
        //Si estan relacionadas
        if($agencia->getAerolineasPlanPiloto()->contains($aerolinea))
        {
            $agencia->removeAerolineasPlanPiloto($aerolinea);
        }
        else
        {
            $agencia->addAerolineasPlanPiloto($aerolinea);
        }
        $em->persist($agencia);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'Operacion procesada satisfactoriamente!');
        return $this->redirect($this->generateUrl('supervisor_index_plan_piloto'));
    }
    public function copiarConfiguracionAction($id)
    {
        $em= $this->getDoctrine()->getManager();
        $conf = $em->getRepository('BaseBundle:Configuracion')->find($id);
        if(!$conf)
        {
            $this->get('session')->getFlashBag()->add('error', 'La configuracion que desea duplicar no existe!');
            return $this->redirect($this->generateUrl('supervisor_index_configuraciones'));
        }
        $newconf= new Configuracion();
        $newconf->setNombre($conf->getNombre().' Copia');
        $newconf->setActiva(false);
        $newconf->setDescripcion($conf->getDescripcion());
        $newconf->setEmailVacaciones($conf->getEmailVacaciones());
        $newconf->setEmailViaticos($conf->getEmailViaticos());
        $newconf->setEmpresa($conf->getEmpresa());
        $newconf->setFinHorarioAtencion($conf->getFinHorarioAtencion());
        $newconf->setInicioHorarioAtencion($conf->getInicioHorarioAtencion());
        $newconf->setLastCounter($conf->getLastCounter());
        $newconf->setPonderacionAnulacion($conf->getPonderacionAnulacion());
        $newconf->setPonderacionEmision($conf->getPonderacionEmision());
        $newconf->setPonderacionNoPlanPiloto($conf->getPonderacionNoPlanPiloto());
        $newconf->setPonderacionPlanPiloto($conf->getPonderacionPlanPiloto());
        $newconf->setPonderacionRevision($conf->getPonderacionRevision());
        $newconf->setTiempoAnulacion($conf->getTiempoAnulacion());
        //$newconf->setTiempoAsignacion($conf->getTiempoAsignacion());
        $newconf->setTiempoEmision($conf->getTiempoEmision());
        $newconf->setTiempoFomaPagoCash($conf->getTiempoFomaPagoCash());
        $newconf->setTiempoFomaPagoPlanPiloto($conf->getTiempoFomaPagoPlanPiloto());
        $newconf->setTiempoFomaPagoVtc($conf->getTiempoFomaPagoVtc());
        $newconf->setTiempoLocal($conf->getTiempoLocal());
        $newconf->setTiempoPorPasajero($conf->getTiempoPorPasajero());
        $newconf->setTiempoRemota($conf->getTiempoRemota());
        $newconf->setTiempoRespuestaNormal($conf->getTiempoRespuestaNormal());
        $newconf->setTiempoRespuestaPlanPiloto($conf->getTiempoRespuestaPlanPiloto());
        $newconf->setTiempoRevision($conf->getTiempoRevision());
        $newconf->setPonderacionSVI($conf->getPonderacionSVI());
        $newconf->setPonderacionNOSVI($conf->getPonderacionNOSVI());
        $newconf->setTiempoPrimeraAlerta($conf->getTiempoPrimeraAlerta());
        $newconf->setTiempoSegundaAlerta($conf->getTiempoSegundaAlerta());
        $newconf->setFeeEmergencia($conf->getFeeEmergencia());
        $em->persist($newconf);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'La configuracion seleccionada ha sido copiada satisfactoriamente!');
        return $this->redirect($this->generateUrl('supervisor_index_configuraciones'));
    }
    public function unable_enableCounterAction($id)
    {
        $em= $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('EmisionesBundle:Usuariointerno')->find($id);
        if(!$usuario)
        {
            $this->get('session')->getFlashBag()->add('error', 'El counter no existe!');
            return $this->redirect($this->generateUrl('supervisor_index_counters'));
        }
        $usuario->setEnabled(!$usuario->isEnabled());
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'Operacion realizada satisfactoriamente!');
        return $this->redirect($this->generateUrl('supervisor_index_counters'));
    }

    public function load_ajax_agentesAction()
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
        $agencias=$empresa->getAgencias();
        //print_r(count($agencias));exit;
        $agentes= array();
        for ($j = 0; $j < count($agencias); $j++) {
            $agents=$agencias[$j]->getAgentes();
            for ($k = 0; $k < count($agents); $k++) {
                $agentes[]=$agents[$k];
            } 
        }
        if($searchfilter!='')
        {
         $agentes=  $this->applyFilterSearchAgentes($agentes, $searchfilter);
        }
        
        //En este punto tengo el total de ordenes que cumple con los filtros
        $result = array();       
        $result['draw']=$draw;
        $result['recordsTotal']=count($agentes);
        $result['recordsFiltered']=count($agentes);        
        $agentes= $this->reduce($pstart, $pend, $agentes);      
        $cont=0;
        $result['data'] = array();
        for ($i = 0; $i < count($agentes); $i++) 
        {     
                $result['data'][$cont]['agencia']= $agentes[$i]->getAgencia()->__toString();
                $result['data'][$cont]['nombre']= $agentes[$i]->__toString();
                $result['data'][$cont]['usuario']= $agentes[$i]->getUserName();
                $result['data'][$cont]['email']= $agentes[$i]->getEmail();
                if($agentes[$i]->getSexo()=='M')
                {
                    $result['data'][$cont]['sexo']='fa fa-male';
                }
                if($agentes[$i]->getSexo()=='F')
                {
                    $result['data'][$cont]['sexo']='fa fa-female';
                }            
                if($agentes[$i]->isLocked())
                {
                    $result['data'][$cont]['bloqueado']='fa fa-lock';
                }
                else
                {
                    $result['data'][$cont]['bloqueado']='fa fa-unlock';
                }
                // verificar si esta habilitado o no el usuario
                if($agentes[$i]->isEnabled())
                {
                    $result['data'][$cont]['habilitado']='fa fa-thumbs-o-up';
                }
                else
                {
                    $result['data'][$cont]['habilitado']='fa fa-thumbs-o-down';
                }                
                $result['data'][$cont]['celular']= $agentes[$i]->getCelular();
                $result['data'][$cont]['editar']= $this->generateUrl('supervisor_agente_edit', array('id' => $agentes[$i]->getId()));               
                $result['data'][$cont]['chpass']= $this->generateUrl('supervisor_agente_edit_clave', array('id' => $agentes[$i]->getId()));
                $result['data'][$cont]['lockUnlock']= $this->generateUrl('supervisor_lock_unlock_agente', array('id' => $agentes[$i]->getId()));
                $result['data'][$cont]['unableEnable']= $this->generateUrl('supervisor_unable_enable_agente', array('id' => $agentes[$i]->getId()));
                $result['data'][$cont]['id']= $agentes[$i]->getId();              
                $cont++;
        }
      
       //print_r($result);exit;
       return new Response(json_encode($result));
    
    }
    
  public function load_ajax_countersAction()
    {
        $peticion = $this->getRequest();
        $empresaid = $peticion->request->get('empresa_id');
        $em = $this->getDoctrine()->getManager();
        $empresa = $em->getRepository('BaseBundle:Empresa')->find($empresaid);
        $counters=$empresa->getCounters();
        $result = array();
        $result['data'] = array();
       
        for ($i = 0; $i < count($counters); $i++) {
               
                $result['aaData'][$i]['foto']= 'no-avatar.png';
                if($counters[$i]->getFoto()!=null)
                {
                    $result['aaData'][$i]['foto']=$counters[$i]->getFoto();
                }
                $result['aaData'][$i]['nombre']= $counters[$i]->__toString();
                $result['aaData'][$i]['email']= $counters[$i]->getEmail();                 
                if($counters[$i]->getSexo()=='F')
                {
                    $result['aaData'][$i]['sexo']='fa fa-female';
                }
                else
                {
                   $result['aaData'][$i]['sexo']='fa fa-male'; 
                }
               if($counters[$i]->isLocked())
                {
                    $result['aaData'][$i]['bloqueado']='fa fa-lock';
                }
                else
                {
                    $result['aaData'][$i]['bloqueado']='fa fa-unlock';
                }
                // verificar si esta habilitado o no el usuario
                if($counters[$i]->isEnabled())
                {
                    $result['aaData'][$i]['habilitado']='fa fa-thumbs-o-up';
                }
                else
                {
                    $result['aaData'][$i]['habilitado']='fa fa-thumbs-o-down';
                }
                $result['aaData'][$i]['convencional']= $counters[$i]->getTelefono();
                if($counters[$i]->getExt())
                {
                    $result['aaData'][$i]['convencional']= $counters[$i]->getTelefono().'-'.$counters[$i]->getExt();
                }
                $result['aaData'][$i]['celular']= $counters[$i]->getCelular();
                $result['aaData'][$i]['almuerzo']= $counters[$i]->getInicioAlmuerzo()->format('H:i:s').'-'.$counters[$i]->getFinAlmuerzo()->format('H:i:s');
                $result['aaData'][$i]['jornada']= $counters[$i]->getInicioJornada()->format('H:i:s').'-'.$counters[$i]->getFinJornada()->format('H:i:s');
                $result['aaData'][$i]['editar']= $this->generateUrl('supervisor_counter_edit', array('id' => $counters[$i]->getId()));
               // $result['aaData'][$i]['eliminar']= $this->generateUrl('supervisor_agente_delete', array('id' => $agentes[$i]->getId()));
                $result['aaData'][$i]['chpass']= $this->generateUrl('supervisor_counter_edit_clave', array('id' => $counters[$i]->getId()));
                $result['aaData'][$i]['lockUnlock']= $this->generateUrl('supervisor_lock_unlock_counter', array('id' => $counters[$i]->getId()));
                $result['aaData'][$i]['unableEnable']= $this->generateUrl('supervisor_unable_enable_counter', array('id' => $counters[$i]->getId()));
                $result['aaData'][$i]['id']= $counters[$i]->getId();
                
            }
           // print_r($result);exit;
        return new Response(json_encode($result));
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
               $result['data'][$cont]['memberUnmember']= $this->generateUrl('supervisor_member_unmember_agencia', array('id' => $agencias[$i]->getId(),'id_empresa'=>$empresa->getId()));
               $result['data'][$cont]['edit']= $this->generateUrl('supervisor_agencia_edit', array('id' => $agencias[$i]->getId()));                              
                $cont++;
        }
      
       //print_r($result);exit;
       return new Response(json_encode($result));
      
    }
    public function updateAgenciaAction(Request $request, $id)
    {
        
        $allowed = array('png', 'jpg', 'gif','jpeg');
        $path = $this->container->getParameter('aplicacion.directorio.imagenes.agencias');
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EmisionesBundle:Agencia')->find($id);
        if (!$entity) {
           $this->get('session')->getFlashBag()->add('error', 'La agencia que usted desea editar no existe!');
             return $this->redirect($this->generateUrl('supervisor_index_agencias'));
        }
        $avatarold=$entity->getLogo();//respaldando el avatar antiguo
        
        $editForm = $this->createEditAgenciaForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
//            $traza=new \aplicacion\BaseBundle\Entity\Traza();
//            $traza->setFecha(new \DateTime());
//            $traza->setAccion($accion);
                 /*Agregar la foto a la carpeta de imagenes*/
        if(isset($_FILES['aplicacion_emisionesbundle_agencia']['name']['logo']) && $_FILES['aplicacion_emisionesbundle_agencia']['error']['logo'] == 0)
            {
           
                $extension = pathinfo($_FILES['aplicacion_emisionesbundle_agencia']['name']['logo'], PATHINFO_EXTENSION);
                //print_r($extension);exit;
                $foto=  $this->crearNombre(10,$extension);
                if(!in_array(strtolower($extension), $allowed)){
                       $this->get('session')->getFlashBag()->add('error', 'El fichero que usted adjunta no es permitido!');
                       return $this->redirect($this->generateUrl('supervisor_agencia_edit',array('id'=>$entity->getId())));
                }
                if($avatarold!='')
                {   /*Eliminando el avatar viejo*/
                    
                    if(is_file($path.$avatarold))
                    {
                        unlink($path.$avatarold);
                    }
                }

                if(move_uploaded_file($_FILES['aplicacion_emisionesbundle_agencia']['tmp_name']['logo'], $path.$foto)){
                     //print_r('entro aki');exit;    
                    $entity->setLogo($foto);
                }
            
            }
            else
            {
                $entity->setLogo($avatarold);
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'La agencia ha sido editada satisfactoriamente!');
            return $this->redirect($this->generateUrl('supervisor_agencia_edit', array('id'=>$entity->getId())));
        }
        else
        {
           $entity->setLogo($avatarold);
           $this->get('session')->getFlashBag()->add('error', 'Ha ocurrido un error al enviar los datos de la agencia, por favor revise cuidadosamente los valores proporcionados en cada unos de los campos requeridos!'); 
        }
        
        return $this->render('EmisionesBundle:SupervisorAdministrarAgentes/AdministrarAgencias:editAgencia.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()            
        ));
    }
     public function loadOrdenesAction()
    {
        $peticion = $this->getRequest();
        $rango = $peticion->request->get('rangoemisiones');
        $estado = $peticion->request->get('estado');
        $tipo = $peticion->request->get('tipo');
        
       // print_r($estado.'--'.$tipo);exit;
        
        $rango = explode('-', $rango);
        $start = $rango[0];
        $end = $rango[1];
        $start= new \DateTime($start);
        $end = new \DateTime($end);
        $em = $this->getDoctrine()->getManager();        
        $ordenes = $this->getUser()->getEmpresa()->getOrdenes();
        //Aplicar filktro de estado
        $ordenes=  $this->applyFilterState($ordenes, $estado);
        //Aplicar filtro de tipo
        $ordenes = $this->applyFilterType($ordenes, $tipo);
        $result['aaData'] = array();
        $totalemisiones=0;
        
        for ($i = 0; $i < count($ordenes); $i++) 
        {
            if(($ordenes[$i]->getFecha()>=$start) && ($ordenes[$i]->getFecha()<=$end))
            {              
                    $result['aaData'][$i]['tiempo']= $ordenes[$i]->timeToProcess();
                    $result['aaData'][$i]['agente']= $ordenes[$i]->getAgente()->__toString();
                    $result['aaData'][$i]['agencia']= $ordenes[$i]->getAgente()->getAgencia()->__toString();
                    $result['aaData'][$i]['fecha']= $ordenes[$i]->getFecha()->format('d M Y H:m:i');
                    $result['aaData'][$i]['tipo']= $ordenes[$i]->getTipo();
                    $result['aaData'][$i]['gds']= $ordenes[$i]->getGds()->__toString();
                    $result['aaData'][$i]['tboleto']= $ordenes[$i]->getTipoBoleto();
                    $totalemisiones++;
            } 
        }
        
        $result['iTotalRecords']=$totalemisiones;
        $result['iTotalDisplayRecords']=10;
        //print_r($result);exit;
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
           if($o->getUsuario())
           {
              if(preg_match('/'.$filter.'/',strtoupper($o->getTipo())) ||
               preg_match('/'.$filter.'/',strtoupper($o->getFecha()->format('d M Y H:i:s'))) ||
               preg_match('/'.$filter.'/',strtoupper($o->getAgente()->getAgencia()->__toString())) ||
               preg_match('/'.$filter.'/',strtoupper($o->getAgente()->__toString())) || 
               preg_match('/'.$filter.'/',strtoupper($o->getUsuario()->getUsername())) || 
               preg_match('/'.$filter.'/',strtoupper($o->getNumeroOrden())) ||
               preg_match('/'.$filter.'/',strtoupper($o->getEstado()->getNombre())) || 
               preg_match('/'.$filter.'/',strtoupper($o->timeSinceArrive())) ||
               preg_match('/'.$filter.'/',strtoupper($o->getPrioridad())))     
                    
                {
                    $result[]=  $o;
                } 
           }
           else
           {
               if(preg_match('/'.$filter.'/',strtoupper($o->getTipo())) ||
               preg_match('/'.$filter.'/',strtoupper($o->getFecha()->format('d M Y H:i:s'))) ||
               preg_match('/'.$filter.'/',strtoupper($o->getAgente()->getAgencia()->__toString())) ||
               preg_match('/'.$filter.'/',strtoupper($o->getAgente()->__toString())) || 
              // preg_match('/'.$filter.'/',strtoupper($o->getUsuario()->getUsername())) || 
               preg_match('/'.$filter.'/',strtoupper($o->getNumeroOrden())) || 
               preg_match('/'.$filter.'/',strtoupper($o->timeSinceArrive())) ||
               preg_match('/'.$filter.'/',strtoupper($o->getPrioridad())))     
                    
                {
                    $result[]=  $o;
                }  
           }
          
            
                
           
       }
       return $result;
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
    /*
     * funcion que filtra los agentes
     */
     public function applyFilterSearchAgentes($agentes,$filter)
    {
      
      $filter=strtoupper($filter);
       $result=array();
       foreach ($agentes as $a) {
          
            if(preg_match('/'.$filter.'/',strtoupper($a->getAgencia()->getNombre())) ||
               preg_match('/'.$filter.'/',strtoupper($a->__toString())) ||
               preg_match('/'.$filter.'/',strtoupper($a->getUsername())) ||
               preg_match('/'.$filter.'/',strtoupper($a->getEmail())) ||     
               preg_match('/'.$filter.'/',strtoupper($a->getCelular())) )     
                    
            {
                $result[]=  $a;
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
    /*
     * Funcion que carga la cola de solicitudes de mayor a menor prioridad
     */
    public function loadColaAction()
    {

         
        $peticion = $this->getRequest();
        $searchfilter = $peticion->request->get('search');
        $searchfilter=$searchfilter['value'];
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
        $ordenes = $em->getRepository('EmisionesBundle:Orden')->getBySortableGroupsQuery(array('empresa' =>$this->getUser()->getEmpresa()))->getResult();
        
        $ordenes=  $this->applyFilterState($ordenes, $estado);
        //Aplicar filtro de tipo
        $ordenes = $this->applyFilterType($ordenes, $tipo);
        //Aplicar filtro counter
        $ordenes = $this->applyFilterCounter($ordenes, $counter);
        //Aplicar Filtro de Fechas
        $ordenes = $this->applyFilterDate($ordenes, $start, $end);
        // si se aplica filtro de busqueda por el usuario
        if($searchfilter!='')
        {
         $ordenes=  $this->applyFilterSearch($ordenes, $searchfilter);
        }
        
        //En este punto tengo el total de ordenes que cumple con los filtros
        $result = array();
        $ordenes=  array_reverse($ordenes);
        $result['draw']=$draw;
        $result['recordsTotal']=count($ordenes);
        $result['recordsFiltered']=count($ordenes);        
        $ordenes= $this->reduce($pstart, $pend, $ordenes);      
        $cont=0;
        $result['data'] = array();
        for ($i = 0; $i < count($ordenes); $i++) 
        {     
                $result['data'][$cont]['adjunto']= null;
                if($ordenes[$i]->getAdjunto())
                {                    
                   $result['data'][$cont]['adjunto']= $ordenes[$i]->getAdjunto();                 
                }
                $result['data'][$cont]['numero_orden']= $ordenes[$i]->getNumeroOrden();                
                $result['data'][$cont]['agente']= $ordenes[$i]->getAgente()->__toString();
                $result['data'][$cont]['avataragente']= $ordenes[$i]->getAgente()->getFoto();
                $result['data'][$cont]['movil']= $ordenes[$i]->getAgente()->getCelular();
                $result['data'][$cont]['telefono']= $ordenes[$i]->getAgente()->getTelefono().'-'.$ordenes[$i]->getAgente()->getExt();
                $result['data'][$cont]['emailagente']= $ordenes[$i]->getAgente()->getEmail();
                $result['data'][$cont]['agencia']= $ordenes[$i]->getAgente()->getAgencia()->__toString();
                $result['data'][$cont]['estado']= $ordenes[$i]->getEstado()->getNombre();
                $result['data'][$cont]['emailagencia']= $ordenes[$i]->getAgente()->getAgencia()->getEmail();
                $result['data'][$cont]['telefonoagencia']= $ordenes[$i]->getAgente()->getAgencia()->getTelefono();
                $result['data'][$cont]['direccionagencia']= $ordenes[$i]->getAgente()->getAgencia()->getDireccion();
                $result['data'][$cont]['logoagencia']= $ordenes[$i]->getAgente()->getAgencia()->getLogo();
                
                if($ordenes[$i]->getObservaciones()!=null)
                {
                    $result['data'][$cont]['observaciones']= $ordenes[$i]->getObservaciones(); 
                }
                else
                {
                    $result['data'][$cont]['observaciones']= 'No existen observaciones...';
                }
                $result['data'][$cont]['fecha']= $ordenes[$i]->getFecha()->format('d-m-Y H:i:s');                    
                $result['data'][$cont]['tipo']= $ordenes[$i]->getTipo();
                $result['data'][$cont]['counter']= $ordenes[$i]->getUsuario();
                $result['data'][$cont]['usernamecounter']= '<b class="text-center">No Asignada</b>';
                if($ordenes[$i]->getUsuario())
                {
                     $result['data'][$cont]['usernamecounter']= $ordenes[$i]->getUsuario()->getUsername().'-'.($ordenes[$i]->getUsuario()->getTimeOfQueue()/60).'min';
                }
                $result['data'][$cont]['gds']= $ordenes[$i]->getGds()->__toString();
                $result['data'][$cont]['tboleto']= $ordenes[$i]->getTipoBoleto();
                $result['data'][$cont]['prioridad']= $ordenes[$i]->getPrioridad();
                $result['data'][$cont]['numero']= $ordenes[$i]->getNumeroOrden();
                $result['data'][$cont]['tiempo']= $ordenes[$i]->timeSinceArrive(); 
                if($ordenes[$i]->isTimeToFirsAlert())
                {
                    //print_r('primera alerta');exit;
                    $result['data'][$cont]['tiempo']= '<b class="text-green"><i class="ion ion-ios7-alarm"></i> '.$ordenes[$i]->timeSinceArrive().'</b>'; 
                }
                if($ordenes[$i]->isTimeToSecondAlert())
                {
                    //print_r('segunda alerta');exit;
                    $result['data'][$cont]['tiempo']= '<b class="text-orange"><i class="ion ion-ios7-alarm"></i> '.$ordenes[$i]->timeSinceArrive().'</b>'; 
                }
                if($ordenes[$i]->isLimitHour())
                {
                    //print_r('tiempo limite');exit;
                    $result['data'][$cont]['tiempo']= '<b style="color:red;"><i class="ion ion-ios7-alarm"></i> '.$ordenes[$i]->timeSinceArrive().'</b>'; 
                }
                if($ordenes[$i]->isOutOfTimeAlert())
                {
                    //print_r('incumpleindo');exit;
                    $result['data'][$cont]['tiempo']= '<b class="text-black"><i class="ion ion-ios7-alarm"></i> '.$ordenes[$i]->timeSinceArrive().'</b>'; 
                }
                if($ordenes[$i]->getEstado()->getId()==3 || $ordenes[$i]->getEstado()->getId()==1)
                {
                   $result['data'][$cont]['tiempo']= ' ';
                }
                $result['data'][$cont]['timetoproccess']= $ordenes[$i]->timeSinceArrive();
                $result['data'][$cont]['openorden']= '';
                if($ordenes[$i] instanceof Anulacion)
                {
                    $result['data'][$cont]['openorden']=$this->generateUrl('counter_anulacion_show', array('id' => $ordenes[$i]->getId()));
                }
                else if($ordenes[$i] instanceof Revision )
                {
                    $result['data'][$cont]['openorden']=$this->generateUrl('counter_revision_show', array('id' => $ordenes[$i]->getId()));
                }
                else if($ordenes[$i] instanceof Emision )
                {
                    $result['data'][$cont]['openorden']=$this->generateUrl('counter_emision_show', array('id' => $ordenes[$i]->getId()));
                }
                
                $cont++;
        }
      // print_r($ordenes[0]->getPrioridad().'-'.$ordenes[1]->getPrioridad().'-'.$ordenes[2]->getPrioridad());exit;
       //print_r($result);exit;
       return new Response(json_encode($result));
    }
    public function loadDaemonsAction()
    {
        $em=  $this->getDoctrine()->getManager();
        $daemons = $em->getRepository('BaseBundle:CronTask')->findBy(array('empresa'=>  $this->getUser()->getEmpresa(),'asignacionOrdenes'=>true));
        
        $result = array();
        $result['data'] = array();
        
        for ($i = 0; $i < count($daemons); $i++) 
        {
                    $result['data'][$i]['nombre']= $daemons[$i]->getNombre();
                    $result['data'][$i]['comandos']= $daemons[$i]->getComandos();
                    $result['data'][$i]['intervalo']= $daemons[$i]->getIntervalo();
                    if($daemons[$i]->getLastrun()!=null)
                    {
                        $result['data'][$i]['lastrun']= $daemons[$i]->getLastrun()->format('d-m-Y H:i:s');
                    }
                    else
                    {
                        $result['data'][$i]['lastrun']="NUNCA";
                    }
                    
                    $result['data'][$i]['activa']= $daemons[$i]->getActiva();
                     $result['data'][$i]['descripcion']= $daemons[$i]->getDescripcion();
                    $result['data'][$i]['iconoactiva']='fa fa-star-o';                     
                    if($daemons[$i]->getActiva())
                    {
                        $result['data'][$i]['iconoactiva']='fa fa-star';
                    } 
                    $result['data'][$i]['activardesactivar']= $this->generateUrl('supervisor_activar_desactivar_daemon', array('id' => $daemons[$i]->getId())); 
        }
      // print_r($ordenes[0]->getPrioridad().'-'.$ordenes[1]->getPrioridad().'-'.$ordenes[2]->getPrioridad());exit;
       //print_r($result);exit;
        return new Response(json_encode($result));
    }
    /*
     * Funcion que carga la cola de solicitudes de mayor a menor prioridad
     */
    public function loadConfiguracionesAction()
    {
        $em= $this->getDoctrine()->getManager();
        $configuraciones = $em->getRepository('BaseBundle:Configuracion')->findBy(array('empresa'=>  $this->getUser()->getEmpresa()));
        
        $result = array();
        $result['data'] = array();
        for ($i = 0; $i < count($configuraciones); $i++) 
        {
            $result['data'][$i]['nombre']= $configuraciones[$i]->getNombre();
            $result['data'][$i]['horario']= $configuraciones[$i]->getInicioHorarioAtencion()->format('H:i:s').' - '.$configuraciones[$i]->getFinHorarioAtencion()->format('H:i:s');
            $result['data'][$i]['iconoactiva']='fa fa-star-o';
            if($configuraciones[$i]->getActiva())
            {
                $result['data'][$i]['iconoactiva']='fa fa-star';
            } 
            if(!$configuraciones[$i]->getEmailViaticos())
            {
                $result['data'][$i]['emailviaticos']= '<b>No Configurado</b>';
            }
            else
            {
                $result['data'][$i]['emailviaticos']= $configuraciones[$i]->getEmailViaticos();
            }
            $result['data'][$i]['editar']= $this->generateUrl('supervisor_configuracion_edit', array('id' => $configuraciones[$i]->getId()));
            $result['data'][$i]['activardesactivar']= $this->generateUrl('supervisor_activar_desactivar_configuracion', array('id' => $configuraciones[$i]->getId()));
            $result['data'][$i]['copiar']= $this->generateUrl('supervisor_copiar_configuracion', array('id' => $configuraciones[$i]->getId()));
            $result['data'][$i]['remove']= $this->generateUrl('supervisor_configuracion_remove', array('id' => $configuraciones[$i]->getId()));
            if(!$configuraciones[$i]->getEmailVacaciones())
            {
                $result['data'][$i]['emailvacaciones']= '<b>No Configurado</b>';
            }
            else
            {
                $result['data'][$i]['emailvacaciones']= $configuraciones[$i]->getEmailVacaciones();
            }
            if(!$configuraciones[$i]->getDescripcion())
            {
                $result['data'][$i]['descripcion']= '<b>No definido...</b>';
            }
            else
            {
                $result['data'][$i]['descripcion']= $configuraciones[$i]->getDescripcion();
            }
            $result['data'][$i]['activa']= $configuraciones[$i]->getActiva(); 
            $result['data'][$i]['empresa']= $configuraciones[$i]->getEmpresa()->__toString();
            if(!$configuraciones[$i]->getLastCounter())
            {
                $result['data'][$i]['lastcounter']= '<b>No configurado</b>';
            }
            else
            {
                $result['data'][$i]['lastcounter']= $configuraciones[$i]->getLastCounter()->__toString();
            }
             $result['data'][$i]['feeEmergencia']= $configuraciones[$i]->getFeeEmergencia();
            $result['data'][$i]['ponderacionsvi']= $configuraciones[$i]->getPonderacionSVI();
            $result['data'][$i]['ponderacionnosvi']= $configuraciones[$i]->getPonderacionNOSVI();
            $result['data'][$i]['tiempoanulacion']= ($configuraciones[$i]->getTiempoAnulacion()/60).' min';
            $result['data'][$i]['tiempoprimeraalerta']= ($configuraciones[$i]->getTiempoPrimeraAlerta()/60).' min';
            $result['data'][$i]['tiemposegundaalerta']= ($configuraciones[$i]->getTiempoSegundaAlerta()/60).' min';
            $result['data'][$i]['tiempoemision']= ($configuraciones[$i]->getTiempoEmision()/60).' min';
            $result['data'][$i]['tiemporevision']= ($configuraciones[$i]->getTiempoRevision()/60).' min';
            $result['data'][$i]['tiempofpcash']= ($configuraciones[$i]->getTiempoFomaPagoCash()/60).' min';
            $result['data'][$i]['tiempofppp']= ($configuraciones[$i]->getTiempoFomaPagoPlanPiloto()/60).' min';
            $result['data'][$i]['tiempofpvtc']= ($configuraciones[$i]->getTiempoFomaPagoVtc()/60).' min';
            $result['data'][$i]['tiempoiatalocal']= ($configuraciones[$i]->getTiempoLocal()/60).' min';
            $result['data'][$i]['tiempoiataremoto']= ($configuraciones[$i]->getTiempoRemota()/60).' min';
            $result['data'][$i]['tiempoxpasajero']= ($configuraciones[$i]->getTiempoPorPasajero()/60).' min';
            $result['data'][$i]['slapp']= ($configuraciones[$i]->getTiempoRespuestaPlanPiloto()/60).' min';
            $result['data'][$i]['sla']= ($configuraciones[$i]->getTiempoRespuestaNormal()/60).' min';
            $result['data'][$i]['ponderacionpp']= $configuraciones[$i]->getPonderacionPlanPiloto();
            $result['data'][$i]['ponderacionnormal']= $configuraciones[$i]->getPonderacionNoPlanPiloto();
            $result['data'][$i]['ponderacionemision']= $configuraciones[$i]->getPonderacionEmision();
            $result['data'][$i]['ponderacionrevision']= $configuraciones[$i]->getPonderacionRevision();
            $result['data'][$i]['ponderacionanulacion']= $configuraciones[$i]->getPonderacionAnulacion();
        }
        
        
      // print_r($ordenes[0]->getPrioridad().'-'.$ordenes[1]->getPrioridad().'-'.$ordenes[2]->getPrioridad());exit;
       //print_r($result);exit;
        return new Response(json_encode($result));
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
       
       return $result;
   }
   public function applyFilterCounter($ordenes,$filtro)
   {
       $result=array();
       if($filtro=='all')
       {
           foreach ($ordenes as $item) {
              if($item->getUsuario()) 
              {
                $result[]=  $item;
              }
           }
       }
       else if($filtro==-2)//Las ordenes no asignadas
       {
           foreach ($ordenes as $item) {
              if($item->getUsuario()==null) 
              {
                $result[]=  $item;
              }
           }
       }
      else if($filtro!=-2 && $filtro!='all')
       {
           
           foreach ($ordenes as $item) {
              if($item->getUsuario() && $item->getUsuario()->getId()==$filtro) 
              {
                $result[]=  $item;
              }
           }
       }
       
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
   
   /*
    * Funcionalidad pra acragar el reporte de incumplimiento de sla
    */
   public function ReporteIncumplimientoSlaAction()
    {
	$em=  $this->getDoctrine()->getManager();
        // Creates a simple grid based on your entity (ORM)
        $source = new Entity('EmisionesBundle:Orden');
        // Get a Grid instance
        $grid = $this->get('grid');
        /*Esconder de la cola global estas columnas que son para el dashboard*/
        $grid->hideColumns('prioridad');
        $grid->hideColumns('recordGds');
        $grid->hideColumns('agente.nombre');
         /*Esconder de la cola global estas columnas que son para el dashboard*/
        
        $source->manipulateRow(
            function ($row) 
            {  
                //para cnvertir a minutos
                $row->setField('tiempoRealProcesamiento',$row->getEntity()-> getTiempoRealProcesamiento()/60);
                // Change the output of the new column with your own code at entity.
                $row->setField('tipo', $row->getEntity()->getTipo());
                return $row;
            }
        );
        $tableAlias = $source->getTableAlias();
        $source->manipulateQuery(
            function ($query) use ($tableAlias)
            {
               $query->andWhere($tableAlias .'.estado !=2 ');
            }
        );
        // Attach the source to the grid
        $grid->setSource($source);
        // Set the selector of the number of items per page
        $grid->setDefaultFilters(array(                         
            'fecha' => array('operator' => 'btwe','from' => date('d-m-Y 00:00'), 'to' => date('d-m-Y 23:59')), // Range filter with the operator 'tbw'   
            'slaIncumplido'=>true
            ));
        
        
        
        
        $rowShow=new RowAction('<i style="margin-left:7px;" class="text-aqua fa fa-eye"></i>','counter_emision_show');
        $rowShow->setRole(array('ROLE_SUPERVISOR'));
        $rowShow->manipulateRender(
            function ($action, $row)
            { if ($row->getEntity() instanceof Anulacion) {
                    $action->setRoute('counter_anulacion_show');
                }
                if ($row->getEntity() instanceof Emision) {
                    $action->setRoute('counter_emision_show');
                }
                if ($row->getEntity() instanceof Revision) {
                    $action->setRoute('counter_revision_show');
                }
                return $action;
            }
        );
        
        $TypeColumn = new BlankColumn(array('operatorsVisible'=>true,'filterable' => false,'type'=>'text','title'=>'Tipo','align'=>'center','size'=>8));
        $TypeColumn->setId('tipo') ;
        $grid->addColumn($TypeColumn,11);      
     
        
        $FPColumn = new BlankColumn(array('operatorsVisible'=>true,'filterable' => true,'type'=>'text','title'=>'FP','align'=>'center','size'=>10));
        $FPColumn->setId('fp') ;
        $FPColumn->setRole(array("ROLE_SUPERVISOR"));
        $FPColumn->manipulateRenderCell(
            function ($action, $row)
            { 
             $contTc=0;
             $contCash=0;
             $fps=$row->getEntity()->getFormasPagos();
             foreach ($fps as $value) {
                 if($value instanceof Tarjetacredito)
                 {
                    $contTc++;
                 }
                 else
                 {
                   $contCash++;  
                 }
             }           
             if ($contTc>0 && $contCash>0) {
                return "Mixta"; 
             }
             else if ($contTc==0 && $contCash==0) {
                 if ($row->getEntity() instanceof Anulacion) {
                     if ($row->getEntity()->getVtc()!=null) {
                        return "TC"; 
                     }
                     else
                     {
                         return "Cash";
                     }
                 }
                 else
                 {
                     return "Ninguna";
                 }
             }
             else if ($contTc>0 && $contCash==0) {
                 return "TC";
             }
             else if ($contTc==0 && $contCash>0) {
                 return "Cash";
             }
            }
        );
        $grid->addColumn($FPColumn);
        
       
       
        $csvExport=new CSVExport('CSV Export');
        $csvExport->setRole(array('ROLE_SUPERVISOR','ROLE_SUPERVISOR_COBRANZA'));    
        $grid->addRowAction($rowShow);
        $grid->addExport($csvExport);
        return $grid->getGridResponse('EmisionesBundle:SupervisorAdministrarAgentes/Reportes:incumplimientoSla.html.twig');
    }
}
