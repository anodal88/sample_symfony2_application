<?php

namespace aplicacion\EmisionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pagodirecto
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="aplicacion\EmisionesBundle\Entity\PagodirectoRepository")
 */
class Pagodirecto extends Formapago
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
     * @var string
     *
     * @ORM\Column(name="tipo_pago", type="string", length=255)
     */
    protected $tipoPago;

    /**
     * @var float
     *
     * @ORM\Column(name="valor", type="float")
     */
    protected $valor;


    

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
     * Set tipoPago
     *
     * @param string $tipoPago
     * @return Pagodirecto
     */
    public function setTipoPago($tipoPago)
    {
        $this->tipoPago = $tipoPago;

        return $this;
    }

    /**
     * Get tipoPago
     *
     * @return string 
     */
    public function getTipoPago()
    {
        return $this->tipoPago;
    }

    /**
     * Set valor
     *
     * @param float $valor
     * @return Pagodirecto
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return float 
     */
    public function getValor()
    {
        return $this->valor;
    }
    /**
     * @var \aplicacion\EmisionesBundle\Entity\Orden
     */
    protected $orden;


    /**
     * Set orden
     *
     * @param \aplicacion\EmisionesBundle\Entity\Orden $orden
     * @return Pagodirecto
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
