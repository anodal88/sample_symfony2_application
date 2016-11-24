<?php

namespace aplicacion\EmisionesBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use aplicacion\EmisionesBundle\Entity\Usuariointerno;
use aplicacion\EmisionesBundle\Form\UsuariointernoType;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\RegistrationController;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
/**
 * Usuariointerno controller.
 *
 */
class UsuariointernoController extends RegistrationController
{

    public function registerAction(Request $request)
    {
        
        $allowed = array('png', 'jpg', 'gif','jpeg');
        $path = $this->container->getParameter('aplicacion.directorio.imagenes.usuarios');
        $em = $this->getDoctrine()->getManager();            

        $dispatcher = $this->get('event_dispatcher');
        $entity = new Usuariointerno();
        $entity->setEnabled(true);
       
      
            
            
        $event = new GetResponseUserEvent($entity, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);
        
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        
       
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);


       
        if ($form->isValid()) { 
            $event = new FormEvent($form, $request);            
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event); 
            
              /*Agregar la foto a la carpeta de imagenes*/
        if(isset($_FILES['aplicacion_emisionesbundle_usuariointerno']['name']['foto']) && $_FILES['aplicacion_emisionesbundle_usuariointerno']['error']['foto'] == 0)
            {

                $extension = pathinfo($_FILES['aplicacion_emisionesbundle_usuariointerno']['name']['foto'], PATHINFO_EXTENSION);
                //print_r($extension);exit;
                $foto=  $this->crearNombre(10,$extension);
                if(!in_array(strtolower($extension), $allowed)){
                       $this->get('session')->getFlashBag()->add('error', 'El fichero que usted adjunta no es permitido!');
                       
                }

                if(move_uploaded_file($_FILES['aplicacion_emisionesbundle_usuariointerno']['tmp_name']['foto'], $path.$foto)){
                        $entity->setFoto($foto);
                }
            }
            
            
            //$entity->addRole('ROLE_USUARIO_INTERNO');
            //$entity->addRole('ROLE_CAJA');
            //$entity->addRole('ROLE_SUPERVISOR_COBRANZA');
            //$entity->addRole('ROLE_SUPERVISOR');
            //$entity->addRole('ROLE_COUNTER');
            $entity->setInicioJornada(new \DateTime('09:00:00'));
            $entity->setFinJornada(new \DateTime('19:00:00'));
            $entity->setInicioAlmuerzo(new \DateTime('13:00:00') );
            $entity->setFinAlmuerzo(new \DateTime('14:00:00') );
            
            $em->persist($entity);
            $em->flush();
            
            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_registration_confirmed');
                //$url = $this->generateUrl('fos_user_security_login');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($entity, $request, $response));

            return $response;
        }

        return $this->render('EmisionesBundle:Usuariointerno:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    function crearNombre($length,$ext)
    {
        if( ! isset($length) or ! is_numeric($length) ) $length=6;

        $str  = "0123456789abcdefghijklmnopqrstuvwxyz";
        $path = '';

        for($i=1 ; $i<$length ; $i++)
          $path .= $str{rand(0,strlen($str)-1)};

        return $path.'_'.date("d-m-Y_H-i-s").'.'.$ext;    
    }
    /**
     * Lists all Usuariointerno entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EmisionesBundle:Usuariointerno')->findAll();

        return $this->render('EmisionesBundle:Usuariointerno:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Usuariointerno entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Usuariointerno();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('usuariointerno_show', array('id' => $entity->getId())));
        }

        return $this->render('EmisionesBundle:Usuariointerno:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Usuariointerno entity.
     *
     * @param Usuariointerno $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Usuariointerno $entity)
    {
        $form = $this->createForm(new UsuariointernoType(), $entity, array(
            'action' => $this->generateUrl('usuariointerno_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Usuariointerno entity.
     *
     */
    public function newAction()
    {
        $entity = new Usuariointerno();
        $form   = $this->createCreateForm($entity);

        return $this->render('EmisionesBundle:Usuariointerno:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Usuariointerno entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Usuariointerno')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuariointerno entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Usuariointerno:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Usuariointerno entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Usuariointerno')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuariointerno entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Usuariointerno:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Usuariointerno entity.
    *
    * @param Usuariointerno $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Usuariointerno $entity)
    {
        $form = $this->createForm(new UsuariointernoType(), $entity, array(
            'action' => $this->generateUrl('usuariointerno_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

       // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Usuariointerno entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $allowed = array('png', 'jpg', 'gif','jpeg');
        $path = $this->container->getParameter('aplicacion.directorio.imagenes.usuarios');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Usuariointerno')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuariointerno entity.');
        }
        $avatarold=$entity->getFoto();//respaldando el avatar antiguo
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
        $entity->setPassword($editForm->getData()->getPlainPassword());           
        /*Agregar la foto a la carpeta de imagenes*/
        if(isset($_FILES['aplicacion_emisionesbundle_usuariointerno']['name']['foto']) && $_FILES['aplicacion_emisionesbundle_usuariointerno']['error']['foto'] == 0)
            {
            
                $extension = pathinfo($_FILES['aplicacion_emisionesbundle_usuariointerno']['name']['foto'], PATHINFO_EXTENSION);
                //print_r($extension);exit;
                $foto=  $this->crearNombre(10,$extension);
                if(!in_array(strtolower($extension), $allowed)){
                       $this->get('session')->getFlashBag()->add('error', 'El fichero que usted adjunta no es permitido!');
                       
                }
                if($avatarold!='')
                {   /*Eliminando el avatar viejo*/
                    
                    if(is_file($path.$avatarold))
                    {
                        unlink($path.$avatarold);
                    }
                }

                if(move_uploaded_file($_FILES['aplicacion_emisionesbundle_usuariointerno']['tmp_name']['foto'], $path.$foto)){
                        $entity->setFoto($foto);
                }
            
            }
            else
            {
                $entity->setFoto($avatarold);
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Su perfil ha sido actualizado!');
            return $this->redirect($this->generateUrl('usuariointerno_edit', array('id' => $id)));
        }
        else
        {
            $entity->setFoto($avatarold);
            $this->get('session')->getFlashBag()->add('error', 'Ha ocurrido un error, por favor revisar detalladamente los valores proporcionados!');
        }

        return $this->render('EmisionesBundle:Usuariointerno:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Usuariointerno entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EmisionesBundle:Usuariointerno')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Usuariointerno entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('usuariointerno'));
    }

    /**
     * Creates a form to delete a Usuariointerno entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('usuariointerno_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
