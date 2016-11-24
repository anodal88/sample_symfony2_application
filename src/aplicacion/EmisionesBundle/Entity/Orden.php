<?php

namespace aplicacion\EmisionesBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use aplicacion\EmisionesBundle\Entity\Emision;
use aplicacion\EmisionesBundle\Entity\Revision;
use aplicacion\EmisionesBundle\Entity\Anulacion;
use Symfony\Component\Validator\Constraints as Assert;
use APY\DataGridBundle\Grid\Mapping as GRID;



/**
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="tipo", type="string")
 * @ORM\DiscriminatorMap({"emision" = "Emision", "revision" = "Revision","anulacion" = "Anulacion"})
 * @ORM\Entity(repositoryClass="aplicacion\EmisionesBundle\Entity\OrdenRepository") 
 * @GRID\Source(columns="id,numeroOrden,prioridad,recordGds,usuario.username,agente.nombre,agente.agencia.nombre,estado.nombre,fecha,aprobadoCaja,detalleAprobacion,slaIncumplido,horaAsignacion,procesedAt,tiempoRealProcesamiento,tipoPago,ciudadDestino.nombre")
 */
class Orden
{
  

    /**
     * @var integer
     * @GRID\Column(title="Id", size="2", type="text",visible=false,filterable=false)
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
       
    /**
     * @var \DateTime
     * @Gedmo\Versioned()
     * @ORM\Column(name="fecha", type="datetime")
     * @GRID\Column(title="Hora",sortable=false,defaultOperator="btwe",operators={"btwe","btw"}, size="12", type="datetime",format="d-m-Y H:i:s",align="center")
     */
    protected $fecha;
    
    /**
     * @Gedmo\Versioned()
     * @ORM\Column(name="horaAsignacion", type="datetime",nullable=true)
     * @GRID\Column(title="Asignada",sortable=false,filterable=false,defaultOperator="btwe",operators={"btwe","btw"}, size="12", type="datetime",format="d-m-Y H:i:s",align="center",role={"ROLE_SUPERVISOR"})
     */
    //Hora en que se asigno la orden a un counter
    protected $horaAsignacion;
    
    /**
     * @Gedmo\Versioned()
     * @ORM\Column(name="procesedAt", type="datetime",nullable=true)
     * @GRID\Column(title="Procesada",sortable=false,filterable=false,defaultOperator="btwe",operators={"btwe","btw"}, size="12", type="datetime",format="d-m-Y H:i:s",align="center",role={"ROLE_SUPERVISOR"})
     */    
    protected $procesedAt;
    
    /**
     * @var integer
     * @ORM\Column(name="tiempoRealProcesamiento", type="integer")
     * @GRID\Column(title="TPR",sortable=false,size="2",align="center",visible=true, filterable=false,role={"ROLE_SUPERVISOR"})
     */
    protected $tiempoRealProcesamiento;
    
    /**
     * @Gedmo\Versioned()
     * @ORM\Column(name="slaIncumplido", type="boolean",options={"default" = false})
     * @GRID\Column(title="Incumplido",sortable=false,size="3",align="center",visible=true, filterable=true,role={"ROLE_SUPERVISOR"})
     */
    protected $slaIncumplido;
    
    
    /**
     * @var string
     * @Gedmo\Versioned()
     * @ORM\Column(name="numero_orden", type="string", length=255)
     * @GRID\Column(title="Numero",sortable=false,operatorsVisible=false,size="10", type="text",align="center")
     */
    protected $numeroOrden;
    
    /**
     * @ORM\Column(name="prioridad", type="integer")
     * @GRID\Column(title="Prioridad",sortable=false,size="3", type="text",align="center", filterable=false,role={"ROLE_SUPERVISOR","ROLE_COUNTER"})
     */
    protected $prioridad;
    
   /**
     * @Gedmo\Versioned()
     * @ORM\Column(name="procesadaEmergencia", type="boolean",nullable=false)
     */
    protected $procesadaEmergencia;
    
 
    
    /**
     * @Gedmo\Versioned()     
     * @ORM\Column(name="numPasajeros", type="integer")
     */
    protected $numPasajeros;

    /**
     * @var string
     * @Gedmo\Versioned()
     * @ORM\Column(name="tipo_boleto", type="string", length=255)
     */
    protected $tipoBoleto;

    
    /**
     * @var string
     * @Gedmo\Versioned()
     * @ORM\Column(name="comentario", type="text", nullable= true)
     */
    protected $comentario;
    
    /**
     * @Gedmo\Versioned()
     * @var agente
     * @ORM\ManyToOne(targetEntity="Agente", inversedBy="ordenes")
     * @ORM\JoinColumn(name="agente", referencedColumnName="id")
     * @GRID\Column(field="agente.nombre",sortable=false,operatorsVisible=false,title="Agente", size="10", type="text",align="center",role={"ROLE_COUNTER","ROLE_SUPERVISOR","ROLE_SUPERVISOR_COBRANZA","ROLE_CAJA"})
     * @GRID\Column(field="agente.agencia.nombre",sortable=false,operatorsVisible=false,title="Agencia", size="20", type="text",align="center")
     */   
    private $agente;
    
    /**
     * @Gedmo\Versioned() 
     * @var usuario
     * @ORM\ManyToOne(targetEntity="Usuariointerno", inversedBy="ordenes")
     * @ORM\JoinColumn(name="usuario", referencedColumnName="id")
     * @GRID\Column(field="usuario.username",sortable=false,filter="select", selectFrom="source",defaultOperator="eq",operatorsVisible=true,operators={"eq","isNull","isNotNull"},title="Counter", size="10", type="text",align="center",role={"ROLE_COUNTER","ROLE_SUPERVISOR","ROLE_SUPERVISOR_COBRANZA","ROLE_CAJA"})
     */   
    private $usuario;
    
   
    
    /**
     * @Gedmo\Versioned()
     * @var estado
     * @ORM\ManyToOne(targetEntity="Estadoorden", inversedBy="ordenes")
     * @ORM\JoinColumn(name="estado", referencedColumnName="id")
     * @GRID\Column(field="estado.nombre",sortable=false,filter="select", selectFrom="values", values={""="Todos","Pendiente"="Pendiente","Procesada"="Procesada","Rechazada"="Rechazada"},title="Estado",operatorsVisible=false, size="7", type="text" ,align="center",role={"ROLE_SUPERVISOR","ROLE_COUNTER"})
     */   
    protected $estado;
    
    /**
     * @Gedmo\Versioned()
     * @var empresa
     * @ORM\ManyToOne(targetEntity="aplicacion\BaseBundle\Entity\Empresa", inversedBy="ordenes")
     * @ORM\JoinColumn(name="empresa", referencedColumnName="id")
     */   
    private $empresa;

    /**
     * @var string
     * @Gedmo\Versioned()
     * @ORM\Column(name="record_gds", type="text")
     * @GRID\Column(title="Record",sortable=false,operatorsVisible=false,size="12", type="text",align="center",role={"ROLE_COUNTER","ROLE_SUPERVISOR","ROLE_SUPERVISOR_COBRANZA","ROLE_CAJA"})
     */
    protected $recordGds;

    /**
     * @var string
     * @Gedmo\Versioned()
     * @ORM\Column(name="tourcode", type="string", length=255,nullable=true)
     */
    protected $tourcode;
    
    /**
     * @var integer
     * @ORM\Column(name="tiempoProceso", type="integer")
     */
    protected $tiempoProceso;
    
   

    /**
     * @var float
     * @Gedmo\Versioned()
     * @Assert\GreaterThanOrEqual(
     *     value = 0,
     *     message="Este valor debe ser mayor o igual que {{ compared_value }}."
     * )
     * @ORM\Column(name="fee_servicios", type="float")
     */
    protected $feeServicios;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="text",nullable=true)
     */
    protected $observaciones;
    
     /**
     * @var string
     *
     * @ORM\Column(name="detalleAprobacion", type="text",nullable=true)
     * @GRID\Column(field="detalleAprobacion",sortable=false,operatorsVisible=false,visible=false,title="Observaciones Conciliacion", size="7", type="text",align="center",role={"ROLE_CAJA"})
     * 
     */
    protected $detalleAprobacion;
    
   
    
     /**
     * @var string
     * 
     * @ORM\Column(name="aprobadoCaja", type="string", length=255,nullable=true,options={"default" = "No Conciliada"})
     * @GRID\Column(title="Conciliado",sortable=false,operatorsVisible=false,filter="select", selectFrom="values", values={""="Todos","No Conciliada"="No Conciliada","Pago Confirmado"="Pago Confirmado","Pendiente Pago"="Pendiente Pago", "Anulada"="Anulada"},size="10", type="text",align="center",role={"ROLE_CAJA","ROLE_SUPERVISOR_COBRANZA"})
     */
    protected $aprobadoCaja;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="modificadoSupervisorCobros", type="boolean",options={"default" = false})
     
     */
    protected $modificadoSupervisorCobros;
    
   
    
    /**
     * @var string
     *
     * @ORM\Column(name="adjunto", type="text",nullable=true)
     */
    protected $adjunto;

  
    /**
     * @var gds
     * @Gedmo\Versioned()
     * @ORM\ManyToOne(targetEntity="Gds", inversedBy="ordenes")
     * @ORM\JoinColumn(name="gds", referencedColumnName="id")
     */   
    protected $gds;
    
    /**
     * @Gedmo\Versioned()
     * @var ciudadDestino
     * @ORM\ManyToOne(targetEntity="Ciudad", inversedBy="ordenes")
     * @ORM\JoinColumn(name="ciudadDestino", referencedColumnName="id")
     * @GRID\Column(title="Ciudad",field="ciudadDestino.nombre",sortable=false,operatorsVisible=false,filter="select", selectFrom="values", values={""="Todos","Quito"="Quito","Guayaquil"="Guayaquil"},size="5", type="text",align="center",role={"ROLE_CAJA","ROLE_SUPERVISOR_COBRANZA"})
     */   
    protected $ciudadDestino;
    
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Formapago", mappedBy="orden",cascade={"persist"})
     * 
     */    
    protected $formasPagos;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="tipoPago", type="string", length=255,nullable=false)
     * @GRID\Column(title="Tipo Pago",sortable=false,operatorsVisible=false,filter="select", selectFrom="values", values={""="Todos","Cash"="Cash","Tarjeta Cred"="Tarjeta Cred", "Mixta"="Mixta"},size="10", type="text",align="center",role={"ROLE_CAJA","ROLE_SUPERVISOR_COBRANZA"})
     */
    protected $tipoPago;
    
    

    public function __construct() {
        $this->formasPagos = new ArrayCollection();
        $this->modificadoSupervisorCobros=false;
        $this->procesadaEmergencia=false;
        $this->slaIncumplido=false;
        $this->tiempoRealProcesamiento=0;
        $this->aprobadoCaja="No Conciliada";
       
    }
    
    
    /*
     * Funcion que me devuelve si la hora actual  
     * esta fuera de tiempo SLA
     */
    public function isOutOfTimeAlert()
    {
            
            if($this->getTimeLimit()+$this->timeOfProcess() <  time())
            {
                return true;
            }
        return false;
    }
    /*
     * Funcion que me devuelve si la hora actual coincide con el 
     * lanzamiento de la primera alerta
     */
    public function isTimeToFirsAlert()
    {
            $timefirstalert= $this->getTimeLimit() - $this->getEmpresa()->getConfiguracionActiva()->getTiempoPrimeraAlerta();
            $timesecondalert= $this->getTimeLimit() - $this->getEmpresa()->getConfiguracionActiva()->getTiempoSegundaAlerta();
            if($timefirstalert <= time() && time()<= $timesecondalert)
            {
                return true;
            }
        return false;
    }
    /*
     * Funcion que me devuelve el tiempo real que le falta a esta 
     * orden para que comience a incumplir con su SLA
     */
    public function getRealTimeToFailSLA()
    {
        return  strtotime($this->fecha->format('Y-m-d H:i:s'))+$this->getSLA()-time();
    }
    /*
     * Funcion que me da el SLA que Aplica para esta orden
     */
    public function getSLA()
    {
        if($this->getEmpresa()->getConfiguracionActiva())
        {
            if($this->isPilotPlan())
            {
                return $this->getEmpresa()->getConfiguracionActiva()->getTiempoRespuestaPlanPiloto();
            }
            else
            {
                return $this->getEmpresa()->getConfiguracionActiva()->getTiempoRespuestaNormal();
            }
        }
        else
        {
            //throw new \Exception('No existe una configuracion activa para esta empresa');
            $this->get('session')->getFlashBag()->add('error', 'La empresa no tiene ninguna configuracion activa!');
        }
    }
    /*
     * Funcion que me devuelve si la hora actual coincide con el 
     * lanzamiento de la segunda alerta
     */
    public function isTimeToSecondAlert()
    {
            $timesecondalert= $this->getTimeLimit() - $this->getEmpresa()->getConfiguracionActiva()->getTiempoSegundaAlerta();
            $tiempolimite = $this->getTimeLimit();
            if($timesecondalert <= time() && time()<= $tiempolimite)
            {
                return true;
            }
        return false;
    }
    
    /*
     * Funcion que devuelve si la hora actual es su hora limite de la orden en cuestion
     */
    public function isLimitHour()
    {
      //La hora limite es desde que la orden llego+tiempo de respuesta
      // de la configuracion de la Empresa - tiempo de proceso
        if($this->getEmpresa()->getConfiguracionActiva()!=null)
        {
           
            if($this->isPilotPlan())
            {
                // print('es plan piloto');
                $limitHour=  strtotime($this->fecha->format('Y-m-d H:i:s'))+($this->getEmpresa()->getConfiguracionActiva()->getTiempoRespuestaPlanPiloto()-($this->timeOfProcess())); 
                $fintimelimit=strtotime($this->fecha->format('Y-m-d H:i:s'))+$this->getEmpresa()->getConfiguracionActiva()->getTiempoRespuestaPlanPiloto();
                if($limitHour <= time() && time()<= $fintimelimit)
                {
                    return true;
                }
            }
            else
            {
                 //print('NOO es plan piloto');
                $limitHour=  strtotime($this->fecha->format('Y-m-d H:i:s'))+($this->getEmpresa()->getConfiguracionActiva()->getTiempoRespuestaNormal()-($this->timeOfProcess())); 
                $fintimelimit=strtotime($this->fecha->format('Y-m-d H:i:s'))+$this->getEmpresa()->getConfiguracionActiva()->getTiempoRespuestaNormal();
                if($limitHour <= time() && time()<=$fintimelimit)
                {
                    return true;
                }
            }
        }
       return false;
    }
    /*
     * Funcion que me devuelve la hora en UNIX enla cual la orden esta 
     * en su hora limite
     */
    public function getTimeLimit()
    {
        if($this->isPilotPlan())
        {
           return  strtotime($this->fecha->format('Y-m-d H:i:s'))+$this->getEmpresa()->getConfiguracionActiva()->getTiempoRespuestaPlanPiloto()-$this->timeOfProcess(); 
            
        }
      
     return strtotime($this->fecha->format('Y-m-d H:i:s'))+$this->getEmpresa()->getConfiguracionActiva()->getTiempoRespuestaNormal()-$this->timeOfProcess(); 
       
    }
    /*
     * Funcion que dado una orden me dice si es plan piloto
     */
    public function isPilotPlan()
    {
         foreach ($this->getFormasPagos() as $fp) {
            if($fp instanceof Tarjetacredito)
            {
                if($fp->getEmisorVtc()=='AGENCIA')
                {
                   if($fp->getAerolinea()->getAgenciasPlanPiloto()->contains($this->getAgente()->getAgencia()))
                    {
                        return true;
                    } 
                }
            }
        }
        return false;
    }
    /*
     * Funcion que determina el tiempo que demora procesar esta orden
     */
    public function timeOfProcess()
    {
        $config=  $this->getEmpresa()->getConfiguracionActiva();
        $tiempoTo=0;
        $cash=0;
        $planpiloto=0;
        $vtc=0;
        $destino=0;
        
        
        switch ($this->getTipo()) {
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
        
        foreach ($this->getFormasPagos() as $fp) {
            if(($fp instanceof Pagodirecto)|| ($fp instanceof DepefectivoTransferenciabancaria))
            {
                $cash+=$config->getTiempoFomaPagoCash();
            }
            if($fp instanceof Tarjetacredito)
            {
                if($this->isPilotPlan()/*$fp->getAerolinea()->getAgenciasPlanPiloto()->contains($this->getAgente()->getAgencia())*/)
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
        if($this->getEmpresa()->getCiudad()== $this->getCiudadDestino())
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
        
       return ($tiempoTo+$cash+$planpiloto+$vtc+$destino)+($config->getTiempoPorPasajero()*$this->getNumPasajeros());//dejarlo en segundos
    }
    
       
    public function getTipo()
    {
        if($this instanceof Emision)
        {
            return 'Emision';
        }
        if($this instanceof Revision)
        {
            return 'Revision';
        }
        if($this instanceof Anulacion)
        {
            return 'Anulacion';
        }
        
    }
  
    
    public function timeSinceArrive()
    {
        $datetime1 =  $this->fecha;
        $datetime2 = new \DateTime("now");;
        $interval=$datetime1->diff($datetime2);
        //var_dump($interval);
        $years=$interval->format('%y%');
        $meses= $interval->format('%m%');
        $dias=$interval->format('%d%');
        $horas=$interval->format('%h%');
        $minutos=$interval->format('%i%');
        $ttp='';
        if($years!=0)
        {
            $ttp=$years.'A-'.$meses.'M-'.$dias.'D'.' '.$horas.'H '.$minutos.'m';
        }
        else
        {
           if($meses!=0) 
           {
               $ttp=$meses.'M-'.$dias.'D'.' '.$horas.'H '.$minutos.'m';
           }
           else
           {
               if($dias!= 0)
               {
                   $ttp=$dias.'D'.' '.$horas.'H '.$minutos.'m';
               }
               else
               {
                  if($horas!=0)
                  {
                     $ttp=$horas.'H '.$minutos.'m'; 
                  }
                  else
                  {
                     $ttp=$minutos.'m';  
                  }
               }
           }
        }
        
        
       return $ttp;//$interval->format('%h%').'H'.$interval->format('%i%').'M';
    }



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Orden
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set tipoBoleto
     *
     * @param string $tipoBoleto
     * @return Orden
     */
    public function setTipoBoleto($tipoBoleto)
    {
        $this->tipoBoleto = $tipoBoleto;

        return $this;
    }

    /**
     * Get tipoBoleto
     *
     * @return string 
     */
    public function getTipoBoleto()
    {
        return $this->tipoBoleto;
    }

    /**
     * Set comentario
     *
     * @param string $comentario
     * @return Orden
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;

        return $this;
    }

    /**
     * Get comentario
     *
     * @return string 
     */
    public function getComentario()
    {
        return $this->comentario;
    }

    /**
     * Set recordGds
     *
     * @param string $recordGds
     * @return Orden
     */
    public function setRecordGds($recordGds)
    {
        $this->recordGds = $recordGds;

        return $this;
    }

    /**
     * Get recordGds
     *
     * @return string 
     */
    public function getRecordGds()
    {
        return $this->recordGds;
    }

    /**
     * Set tourcode
     *
     * @param string $tourcode
     * @return Orden
     */
    public function setTourcode($tourcode)
    {
        $this->tourcode = $tourcode;

        return $this;
    }

    /**
     * Get tourcode
     *
     * @return string 
     */
    public function getTourcode()
    {
        return $this->tourcode;
    }

    /**
     * Set feeServicios
     *
     * @param float $feeServicios
     * @return Orden
     */
    public function setFeeServicios($feeServicios)
    {
        $this->feeServicios = $feeServicios;

        return $this;
    }

    /**
     * Get feeServicios
     *
     * @return float 
     */
    public function getFeeServicios()
    {
        return $this->feeServicios;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return Orden
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set agente
     *
     * @param \aplicacion\EmisionesBundle\Entity\Agente $agente
     * @return Orden
     */
    public function setAgente(\aplicacion\EmisionesBundle\Entity\Agente $agente = null)
    {
        $this->agente = $agente;

        return $this;
    }

    /**
     * Get agente
     *
     * @return \aplicacion\EmisionesBundle\Entity\Agente 
     */
    public function getAgente()
    {
        return $this->agente;
    }

    /**
     * Set estado
     *
     * @param \aplicacion\EmisionesBundle\Entity\Estadoorden $estado
     * @return Orden
     */
    public function setEstado(\aplicacion\EmisionesBundle\Entity\Estadoorden $estado = null)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return \aplicacion\EmisionesBundle\Entity\Estadoorden 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set gds
     *
     * @param \aplicacion\EmisionesBundle\Entity\Gds $gds
     * @return Orden
     */
    public function setGds(\aplicacion\EmisionesBundle\Entity\Gds $gds = null)
    {
        $this->gds = $gds;

        return $this;
    }

    /**
     * Get gds
     *
     * @return \aplicacion\EmisionesBundle\Entity\Gds 
     */
    public function getGds()
    {
        return $this->gds;
    }

    /**
     * Add formasPagos
     *
     * @param \aplicacion\EmisionesBundle\Entity\Formapago $formasPagos
     * @return Orden
     */
    public function addFormasPago(\aplicacion\EmisionesBundle\Entity\Formapago $formasPagos)
    {
        $this->formasPagos[] = $formasPagos;
        $formasPagos->setOrden($this);
        return $this;
    }
  
 

    /**
     * Remove formasPagos
     *
     * @param \aplicacion\EmisionesBundle\Entity\Formapago $formasPagos
     */
    public function removeFormasPago(\aplicacion\EmisionesBundle\Entity\Formapago $formasPagos)
    {
        $this->formasPagos->removeElement($formasPagos);
    }

    /**
     * Get formasPagos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFormasPagos()
    {
        return $this->formasPagos;
    }

    /**
     * Set numeroOrden
     *
     * @param string $numeroOrden
     * @return Orden
     */
    public function setNumeroOrden($numeroOrden)
    {
        $this->numeroOrden = $numeroOrden;

        return $this;
    }

    /**
     * Get numeroOrden
     *
     * @return string 
     */
    public function getNumeroOrden()
    {
        return $this->numeroOrden;
    }

    

   
    /**
     * Set empresa
     *
     * @param \aplicacion\BaseBundle\Entity\Empresa $empresa
     * @return Orden
     */
    public function setEmpresa(\aplicacion\BaseBundle\Entity\Empresa $empresa = null)
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get empresa
     *
     * @return \aplicacion\BaseBundle\Entity\Empresa 
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * Set usuario
     *
     * @param \aplicacion\EmisionesBundle\Entity\Usuariointerno $usuario
     * @return Orden
     */
    public function setUsuario(\aplicacion\EmisionesBundle\Entity\Usuariointerno $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \aplicacion\EmisionesBundle\Entity\Usuariointerno 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

  


    /**
     * Set ciudadDestino
     *
     * @param \aplicacion\EmisionesBundle\Entity\Ciudad $ciudadDestino
     * @return Orden
     */
    public function setCiudadDestino(\aplicacion\EmisionesBundle\Entity\Ciudad $ciudadDestino = null)
    {
        $this->ciudadDestino = $ciudadDestino;

        return $this;
    }

    /**
     * Get ciudadDestino
     *
     * @return \aplicacion\EmisionesBundle\Entity\Ciudad 
     */
    public function getCiudadDestino()
    {
        return $this->ciudadDestino;
    }

    /**
     * Set numPasajeros
     *
     * @param integer $numPasajeros
     * @return Orden
     */
    public function setNumPasajeros($numPasajeros)
    {
        $this->numPasajeros = $numPasajeros;

        return $this;
    }

    /**
     * Get numPasajeros
     *
     * @return integer 
     */
    public function getNumPasajeros()
    {
        return $this->numPasajeros;
    }

    /**
     * Set horaAsignacion
     *
     * @param \DateTime $horaAsignacion
     * @return Orden
     */
    public function setHoraAsignacion($horaAsignacion)
    {
        $this->horaAsignacion = $horaAsignacion;

        return $this;
    }

    /**
     * Get horaAsignacion
     *
     * @return \DateTime 
     */
    public function getHoraAsignacion()
    {
        return $this->horaAsignacion;
    }

    /**
     * Set procesadaEmergencia
     *
     * @param boolean $procesadaEmergencia
     * @return Orden
     */
    public function setProcesadaEmergencia($procesadaEmergencia)
    {
        $this->procesadaEmergencia = $procesadaEmergencia;

        return $this;
    }

    /**
     * Get procesadaEmergencia
     *
     * @return boolean 
     */
    public function getProcesadaEmergencia()
    {
        return $this->procesadaEmergencia;
    }



    /**
     * Set adjunto
     *
     * @param string $adjunto
     * @return Orden
     */
    public function setAdjunto($adjunto)
    {
        $this->adjunto = $adjunto;

        return $this;
    }

    /**
     * Get adjunto
     *
     * @return string 
     */
    public function getAdjunto()
    {
        return $this->adjunto;
    }

    /**
     * Set prioridad
     *
     * @param integer $prioridad
     * @return Orden
     */
    public function setPrioridad($prioridad)
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    /**
     * Get prioridad
     *
     * @return integer 
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }

   

    /**
     * Set detalleAprobacion
     *
     * @param string $detalleAprobacion
     * @return Orden
     */
    public function setDetalleAprobacion($detalleAprobacion)
    {
        $this->detalleAprobacion = $detalleAprobacion;

        return $this;
    }

    /**
     * Get detalleAprobacion
     *
     * @return string 
     */
    public function getDetalleAprobacion()
    {
        return $this->detalleAprobacion;
    }



    /**
     * Set aprobadoCaja
     *
     * @param string $aprobadoCaja
     * @return Orden
     */
    public function setAprobadoCaja($aprobadoCaja)
    {
        $this->aprobadoCaja = $aprobadoCaja;

        return $this;
    }

    /**
     * Get aprobadoCaja
     *
     * @return string 
     */
    public function getAprobadoCaja()
    {
        return $this->aprobadoCaja;
    }

    /**
     * Set cajaGeneroAnulacion
     *
     * @param boolean $cajaGeneroAnulacion
     * @return Orden
     */
    public function setCajaGeneroAnulacion($cajaGeneroAnulacion)
    {
        $this->cajaGeneroAnulacion = $cajaGeneroAnulacion;

        return $this;
    }

    /**
     * Get cajaGeneroAnulacion
     *
     * @return boolean 
     */
    public function getCajaGeneroAnulacion()
    {
        return $this->cajaGeneroAnulacion;
    }

    /**
     * Set modificadoSupervisorCobros
     *
     * @param boolean $modificadoSupervisorCobros
     * @return Orden
     */
    public function setModificadoSupervisorCobros($modificadoSupervisorCobros)
    {
        $this->modificadoSupervisorCobros = $modificadoSupervisorCobros;

        return $this;
    }

    /**
     * Get modificadoSupervisorCobros
     *
     * @return boolean 
     */
    public function getModificadoSupervisorCobros()
    {
        return $this->modificadoSupervisorCobros;
    }

    /**
     * Set tiempoProceso
     *
     * @param integer $tiempoProceso
     * @return Orden
     */
    public function setTiempoProceso($tiempoProceso)
    {
        $this->tiempoProceso = $tiempoProceso;

        return $this;
    }

    /**
     * Get tiempoProceso
     *
     * @return integer 
     */
    public function getTiempoProceso()
    {
        return $this->tiempoProceso;
    }

    /**
     * Set procesedAt
     *
     * @param \DateTime $procesedAt
     * @return Orden
     */
    public function setProcesedAt($procesedAt)
    {
        $this->procesedAt = $procesedAt;

        return $this;
    }

    /**
     * Get procesedAt
     *
     * @return \DateTime 
     */
    public function getProcesedAt()
    {
        return $this->procesedAt;
    }

    /**
     * Set tiempoRealProcesamiento
     *
     * @param integer $tiempoRealProcesamiento
     * @return Orden
     */
    public function setTiempoRealProcesamiento($tiempoRealProcesamiento)
    {
        $this->tiempoRealProcesamiento = $tiempoRealProcesamiento;

        return $this;
    }

    /**
     * Get tiempoRealProcesamiento
     *
     * @return integer 
     */
    public function getTiempoRealProcesamiento()
    {
        return $this->tiempoRealProcesamiento;
    }

    /**
     * Set slaIncumplido
     *
     * @param boolean $slaIncumplido
     * @return Orden
     */
    public function setSlaIncumplido($slaIncumplido)
    {
        $this->slaIncumplido = $slaIncumplido;

        return $this;
    }

    /**
     * Get slaIncumplido
     *
     * @return boolean 
     */
    public function getSlaIncumplido()
    {
        return $this->slaIncumplido;
    }

    

    /**
     * Set tipoPago
     *
     * @param string $tipoPago
     * @return Orden
     */
    public function setTipoPago($tipoPago)
    {
        $this->tipoPago = $tipoPago;

        return $this;
    }

    /**
     * Get tipoPago
     *
     * @return string 
     */
    public function getTipoPago()
    {
        return $this->tipoPago;
    }
}
