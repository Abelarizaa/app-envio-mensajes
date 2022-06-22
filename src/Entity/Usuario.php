<?php
// src/Entity/Usuario.php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
// =======================================================
// La clase debe implementar UserInterface y Serializable
// =======================================================

/**
 * @ORM\Entity @ORM\Table(name="usuarios")
 */
class Usuario implements UserInterface, \Serializable 
{
	// =======================================================
	// Atributos privados
	// =======================================================

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $usuario;
    /**
     * @ORM\Column(type="string")
     */
    private $clave;
    /**
     * @ORM\Column(type="integer")
     */
    private $rol;
    /**
     * @ORM\Column(type="string")
     */
    private $email;	
    /**
     * @ORM\Column(type="string")
     */
    private $recupera;
	 /**
     * @ORM\Column(type="string")
     */
    private $claveact;
    /**
     * @ORM\Column(type="integer")
     */
    private $activado;
    /**
     * @ORM\Column(type="integer")
     */
    private $edad;
	 /**
     * @ORM\Column(type="string")
     */
    private $ciudad;
	 /**
     * @ORM\Column(type="string")
     */
    private $aficiones;
	 /**
     * @ORM\Column(type="string")
     */
    private $avatar;
    /**
     * @ORM\Column(type="integer")
     */
    private $bloqueo;
	
	/**
     * Un usuario recibe muchos mensajes
     * @ORM\OneToMany(targetEntity="Mensaje", mappedBy="id_destinatario")
     */
	private $recibidos;

	public function getRecibidos()
    {
        return $this->recibidos;

    }


	/**
     * Un usuario envia muchos mensajes
     * @ORM\OneToMany(targetEntity="Mensaje", mappedBy="id_remitente")
     */
	private $enviados;
	public function __construct() {
        $this->recibidos = new ArrayCollection();
        $this->enviados = new ArrayCollection();
    }	
	
	public function getEnviados()
    {
        return $this->enviados;

    }
	// =======================================================
	// Setters y getters
	// =======================================================
    public function getUsuario() {
        return $this->usuario;
    }
    public function setUsuario($usuario) {
       $this->usuario = $usuario;
    }
    public function getClave() {
        return $this->clave;
    }
    public function setClave($clave) {
        $this->clave = $clave;
    }
	public function getRol() {
        return $this->rol;
    }
    public function setRol($rol) {
        $this->rol = $rol;
    }
 	public function getEmail() {
        return $this->email;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
  	public function getRecupera() {
        return $this->recupera;
    }
    public function setRecupera($recupera) {
        $this->recupera = $recupera;
    }
  	public function getClaveact() {
        return $this->claveact;
    }
    public function setClaveact($claveact) {
        $this->claveact = $claveact;
    }
  	public function getActivado() {
        return $this->activado;
    }
    public function setActivado($activado) {
        $this->activado = $activado;
    }
	public function getEdad() {
        return $this->edad;
    }
    public function setEdad($edad) {
        $this->edad = $edad;
    }
	public function getCiudad() {
        return $this->ciudad;
    }
    public function setCiudad($ciudad) {
        $this->ciudad = $ciudad;
    }
	public function getAficiones() {
        return $this->aficiones;
    }
    public function setAficiones($aficiones) {
        $this->aficiones = $aficiones;
    }
	public function getAvatar() {
        return $this->avatar;
    }
    public function setAvatar($avatar) {
        $this->avatar = $avatar;
    }
	public function getBloqueo() {
        return $this->bloqueo;
    }
    public function setBloqueo($bloqueo) {
        $this->bloqueo = $bloqueo;
    }
	
	
 	// =======================================================
	// Elementos necesarios para la autenticación
	// =======================================================
   public function getRoles()
    {
		// Si el usuario es supervisor, entonces tiene acceso
		// a la zona de administración.
	   if($this->bloqueo){		
			return array('ROLE_BLOQUEADO');  
		}
		if($this->rol==true && $this->activado==true){
			return array('ROLE_USER', 'ROLE_ADMIN'); 
		} else if($this->activado){
					
			return array('ROLE_USER');  
		}
		  return array();  
	}

    public function getPassword()
    {
        return $this->getClave();
    }

    public function getSalt()
    {
        return;
    }

    public function getUsername()
    {
        return $this->getUsuario();
    }

    public function eraseCredentials()
    {
        return;
    }
	
	public function serialize(){
        return serialize(array(
            $this->usuario,
            $this->clave,
            $this->rol,
			$this->email,
			$this->recupera,
            $this->claveact,
            $this->activado,
            $this->edad,
			$this->ciudad,
			$this->aficiones,
			$this->avatar,
			$this->bloqueo
        ));
    }
	
    public function unserialize($serialized){
        list (
            $this->usuario,
            $this->clave,
            $this->rol,
			$this->email,
			$this->recupera,
            $this->claveact,
            $this->activado,
            $this->edad,
			$this->ciudad,
			$this->aficiones,
			$this->avatar,
			$this->bloqueo
            ) = unserialize($serialized);
    }
}