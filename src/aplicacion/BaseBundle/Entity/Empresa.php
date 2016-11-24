<?php

namespace aplicacion\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use \aplicacion\EmisionesBundle\Entity\Agencia;
use \aplicacion\EmisionesBundle\Entity\Agente;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Empresa
 * @UniqueEntity("email",message="Ya existe una empresa registrada con este email.")
 * @UniqueEntity("ruc",message="Ya existe una empresa registrada con este ruc.")
 * @UniqueEntity("razonsocial",message="Ya existe una empresa registrada con esta razon social.")
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="aplicacion\BaseBundle\Entity\EmpresaRepository")
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza")
 */
class Empresa  
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
     * @Assert\NotBlank(message="Por favor introduzca el ruc de la agencia.")
     * @Assert\Regex(
     *     pattern="/^((2[0-4]{1})|((1)[0-9]{1})|((0)[1-9]{1}))[0-9]{1}[0-9]{6}[0-9]{1}001$/",
     *     message="El ruc no coincide con un valor real"
     * )
     * @ORM\Column(name="ruc", type="string", length=13)
     */
    private $ruc;

    /**
     * @Gedmo\Versioned()
     * @var string
     * @Assert\NotBlank(message="Por favor introduzca direccion de la empresa.") 
     * @ORM\Column(name="direccion", type="string", length=255)
     */
    private $direccion;

    /**
     * @Gedmo\Versioned()
     * @var string
     * @Assert\NotBlank(message="Por favor introduzca telefono convencional de la empresa.")
     * @Assert\Regex(
     *     pattern="/^(([1-9]{3})(\s|-)0[1-9]{1}(\s|-)[0-9]{3}(\s|-)[0-9]{4}(|--([0-9]{1,6})))$/",
     *     message="El numero de telefono no coincide con un valor real,  Ej:  593 02 456 5478 o 593-04-235-5468 o 593-02 456-7894--123 o combinacion."
     * )
     * @ORM\Column(name="telefono", type="string", length=23)
     */
    private $telefono;

    /**
     * @Gedmo\Versioned()
     * @var string
     * @Assert\NotBlank(message="Por favor introduzca el nombre de la empresa.")
     * @ORM\Column(name="razonsocial", type="text")
     */
    private $razonsocial;

    /**
     * @Gedmo\Versioned()
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "El email '{{ value }}' no es un email valido .",
     *     checkMX = true
     * ) 
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @Gedmo\Versioned()
     * @var string
     * @Assert\NotBlank(message="Por favor introduzca el representante.") 
     * @ORM\Column(name="representante", type="string", length=255)
     */
    private $representante;

    /**
     * @Gedmo\Versioned()
     * @Assert\NotBlank(message="Por favor seleccione la ciudad.") 
     * @var ciudad
     * @ORM\ManyToOne(targetEntity="aplicacion\EmisionesBundle\Entity\Ciudad", inversedBy="empresas")
     * @ORM\JoinColumn(name="ciudad", referencedColumnName="id")
     */   
    private $ciudad;

    
    /**
     * 
     * @var Collection
     * @ORM\OneToMany(targetEntity="aplicacion\BaseBundle\Entity\Empresa", mappedBy="matriz")
     */
     private $sucursales;
     
    /**
     * @Gedmo\Versioned()
     * @var matriz
     * @ORM\ManyToOne(targetEntity="aplicacion\BaseBundle\Entity\Empresa", inversedBy="sucursales")
     * @ORM\JoinColumn(name="matriz", referencedColumnName="id")
     */   
    private $matriz;
    
    /**
     * 
     * @var Collection
     * @ORM\OneToMany(targetEntity="aplicacion\EmisionesBundle\Entity\Usuariointerno", mappedBy="empresa")
     */
    private $usuarios;
    
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="aplicacion\EmisionesBundle\Entity\Orden", mappedBy="empresa")
     */
    private $ordenes;
    
    /**
     * 
     * @var Collection
     * @ORM\OneToMany(targetEntity="aplicacion\BaseBundle\Entity\CronTask", mappedBy="empresa")
     */
    private $daemons;
    
    /**
     * @Gedmo\Versioned()
     * @var string
     * @Assert\File(
     *     maxSize = "1M"  
     * )
     * @ORM\Column(name="logo", type="text")
     */
    private $logo;
    
    /**
     * 
     * @ORM\ManyToMany(targetEntity="Feriado")
     * @ORM\JoinTable(name="empresa_feriado",
     *      joinColumns={@ORM\JoinColumn(name="empresa", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feriado", referencedColumnName="id")}
     * )
     */
    private $feriados;    
 
    
    /**
     * 
     * @ORM\ManyToMany(targetEntity="aplicacion\EmisionesBundle\Entity\Agencia", inversedBy="empresas")
     * @ORM\JoinTable(name="empresa_agencia",
     *      joinColumns={@ORM\JoinColumn(name="empresa", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="agencia", referencedColumnName="id")}
     * )
     */
    private $agencias;
    
    /**
     * 
     * @var Collection
     * @ORM\OneToMany(targetEntity="Configuracion", mappedBy="empresa")
     */
    private $configuraciones;
    
   /*
    * Funcion que me da la configuracion activa de la empresa
    */
    public function getConfiguracionActiva()
    {
        
        foreach ($this->getConfiguraciones() as $conf) {
            if($conf->getActiva())
            {
                return $conf;
            }
        }
        return null;//no existe configuracion activa
    }
    
    public function __isMember(Agencia $agencia)
    {
        for ($i = 0; $i < count($this->agencias); $i++) {
            if($this->agencias[$i]==$agencia)
            {
                return true;
            }
        } 
        return false;
    }

    public function getAgentes()
    {
        $agentes=array();
        for ($i = 0; $i < count($this->agencias); $i++) {
             $agents=$this->agencias[$i]->getAgentes();
             for ($j = 0; $j < count($agents); $j++) {
                 $agentes[] = $agents[$j];    
             }
        }
        return $agentes;
    }
    public function getCounters()
    {
        $counters = array();
        $users=  $this->getUsuarios();
        for ($i = 0; $i < count($users); $i++) {
           if($users[$i]->hasRole('ROLE_COUNTER') )
                {
                    $counters[]=$users[$i];
                }
        }
        return $counters;
    }
    /*
     * Funcion que me da todos los counters de esta empresa que estan habilitados
     */
    public function getEnabledCounters()
    {
        $counters = array();
        $users=  $this->getUsuarios();
        for ($i = 0; $i < count($users); $i++) {
           if($users[$i]->hasRole('ROLE_COUNTER') && $users[$i]->isEnabled() )
                {
                    $counters[]=$users[$i];
                }
        }
        return $counters;
    }
    /*
     * Funcion que me da todos los counters de esta empresa que no estan bloqueados
     */
    public function getUnlockedCounters()
    {
        $counters = array();
        $users=  $this->getUsuarios();
        for ($i = 0; $i < count($users); $i++) {
           if($users[$i]->hasRole('ROLE_COUNTER') && !$users[$i]->isLocked() )
                {
                    $counters[]=$users[$i];
                }
        }
        return $counters;
    }
    
    public function __toString()
    {
        return $this->razonsocial;
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
     * Set ruc
     *
     * @param string $ruc
     * @return Empresa
     */
    public function setRuc($ruc)
    {
        $this->ruc = $ruc;

        return $this;
    }

    /**
     * Get ruc
     *
     * @return string 
     */
    public function getRuc()
    {
        return $this->ruc;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Empresa
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string 
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     * @return Empresa
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set razonsocial
     *
     * @param string $razonsocial
     * @return Empresa
     */
    public function setRazonsocial($razonsocial)
    {
        $this->razonsocial = $razonsocial;

        return $this;
    }

    /**
     * Get razonsocial
     *
     * @return string 
     */
    public function getRazonsocial()
    {
        return $this->razonsocial;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Empresa
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set representante
     *
     * @param string $representante
     * @return Empresa
     */
    public function setRepresentante($representante)
    {
        $this->representante = $representante;

        return $this;
    }

    /**
     * Get representante
     *
     * @return string 
     */
    public function getRepresentante()
    {
        return $this->representante;
    }

    /**
     * Set ciudad
     *
     * @param string $ciudad
     * @return Empresa
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    /**
     * Get ciudad
     *
     * @return string 
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->agencias= new ArrayCollection();
        $this->sucursales = new ArrayCollection();
        $this->feriados= new ArrayCollection();
        $this->configuraciones= new ArrayCollection();
        $this->usuarios= new ArrayCollection();
        $this->ordenes = new ArrayCollection();
    }

    /**
     * Add sucursales
     *
     * @param \aplicacion\BaseBundle\Entity\Empresa $sucursales
     * @return Empresa
     */
    public function addSucursale(\aplicacion\BaseBundle\Entity\Empresa $sucursales)
    {
        $this->sucursales[] = $sucursales;

        return $this;
    }

    /**
     * Remove sucursales
     *
     * @param \aplicacion\BaseBundle\Entity\Empresa $sucursales
     */
    public function removeSucursale(\aplicacion\BaseBundle\Entity\Empresa $sucursales)
    {
        $this->sucursales->removeElement($sucursales);
    }

    /**
     * Get sucursales
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSucursales()
    {
        return $this->sucursales;
    }

    /**
     * Set matriz
     *
     * @param \aplicacion\BaseBundle\Entity\Empresa $matriz
     * @return Empresa
     */
    public function setMatriz(\aplicacion\BaseBundle\Entity\Empresa $matriz = null)
    {
        $this->matriz = $matriz;

        return $this;
    }

    /**
     * Get matriz
     *
     * @return \aplicacion\BaseBundle\Entity\Empresa 
     */
    public function getMatriz()
    {
        return $this->matriz;
    }

    /**
     * Add usuarios
     *
     * @param \aplicacion\EmisionesBundle\Entity\Usuariointerno $usuarios
     * @return Empresa
     */
    public function addUsuario(\aplicacion\EmisionesBundle\Entity\Usuariointerno $usuarios)
    {
        $this->usuarios[] = $usuarios;

        return $this;
    }

    /**
     * Remove usuarios
     *
     * @param \aplicacion\EmisionesBundle\Entity\Usuariointerno $usuarios
     */
    public function removeUsuario(\aplicacion\EmisionesBundle\Entity\Usuariointerno $usuarios)
    {
        $this->usuarios->removeElement($usuarios);
    }

    /**
     * Get usuarios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    /**
     * Set logo
     *
     * @param string $logo
     * @return Empresa
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Add feriados
     *
     * @param \aplicacion\BaseBundle\Entity\Feriado $feriados
     * @return Empresa
     */
    public function addFeriado(\aplicacion\BaseBundle\Entity\Feriado $feriados)
    {
        $this->feriados[] = $feriados;

        return $this;
    }

    /**
     * Remove feriados
     *
     * @param \aplicacion\BaseBundle\Entity\Feriado $feriados
     */
    public function removeFeriado(\aplicacion\BaseBundle\Entity\Feriado $feriados)
    {
        $this->feriados->removeElement($feriados);
    }

    /**
     * Get feriados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFeriados()
    {
        return $this->feriados;
    }



    /**
     * Add agencias
     *
     * @param \aplicacion\EmisionesBundle\Entity\Agencia $agencias
     * @return Empresa
     */
    public function addAgencia(\aplicacion\EmisionesBundle\Entity\Agencia $agencias)
    {
        $this->agencias[] = $agencias;

        return $this;
    }

    /**
     * Remove agencias
     *
     * @param \aplicacion\EmisionesBundle\Entity\Agencia $agencias
     */
    public function removeAgencia(\aplicacion\EmisionesBundle\Entity\Agencia $agencias)
    {
        $this->agencias->removeElement($agencias);
    }

    /**
     * Get agencias
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAgencias()
    {
        return $this->agencias;
    }

    /**
     * Add ordenes
     *
     * @param \aplicacion\EmisionesBundle\Entity\Orden $ordenes
     * @return Empresa
     */
    public function addOrdene(\aplicacion\EmisionesBundle\Entity\Orden $ordenes)
    {
        $this->ordenes[] = $ordenes;

        return $this;
    }

    
    public function removeOrdene(\aplicacion\EmisionesBundle\Entity\Orden $ordenes)
    {
        $this->ordenes->removeElement($ordenes);
    }

  
    public function getOrdenes()
    {
        return $this->ordenes;
    }

    /**
     * Add configuraciones
     *
     * @param \aplicacion\BaseBundle\Entity\Configuracion $configuraciones
     * @return Empresa
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

    /**
     * Add daemons
     *
     * @param \aplicacion\BaseBundle\Entity\CronTask $daemons
     * @return Empresa
     */
    public function addDaemon(\aplicacion\BaseBundle\Entity\CronTask $daemons)
    {
        $this->daemons[] = $daemons;

        return $this;
    }

    /**
     * Remove daemons
     *
     * @param \aplicacion\BaseBundle\Entity\CronTask $daemons
     */
    public function removeDaemon(\aplicacion\BaseBundle\Entity\CronTask $daemons)
    {
        $this->daemons->removeElement($daemons);
    }

    /**
     * Get daemons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDaemons()
    {
        return $this->daemons;
    }
}
