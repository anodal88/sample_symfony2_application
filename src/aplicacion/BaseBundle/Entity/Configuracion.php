<?php

namespace aplicacion\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Configuracion
 * @UniqueEntity("nombre",message="Ya existe una configuracion con este nombre.")
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="aplicacion\BaseBundle\Entity\ConfiguracionRepository")
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza")
 */
class Configuracion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

   /**
    * @ORM\ManyToOne(targetEntity="aplicacion\EmisionesBundle\Entity\Usuariointerno",inversedBy="configuraciones")
    * @ORM\JoinColumn(name="lastCounter", referencedColumnName="id")
    *
    */
    private $lastCounter;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\Length(
     *      min = 10,
     *      max = 150,
     *      minMessage = "El nombre de la configuracion debe tener al menos {{ limit }} caracteres",
     *      maxMessage = "El nombre de la configuracion debe tener maximo {{ limit }} characters long"
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;//nombre de la conf
    
    /**
     * @Gedmo\Versioned()
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 50,
     *      max = 250,
     *      minMessage = "La descripcion debe tener al menos {{ limit }} caracteres",
     *      maxMessage = "La descripcion debe tener maximo {{ limit }} characters long"
     * )
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;//Breve explicacion de la configuracion
    
   /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="tiempoPrimeraAlerta", type="integer")
     */
    //Tiempo para llegar al tiem limit para lanzar esta alerta
    private $tiempoPrimeraAlerta;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\NotBlank()
     * @ORM\Column(name="feeEmergencia", type="float")
     */
    //Monto en dolares que se cobra por cada ordene que son emitidas despue de las 7 00 PM
    private $feeEmergencia;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="tiempoSegundaAlerta", type="integer")
     */
    //Tiempo para llegar al tiem limit para lanzar esta alerta
    private $tiempoSegundaAlerta;
    
  
    
    
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="tiempoRespuestaPlanPiloto", type="integer")
     */
    //Tiempo maximo que emplea la empresa para dar respuesta a una solicitud de tipo plan piloto en segundos
    private $tiempoRespuestaPlanPiloto;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="tiempoRespuestaNormal", type="integer")
     */
    //Tiempo maximo que emplea la empresa para dar respuesta a una solicitud normal en segundos
    private $tiempoRespuestaNormal;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "El email '{{ value }}' no es un email valido .",
     *     checkMX = true
     * )
     * @ORM\Column(name="emailViaticos", type="string",length=255)
     */
    private $emailViaticos;
    /**
     * @Gedmo\Versioned()
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "El email '{{ value }}' no es un email valido .",
     *     checkMX = true
     * )
     * @ORM\Column(name="emailVacaciones", type="string",length=255)
     */
    private $emailVacaciones;
    

    
    /**
     * 
     * @Assert\NotBlank()
     * @var empresa
     * @ORM\ManyToOne(targetEntity="Empresa", inversedBy="configuraciones")
     * @ORM\JoinColumn(name="empresa", referencedColumnName="id")
     */   
    private $empresa;
    
    
    /**
     * @Gedmo\Versioned()
     * @var string
     *
     * @ORM\Column(name="activa", type="boolean")
     */
    private $activa;
    
    /*********Parametros para calcular el tiempo en segundos que demora una orde en procesarce******/
    
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="tiempoAnulacion", type="integer")
     */
    private $tiempoAnulacion;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="tiempoEmision", type="integer")
     */
    private $tiempoEmision;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\Time()
     * @Assert\NotBlank()
     * @ORM\Column(name="inicioHorarioAtencion", type="time")
     */
    private $inicioHorarioAtencion;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\Time()
     * @Assert\NotBlank()
     * @ORM\Column(name="finHorarioAtencion", type="time")
     */
    private $finHorarioAtencion;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="tiempoRevision", type="integer")
     */
    private $tiempoRevision;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="fomaPagoCash", type="integer")
     */
    private $tiempoFomaPagoCash;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="fomaPagoPlanPiloto", type="integer")
     */
    private $tiempoFomaPagoPlanPiloto;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="fomaPagoVtc", type="integer")
     */
    private $tiempoFomaPagoVtc;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="local", type="integer")
     */
    private $tiempoLocal;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="remota", type="integer")
     */
    private $tiempoRemota;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
    * @ORM\Column(name="tiempoPorPasajero", type="integer")
     */
    private $tiempoPorPasajero;
    
    
    
    /****************************************FIN****************************************/
    
    
    
    
    /****************************Parametros Calculo Prioridad***************************/
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="ponderacionPlanPiloto", type="integer")
     */
    private $ponderacionPlanPiloto;
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="ponderacionNoPlanPiloto", type="integer")
     */
    private $ponderacionNoPlanPiloto;
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="ponderacionEmision", type="integer")
     */
    private $ponderacionEmision;
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="ponderacionRevision", type="integer")
     */
    private $ponderacionRevision;
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="ponderacionAnulacion", type="integer")
     */
    private $ponderacionAnulacion;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="ponderacionSVI", type="integer")
     */
    private $ponderacionSVI;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Este valor debe ser mayor que {{ compared_value }}."
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="ponderacionNOSVI", type="integer")
     */
    private $ponderacionNOSVI;
    
    
    
    
    
    
    /****************************Parametros Calculo Prioridad***************************/

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
     * Set emailViaticos
     *
     * @param string $emailViaticos
     * @return Configuracion
     */
    public function setEmailViaticos($emailViaticos)
    {
        $this->emailViaticos = $emailViaticos;

        return $this;
    }

    /**
     * Get emailViaticos
     *
     * @return string 
     */
    public function getEmailViaticos()
    {
        return $this->emailViaticos;
    }

    /**
     * Set emailVacaciones
     *
     * @param string $emailVacaciones
     * @return Configuracion
     */
    public function setEmailVacaciones($emailVacaciones)
    {
        $this->emailVacaciones = $emailVacaciones;

        return $this;
    }

    /**
     * Get emailVacaciones
     *
     * @return string 
     */
    public function getEmailVacaciones()
    {
        return $this->emailVacaciones;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Configuracion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set activa
     *
     * @param boolean $activa
     * @return Configuracion
     */
    public function setActiva($activa)
    {
        $this->activa = $activa;

        return $this;
    }

    /**
     * Get activa
     *
     * @return boolean 
     */
    public function getActiva()
    {
        return $this->activa;
    }

    /**
     * Set tiempoAnulacion
     *
     * @param integer $tiempoAnulacion
     * @return Configuracion
     */
    public function setTiempoAnulacion($tiempoAnulacion)
    {
        $this->tiempoAnulacion = $tiempoAnulacion;

        return $this;
    }

    /**
     * Get tiempoAnulacion
     *
     * @return integer 
     */
    public function getTiempoAnulacion()
    {
        return $this->tiempoAnulacion;
    }

    /**
     * Set tiempoEmision
     *
     * @param integer $tiempoEmision
     * @return Configuracion
     */
    public function setTiempoEmision($tiempoEmision)
    {
        $this->tiempoEmision = $tiempoEmision;

        return $this;
    }

    /**
     * Get tiempoEmision
     *
     * @return integer 
     */
    public function getTiempoEmision()
    {
        return $this->tiempoEmision;
    }

    /**
     * Set tiempoRevision
     *
     * @param integer $tiempoRevision
     * @return Configuracion
     */
    public function setTiempoRevision($tiempoRevision)
    {
        $this->tiempoRevision = $tiempoRevision;

        return $this;
    }

    /**
     * Get tiempoRevision
     *
     * @return integer 
     */
    public function getTiempoRevision()
    {
        return $this->tiempoRevision;
    }

    /**
     * Set tiempoFomaPagoCash
     *
     * @param integer $tiempoFomaPagoCash
     * @return Configuracion
     */
    public function setTiempoFomaPagoCash($tiempoFomaPagoCash)
    {
        $this->tiempoFomaPagoCash = $tiempoFomaPagoCash;

        return $this;
    }

    /**
     * Get tiempoFomaPagoCash
     *
     * @return integer 
     */
    public function getTiempoFomaPagoCash()
    {
        return $this->tiempoFomaPagoCash;
    }

    /**
     * Set tiempoFomaPagoPlanPiloto
     *
     * @param integer $tiempoFomaPagoPlanPiloto
     * @return Configuracion
     */
    public function setTiempoFomaPagoPlanPiloto($tiempoFomaPagoPlanPiloto)
    {
        $this->tiempoFomaPagoPlanPiloto = $tiempoFomaPagoPlanPiloto;

        return $this;
    }

    /**
     * Get tiempoFomaPagoPlanPiloto
     *
     * @return integer 
     */
    public function getTiempoFomaPagoPlanPiloto()
    {
        return $this->tiempoFomaPagoPlanPiloto;
    }

    /**
     * Set tiempoFomaPagoVtc
     *
     * @param integer $tiempoFomaPagoVtc
     * @return Configuracion
     */
    public function setTiempoFomaPagoVtc($tiempoFomaPagoVtc)
    {
        $this->tiempoFomaPagoVtc = $tiempoFomaPagoVtc;

        return $this;
    }

    /**
     * Get tiempoFomaPagoVtc
     *
     * @return integer 
     */
    public function getTiempoFomaPagoVtc()
    {
        return $this->tiempoFomaPagoVtc;
    }

    /**
     * Set tiempoLocal
     *
     * @param integer $tiempoLocal
     * @return Configuracion
     */
    public function setTiempoLocal($tiempoLocal)
    {
        $this->tiempoLocal = $tiempoLocal;

        return $this;
    }

    /**
     * Get tiempoLocal
     *
     * @return integer 
     */
    public function getTiempoLocal()
    {
        return $this->tiempoLocal;
    }

    /**
     * Set tiempoRemota
     *
     * @param integer $tiempoRemota
     * @return Configuracion
     */
    public function setTiempoRemota($tiempoRemota)
    {
        $this->tiempoRemota = $tiempoRemota;

        return $this;
    }

    /**
     * Get tiempoRemota
     *
     * @return integer 
     */
    public function getTiempoRemota()
    {
        return $this->tiempoRemota;
    }

    /**
     * Set tiempoPorPasajero
     *
     * @param integer $tiempoPorPasajero
     * @return Configuracion
     */
    public function setTiempoPorPasajero($tiempoPorPasajero)
    {
        $this->tiempoPorPasajero = $tiempoPorPasajero;

        return $this;
    }

    /**
     * Get tiempoPorPasajero
     *
     * @return integer 
     */
    public function getTiempoPorPasajero()
    {
        return $this->tiempoPorPasajero;
    }

    /**
     * Set lastCounter
     *
     * @param \aplicacion\EmisionesBundle\Entity\Usuariointerno $lastCounter
     * @return Configuracion
     */
    public function setLastCounter(\aplicacion\EmisionesBundle\Entity\Usuariointerno $lastCounter = null)
    {
        $this->lastCounter = $lastCounter;

        return $this;
    }

    /**
     * Get lastCounter
     *
     * @return \aplicacion\EmisionesBundle\Entity\Usuariointerno 
     */
    public function getLastCounter()
    {
        return $this->lastCounter;
    }

    /**
     * Set empresa
     *
     * @param \aplicacion\BaseBundle\Entity\Empresa $empresa
     * @return Configuracion
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
     * Set tiempoRespuestaPlanPiloto
     *
     * @param integer $tiempoRespuestaPlanPiloto
     * @return Configuracion
     */
    public function setTiempoRespuestaPlanPiloto($tiempoRespuestaPlanPiloto)
    {
        $this->tiempoRespuestaPlanPiloto = $tiempoRespuestaPlanPiloto;

        return $this;
    }

    /**
     * Get tiempoRespuestaPlanPiloto
     *
     * @return integer 
     */
    public function getTiempoRespuestaPlanPiloto()
    {
        return $this->tiempoRespuestaPlanPiloto;
    }

    /**
     * Set tiempoRespuestaNormal
     *
     * @param integer $tiempoRespuestaNormal
     * @return Configuracion
     */
    public function setTiempoRespuestaNormal($tiempoRespuestaNormal)
    {
        $this->tiempoRespuestaNormal = $tiempoRespuestaNormal;

        return $this;
    }

    /**
     * Get tiempoRespuestaNormal
     *
     * @return integer 
     */
    public function getTiempoRespuestaNormal()
    {
        return $this->tiempoRespuestaNormal;
    }

    /**
     * Set ponderacionPlanPiloto
     *
     * @param integer $ponderacionPlanPiloto
     * @return Configuracion
     */
    public function setPonderacionPlanPiloto($ponderacionPlanPiloto)
    {
        $this->ponderacionPlanPiloto = $ponderacionPlanPiloto;

        return $this;
    }

    /**
     * Get ponderacionPlanPiloto
     *
     * @return integer 
     */
    public function getPonderacionPlanPiloto()
    {
        return $this->ponderacionPlanPiloto;
    }

    /**
     * Set ponderacionNoPlanPiloto
     *
     * @param integer $ponderacionNoPlanPiloto
     * @return Configuracion
     */
    public function setPonderacionNoPlanPiloto($ponderacionNoPlanPiloto)
    {
        $this->ponderacionNoPlanPiloto = $ponderacionNoPlanPiloto;

        return $this;
    }

    /**
     * Get ponderacionNoPlanPiloto
     *
     * @return integer 
     */
    public function getPonderacionNoPlanPiloto()
    {
        return $this->ponderacionNoPlanPiloto;
    }

    /**
     * Set ponderacionEmision
     *
     * @param integer $ponderacionEmision
     * @return Configuracion
     */
    public function setPonderacionEmision($ponderacionEmision)
    {
        $this->ponderacionEmision = $ponderacionEmision;

        return $this;
    }

    /**
     * Get ponderacionEmision
     *
     * @return integer 
     */
    public function getPonderacionEmision()
    {
        return $this->ponderacionEmision;
    }

    /**
     * Set ponderacionRevision
     *
     * @param integer $ponderacionRevision
     * @return Configuracion
     */
    public function setPonderacionRevision($ponderacionRevision)
    {
        $this->ponderacionRevision = $ponderacionRevision;

        return $this;
    }

    /**
     * Get ponderacionRevision
     *
     * @return integer 
     */
    public function getPonderacionRevision()
    {
        return $this->ponderacionRevision;
    }

    /**
     * Set ponderacionAnulacion
     *
     * @param integer $ponderacionAnulacion
     * @return Configuracion
     */
    public function setPonderacionAnulacion($ponderacionAnulacion)
    {
        $this->ponderacionAnulacion = $ponderacionAnulacion;

        return $this;
    }

    /**
     * Get ponderacionAnulacion
     *
     * @return integer 
     */
    public function getPonderacionAnulacion()
    {
        return $this->ponderacionAnulacion;
    }

    

    /**
     * Set inicioHorarioAtencion
     *
     * @param \DateTime $inicioHorarioAtencion
     * @return Configuracion
     */
    public function setInicioHorarioAtencion($inicioHorarioAtencion)
    {
        $this->inicioHorarioAtencion = $inicioHorarioAtencion;

        return $this;
    }

    /**
     * Get inicioHorarioAtencion
     *
     * @return \DateTime 
     */
    public function getInicioHorarioAtencion()
    {
        return $this->inicioHorarioAtencion;
    }

    /**
     * Set finHorarioAtencion
     *
     * @param \DateTime $finHorarioAtencion
     * @return Configuracion
     */
    public function setFinHorarioAtencion($finHorarioAtencion)
    {
        $this->finHorarioAtencion = $finHorarioAtencion;

        return $this;
    }

    /**
     * Get finHorarioAtencion
     *
     * @return \DateTime 
     */
    public function getFinHorarioAtencion()
    {
        return $this->finHorarioAtencion;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Configuracion
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set ponderacionSVI
     *
     * @param integer $ponderacionSVI
     * @return Configuracion
     */
    public function setPonderacionSVI($ponderacionSVI)
    {
        $this->ponderacionSVI = $ponderacionSVI;

        return $this;
    }

    /**
     * Get ponderacionSVI
     *
     * @return integer 
     */
    public function getPonderacionSVI()
    {
        return $this->ponderacionSVI;
    }

    /**
     * Set ponderacionNOSVI
     *
     * @param integer $ponderacionNOSVI
     * @return Configuracion
     */
    public function setPonderacionNOSVI($ponderacionNOSVI)
    {
        $this->ponderacionNOSVI = $ponderacionNOSVI;

        return $this;
    }

    /**
     * Get ponderacionNOSVI
     *
     * @return integer 
     */
    public function getPonderacionNOSVI()
    {
        return $this->ponderacionNOSVI;
    }

    /**
     * Set tiempoPrimeraAlerta
     *
     * @param integer $tiempoPrimeraAlerta
     * @return Configuracion
     */
    public function setTiempoPrimeraAlerta($tiempoPrimeraAlerta)
    {
        $this->tiempoPrimeraAlerta = $tiempoPrimeraAlerta;

        return $this;
    }

    /**
     * Get tiempoPrimeraAlerta
     *
     * @return integer 
     */
    public function getTiempoPrimeraAlerta()
    {
        return $this->tiempoPrimeraAlerta;
    }

    /**
     * Set tiempoSegundaAlerta
     *
     * @param integer $tiempoSegundaAlerta
     * @return Configuracion
     */
    public function setTiempoSegundaAlerta($tiempoSegundaAlerta)
    {
        $this->tiempoSegundaAlerta = $tiempoSegundaAlerta;

        return $this;
    }

    /**
     * Get tiempoSegundaAlerta
     *
     * @return integer 
     */
    public function getTiempoSegundaAlerta()
    {
        return $this->tiempoSegundaAlerta;
    }

   

    /**
     * Set feeEmergencia
     *
     * @param float $feeEmergencia
     * @return Configuracion
     */
    public function setFeeEmergencia($feeEmergencia)
    {
        $this->feeEmergencia = $feeEmergencia;

        return $this;
    }

    /**
     * Get feeEmergencia
     *
     * @return float 
     */
    public function getFeeEmergencia()
    {
        return $this->feeEmergencia;
    }
}
