<?php

namespace aplicacion\EmisionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tarjetacredito
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="aplicacion\EmisionesBundle\Entity\TarjetacreditoRepository")
 */
class Tarjetacredito extends Formapago
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
     * @ORM\Column(name="emisor_vtc", type="string", length=255)
     */
    protected $emisorVtc;
    
    /**
     * @var aerolinea
     * @ORM\ManyToOne(targetEntity="Aerolinea", inversedBy="fpTarjetasCredito")
     * @ORM\JoinColumn(name="aerolinea", referencedColumnName="id")
     */
    protected $aerolinea;

    /**
     * @var string
     *
     * @ORM\Column(name="banco_emisor_tarjeta", type="string", length=255)
     */
    protected $bancoEmisorTarjeta;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_tarjeta", type="string", length=255)
     */
    protected $tipoTarjeta;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_tarjeta", type="string", length=255)
     */
    protected $numeroTarjeta;

    /**
     * @var string
     *
     * @ORM\Column(name="propietario", type="string", length=255)
     */
    protected $propietario;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="vence", type="date")
     */
    protected $vence;

    /**
     * @var string
     *
     * @ORM\Column(name="pin", type="string", length=255)
     */
    protected $pin;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_pago", type="string", length=255)
     */
    protected $tipoPago;

    /**
     * @var string
     *
     * @ORM\Column(name="plazo", type="string", length=255)
     */
    protected $plazo;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_autorizacion", type="string", length=255)
     */
    protected $tipoAutorizacion;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_autorizacion", type="string", length=255)
     */
    protected $numeroAutorizacion;

    /**
     * @var float
     *
     * @ORM\Column(name="valor_tarjeta", type="float")
     */
    protected $valorTarjeta;

    /**
     * @var float
     *
     * @ORM\Column(name="interes_tarjeta", type="float")
     */
    protected $interesTarjeta;

    /**
     * @var float
     *
     * @ORM\Column(name="valor_total", type="float")
     */
    protected $valorTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="pago_pasajeros", type="string", length=255)
     */
    protected $pagoPasajeros;


    

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
     * Set emisorVtc
     *
     * @param string $emisorVtc
     * @return Tarjetacredito
     */
    public function setEmisorVtc($emisorVtc)
    {
        $this->emisorVtc = $emisorVtc;

        return $this;
    }

    /**
     * Get emisorVtc
     *
     * @return string 
     */
    public function getEmisorVtc()
    {
        return $this->emisorVtc;
    }

    /**
     * Set aerolinea
     *
     * @param string $aerolinea
     * @return Tarjetacredito
     */
    public function setAerolinea($aerolinea)
    {
        $this->aerolinea = $aerolinea;

        return $this;
    }

    /**
     * Get aerolinea
     *
     * @return string 
     */
    public function getAerolinea()
    {
        return $this->aerolinea;
    }

    /**
     * Set bancoEmisorTarjeta
     *
     * @param string $bancoEmisorTarjeta
     * @return Tarjetacredito
     */
    public function setBancoEmisorTarjeta($bancoEmisorTarjeta)
    {
        $this->bancoEmisorTarjeta = $bancoEmisorTarjeta;

        return $this;
    }

    /**
     * Get bancoEmisorTarjeta
     *
     * @return string 
     */
    public function getBancoEmisorTarjeta()
    {
        return $this->bancoEmisorTarjeta;
    }

    /**
     * Set tipoTarjeta
     *
     * @param string $tipoTarjeta
     * @return Tarjetacredito
     */
    public function setTipoTarjeta($tipoTarjeta)
    {
        $this->tipoTarjeta = $tipoTarjeta;

        return $this;
    }

    /**
     * Get tipoTarjeta
     *
     * @return string 
     */
    public function getTipoTarjeta()
    {
        return $this->tipoTarjeta;
    }

    /**
     * Set numeroTarjeta
     *
     * @param string $numeroTarjeta
     * @return Tarjetacredito
     */
    public function setNumeroTarjeta($numeroTarjeta)
    {
        $this->numeroTarjeta = $numeroTarjeta;

        return $this;
    }

    /**
     * Get numeroTarjeta
     *
     * @return string 
     */
    public function getNumeroTarjeta()
    {
        return $this->numeroTarjeta;
    }

    /**
     * Set propietario
     *
     * @param string $propietario
     * @return Tarjetacredito
     */
    public function setPropietario($propietario)
    {
        $this->propietario = $propietario;

        return $this;
    }

    /**
     * Get propietario
     *
     * @return string 
     */
    public function getPropietario()
    {
        return $this->propietario;
    }

    /**
     * Set vence
     *
     * @param \DateTime $vence
     * @return Tarjetacredito
     */
    public function setVence($vence)
    {
        $this->vence = $vence;

        return $this;
    }

    /**
     * Get vence
     *
     * @return \DateTime 
     */
    public function getVence()
    {
        return $this->vence;
    }

    /**
     * Set pin
     *
     * @param string $pin
     * @return Tarjetacredito
     */
    public function setPin($pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * Get pin
     *
     * @return string 
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * Set tipoPago
     *
     * @param string $tipoPago
     * @return Tarjetacredito
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
     * Set plazo
     *
     * @param string $plazo
     * @return Tarjetacredito
     */
    public function setPlazo($plazo)
    {
        $this->plazo = $plazo;

        return $this;
    }

    /**
     * Get plazo
     *
     * @return string 
     */
    public function getPlazo()
    {
        return $this->plazo;
    }

    /**
     * Set tipoAutorizacion
     *
     * @param string $tipoAutorizacion
     * @return Tarjetacredito
     */
    public function setTipoAutorizacion($tipoAutorizacion)
    {
        $this->tipoAutorizacion = $tipoAutorizacion;

        return $this;
    }

    /**
     * Get tipoAutorizacion
     *
     * @return string 
     */
    public function getTipoAutorizacion()
    {
        return $this->tipoAutorizacion;
    }

    /**
     * Set numeroAutorizacion
     *
     * @param string $numeroAutorizacion
     * @return Tarjetacredito
     */
    public function setNumeroAutorizacion($numeroAutorizacion)
    {
        $this->numeroAutorizacion = $numeroAutorizacion;

        return $this;
    }

    /**
     * Get numeroAutorizacion
     *
     * @return string 
     */
    public function getNumeroAutorizacion()
    {
        return $this->numeroAutorizacion;
    }

    /**
     * Set valorTarjeta
     *
     * @param float $valorTarjeta
     * @return Tarjetacredito
     */
    public function setValorTarjeta($valorTarjeta)
    {
        $this->valorTarjeta = $valorTarjeta;

        return $this;
    }

    /**
     * Get valorTarjeta
     *
     * @return float 
     */
    public function getValorTarjeta()
    {
        return $this->valorTarjeta;
    }

    /**
     * Set interesTarjeta
     *
     * @param float $interesTarjeta
     * @return Tarjetacredito
     */
    public function setInteresTarjeta($interesTarjeta)
    {
        $this->interesTarjeta = $interesTarjeta;

        return $this;
    }

    /**
     * Get interesTarjeta
     *
     * @return float 
     */
    public function getInteresTarjeta()
    {
        return $this->interesTarjeta;
    }

    /**
     * Set valorTotal
     *
     * @param float $valorTotal
     * @return Tarjetacredito
     */
    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;

        return $this;
    }

    /**
     * Get valorTotal
     *
     * @return float 
     */
    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    /**
     * Set pagoPasajeros
     *
     * @param string $pagoPasajeros
     * @return Tarjetacredito
     */
    public function setPagoPasajeros($pagoPasajeros)
    {
        $this->pagoPasajeros = $pagoPasajeros;

        return $this;
    }

    /**
     * Get pagoPasajeros
     *
     * @return string 
     */
    public function getPagoPasajeros()
    {
        return $this->pagoPasajeros;
    }
    /**
     * @var \aplicacion\EmisionesBundle\Entity\Orden
     */
    protected $orden;


    /**
     * Set orden
     *
     * @param \aplicacion\EmisionesBundle\Entity\Orden $orden
     * @return Tarjetacredito
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
