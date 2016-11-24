<?php

namespace aplicacion\EmisionesBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Model\User as BaseUser;
use aplicacion\BaseBundle\Entity\User as AplicationUser;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Agente
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="aplicacion\BaseBundle\Entity\UserRepository")
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza")
 */

class Agente extends AplicationUser
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
     * @Gedmo\Versioned()
     * @var agencia
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Agencia", inversedBy="agentes")
     * @ORM\JoinColumn(name="agencia", referencedColumnName="id")
     */   
    private $agencia;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="Orden", mappedBy="agente")
     */
    private $ordenes;

    public function __construct() {
        parent::__construct(); 
        $this->ordenes = new ArrayCollection();
    }
  

   
     public function __toString() {
        return $this->nombre.' '.$this->apellidos;
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
     * @return Agente
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
    public function getNombreCompleto()
    {
        return $this->nombre.' '.$this->apellidos;
    }
  

    /**
     * Set apellidos
     *
     * @param string $apellidos
     * @return Agente
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * Get apellidos
     *
     * @return string 
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

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
     * Set sexo
     *
     * @param string $sexo
     * @return Agente
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo
     *
     * @return string 
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Set celular
     *
     * @param string $celular
     * @return Agente
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;

        return $this;
    }

    /**
     * Get celular
     *
     * @return string 
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     * @return Agente
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
     * Set ext
     *
     * @param string $ext
     * @return Agente
     */
    public function setExt($ext)
    {
        $this->ext = $ext;

        return $this;
    }

    /**
     * Get ext
     *
     * @return string 
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * Set agencia
     *
     * @param \aplicacion\EmisionesBundle\Entity\Agencia $agencia
     * @return Agente
     */
    public function setAgencia(\aplicacion\EmisionesBundle\Entity\Agencia $agencia = null)
    {
        $this->agencia = $agencia;

        return $this;
    }

    /**
     * Get agencia
     *
     * @return \aplicacion\EmisionesBundle\Entity\Agencia 
     */
    public function getAgencia()
    {
        return $this->agencia;
    }

 

   

   

    /**
     * Add grupos
     *
     * @param \aplicacion\BaseBundle\Entity\Grupo $grupos
     * @return Agente
     */
    public function addGrupo(\aplicacion\BaseBundle\Entity\Grupo $grupos)
    {
        $this->grupos[] = $grupos;

        return $this;
    }

    /**
     * Remove grupos
     *
     * @param \aplicacion\BaseBundle\Entity\Grupo $grupos
     */
    public function removeGrupo(\aplicacion\BaseBundle\Entity\Grupo $grupos)
    {
        $this->grupos->removeElement($grupos);
    }

    /**
     * Get grupos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGrupos()
    {
        return $this->grupos;
    }
    /**
     * @var string
     */
    protected $foto;


    /**
     * Set foto
     *
     * @param string $foto
     * @return Agente
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
     * Add ordenes
     *
     * @param \aplicacion\EmisionesBundle\Entity\Orden $ordenes
     * @return Agente
     */
    public function addOrdene(\aplicacion\EmisionesBundle\Entity\Orden $ordenes)
    {
        $this->ordenes[] = $ordenes;

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
}
