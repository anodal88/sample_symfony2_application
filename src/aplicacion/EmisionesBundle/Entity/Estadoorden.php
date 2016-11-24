<?php

namespace aplicacion\EmisionesBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Estadoorden
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="aplicacion\EmisionesBundle\Entity\EstadoordenRepository")
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza")
 */
class Estadoorden
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
     * @ORM\Column(name="icono", type="string", length=255)
     */
    private $icono;

    /**
     * @var string
     * @Gedmo\Versioned()
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Orden", mappedBy="estado")
     */
    protected $ordenes;
    
    public function __construct() {
        $this->ordenes= new ArrayCollection();
    }
    public function __toString() {
        return $this->nombre;
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
     * @return Estadoorden
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
     * @return Estadoorden
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
     * Add ordenes
     *
     * @param \aplicacion\EmisionesBundle\Entity\Orden $ordenes
     * @return Estadoorden
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

    /**
     * Set icono
     *
     * @param string $icono
     * @return Estadoorden
     */
    public function setIcono($icono)
    {
        $this->icono = $icono;

        return $this;
    }

    /**
     * Get icono
     *
     * @return string 
     */
    public function getIcono()
    {
        return $this->icono;
    }
}
