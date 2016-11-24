<?php

namespace aplicacion\BaseBundle\Entity;
use FOS\UserBundle\Model\User as BaseUser;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityRepository;
use \Doctrine\Common\Collections\ArrayCollection;
use \aplicacion\EmisionesBundle\Entity\Usuariointerno;
use \aplicacion\EmisionesBundle\Entity\Agente;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * 
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza") 
 * @ORM\Entity(repositoryClass="aplicacion\BaseBundle\Entity\UserRepository")
 * @UniqueEntity(fields={"email"}, repositoryMethod="findByEmail", message="El email proporcionado esta siendo usado por otro usuario.")
 * @UniqueEntity(fields={"username"}, repositoryMethod="findByUsername", message="El nombre de usuario no esta disponible.")
 * @UniqueEntity(fields={"ci"}, repositoryMethod="findByCI", message="Ya existe un usuario registrado en el sistema con ese numero de cedula.")
 * @ORM\Table(name="fos_user")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="tipo", type="string")
 * @ORM\DiscriminatorMap({"agente" = "aplicacion\EmisionesBundle\Entity\Agente", "usuariointerno" = "aplicacion\EmisionesBundle\Entity\Usuariointerno"})
 * 
 */
abstract class User extends BaseUser
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
     * @ORM\Column(type="string", length=255)
     */
    protected $nombre;
    
    /**
     * @ORM\ManyToMany(targetEntity="aplicacion\BaseBundle\Entity\Grupo")
     * @ORM\JoinTable(name="usuario_grupo",
     *      joinColumns={@ORM\JoinColumn(name="usuario", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="grupo", referencedColumnName="id")}
     * )
     */
    protected $grupos;
    
    /**
     * @Gedmo\Versioned()
     * @ORM\Column(type="string", length=255)  
     */
    protected $apellidos;
    /**
     * @Gedmo\Versioned()
     * @ORM\Column(type="string", length=10)      
     */
    protected $ci;
    
    /**
     * @Gedmo\Versioned()
     * @ORM\Column(type="text", nullable=true)  
     *    
     */
    protected $foto;
    /**
     * @Gedmo\Versioned()
     * @ORM\Column(type="string", length=1)    
     */
    protected $sexo;
    /**
     * @Gedmo\Versioned()
     * @ORM\Column(type="string", length=10) 
     */
    protected $celular;
    
    /**
     * @Gedmo\Versioned()
     * @ORM\Column(type="string", length=23)
     */
    protected $telefono;
    
    /**
     * @Gedmo\Versioned()
     * @ORM\Column(type="string", length=6, nullable=true)    
     */
    protected $ext;
    
   
    
    public function __toString() {
        return $this->nombre.' '.$this->apellidos;
    }

    public function getTipo()
    {
        if($this instanceof Usuariointerno)
        {
            return 'usuariointerno';
        }
        if($this instanceof Agente)
        {
            return 'agente';
        }
    }
    
  
   
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->grupos = new ArrayCollection();
        
       
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
     * @return User
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
     * Set apellidos
     *
     * @param string $apellidos
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * Add grupos
     *
     * @param \aplicacion\BaseBundle\Entity\Grupo $grupos
     * @return User
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
     * Set foto
     *
     * @param string $foto
     * @return User
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


}
