<?php

namespace aplicacion\EmisionesBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Model\User as BaseUser; 
use aplicacion\BaseBundle\Entity\User as AplicationUser;
use aplicacion\EmisionesBundle\Entity\Estadoorden;
use aplicacion\EmisionesBundle\Entity\Emision;
use aplicacion\EmisionesBundle\Entity\Revision;
use aplicacion\EmisionesBundle\Entity\Anulacion;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Usuariointerno
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="aplicacion\BaseBundle\Entity\UserRepository")
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza") 
 */

class Usuariointerno extends AplicationUser
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
     * @var Collection
     * @ORM\OneToMany(targetEntity="aplicacion\EmisionesBundle\Entity\Usuariointerno", mappedBy="jefe")
     */
     private $subordinados;
     
    /**
     * @Gedmo\Versioned()
     * @var jefe
     * @ORM\ManyToOne(targetEntity="aplicacion\EmisionesBundle\Entity\Usuariointerno", inversedBy="subordinados")
     * @ORM\JoinColumn(name="jefe", referencedColumnName="id")
     */   
     protected $jefe;
     
    /**
     * 
     * @ORM\OneToMany(targetEntity="Orden", mappedBy="usuario")
     */
     protected $ordenes;
     
     /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="aplicacion\BaseBundle\Entity\Configuracion", mappedBy="lastCounter")
     */
     private $configuraciones;
     
    /**
     * @Gedmo\Versioned()
     * @ORM\Column(name="inicioAlmuerzo", type="time")
     */
    private $inicioAlmuerzo;
    
    /**
     * @Gedmo\Versioned()
     * @ORM\Column(name="finAlmuerzo", type="time")
     */
    private $finAlmuerzo;
    
    /**
     * @Gedmo\Versioned()
     * @ORM\Column(name="inicioJornada", type="time")
     */
    private $inicioJornada;
    
    /**
     * @Gedmo\Versioned()
     * @ORM\Column(name="finJornada", type="time")
     */
    private $finJornada;
     
    /**
     * @Gedmo\Versioned()
     * @var empresa
     * @ORM\ManyToOne(targetEntity="aplicacion\BaseBundle\Entity\Empresa", inversedBy="usuarios")
     * @ORM\JoinColumn(name="empresa", referencedColumnName="id")
     */   
    private $empresa;
    
   
    public function __toString() {
        return $this->nombre.' '.$this->apellidos;
    }

   /*
    * Funcion que me da el tiempo de proceso de todas las ordenes de la cola del counter
    */
    public function getTimeOfQueue()
    {
        $totalTime=0;
        foreach ($this->getOrdenes() as $orden) {
            if($orden->getEstado()->getNombre()== 'Pendiente')
            {
                $totalTime+=$orden->timeOfProcess();
            }
            
        }
        return $totalTime;
    }
    /*
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();  
        $this->subordinados= new ArrayCollection();
        $this->ordenes = new ArrayCollection();
    }

    /**
     * @var string
     */
    protected $nombre;

    /**
     * @var string
     */
    protected $apellidos;

    /**
     * @var string
     */
    protected $ci;
    
     /**
     * Set ci
     *
     * @param string $ci
     * @return Agente
     */
    public function setCi($ci)
    {
        $this->ci = $ci;

        return $this;
    }

    /**
     * Get ci
     *
     * @return string 
     */
    public function getCi()
    {
        return $this->ci;
    }

    /**
     * @var string
     */
    protected $sexo;

    /**
     * @var string
     */
    protected $celular;

    /**
     * @var string
     */
    protected $telefono;

    /**
     * @var string
     */
    protected $ext;

   

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $grupos;


    


    /**
     * @var string
     */
    protected $foto;


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
     * Set foto
     *
     * @param string $foto
     * @return Usuariointerno
     */
    public function setFoto($foto)
    {
        $this->foto = $foto;

        return $this;
    }

    /**
     * Get foto
     *
     * @return string 
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * Add subordinados
     *
     * @param \aplicacion\EmisionesBundle\Entity\Usuariointerno $subordinados
     * @return Usuariointerno
     */
    public function addSubordinado(\aplicacion\EmisionesBundle\Entity\Usuariointerno $subordinados)
    {
        $this->subordinados[] = $subordinados;

        return $this;
    }

    /**
     * Remove subordinados
     *
     * @param \aplicacion\EmisionesBundle\Entity\Usuariointerno $subordinados
     */
    public function removeSubordinado(\aplicacion\EmisionesBundle\Entity\Usuariointerno $subordinados)
    {
        $this->subordinados->removeElement($subordinados);
    }

    /**
     * Get subordinados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubordinados()
    {
        return $this->subordinados;
    }

    /**
     * Set jefe
     *
     * @param \aplicacion\EmisionesBundle\Entity\Usuariointerno $jefe
     * @return Usuariointerno
     */
    public function setJefe(\aplicacion\EmisionesBundle\Entity\Usuariointerno $jefe = null)
    {
        $this->jefe = $jefe;

        return $this;
    }

    /**
     * Get jefe
     *
     * @return \aplicacion\EmisionesBundle\Entity\Usuariointerno 
     */
    public function getJefe()
    {
        return $this->jefe;
    }

   

    /**
     * Set empresa
     *
     * @param \aplicacion\BaseBundle\Entity\Empresa $empresa
     * @return Usuariointerno
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
     * Add ordenes
     *
     * @param \aplicacion\EmisionesBundle\Entity\Orden $ordenes
     * @return Usuariointerno
     */
    public function addOrdene(\aplicacion\EmisionesBundle\Entity\Orden $orden)
    {
        if(!($this->ordenes->contains($orden)) && is_null($orden->getUsuario()))
        {
            $this->ordenes[] = $orden;
            $orden->setUsuario($this);
        }
        return $this;
    }

    /**
     * Remove ordenes
     *
     * @param \aplicacion\EmisionesBundle\Entity\Orden $ordenes
     */
    public function removeOrdene(\aplicacion\EmisionesBundle\Entity\Orden $ordenes)
    {
        $this->ordenes->removeElement($ordenes);
    }

    /**
     * Get ordenes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrdenes()
    {
        return $this->ordenes;
    }

    /**
     * Set inicioAlmuerzo
     *
     * @param \DateTime $inicioAlmuerzo
     * @return Usuariointerno
     */
    public function setInicioAlmuerzo($inicioAlmuerzo)
    {
        $this->inicioAlmuerzo = $inicioAlmuerzo;

        return $this;
    }

    /**
     * Get inicioAlmuerzo
     *
     * @return \DateTime 
     */
    public function getInicioAlmuerzo()
    {
        return $this->inicioAlmuerzo;
    }

    /**
     * Set finAlmuerzo
     *
     * @param \DateTime $finAlmuerzo
     * @return Usuariointerno
     */
    public function setFinAlmuerzo($finAlmuerzo)
    {
        $this->finAlmuerzo = $finAlmuerzo;

        return $this;
    }

    /**
     * Get finAlmuerzo
     *
     * @return \DateTime 
     */
    public function getFinAlmuerzo()
    {
        return $this->finAlmuerzo;
    }

    /**
     * Set inicioJornada
     *
     * @param \DateTime $inicioJornada
     * @return Usuariointerno
     */
    public function setInicioJornada($inicioJornada)
    {
        $this->inicioJornada = $inicioJornada;

        return $this;
    }

    /**
     * Get inicioJornada
     *
     * @return \DateTime 
     */
    public function getInicioJornada()
    {
        return $this->inicioJornada;
    }

    /**
     * Set finJornada
     *
     * @param \DateTime $finJornada
     * @return Usuariointerno
     */
    public function setFinJornada($finJornada)
    {
        $this->finJornada = $finJornada;

        return $this;
    }

    /**
     * Get finJornada
     *
     * @return \DateTime 
     */
    public function getFinJornada()
    {
        return $this->finJornada;
    }

    /**
     * Add configuraciones
     *
     * @param \aplicacion\BaseBundle\Entity\Configuracion $configuraciones
     * @return Usuariointerno
     */
    public function addConfiguracione(\aplicacion\BaseBundle\Entity\Configuracion $configuraciones)
    {
        $this->configuraciones[] = $configuraciones;

        return $this;
    }

    /**
     * Remove configuraciones
     *
     * @param \aplicacion\BaseBundle\Entity\Configuracion $configuraciones
     */
    public function removeConfiguracione(\aplicacion\BaseBundle\Entity\Configuracion $configuraciones)
    {
        $this->configuraciones->removeElement($configuraciones);
    }

    /**
     * Get configuraciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConfiguraciones()
    {
        return $this->configuraciones;
    }
}
