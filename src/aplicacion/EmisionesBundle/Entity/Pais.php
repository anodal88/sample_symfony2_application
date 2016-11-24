<?php

namespace aplicacion\EmisionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Pais
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="aplicacion\EmisionesBundle\Entity\PaisRepository")
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza")
 */
class Pais
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
     * @Gedmo\Versioned()
     * @var continente
     * @ORM\ManyToOne(targetEntity="Continente", inversedBy="paises")
     * @ORM\JoinColumn(name="continente", referencedColumnName="id")
     */   
    private $continente;
    
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Ciudad", mappedBy="pais")
     */
    private $ciudades;
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->nombre;
    }


    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Pais
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
     * Set continente
     *
     * @param \aplicacion\EmisionesBundle\Entity\Continente $continente
     * @return Pais
     */
    public function setContinente(\aplicacion\EmisionesBundle\Entity\Continente $continente = null)
    {
        $this->continente = $continente;

        return $this;
    }

    /**
     * Get continente
     *
     * @return \aplicacion\EmisionesBundle\Entity\Continente 
     */
    public function getContinente()
    {
        return $this->continente;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ciudades = new ArrayCollection();
    }

    /**
     * Add ciudades
     *
     * @param \aplicacion\EmisionesBundle\Entity\Ciudad $ciudades
     * @return Pais
     */
    public function addCiudade(\aplicacion\EmisionesBundle\Entity\Ciudad $ciudades)
    {
        $this->ciudades[] = $ciudades;

        return $this;
    }

    /**
     * Remove ciudades
     *
     * @param \aplicacion\EmisionesBundle\Entity\Ciudad $ciudades
     */
    public function removeCiudade(\aplicacion\EmisionesBundle\Entity\Ciudad $ciudades)
    {
        $this->ciudades->removeElement($ciudades);
    }

    /**
     * Get ciudades
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCiudades()
    {
        return $this->ciudades;
    }
}
