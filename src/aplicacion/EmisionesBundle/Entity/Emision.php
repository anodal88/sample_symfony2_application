<?php

namespace aplicacion\EmisionesBundle\Entity;
use aplicacion\EmisionesBundle\Entity\Orden;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Emision
 *
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza")
 * @ORM\Entity(repositoryClass="aplicacion\EmisionesBundle\Entity\EmisionRepository") 
 */
class Emision extends Orden
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Gedmo\Versioned()
     * @ORM\Column(name="reserva_pnr", type="text")
     */
    private $reservaPnr;

    /**
     * @var string
     * @Gedmo\Versioned()
     * @ORM\Column(name="tarifa_reserva",type="text" )
     */
    private $tarifaReserva;


    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    
    /**
     * @var \DateTime
     */
    protected $fecha;

    /**
     * @var string
     */
    protected $tipoBoleto;

    /**
     * @var string
     */
    protected $comentario;

    /**
     * @var string
     */
    protected $recordGds;

    /**
     * @var string
     */
    protected $tourcode;

    /**
     * @var float
     */
    protected $feeServicios;

    /**
     * @var string
     */
    protected $observaciones;

   

    /**
     * @var \aplicacion\EmisionesBundle\Entity\Estadoorden
     */
    protected $estado;

    /**
     * @var \aplicacion\EmisionesBundle\Entity\Gds
     */
    protected $gds;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $formasPagos;


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
     * Set reservaPnr
     *
     * @param string $reservaPnr
     * @return Emision
     */
    public function setReservaPnr($reservaPnr)
    {
        $this->reservaPnr = $reservaPnr;

        return $this;
    }

    /**
     * Get reservaPnr
     *
     * @return string 
     */
    public function getReservaPnr()
    {
        return $this->reservaPnr;
    }

    /**
     * Set tarifaReserva
     *
     * @param string $tarifaReserva
     * @return Emision
     */
    public function setTarifaReserva($tarifaReserva)
    {
        $this->tarifaReserva = $tarifaReserva;

        return $this;
    }

    /**
     * Get tarifaReserva
     *
     * @return string 
     */
    public function getTarifaReserva()
    {
        return $this->tarifaReserva;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Emision
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
     * @return Emision
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
     * @return Emision
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
     * @return Emision
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
     * @return Emision
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
     * @return Emision
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
     * @return Emision
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
     * Set estado
     *
     * @param \aplicacion\EmisionesBundle\Entity\Estadoorden $estado
     * @return Emision
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
     * @return Emision
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
     * @return Emision
     */
    public function addFormasPago(\aplicacion\EmisionesBundle\Entity\Formapago $formasPagos)
    {
        parent::addFormasPago($formasPagos);
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
     * @var string
     */
    protected $numeroOrden;

    /**
     * @var integer
     */
    protected $prioridad;


    /**
     * Set numeroOrden
     *
     * @param string $numeroOrden
     * @return Emision
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
     * @var \aplicacion\EmisionesBundle\Entity\Ciudad
     */
    protected $ciudadDestino;


    /**
     * Set ciudadDestino
     *
     * @param \aplicacion\EmisionesBundle\Entity\Ciudad $ciudadDestino
     * @return Emision
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
     * @var integer
     */
    protected $numPasajeros;


    /**
     * Set numPasajeros
     *
     * @param integer $numPasajeros
     * @return Emision
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
     * @var \DateTime
     */
    protected $horaAsignacion;


    /**
     * Set horaAsignacion
     *
     * @param \DateTime $horaAsignacion
     * @return Emision
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
     * @var boolean
     */
    protected $procesadaEmergencia;


    /**
     * Set procesadaEmergencia
     *
     * @param boolean $procesadaEmergencia
     * @return Emision
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
     * @var string
     */
    protected $adjunto;


    /**
     * Set adjunto
     *
     * @param string $adjunto
     * @return Emision
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
     * @var string
     */
    protected $detalleAprobacion;

    /**
     * @var string
     */
    protected $aprobadoCaja;

    /**
     * @var boolean
     */
    protected $modificadoSupervisorCobros;


    /**
     * Set detalleAprobacion
     *
     * @param string $detalleAprobacion
     * @return Emision
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
     * @return Emision
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
     * Set modificadoSupervisorCobros
     *
     * @param boolean $modificadoSupervisorCobros
     * @return Emision
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
     * @var integer
     */
    protected $tiempoProceso;


    /**
     * Set tiempoProceso
     *
     * @param integer $tiempoProceso
     * @return Emision
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
     * @var \DateTime
     */
    protected $procesedAt;

    /**
     * @var integer
     */
    protected $tiempoRealProcesamiento;

    /**
     * @var boolean
     */
    protected $slaIncumplido;


    /**
     * Set procesedAt
     *
     * @param \DateTime $procesedAt
     * @return Emision
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
     * @return Emision
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
     * @return Emision
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
     * @var string
     */
    protected $tipoPago;


    /**
     * Set tipoPago
     *
     * @param string $tipoPago
     * @return Emision
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
