<?php

namespace aplicacion\EmisionesBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Gds
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="aplicacion\EmisionesBundle\Entity\GdsRepository")
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza")
 */
class Gds
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
     * @var Collection
     * @ORM\OneToMany(targetEntity="Orden", mappedBy="gds")
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
     * @return Gds
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
     * @return Gds
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
     * @return Gds
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
