<?php

namespace aplicacion\EmisionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Ciudad
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="aplicacion\EmisionesBundle\Entity\CiudadRepository")
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza")
 */
class Ciudad
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
     * @var string
     * @Gedmo\Versioned()
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     * @Gedmo\Versioned()
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;
    
    /**
     * @Gedmo\Versioned()
     * @var pais
     * @ORM\ManyToOne(targetEntity="Pais", inversedBy="ciudades")
     * @ORM\JoinColumn(name="pais", referencedColumnName="id")
     */   
    private $pais;
    
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Orden", mappedBy="ciudadDestino")
     */
    protected $ordenes;
    
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Agencia", mappedBy="ciudad")
     */
    private $agencias;
    
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="aplicacion\BaseBundle\Entity\Empresa", mappedBy="ciudad")
     */
    private $empresas;
    public function __toString()
    {
        return $this->nombre;
    }

    
    public function __construct()
    {
        $this->agencias= new ArrayCollection();
        $this->empresas = new ArrayCollection();
        $this->ordenes = new ArrayCollection();
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
     * @return Ciudad
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Ciudad
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
     * Set pais
     *
     * @param \aplicacion\EmisionesBundle\Entity\Pais $pais
     * @return Ciudad
     */
    public function setPais(\aplicacion\EmisionesBundle\Entity\Pais $pais = null)
    {
        $this->pais = $pais;

        return $this;
    }

    /**
     * Get pais
     *
     * @return \aplicacion\EmisionesBundle\Entity\Pais 
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * Add agencias
     *
     * @param \aplicacion\EmisionesBundle\Entity\Agencia $agencias
     * @return Ciudad
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
     * Add empresas
     *
     * @param \aplicacion\BaseBundle\Entity\Empresa $empresas
     * @return Ciudad
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
     * Add ordenes
     *
     * @param \aplicacion\EmisionesBundle\Entity\Orden $ordenes
     * @return Ciudad
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
