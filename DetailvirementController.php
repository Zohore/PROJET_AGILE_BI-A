<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Detailvirement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Detailvirement controller.
 *
 * @Route("detailvirement")
 */
class DetailvirementController extends Controller
{

    /**
     * Lists all detailvirement entities.
     *
     * @Route("/", name="detailvirement_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $detailvirements = $em->getRepository('AppBundle:Detailvirement')->findAll();

        return $this->render('detailvirement/index.html.twig', array(
            'detailvirements' => $detailvirements,
        ));
    }

    /**
     * Creates a new bcfournisseur entity.
     *
     * @Route("/getvirementf", name="getvirementf")
     * @Method({"GET", "POST"})
     */
    public function getvirementFAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $detailVirements = $em->getRepository('AppBundle:Detailvirement')->findAll();
        if (!empty($detailVirements)) {
            foreach ($detailVirements as $key => $virement) {
                $arr[$key][0] = $virement->getId();

                if ($virement->getFournisseur() == null)
                    $arr[$key][1] = '--';
                else
                    $arr[$key][1] = $virement->getFournisseur()->getNom();

                if ($virement->getTotal() == null)
                    $arr[$key][2] = '--';
                else
                    $arr[$key][2] = number_format($virement->getTotal(), 2, '.', '');
                if ($virement->getVirementf() == null)
                    $arr[$key][3] = '';
                else
                    $arr[$key][3] = $virement->getVirementf()->getDate()->format('d/m/Y');
                if ($virement->getVirementf() == null)
                    $arr[$key][4] = '';
                else
                    $arr[$key][4] = $virement->getVirementf()->getDate()->format('m/Y');

                if ($virement->getVirementf() == null)
                    $arr[$key][5] = '--';
                else
                    $arr[$key][5] = $virement->getVirementf()->getNumero();


                $arr[$key][6] = '<a class="blue" href="' . $request->getBaseUrl() . '/detailvirement/' . $virement->getId() . '" title="voir">
                 <i class="ace-icon fa fa-search-plus bigger-130"></i>
             </a>
             <a class="orange"
                href="' . $request->getBaseUrl() . '/detailvirement/' . $virement->getId() . '/edit"
                title="modifier">
                 <i class="ace-icon fa fa-pencil bigger-130"></i>
             </a>
        ';

            }
        } else {
            $arr['draw'] = 1;

            $arr['data'] = [];
        }

        return new Response(json_encode(["data" => $arr]), 200, ['Content-Type' => 'application/json']); //tried this
    }

    /**
     * Creates a new detailvirement entity.
     *
     * @Route("/new", name="detailvirement_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $detailvirement = new Detailvirement();
        $form = $this->createForm('AppBundle\Form\DetailvirementType', $detailvirement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($detailvirement);
            $em->flush();

            return $this->redirectToRoute('detailvirement_show', array('id' => $detailvirement->getId()));
        }

        return $this->render('detailvirement/new.html.twig', array(
            'detailvirement' => $detailvirement,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/log/{id}", name="detailvirement_log")
     * @Method("GET")
     */
    public function LogObjectAction(Detailvirement $object)
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
     * Finds and displays a detailvirement entity.
     *
     * @Route("/{id}", name="detailvirement_show")
     * @Method("GET")
     */
    public function showAction(Detailvirement $detailvirement)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $deleteForm = $this->createDeleteForm($detailvirement);

        return $this->render('detailvirement/show.html.twig', array(
            'detailvirement' => $detailvirement,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing detailvirement entity.
     *
     * @Route("/{id}/edit", name="detailvirement_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Detailvirement $detailvirement)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        //check role
        $operation = $this->get('app.operation');
        return $operation->checkAdmin();
        //end check
        $deleteForm = $this->createDeleteForm($detailvirement);
        $editForm = $this->createForm('AppBundle\Form\DetailvirementType', $detailvirement);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('detailvirement_edit', array('id' => $detailvirement->getId()));
        }

        return $this->render('detailvirement/edit.html.twig', array(
            'detailvirement' => $detailvirement,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a detailvirement entity.
     *
     * @Route("/{id}", name="detailvirement_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Detailvirement $detailvirement)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $form = $this->createDeleteForm($detailvirement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
                $em->remove($detailvirement);
                $em->flush();

            } else {

                return $this->redirectToRoute('error_access');
            }

        }

        return $this->redirectToRoute('detailvirement_index');
    }

    /**
     * Creates a form to delete a detailvirement entity.
     *
     * @param Detailvirement $detailvirement The detailvirement entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Detailvirement $detailvirement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('detailvirement_delete', array('id' => $detailvirement->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
