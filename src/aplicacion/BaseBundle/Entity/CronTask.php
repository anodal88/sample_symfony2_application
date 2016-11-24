<?php

namespace aplicacion\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @UniqueEntity("name")
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza")
 */
class CronTask
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
     * @Gedmo\Versioned()
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;
    
    /**
     * @Gedmo\Versioned()
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;
    
    /**
     * @Gedmo\Versioned()
     * @var boolean
     *
     * @ORM\Column(name="activa", type="boolean")
     */
    private $activa;
    
    /**
     * @Gedmo\Versioned()
     * @var boolean
     *
     * @ORM\Column(name="asignacionOrdenes", type="boolean")
     */
    private $asignacionOrdenes;

    /**
     * @Gedmo\Versioned()
     * @var array
     *
     * @ORM\Column(name="comandos", type="array")
     */
    private $comandos;

    /**
     * @Gedmo\Versioned()
     * @var integer
     *
     * @ORM\Column(name="intervalo", type="integer")
     */
    private $intervalo;

    /**
     * 
     * @var \DateTime
     *
     * @ORM\Column(name="lastrun", type="datetime", nullable=true)
     */
    private $lastrun;
    
    /**
     * @Gedmo\Versioned()
     * @var empresa
     * @ORM\ManyToOne(targetEntity="aplicacion\BaseBundle\Entity\Empresa", inversedBy="daemons")
     * @ORM\JoinColumn(name="empresa", referencedColumnName="id")
     */   
    private $empresa;


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
     * Set nombre
     *
     * @param string $nombre
     * @return CronTask
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
     * Set comandos
     *
     * @param array $comandos
     * @return CronTask
     */
    public function setComandos($comandos)
    {
        $this->comandos = $comandos;

        return $this;
    }

    /**
     * Get comandos
     *
     * @return array 
     */
    public function getComandos()
    {
        return $this->comandos;
    }

    /**
     * Set intervalo
     *
     * @param integer $intervalo
     * @return CronTask
     */
    public function setIntervalo($intervalo)
    {
        $this->intervalo = $intervalo;

        return $this;
    }

    /**
     * Get intervalo
     *
     * @return integer 
     */
    public function getIntervalo()
    {
        return $this->intervalo;
    }

    /**
     * Set lastrun
     *
     * @param \DateTime $lastrun
     * @return CronTask
     */
    public function setLastrun($lastrun)
    {
        $this->lastrun = $lastrun;

        return $this;
    }

    /**
     * Get lastrun
     *
     * @return \DateTime 
     */
    public function getLastrun()
    {
        return $this->lastrun;
    }

    /**
     * Set activa
     *
     * @param boolean $activa
     * @return CronTask
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
     * Set empresa
     *
     * @param \aplicacion\BaseBundle\Entity\Empresa $empresa
     * @return CronTask
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return CronTask
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
     * Set asignacionOrdenes
     *
     * @param boolean $asignacionOrdenes
     * @return CronTask
     */
    public function setAsignacionOrdenes($asignacionOrdenes)
    {
        $this->asignacionOrdenes = $asignacionOrdenes;

        return $this;
    }

    /**
     * Get asignacionOrdenes
     *
     * @return boolean 
     */
    public function getAsignacionOrdenes()
    {
        return $this->asignacionOrdenes;
    }
}
