<?php
// src/Controller/ControladorFormularios.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\UsuarioType;
use App\Entity\Usuario;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Mensaje;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class ControladorFormularios extends AbstractController
{
	
	
/**
* @Route("/registro", name = "registro")
*/
	
public function registro(Request $request, UserPasswordEncoderInterface $encoder, MailerInterface $mailer)
	{				
		// Cargo el formulario
		$form = $this->createForm(UsuarioType::class);
		
		// Recojo la respuesta
		$form->handleRequest($request);
		if($form->isSubmitted()) {
			
		// Compruebo si no es válido
		if(!$form->isValid()) {
			return new Response("ERROR: algún campo falla!");
		} 
			
		// El formulario es válido, asigno los datos
		$datos = $form->getData();		
		$nombre 	= $datos['nombre'];
		$password 		= $datos['password'];
		$email 		= $datos['email'];
		$edad 		= $datos['edad'];
		$ciudad 		= $datos['ciudad'];
		$aficiones 		= $datos['aficiones'];
		$avatar 		= $datos['avatar'];
			
// Sacamos la extensión del fichero si se ha subido
if($avatar!=null)
$ext=$avatar->guessExtension();
else 
$ext = null;	
if($ext=="jpg"||$ext=="png"||$ext=="jpeg") {
// Le ponemos un nombre al fichero aleatorio para evitar duplicidades
$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
$codigoar = substr(str_shuffle($permitted_chars), 0, 10);	
$file_name=$codigoar.".".$ext;
 
// Guardamos el fichero en el directorio uploads que estará en el directorio /web del framework
$avatar->move("uploads", $file_name);
} else {
	$file_name=null;
}
	//Asignamos codigo de activacion y demas parametros
	$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
	$codigo = substr(str_shuffle($permitted_chars), 0, 10);	
	$entityManager = $this->getDoctrine()->getManager();
	$usubusca = $this->getDoctrine()->getRepository(Usuario::class)->find($nombre);
	if($usubusca != null){
			$mensajeconf = "Has puesto un nombre de usuario que ya existe, por favor vuelve a intentar registrarte";
			return $this->render('confirmaciones.html.twig', array('mensaje' => $mensajeconf));
	}
	$usuario = new Usuario();
	$usuario->setUsuario($nombre);
	$usuario->setClave($encoder->encodePassword($usuario, $password));
	$usuario->setRol(0);
	$usuario->setEmail($email);
	$usuario->setEdad($edad);
	$usuario->setAficiones($aficiones);
	$usuario->setCiudad($ciudad);
		if($file_name!=null) {
		$usuario->setAvatar($file_name);	
		} else {
			$usuario->setAvatar('default.png');
		}
		$usuario->setActivado(0);
		$usuario->setClaveact($codigo);
		$entityManager->persist($usuario);
		$entityManager->flush();
			

	 	//Enviamos el mail de registro
		$message = new email();
        $message->from(new Address('no-reply@whatschat.com', "WhatsChat!"));
        $message->to(new Address($email));
		$message->subject("Bienvenido a WhatsChat! Activa tu cuenta");
		$message->html("<h1>Bienvenido a WhatsChat!</h1><br/>
		Por favor, para poder usar tu cuenta haz click en el siguiente <a href=http://localhost/proyecto/public/confirmacion/$nombre/$codigo>enlace</a>.<br/><br/>
		Gracias por su registro!");
		$mailer->send($message);	
			$mensajeconf = "Se ha enviado un correo electrónico para activar su cuenta";
		return $this->render('confirmaciones.html.twig', array('mensaje' => $mensajeconf));
	 
		}
		
		// En otro caso, envío el formulario
		return $this->render('formuHola.html.twig', array('form' => $form->createView()));
	} 
	
/**
* @Route("/enviar_mensaje", name = "enviar_mensaje")
*/
	
public function enviar_mensaje(Request $request){	
			
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		$usuarios = $this->getDoctrine()->getRepository(Usuario::class)->findAll();
		// Cargo el formulario

		$usuario = $this->getUser()->getUsuario();
		$form = $this->createForm(EnvioType::class,  array('usuarios'=>$usuarios, 'usuActual'=>$usuario));
		
		// Recojo la respuesta
		$form->handleRequest($request);
		if($form->isSubmitted()) {
			
			// Compruebo si no es válido
			if(!$form->isValid()) {
				return new Response("ERROR: algún campo falla!");
			} 
			
			// El formulario es válido, recojo los datos
			$datos = $form->getData();		
			$destinatarios 	= $datos['destinatarios'];
			$mensaje 		= $datos['mensaje'];;
			
			foreach($destinatarios as $destinatario) {
		       $entityManager = $this->getDoctrine()->getManager();
				$remitente = $entityManager->find(Usuario::class, $usuario);
				$destino = $entityManager->find(Usuario::class, $destinatario);
				$msj = new Mensaje();
				$msj->setMensaje($mensaje);
				$msj->setRemitente($remitente);
				$msj->setDestinatario($destino);
				$msj->setLeido(false);
				$msj->setBorrado_salida(false);
				$msj->setBorrado_entrada(false);
				$msj->setFecha(date ('Y-m-d H:i:s'));
			$entityManager->persist($msj);
			$entityManager->flush();
				
			}

			return $this->render('bandejaSalida.html.twig' );
		}
		
		// En otro caso, envío el formulario
		return $this->render('formuMensaje.html.twig', array('form' => $form->createView(), 'usuarios'=>$usuarios));
	} 

/**
* @Route("/recuperacionclave", name = "recuperacionclave")
*/
	
	public function recuperacionclave(Request $request, UserPasswordEncoderInterface $encoder, MailerInterface $mailer)
	{				
		// Cargo el formulario
		$form = $this->createForm(RecuperaType::class);
		
		// Recojo la respuesta
		$form->handleRequest($request);
		if($form->isSubmitted()) {
			
			// Compruebo si no es válido
			if(!$form->isValid()) {
				return new Response("ERROR: algún campo falla!");
			} 
			
		// El formulario es válido, imprimo los datos
		$datos = $form->getData();		
		$user 	= $datos['nombre'];

		$entityManager = $this->getDoctrine()->getManager();
		$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
		$codigo = substr(str_shuffle($permitted_chars), 0, 10);	
		$usuario = $entityManager->find(Usuario::class, $user);
		if($usuario == null){
			$mensajeconf = "Usuario no encontrado";
			return $this->render('confirmaciones.html.twig', array('mensaje' => $mensajeconf));
		}	
		$email = $usuario->getEmail();
		$usuario->setRecupera($codigo);
		$entityManager->persist($usuario);
		$entityManager->flush();
			

	 	
		$message = new email();
        $message->from(new Address('no-reply@whatschat.com', "WhatsChat!"));
        $message->to(new Address($email));
		$message->subject("Recupera tu clave en WhatsChat!");
		$message->html("<h1>Recupera tu clave WhatsChat!</h1><br/>
		Por favor, para recuperar tu contraseña haz click en el siguiente <a href=http://localhost/proyecto/public/recuperacion/$user/$codigo>enlace</a>.<br/><br/>
		Gracias por su registro!");
		$mailer->send($message);	
		$mensajeconf = "Se ha enviado un correo electrónico para restablecer su clave";
		return $this->render('confirmaciones.html.twig', array('mensaje' => $mensajeconf));
		}
		
		// En otro caso, envío el formulario
		return $this->render('formuRecupera.html.twig', array('form' => $form->createView()));
	} 
	
	
/**
* @Route("/area_usuario", name = "area_usuario")
*/
	
public function area_usuario(Request $request, UserPasswordEncoderInterface $encoder,  MailerInterface $mailer){				
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		$usuario = $this->getUser();
		$cambiado = false;
		// Cargo el formulario
		$form = $this->createForm(AreauserType::class,  array('usuActual'=>$usuario));
		
		// Recojo la respuesta
		$form->handleRequest($request);
		if($form->isSubmitted()) {
			
		// Compruebo si no es válido
		if(!$form->isValid()) {
			return new Response("ERROR: algún campo falla!");
		} 
			
		// El formulario es válido, imprimo los datos
		$datos = $form->getData();		
		$email 		= $datos['email'];
		$edad 		= $datos['edad'];
		$aficiones 		= $datos['aficiones'];
		$ciudad 		= $datos['ciudad'];
		$avatar 		= $datos['cambia_tu_avatar'];
			// Sacamos la extensión del fichero
if($avatar!=null)
$ext=$avatar->guessExtension();
else 
$ext = null;	
if($ext=="jpg"||$ext=="png"||$ext=="jpeg") {
// Le ponemos un nombre al fichero
$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
$codigoar = substr(str_shuffle($permitted_chars), 0, 10);	
$file_name=$codigoar.".".$ext;
 
// Guardamos el fichero en el directorio uploads que estará en el directorio /web del framework
$avatar->move("uploads", $file_name);
$mensaje = "Has cambiado tus datos correctamente!";
} else {
	$file_name=null;
	$mensaje = "No se ha podido cambiar la foto de perfil, comprueba su extensión, has cambiado el resto de datos";
	if($avatar==null) {
	$mensaje = "Has cambiado tus datos correctamente!";	
	}
}


	
		$usuario = $this->getUser();
		$usuario->setEmail($email);
		$usuario->setEdad($edad);
		$usuario->setCiudad($ciudad);
		$usuario->setAficiones($aficiones);
		if($file_name!=null) {
		$nombrearchivo = $usuario->getAvatar();
		if($nombrearchivo != "default.png") {
			if(file_exists("uploads/".$nombrearchivo)){
    		unlink("uploads/".$nombrearchivo);
			}
		}	
		$usuario->setAvatar($file_name);	
		};
	    $entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($usuario);
		$entityManager->flush();
			
	 	//Avisamos al usuario de que han cambiado sus datos con un correo
		$message = new email();
        $message->from(new Address('no-reply@whatschat.com', "WhatsChat!"));
        $message->to(new Address($email));
		$message->subject("Se han cambiado tus datos");
		$message->html("<h1>Has cambiado tus datos WhatsChat!</h1><br/>
		Por favor, si no has sido tu, ponte en contacto con nosotros o cambia tu clave.!");
		$mailer->send($message);	
		//return $this->redirectToRoute('area_usuario', array('cambiado' => true));
		sleep(5);
		$cambiado = true;
		}
	
		if($cambiado) {
		return $this->render('formuPerfil.html.twig', array('form' => $form->createView(), 'mensaje' => $mensaje));
		} else {
		// En otro caso, envío el formulario
		return $this->render('formuPerfil.html.twig', array('form' => $form->createView()));
		}

	} 
	
}