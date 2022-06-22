<?php
// src/Controller/ControladorLogin.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Mensaje;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpFoundation\Request;


class ControladorLogin extends AbstractController
{
	/**
     * @Route("/login", name="controlador_login")
     */
    public function login(){    
        return $this->render('acceso.html.twig');
    }
	
	/**
	 * @Route("/logout", name="controlador_logout")
	 */
	public function logout()
	{
		// Este método nunca se llamará, pero es necesario para crear la ruta "/logout".
		return;
	}	
	
	/**
	 * @Route("/portaladmin", name="controlador_portal_admin")
	 */
	public function portalAdmin()
	{
		//Si tiene el role administrador le permitimos el acceso
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		$usuarios = $this->getDoctrine()->getRepository(Usuario::class)->findAll();
		// Cargo el formulario

		$usuario = $this->getUser()->getUsuario();
		//Hacemos un bucle for para enviarle todos los usuarios menos a si mismo
		for ($i = 0; $i <=count($usuarios) ; $i++) {
   			if(!strcmp($usuarios[$i]->getUsuario(), $this->getUser()->getUsuario())) {
				unset($usuarios[$i]);
			}
		}

		return $this->render('administracion.html.twig', array('usuarios'=>$usuarios));
	} 

	
	
/**
* @Route("/bandeja_entrada", name="bandeja_entrada")
*/
public function bandeja_entrada()
{
// Comprobamos si el usuario al menos se ha logueado
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');	
    return $this->render('bandejaEntrada.html.twig');
}
	
/**
* @Route("/bandeja_salida", name="bandeja_salida")
*/
public function bandeja_salida()
	{
		// Comprobamos si el usuario al menos se ha logueado
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
        return $this->render('bandejaSalida.html.twig');
	}
	
/**
* @Route("/bandeja_salida2", name="bandeja_salida2")
*/
public function bandeja_salida2()
{
	// Comprobamos si el usuario al menos se ha logueado
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
	
	return $this->render('bandejaSalida.html.twig');
}
	
	
	/**
	 * @Route("/eliminar_mensaje/{id}", name="eliminar_mensaje")
	 */
	public function eliminar_mensaje(Request $request, $id)
	{
		// Comprobamos si el usuario al menos se ha logueado
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
 		$mensaje = $this->getDoctrine()->getRepository(Mensaje::class)->find($id);
       if (!$mensaje) {
            throw $this->createNotFoundException('Mensaje no encontrado');
        }
	    $entityManager = $this->getDoctrine()->getManager();
		//Si el mensaje ha sido borrado de la bandeja de salida lo borramos definitivamente
		if($mensaje->getBorrado_salida()==1) {
			$entityManager->remove($mensaje);
			$entityManager->flush();
			    return $this->redirectToRoute('bandeja_entrada');
		}
		
		$mensaje->setBorrado_entrada(true);
		$entityManager->persist($mensaje);
		$entityManager->flush();

		return $this->redirectToRoute('bandeja_entrada');
		
	}
	/**
	 * @Route("/eliminar_mensaje_salida/{id}", name="eliminar_mensaje_salida")
	 */
	public function eliminar_mensaje_salida(Request $request, $id)
	{
		// Comprobamos si el usuario al menos se ha logueado
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
 		$mensaje = $this->getDoctrine()->getRepository(Mensaje::class)->find($id);
       if (!$mensaje) {
            throw $this->createNotFoundException('Mensaje no encontrado');
        }
	    $entityManager = $this->getDoctrine()->getManager();
		//Si el mensaje ha sido borrado de la bandeja de salida lo borramos definitivamente
		if($mensaje->getBorrado_Entrada()==1) {
			$entityManager->remove($mensaje);
			$entityManager->flush();
			    return $this->redirectToRoute('bandeja_salida');
		}
		
		$mensaje->setBorrado_salida(true);
		$entityManager->persist($mensaje);
		$entityManager->flush();

		return $this->redirectToRoute('bandeja_salida');
		
	}
	
	
/**
 * @Route("/ver_users", name="ver_users")
 */
public function ver_users(Request $request){	
	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
	$usuarios = $this->getDoctrine()->getRepository(Usuario::class)->findAll();
	$usuario = $this->getUser()->getUsuario();
	for ($i = 0; $i <=count($usuarios) ; $i++) {
   	if(!strcmp($usuarios[$i]->getUsuario(), $this->getUser()->getUsuario())) {
	unset($usuarios[$i]);
		}
	}
	return $this->render('ver_users.html.twig', array('usuarios'=>$usuarios));
} 
	
	
/**
 * @Route("/responder_mensaje/{rmt}/{dst}/{msj}" )
 */
	public function responder_mensaje($rmt,$dst,$msj){
	$entityManager = $this->getDoctrine()->getManager();
	$mensaje = new Mensaje();
	$remitente = $entityManager->find(Usuario::class, $rmt);
	$destinatario = $entityManager->find(Usuario::class, $dst);
	$mensaje->setDestinatario($destinatario);
	$mensaje->setRemitente($remitente);
	$mensaje->setMensaje($msj);
	$mensaje->setLeido(false);
	$mensaje->setBorrado_salida(false);
	$mensaje->setBorrado_entrada(false);
	$mensaje->setFecha(date ('Y-m-d H:i:s'));

	$entityManager->persist($mensaje);
	$entityManager->flush();
	return $this->redirectToRoute('bandeja_salida');

	}
	
/**
 * @Route("/ver_mensaje_entrada/{id}" )
 */
	public function ver_mensaje_entrada($id){
	$entityManager = $this->getDoctrine()->getManager();
	$mensaje = new Mensaje();
	$mensaje = $entityManager->find(Mensaje::class, $id);

	$mensaje2 = Array();
	$mensaje2[0]['id_remitente'] = $mensaje->getRemitente()->getUsuario();
	$mensaje2[0]['fecha'] = $mensaje->getFecha();
	$mensaje2[0]['mensaje'] = $mensaje->getMensaje();
	$mensaje->setLeido(true);
	$entityManager->persist($mensaje);
	$entityManager->flush();
		
	$json = json_encode($mensaje2);
	return new Response($json);	

}

/**
 * @Route("/ver_mensaje_salida/{id}" )
*/
public function ver_mensaje_salida($id){
	$entityManager = $this->getDoctrine()->getManager();
	$mensaje = new Mensaje();
	$mensaje = $entityManager->find(Mensaje::class, $id);

	$mensaje2 = Array();
	$mensaje2[0]['id_remitente'] = $mensaje->getDestinatario()->getUsuario();
	$mensaje2[0]['fecha'] = $mensaje->getFecha();
	$mensaje2[0]['mensaje'] = $mensaje->getMensaje();
		
	$json = json_encode($mensaje2);
	return new Response($json);	

	}
	
/**
* @Route("/bloquear/{id}/{opcion}", name="bloquear")
*/
public function bloquear($id,$opcion){
	$this->denyAccessUnlessGranted('ROLE_ADMIN');
	$entityManager = $this->getDoctrine()->getManager();
	$usuario = $entityManager->find(Usuario::class, $id);

		$usuario->setBloqueo($opcion);

	

	$entityManager->persist($usuario);
	$entityManager->flush();
	return new Response("bloqueado");	

	}
}


	
