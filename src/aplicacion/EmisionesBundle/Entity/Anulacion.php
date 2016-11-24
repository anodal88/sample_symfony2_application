<?php

namespace aplicacion\EmisionesBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Anulacion
 *
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza")
 * @ORM\Entity(repositoryClass="aplicacion\EmisionesBundle\Entity\AnulacionRepository")
 */
class Anulacion extends Orden
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
     * @ORM\Column(name="datos_boleto", type="text")
     */
    private $datosBoleto;

    /**
     * @var string
     * @Gedmo\Versioned()
     * @ORM\Column(name="vtc", type="text")
     */
    private $vtc;

    /**
     * @var string
     * @Gedmo\Versioned()
     * @ORM\Column(name="tarjet", type="string", length=255)
     */
    private $tarjet;
   

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
     * Set datosBoleto
     *
     * @param string $datosBoleto
     * @return Anulacion
     */
    public function setDatosBoleto($datosBoleto)
    {
        $this->datosBoleto = $datosBoleto;

        return $this;
    }

    /**
     * Get datosBoleto
     *
     * @return string 
     */
    public function getDatosBoleto()
    {
        return $this->datosBoleto;
    }

    /**
     * Set vtc
     *
     * @param string $vtc
     * @return Anulacion
     */
    public function setVtc($vtc)
    {
        $this->vtc = $vtc;

        return $this;
    }

    /**
     * Get vtc
     *
     * @return string 
     */
    public function getVtc()
    {
        return $this->vtc;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
     */
    public function addFormasPago(\aplicacion\EmisionesBundle\Entity\Formapago $formasPagos)
    {
        parent::addFormasPago($formasPagos);
        /*$this->formasPagos[] = $formasPagos;
        return $this;*/
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
     * Set numeroOrden
     *
     * @param string $numeroOrden
     * @return Anulacion
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
     * @var integer
     */
    protected $prioridad;


    
    /**
     * @var \aplicacion\EmisionesBundle\Entity\Ciudad
     */
    protected $ciudadDestino;


    /**
     * Set ciudadDestino
     *
     * @param \aplicacion\EmisionesBundle\Entity\Ciudad $ciudadDestino
     * @return Anulacion
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
     * @return Anulacion
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
     * Set tarjet
     *
     * @param string $tarjet
     * @return Anulacion
     */
    public function setTarjet($tarjet)
    {
        $this->tarjet = $tarjet;

        return $this;
    }

    /**
     * Get tarjet
     *
     * @return string 
     */
    public function getTarjet()
    {
        return $this->tarjet;
    }
    /**
     * @var \DateTime
     */
    protected $horaAsignacion;


    /**
     * Set horaAsignacion
     *
     * @param \DateTime $horaAsignacion
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
     * @return Anulacion
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
