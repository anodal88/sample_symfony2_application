<?php

namespace aplicacion\EmisionesBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Agencia
 * @UniqueEntity("email",message="Ya existe una agencia registrada con este email.")
 * @UniqueEntity("ruc",message="Ya existe una agencia registrada con este ruc.")
 * @UniqueEntity("nombre",message="Ya existe una agencia registrada con esta razon social.")
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="aplicacion\EmisionesBundle\Entity\AgenciaRepository")
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza")
 */
class Agencia
{
    /**
     * @var integer
     
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\Versioned()
     * @var string
     * @Assert\NotBlank(message="Por favor introduzca el nombre de la agencia.")
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

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
     * @ORM\ManyToMany(targetEntity="aplicacion\BaseBundle\Entity\Empresa", mappedBy="agencias")
     */
    private $empresas;

    /**
     * @Gedmo\Versioned()
     * @var string
     * @Assert\NotBlank(message="Por favor introduzca direccion de la agencia.") 
     * @ORM\Column(name="direccion", type="string", length=255)
     */
    private $direccion;

    /**
     * @Gedmo\Versioned()
     * @var string
     * @Assert\NotBlank(message="Por favor introduzca telefono convencional de la agencia.")
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
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/",
     *     message="El email tiene que ser un email valido."
     * )     
     * @ORM\Column(name="email", type="string", nullable=false)
     */
    private $email;
    
    /**
     * @Gedmo\Versioned()
     * @Assert\NotBlank() 
     * @var ciudad
     * @ORM\ManyToOne(targetEntity="Ciudad", inversedBy="agencias")
     * @ORM\JoinColumn(name="ciudad", referencedColumnName="id")
     */   
    private $ciudad;

    /**
     * @Gedmo\Versioned()
     * @var string
     * @Assert\File(
     *     maxSize = "1M"  
     * )

     * @ORM\Column(name="logo", type="text", nullable=true)
     */
    private $logo;
    
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Agente", mappedBy="agencia")
     */
    protected $agentes;
    
   /**
    * @ORM\ManyToMany(targetEntity="Aerolinea", inversedBy="agenciasPlanPiloto")
    * @ORM\JoinTable(name="Plan_Piloto",
    *      joinColumns={@ORM\JoinColumn(name="agencia", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="aerolinea", referencedColumnName="id")}
    *      )
    **/
    protected $aerolineasPlanPiloto;
    

    public function __toString() {
        return $this->nombre;
    }

    public function __construct() {
        $this->agentes = new ArrayCollection();
        $this->aerolineasPlanPiloto= new ArrayCollection();
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
     * Set nombre
     *
     * @param string $nombre
     * @return Agencia
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
     * Set ruc
     *
     * @param string $ruc
     * @return Agencia
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
     * @return Agencia
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
     * @return Agencia
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
     * Add agentes
     *
     * @param \aplicacion\EmisionesBundle\Entity\Agente $agentes
     * @return Agencia
     */
    public function addAgente(\aplicacion\EmisionesBundle\Entity\Agente $agentes)
    {
        $this->agentes[] = $agentes;

        return $this;
    }

    /**
     * Remove agentes
     *
     * @param \aplicacion\EmisionesBundle\Entity\Agente $agentes
     */
    public function removeAgente(\aplicacion\EmisionesBundle\Entity\Agente $agentes)
    {
        $this->agentes->removeElement($agentes);
    }

    /**
     * Get agentes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAgentes()
    {
        return $this->agentes;
    }

    /**
     * Set ciudad
     *
     * @param \aplicacion\EmisionesBundle\Entity\Ciudad $ciudad
     * @return Agencia
     */
    public function setCiudad(\aplicacion\EmisionesBundle\Entity\Ciudad $ciudad = null)
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    /**
     * Get ciudad
     *
     * @return \aplicacion\EmisionesBundle\Entity\Ciudad 
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Agencia
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
     * Add empresas
     *
     * @param \aplicacion\BaseBundle\Entity\Empresa $empresas
     * @return Agencia
     */
    public function addEmpresa(\aplicacion\BaseBundle\Entity\Empresa $empresas)
    {
        $this->empresas[] = $empresas;

        return $this;
    }

    /**
     * Remove empresas
     *
     * @param \aplicacion\BaseBundle\Entity\Empresa $empresas
     */
    public function removeEmpresa(\aplicacion\BaseBundle\Entity\Empresa $empresas)
    {
        $this->empresas->removeElement($empresas);
    }

    /**
     * Get empresas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmpresas()
    {
        return $this->empresas;
    }

    /**
     * Add aerolineasPlanPiloto
     *
     * @param \aplicacion\EmisionesBundle\Entity\Aerolinea $aerolineasPlanPiloto
     * @return Agencia
     */
    public function addAerolineasPlanPiloto(\aplicacion\EmisionesBundle\Entity\Aerolinea $aerolineasPlanPiloto)
    {
        $this->aerolineasPlanPiloto[] = $aerolineasPlanPiloto;

        return $this;
    }

    /**
     * Remove aerolineasPlanPiloto
     *
     * @param \aplicacion\EmisionesBundle\Entity\Aerolinea $aerolineasPlanPiloto
     */
    public function removeAerolineasPlanPiloto(\aplicacion\EmisionesBundle\Entity\Aerolinea $aerolineasPlanPiloto)
    {
        $this->aerolineasPlanPiloto->removeElement($aerolineasPlanPiloto);
    }

    /**
     * Get aerolineasPlanPiloto
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAerolineasPlanPiloto()
    {
        return $this->aerolineasPlanPiloto;
    }

    /**
     * Set logo
     *
     * @param string $logo
     * @return Agencia
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
}
