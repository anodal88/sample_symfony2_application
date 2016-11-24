<?php

namespace aplicacion\BaseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use aplicacion\BaseBundle\Entity\Feriado;
use aplicacion\BaseBundle\Entity\Empresa;
use aplicacion\BaseBundle\Form\FeriadoType;
use Symfony\Component\HttpFoundation\Response;
/**
 * Feriado controller.
 *
 */
class FeriadoController extends Controller
{

    /**
     * Lists all Feriado entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BaseBundle:Feriado')->findAll();

        return $this->render('BaseBundle:Feriado:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    public function load_ajaxAction()
    {
        $peticion = $this->getRequest();
        $start = $peticion->request->get('comienzo');
       $start=new \DateTime($start);
        $end = $peticion->request->get('fin');
       $end=new \DateTime($end);
       // print_r($start->format('Y-m-d').'-'.$end->format('Y-m-d'));exit;
        $em = $this->getDoctrine()->getManager();
       //$feriados= $em->getRepository('BaseBundle:Feriado')->findAll();
        $feriados=$em->getRepository('BaseBundle:Feriado')->loadFeriadosByDate($start,$end);
        //print_r(count($feriados));exit;
        $result= array();
        for ($i = 0; $i < count($feriados); $i++) {
            $result[$i]['id']=$feriados[$i]['id'];
            $result[$i]['title']=$feriados[$i]['descripcion'];
            $result[$i]['start']=$feriados[$i]['inicio']->format(\DateTime::ISO8601);           
            //print_r( $result[$i]['start']=$feriados[$i]['inicio']->format('Y-m-dTH:m:i'));exit;
            $result[$i]['end']=$feriados[$i]['fin']->format(\DateTime::ISO8601);   
            $result[$i]['allDay']=false;
            $result[$i]['url']='#'; //$this->generateUrl('feriado_show', array('id' => $feriados[$i]->getId()));
        }
        
        return new Response(json_encode($result));
    }
    /**
     * Creates a new Feriado entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Feriado();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('feriado_show', array('id' => $entity->getId())));
        }

        return $this->render('BaseBundle:Feriado:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    
    public function new_ajaxAction()
    {
        $em = $this->getDoctrine()->getManager();
        $peticion = $this->getRequest();
        $rango = $peticion->request->get('rango');
        $descripcion=$peticion->request->get('descripcion');
        $empresa_id=$peticion->request->get('empresa_id');
        $entity = new Feriado();
        $empresa = $em->getRepository('BaseBundle:Empresa')->find($empresa_id);
        $rango=  explode('-', $rango);
        $inicio=$rango[0];
        $fin =$rango[1];
        $entity->setInicio(new \DateTime($inicio));
        $entity->setFin(new \DateTime($fin));
        $entity->setDescripcion($descripcion);        
        $em->persist($entity);        
        $empresa->addFeriado($entity);
        $em->persist($empresa);
        $em->flush();
        $feriados= $em->getRepository('BaseBundle:Feriado')->findAll();
        $result= array();
        for ($i = 0; $i < count($feriados); $i++) {
            $result[$i]['id']=$feriados[$i]->getId();
            $result[$i]['title']=$feriados[$i]->getDescripcion();
            $result[$i]['start']=$feriados[$i]->getInicio();
            $result[$i]['end']=$feriados[$i]->getFin();
            
        }
      
        return new Response(json_encode($result));
    }
    
    public function update_ajaxAction()
    {
        $em = $this->getDoctrine()->getManager();
        $peticion = $this->getRequest();
        $id_feriado = $peticion->request->get('id');
        $descripcion= $peticion->request->get('descripcion');
        $rango= $peticion->request->get('rango');
        $rango=  explode('-', $rango);
        $inicio=$rango[0];
        $fin =$rango[1];
        $entity= $em->getRepository('BaseBundle:Feriado')->find($id_feriado);
        if (!$entity) {
            throw $this->createNotFoundException('El Feriado que usted esta tratando de modificar no existe, otro usuario lo puede haber eliminado.');
        }
        else
        {
            $entity->setInicio(new \DateTime($inicio));
            $entity->setFin(new \DateTime($fin));
            $entity->setDescripcion($descripcion);
            $em->flush();
            return new Response(json_encode('El proceso ha culminado satisfactoriamente.')); 
        }
        
    }

    /**
     * Creates a form to create a Feriado entity.
     *
     * @param Feriado $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Feriado $entity)
    {
        $form = $this->createForm(new FeriadoType(), $entity, array(
            'action' => $this->generateUrl('feriado_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Feriado entity.
     *
     */
    public function newAction()
    {
        $entity = new Feriado();
        $form   = $this->createCreateForm($entity);

        return $this->render('BaseBundle:Feriado:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Feriado entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BaseBundle:Feriado')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feriado entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BaseBundle:Feriado:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Feriado entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BaseBundle:Feriado')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feriado entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BaseBundle:Feriado:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Feriado entity.
    *
    * @param Feriado $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Feriado $entity)
    {
        $form = $this->createForm(new FeriadoType(), $entity, array(
            'action' => $this->generateUrl('feriado_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Feriado entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BaseBundle:Feriado')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feriado entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('feriado_edit', array('id' => $id)));
        }

        return $this->render('BaseBundle:Feriado:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Feriado entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BaseBundle:Feriado')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Feriado entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('feriado'));
    }
    public function delete_ajaxAction()
    {
        $em = $this->getDoctrine()->getManager();
        $peticion = $this->getRequest();
        $id_feriado = $peticion->request->get('id');
        $descripcion= $peticion->request->get('descripcion');
        $rango= $peticion->request->get('rango');
        $empresa_id= $peticion->request->get('empresa');
        $rango=  explode('-', $rango);
        $inicio=$rango[0];
        $fin =$rango[1];
        $entity= $em->getRepository('BaseBundle:Feriado')->find($id_feriado);
        $empresa=$em->getRepository('BaseBundle:Empresa')->find($empresa_id);
        if (!$entity || !$empresa) {
           return new Response(json_encode('El proceso no se ha realizado debido a que no existe la empresa a la que corresponde el feriado, o este fue eliminado por otro usuario.'));
        }
        else
        {
           
            $empresa->removeFeriado($entity);
            $em->remove($entity);
            $em->persist($empresa);            
            $em->flush();
            return new Response(json_encode('El proceso ha culminado satisfactoriamente.')); 
        }
        
    }

    /**
     * Creates a form to delete a Feriado entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('feriado_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
