<?php

namespace aplicacion\EmisionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="tipo", type="string")
 * @ORM\DiscriminatorMap({"Tarjeta Credito" = "Tarjetacredito", "Pago Directo" = "Pagodirecto","deposito_efectivo_transferencia_bancaria" = "DepefectivoTransferenciabancaria"})
 * @ORM\Entity(repositoryClass="aplicacion\EmisionesBundle\Entity\FormapagoRepository")
 */
class Formapago
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
     * @var orden
     * @ORM\ManyToOne(targetEntity="Orden", inversedBy="formasPagos")
     * @ORM\JoinColumn(name="orden", referencedColumnName="id")
     */
    protected $orden;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ordenes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add ordenes
     *
     * @param \aplicacion\EmisionesBundle\Entity\Orden $ordenes
     * @return Formapago
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
    public function getTipo()
    {
       if($this instanceof Tarjetacredito)
        {
            return 'Tarjeta Credito';
        }
        if($this instanceof Pagodirecto)
        {
            return 'Pago Directo';
        }
        if($this instanceof DepefectivoTransferenciabancaria)
        {
            return 'DETB';
        }
       
    }

    /**
     * Set orden
     *
     * @param \aplicacion\EmisionesBundle\Entity\Orden $orden
     * @return Formapago
     */
    public function setOrden(\aplicacion\EmisionesBundle\Entity\Orden $orden = null)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return \aplicacion\EmisionesBundle\Entity\Orden 
     */
    public function getOrden()
    {
        return $this->orden;
    }
}
