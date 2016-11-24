<?php

namespace aplicacion\EmisionesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DepefectivoTransferenciabancaria
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="aplicacion\EmisionesBundle\Entity\DepefectivoTransferenciabancariaRepository")
 */
class DepefectivoTransferenciabancaria extends Formapago
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
     * @ORM\Column(name="banco", type="string", length=255)
     */
    protected $banco;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_documento", type="string", length=255)
     */
    protected $numeroDocumento;

    /**
     * @var float
     *
     * @ORM\Column(name="valor", type="float")
     */
    protected $valor;

    /**
     * @var string
     *
     * @ORM\Column(name="transaccion", type="string", length=255)
     */
    protected $transaccion;



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
     * Set banco
     *
     * @param string $banco
     * @return DepefectivoTransferenciabancaria
     */
    public function setBanco($banco)
    {
        $this->banco = $banco;

        return $this;
    }

    /**
     * Get banco
     *
     * @return string 
     */
    public function getBanco()
    {
        return $this->banco;
    }

    /**
     * Set numeroDocumento
     *
     * @param string $numeroDocumento
     * @return DepefectivoTransferenciabancaria
     */
    public function setNumeroDocumento($numeroDocumento)
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    /**
     * Get numeroDocumento
     *
     * @return string 
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    /**
     * Set valor
     *
     * @param float $valor
     * @return DepefectivoTransferenciabancaria
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
     * Set transaccion
     *
     * @param string $transaccion
     * @return DepefectivoTransferenciabancaria
     */
    public function setTransaccion($transaccion)
    {
        $this->transaccion = $transaccion;

        return $this;
    }

    /**
     * Get transaccion
     *
     * @return string 
     */
    public function getTransaccion()
    {
        return $this->transaccion;
    }
    /**
     * @var \aplicacion\EmisionesBundle\Entity\Orden
     */
    protected $orden;


    /**
     * Set orden
     *
     * @param \aplicacion\EmisionesBundle\Entity\Orden $orden
     * @return DepefectivoTransferenciabancaria
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
