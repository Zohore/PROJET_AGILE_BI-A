<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Virement;
use AppBundle\Entity\facturehorsprestation;
use AppBundle\Entity\Fournisseur;
use AppBundle\Entity\lettragefournisseur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Handler\DownloadHandler;


/**
 * Facturehorsprestation controller.
 *
 * @Route("facturehorsprestation")
 */
class facturehorsprestationController extends Controller
{
    /**
     * @Route("/FactureHP",name="factureHP_index")
     */
    public function indexAction()
    {
        
        $em = $this->getDoctrine()->getManager();

        $facturehorsprestations = $em->getRepository('AppBundle:facturehorsprestation')->findAll();

        

        return $this->render('facturehorsprestation/index.html.twig', array(
            'facturehorsprestations' => $facturehorsprestations,
            
        ));
    }
    

    

    /**
     * Displays a form to edit an existing facturehorsprrestation entity.
     *
     * @Route("/{id}/edit", name="facturehorsprestation_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, facturehorsprestation $facturehorsprestation)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true) or in_array('ROLE_MANAGER', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');

        }
        //check role
        $operation = $this->get('app.operation');
        return $operation->checkAdmin();
        //end check
        $deleteForm = $this->createDeleteForm($facturehorsprestation);
        $editForm = $this->createForm('AppBundle\Form\facturehorsprestationType', $facturehorsprestation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('facturehorsprestation_edit', array('id' => $facturehorsprestation->getId()));
        }


        return $this->render('facturehorsprestation/edit.html.twig', array(
            'facturehorsprestation' => $facturehorsprestation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/new",name="factureHP_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $date = new \DateTime('now');
        $mois = intval($date->format('m')) - 1;
        $year = intval($date->format('Y'));
        $facturehorsprestation = new facturehorsprestation();
        $lettragefournisseur = new lettragefournisseur();
        $facturehorsprestation->setYear($year);
        $facturehorsprestation->setMois($mois);

        $form = $this->createForm('AppBundle\Form\facturehorsprestationType', $facturehorsprestation);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $facturehorsprestation->setEtat('non payé');
            $lettragefournisseur->setFournisseur($facturehorsprestation->getFournisseur());
            $lettragefournisseur->setNbjours($facturehorsprestation->getNbjours());
            $lettragefournisseur->setEtat($facturehorsprestation->getEtat());
            $lettragefournisseur->setnumerofacture($facturehorsprestation->getnumero());
            $lettragefournisseur->setDesignation($facturehorsprestation->getDesignation());
            $lettragefournisseur->setMois($facturehorsprestation->getMois());
            $lettragefournisseur->setYear($facturehorsprestation->getYear());
            $lettragefournisseur->setAchatHT($facturehorsprestation->getAchatHT());
            $lettragefournisseur->setTaxe($facturehorsprestation->getTaxe());
            $lettragefournisseur->setAchatTTC($facturehorsprestation->getAchatTTC());
            $lettragefournisseur = $em->merge($lettragefournisseur);
            $virement = new Virement();
            $virement->setEtat('en attente');
            $virement->setfacturehorsprestation($facturehorsprestation);
            $virement->setFournisseur($facturehorsprestation->getFournisseur());
            $virement->setAchat($facturehorsprestation->getAchatTTC());
            $virement->setDate($facturehorsprestation->getDate());
            $virement->setYear($facturehorsprestation->getYear());
            $virement->setMois($facturehorsprestation->getMois());
            
            $virement->setDesignation($facturehorsprestation->getDesignation());
            $virement->setlettragefournisseur($lettragefournisseur);
            $lettragefournisseur->setdatefacture($virement->getDate());
            $em->persist($facturehorsprestation);
            $em->flush();
            $em->persist($virement);
            $em->flush();
            $em->persist($lettragefournisseur);
            $em->flush();


            return $this->redirectToRoute('facturehorsprestation_show', array('id' => $facturehorsprestation->getId()));
        }

        return $this->render('facturehorsprestation/new.html.twig', array(
            'facturehorsprestation' => $facturehorsprestation,
            'form' => $form->createView(),
        ));
    }
    /**
     * Creates a new virement entity.
     *
     * @Route("/getfacturehorsprestation", name="getfacturehorsprestation")
     * @Method({"GET", "POST"})
     */
    public function getfacturehorsprestationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $facturehorsprestations = $em->getRepository('AppBundle:facturehorsprestation')->findAll();

        

        if (!empty($facturehorsprestations)) {

            foreach ($facturehorsprestations as $key => $facturehorsprestation) {
                $arr[$key][0] = ' <label class="pos-rel center">
                <input type="checkbox" class="ace" value="' . $facturehorsprestation->getId() . '" data-id="' . $facturehorsprestation->getVirements()->count() . '">
                <span class="lbl"></span>
            </label>';


                $arr[$key][1] = $facturehorsprestation->getNumero();

                if ($facturehorsprestation->getDesignation() == null)
                    $arr[$key][2] = '';
                else
                    $arr[$key][2] = $facturehorsprestation->getDesignation();

                if ($facturehorsprestation->getFournisseur() == null)
                    $arr[$key][3] = '';
                else
                    $arr[$key][3] = $facturehorsprestation->getFournisseur()->getNom();



                if ($facturehorsprestation->getDate() == null)
                    $arr[$key][4] = '';
                else
                    $arr[$key][4] = $facturehorsprestation->getDate()->format('Y-m-d');

                if ($facturehorsprestation->getMois() == null)
                    $arr[$key][5] = '--';
                else
                    $arr[$key][5] = str_pad($facturehorsprestation->getMois(), 2, '0', STR_PAD_LEFT) . '/' . $facturehorsprestation->getYear();

                if ($facturehorsprestation->getNbjours() == null)
                    $arr[$key][6] = '';
                else
                    $arr[$key][6] = $facturehorsprestation->getNbjours();


                if ($facturehorsprestation->getAchatHT() == null)
                    $arr[$key][7] = '';
                else
                    $arr[$key][7] = number_format($facturehorsprestation->getAchatHT(), 2, ',', ' ');

                if ($facturehorsprestation->getAchatTTC() == null)
                    $arr[$key][8] = '--';
                else
                    $arr[$key][8] = number_format($facturehorsprestation->getAchatTTC(), 2, ',', ' ');

                $arr[$key][9] = $facturehorsprestation->getEtat();


                $arr[$key][10] = '<div class="hidden-sm hidden-xs action-buttons">
                <a class="blue"
                   href="' . $request->getBaseUrl() . '/facturehorsprestation/' . $facturehorsprestation->getId() . '"

                   title="voir">
                    <i class="ace-icon fa fa-search-plus bigger-160"></i>
                </a>
                 <a class="dark-10"
                   href="' . $request->getBaseUrl() . '/facturehorsprestation/log/' . $facturehorsprestation->getId() . '"

                   title="Historique">
                    <i class="ace-icon fa fa-history bigger-160"></i>
                </a>
                
                ';

                if (in_array('ROLE_MANAGER', $this->getUser()->getRoles(), true)) {
                    $arr[$key][10] = $arr[$key][10] . ' 
                    <a class="orange"
                    href="' . $request->getBaseUrl() . '/facturehorsprestation/' . $facturehorsprestation->getId() . '/edit"
                    title="modifier">
                     <i class="ace-icon fa fa-pencil bigger-160"></i>
                    </a>';
                }

                

                $arr[$key][10] = $arr[$key][10] . '</div>';

                

                


            }
        } else {
            $arr['draw'] = 1;

            $arr['data'] = [];
        }

        return new Response(json_encode(["data" => $arr]), 200, ['Content-Type' => 'application/json']); //tried this


    }
    /**
     * @Route("/facturehorsprestation/log/{id}", name="facturefournisseur_log")
     * @Method("GET")
     */
    public function LogObjectAction(facturehorsprestation $object)
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        if ($object == null) {
            die('object null ');

        } else {
            $operation = $this->get('app.operation');

            $logs = $operation->getLastVersion($object);
            //dump($logs);

            return $this->render('default/logs.html.twig', array(
                'logs' => $logs,
            ));
        }
    

    }
    /**
     * Finds and displays a facturehorsprestation entity.
     *
     * @Route("/{id}", name="facturehorsprestation_show")
     * @Method("GET")
     */
    public function showAction(facturehorsprestation $facturehorsprestation)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $deleteForm = $this->createDeleteForm($facturehorsprestation);

        return $this->render('facturehorsprestation/show.html.twig', array(
            'facturehorsprestation' => $facturehorsprestation,
            'delete_form' => $deleteForm->createView(),
        ));
}


 /**
 * Deletes a facturehorsprestation entity.
 *
 * @Route("/{id}", name="facturehorsprestation_delete")
 * @Method("DELETE")
 */
public function deleteAction(Request $request, facturehorsprestation $facturehorsprestation)
{
    if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
        return $this->redirectToRoute('error_access');
    }
    $form = $this->createDeleteForm($facturehorsprestation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {

            $em->remove($facturehorsprestation);
            $em->flush();
        } else {

            return $this->redirectToRoute('error_access');
        }

    }

    return $this->redirectToRoute('factureHP_index');
}
 /**
     * Creates a form to delete a facturehorsprestation entity.
     *
     * @param facturehorsprestation $facturehorsprestation The facturehorsprestation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(facturehorsprestation $facturehorsprestation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('facturehorsprestation_delete', array('id' => $facturehorsprestation->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

}