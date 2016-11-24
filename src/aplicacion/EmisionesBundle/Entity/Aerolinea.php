<?php

namespace aplicacion\EmisionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Aerolinea
 * 
 * @ORM\Table()
 * @ORM\Entity
 * @Gedmo\Loggable(logEntryClass="aplicacion\AuditoriaBundle\Entity\Traza")
 */
class Aerolinea
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
     * @Assert\NotBlank()
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @Gedmo\Versioned()
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="iata", type="string", length=5)
     */
     private $iata;


    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;
    
    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Tarjetacredito", mappedBy="aerolinea")
     */
    private $fpTarjetasCredito;
    
   /**
    * @ORM\ManyToMany(targetEntity="Agencia", mappedBy="aerolineasPlanPiloto")
    **/
    private $agenciasPlanPiloto;


    public function __toString()
    {
        return $this->getNombre();
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
     * @return Aerolinea
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
     * @return Aerolinea
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
     * Constructor
     */
    public function __construct()
    {
        $this->fpTarjetasCredito = new \Doctrine\Common\Collections\ArrayCollection();
        $this->agenciasPlanPiloto = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add fpTarjetasCredito
     *
     * @param \aplicacion\EmisionesBundle\Entity\TarjetaCredito $fpTarjetasCredito
     * @return Aerolinea
     */
    public function addFpTarjetasCredito(\aplicacion\EmisionesBundle\Entity\TarjetaCredito $fpTarjetasCredito)
    {
        $this->fpTarjetasCredito[] = $fpTarjetasCredito;

        return $this;
    }

    /**
     * Remove fpTarjetasCredito
     *
     * @param \aplicacion\EmisionesBundle\Entity\TarjetaCredito $fpTarjetasCredito
     */
    public function removeFpTarjetasCredito(\aplicacion\EmisionesBundle\Entity\TarjetaCredito $fpTarjetasCredito)
    {
        $this->fpTarjetasCredito->removeElement($fpTarjetasCredito);
    }

    /**
     * Get fpTarjetasCredito
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFpTarjetasCredito()
    {
        return $this->fpTarjetasCredito;
    }

    /**
     * Add agenciasPlanPiloto
     *
     * @param \aplicacion\EmisionesBundle\Entity\Agencia $agenciasPlanPiloto
     * @return Aerolinea
     */
    public function addAgenciasPlanPiloto(\aplicacion\EmisionesBundle\Entity\Agencia $agenciasPlanPiloto)
    {
        $this->agenciasPlanPiloto[] = $agenciasPlanPiloto;

        return $this;
    }

    /**
     * Remove agenciasPlanPiloto
     *
     * @param \aplicacion\EmisionesBundle\Entity\Agencia $agenciasPlanPiloto
     */
    public function removeAgenciasPlanPiloto(\aplicacion\EmisionesBundle\Entity\Agencia $agenciasPlanPiloto)
    {
        $this->agenciasPlanPiloto->removeElement($agenciasPlanPiloto);
        
    }

    /**
     * Get agenciasPlanPiloto
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAgenciasPlanPiloto()
    {
        return $this->agenciasPlanPiloto;
    }

    /**
     * Set iata
     *
     * @param string $iata
     * @return Aerolinea
     */
    public function setIata($iata)
    {
        $this->iata = $iata;

        return $this;
    }

    /**
     * Get iata
     *
     * @return string 
     */
    public function getIata()
    {
        return $this->iata;
    }
}
