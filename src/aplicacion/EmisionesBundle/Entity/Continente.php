<?php

namespace aplicacion\EmisionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Continente
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="aplicacion\EmisionesBundle\Entity\ContinenteRepository")
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza")
 */
class Continente
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
     * @var Collection
     * @ORM\OneToMany(targetEntity="Pais", mappedBy="continente")
     */
    private $paises;


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
     * @return Continente
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function __toString()
    {
        return $this->nombre;
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
     * Constructor
     */
    public function __construct()
    {
        $this->paises = new ArrayCollection();
    }

    /**
     * Add paises
     *
     * @param \aplicacion\EmisionesBundle\Entity\Pais $paises
     * @return Continente
     */
    public function addPaise(\aplicacion\EmisionesBundle\Entity\Pais $paises)
    {
        $this->paises[] = $paises;

        return $this;
    }

    /**
     * Remove paises
     *
     * @param \aplicacion\EmisionesBundle\Entity\Pais $paises
     */
    public function removePaise(\aplicacion\EmisionesBundle\Entity\Pais $paises)
    {
        $this->paises->removeElement($paises);
    }

    /**
     * Get paises
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPaises()
    {
        return $this->paises;
    }
}
