<?php

namespace aplicacion\EmisionesBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use aplicacion\BaseBundle\Libs\Pdf\HTML2PDF;
use aplicacion\EmisionesBundle\Entity\Emision;
use aplicacion\EmisionesBundle\Form\EmisionType;

/**
 * Emision controller.
 *
 */
class EmisionController extends Controller
{

    public function renderMailAction($entity)//se pasa para que tambien pueda generar el pdf en la vist del mail
    {
        
        $em = $this->getDoctrine()->getManager();
        $orden=$em->getRepository('EmisionesBundle:Orden')->find($entity);
        return $this->render('EmisionesBundle:Ordenes:mail.html.twig', array(
            'entity'=>$orden
        ));
    }
    public function sendMailAction()
    {
        
        $peticion = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $orden=$em->getRepository('EmisionesBundle:Orden')->find($peticion->request->get('entity'));
        $to = $peticion->request->get('email_to');
        $cc = $peticion->request->get('email_cc');
        $bcc= $peticion->request->get('email_bcc');
        $subject=$peticion->request->get('email_subject');
        $from=$peticion->request->get('sender');
        $cuerpo=$peticion->request->get('email_message');
        $adjunto='';
        $allowed = array('png', 'jpg', 'gif','zip','doc','docx','pdf','jpeg','ppt'
            ,'rar','txt','xlsx','xls','csv');
        $path = $this->container->getParameter('aplicacion.directorio.documentos.adjuntos');
        if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0)
            {

                $extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
               // print_r($extension);exit;
                if(!in_array(strtolower($extension), $allowed)){
                       $this->get('session')->getFlashBag()->add('error', 'El fichero que usted adjunta no es permitido!');
                       return $this->render('EmisionesBundle:Ordenes:mail.html.twig', array('entity'=>$orden));
                }

                if(move_uploaded_file($_FILES['upl']['tmp_name'], $path.$_FILES['upl']['name'])){
                        $adjunto=$path.$_FILES['upl']['name'];
                }
            }
          
        $message = \Swift_Message::newInstance()
        ->setFrom(array($from=>$from))   
        ->addReplyTo($from)
        ->setSubject($subject)
        ->setTo($to)
        ->setBody($cuerpo);
        
        if(isset($cc)&& $cc!='')
        {
           $message->setCc($cc);
        }
        if(isset($bcc)&& $bcc!='')
        {
           $message->setBcc($bcc);
        }
        if(isset($adjunto) && $adjunto !='')
        {
            $message->attach(\Swift_Attachment::fromPath($adjunto));
        }
        
       if( $this->get('mailer')->send($message))
       {
           //print_r('entro');exit;
           $this->get('session')->getFlashBag()->add('success', 'Su mensaje se ha enviado correctamente!');
           if(file_exists($adjunto))
           {
               unlink($adjunto);
           }
       }
       else
       {
           $this->get('session')->getFlashBag()->add('error', 'Ha ocurrido un error al enviar su mensaje!');
       }
       
        
        
   
    return $this->render('EmisionesBundle:Ordenes:mail.html.twig', array('entity'=>$orden));
       
    }
    
    public function makePdfAction()
    {
       
        $peticion = $this->getRequest();
        $style = $peticion->request->get('style');
        $content = $peticion->request->get('content');
        $title=$peticion->request->get('title');
        $nombre=$peticion->request->get('nombre').'.pdf';
        if(!file_exists($this->container->getParameter('aplicacion.directorio.documentos.pdf').$nombre))
        {
            
            //Genero el documento
            $content='
                    <!doctype html>
                    <html>
                    <head>        
                        '.$style.'
                    </head>
                    <body>'.
                        '<page_header>
                    <table class="page_header">
                        <tr>
                            <td style="width: 40%; text-align: left;">
                               <br>
                                    <img src="./bundles/base/img/logos/logomym.png" alt="Logo HTML2PDF" style="width:40%">
                                    <br>
                            </td>
                            <td style="width: 30%;">
                                <b style="font-size: 10px;float: right;">'.$title.'</b>                    
                            </td>
                             <td style="width: 30%;text-align: right;">
                                 <b style="font-size: 10px;text-align: right;">KOBRA Queue System v0.1</b>
                                 <hr style="width: 10%;"/><b style="font-size: 10px;text-align: right;">'.  date('d-M-Y H:i:s').'</b>

                            </td>


                        </tr>
                    </table>

                </page_header>
                <page_footer>
                    <table class="page_footer">
                        <tr>
                            <td style="width: 33%; text-align: left;">
                                <address style="margin-top: -5px;">
                                    <p style="font-style: oblique;font-size: 10px;text-decoration: underline;"><b>Direcci&oacute;n:</b>
                                    <span>Edificio Pinzón Piso 2,
                                        Av. Yánez Pinzón N26-243 y Av. Francisco de Orellana</span>
                                    <p ><b>Tel&eacute;fono:</b> 
                                    <span> +593 22983840</span>
                                    </p> 
                                    </p> 
                                </address>
                            </td>
                            <td style="width: 34%; text-align: center">
                                <span style="font-size: 10px;"> P&aacute;gina [[page_cu]] de [[page_nb]] </span>
                            </td>
                            <td style="width: 33%; text-align: right">
                                <a style="text-decoration-line:none;font-size: 10px;text-decoration: none; " href="http://www.mymtravel.com"> &copy; M&M Travel Group </a>
                            </td>
                        </tr>
                    </table>
                </page_footer>'.$content.
                    '</body>
                    </html>';

                     $path = $this->container->getParameter('aplicacion.directorio.documentos.pdf');
                   ob_start();        
                    echo '<page>'.$content.'</page>';
                    $content = ob_get_clean(); 
                    //Orientación / formato del pdf y el idioma.
                    $pdf = new HTML2PDF('P','A4','es'/*, array(10, 10, 10, 10) /*márgenes*/); //los márgenes también pueden definirse en <page> ver documentación
                    $pdf->WriteHTML($content);
                    $pdf->Output($path.$nombre, 'F'); //guardar archivo ( ¡ojo! si ya existe lo sobreescribe )
        }
        
        return new Response($this->get('request')->getBasePath().'/uploads/pdf/'.$nombre);
    }
    function crearNombre($length)
    {
        if( ! isset($length) or ! is_numeric($length) ) $length=6;

        $str  = "0123456789abcdefghijklmnopqrstuvwxyz";
        $path = '';

        for($i=1 ; $i<$length ; $i++)
          $path .= $str{rand(0,strlen($str)-1)};

        return $path.'_'.date("d-m-Y_H-i-s").'.pdf';    
    }
    /**
     * Lists all Emision entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EmisionesBundle:Emision')->findAll();

        return $this->render('EmisionesBundle:Emision:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Emision entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Emision();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('emision_show', array('id' => $entity->getId())));
        }

        return $this->render('EmisionesBundle:Emision:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Emision entity.
     *
     * @param Emision $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Emision $entity)
    {
        $form = $this->createForm(new EmisionType(), $entity, array(
            'action' => $this->generateUrl('emision_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Emision entity.
     *
     */
    public function newAction()
    {
        $entity = new Emision();
        $form   = $this->createCreateForm($entity);

        return $this->render('EmisionesBundle:Emision:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Emision entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Emision')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Emision entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Emision:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Emision entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Emision')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Emision entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Emision:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Emision entity.
    *
    * @param Emision $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Emision $entity)
    {
        $form = $this->createForm(new EmisionType(), $entity, array(
            'action' => $this->generateUrl('emision_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

       // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Emision entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Emision')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Emision entity.');
        }
        
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) { 
            //print_r('valido');exit;
            $em->flush();
           
            return $this->redirect($this->generateUrl('emision_edit', array('id' => $id)));
        }

        return $this->render('EmisionesBundle:Emision:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Emision entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EmisionesBundle:Emision')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Emision entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('emision'));
    }

    /**
     * Creates a form to delete a Emision entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('emision_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
