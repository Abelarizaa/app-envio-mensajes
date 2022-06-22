<?php
// src/Entity/Mensaje.php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;


// =======================================================
// La clase debe implementar UserInterface y Serializable
// =======================================================

/**
 * @ORM\Entity @ORM\Table(name="mensajes")
 */
class Mensaje 
{
	// =======================================================
	// Atributos privados
	// =======================================================

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
	 *@ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
	/**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy = "recibidos")
     * @ORM\JoinColumn(name="id_destinatario", referencedColumnName="usuario")
     **/
    private $id_destinatario;
	/**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy = "enviados")
     * @ORM\JoinColumn(name="id_remitente", referencedColumnName="usuario")
     **/
    private $id_remitente;
    /**
     * @ORM\Column(type="string")
     */
    private $mensaje;	
	/**
    * @ORM\Column(type="string")
    */
    private $fecha;
	/**
    * @ORM\Column(type="integer")
* @ORM\GeneratedValue(strategy="IDENTITY")
    */
    private $leido;
	/**
    * @ORM\Column(type="integer")
* @ORM\GeneratedValue(strategy="IDENTITY")
    */
    private $borrado_salida;
	/**
    * @ORM\Column(type="integer")
    */
    private $borrado_entrada;
	

	// =======================================================
	// Setters y getters
	// =======================================================
    public function getId() {
        return $this->id;
    }

    public function getDestinatario() {
        return $this->id_destinatario;
    }
    public function setDestinatario($destinatario) {
        $this->id_destinatario = $destinatario;
    }
	public function getRemitente() {
        return $this->id_remitente;
    }
    public function setRemitente($remitente) {
        $this->id_remitente = $remitente;
    }
 	public function getMensaje() {
        return $this->mensaje;
    }
    public function setMensaje($mensaje) {
        $this->mensaje = $mensaje;
    }
	
	public function getLeido() {
     return $this->leido;
    }
    public function setLeido($leido) {
        $this->leido = $leido;
    }
	public function getBorrado_salida() {
     return $this->borrado_salida;
    }
    public function setBorrado_salida($opcion) {
        $this->borrado_salida = $opcion;
    }
	public function getBorrado_entrada() {
     return $this->borrado_entrada;
    }
    public function setBorrado_entrada($opcion) {
        $this->borrado_entrada = $opcion;
    }
	public function getFecha() {
     return $this->fecha;
    }
    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }
 

	
	public function serialize(){
        return serialize(array(
            $this->id,
            $this->id_destinatario,
            $this->id_remitente,
			$this->mensaje,
			$this->fecha,
			$this->leido,
			$this->borrado_salida,
			$this->borrado_entrada
        ));
    }
	
    public function unserialize($serialized){
        list (
            $this->id,
            $this->id_destinatario,
            $this->id_remitente,
			$this->mensaje,
			$this->fecha,
			$this->leido,
			$this->borrado_salida,
			$this->borrado_entrada
            ) = unserialize($serialized);
    }
}