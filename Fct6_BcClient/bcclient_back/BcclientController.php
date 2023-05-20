<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bcclient;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Bcclient controller.
 * 
 * @Route("bcclient")
 */
class BcclientController extends Controller
{
    /**
     * save BC with ajax ( not refreshing page ).
     *
     * @Route("/save/Bcclient/FromMission", name="save_bc",options={"expose"=true})
     * @Method({"GET","POST"})
     */
    public function saveBcwithAjaxfunction(Request $request)
    {
        $code = $request->get('code');
        $contrat = $request->get('contrat');
        $application = $request->get('application');
        $avenant = $request->get('avenant');
        $nbjour = $request->get('nbjour');
        $type = $request->get('type');
        $date = $request->get('date');
        $file = $request->get('file');
//        dump($request->request);
        $datego = DateTime::createFromFormat('Y-m-d H:i', $date);
        $datego ? $datego->format('Y-m-d H:i') : true;
        $bcclient = new Bcclient();
        $bcclient->setCode($code)
            ->setApplication($application)
            ->setAvenant($avenant)
            ->setNcontrat($contrat)
            ->setType($type)
            ->setDate($datego)
            ->setNbJrs($nbjour)
            ->setNbJrsR($nbjour);
        $em = $this->getDoctrine()->getManager();
        $em->persist($bcclient);
        $em->flush();
        $data = [

            'id' => $bcclient->getId(),

            'code' => $bcclient->getCode() . ' | nbjrs: ' . $bcclient->getNbJrsR(),

            'file' => $file

        ];
        return new JsonResponse($data);

    }

    /**
     * Lists all bcclient entities.
     *
     * @Route("/", name="bcclient_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT p
    FROM AppBundle:Bcclient p
    WHERE p.bcName IS NULL
    AND p.expired = 0
    '
        );

        $alerts = $query->getResult();
        $count = count($query->getResult());

        $bcclients = $em->getRepository('AppBundle:Bcclient')->findBy([
            'expired' => false

        ]);
        $bcclients_expired = $em->getRepository('AppBundle:Bcclient')->findBy([
            'expired' => true

        ]);
//dump($bcclients,$alerts);
        return $this->render('bcclient/index.html.twig', array(
            'bcclients' => $bcclients,
            'bcclients_expired' => $bcclients_expired,
            'count' => $count,

        ));
    }

    /**
     * Make BC expired or not expired .
     *
     * @Route("/expired", name="bcclient_index_expired")
     * @Method("GET")
     */
    public function expiredBcAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT p
    FROM AppBundle:Bcclient p
    WHERE p.bcName IS NULL
    AND p.expired = 0
    '
        );

        $alerts = $query->getResult();
        $count = count($query->getResult());

        $bcclients = $em->getRepository('AppBundle:Bcclient')->findBy([
            'expired' => true

        ]);
//dump($bcclients,$alerts);
        return $this->render('bcclient/expired.html.twig', array(
            'bcclients' => $bcclients,
            'count' => $count,

        ));
    }

    /**
     * Lists all bcclient entities without documents.
     *
     * @Route("/bcclient/sans_documents", name="bcclient_sans_document")
     * @Method("GET")
     */
    public function sansBcAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT p
    FROM AppBundle:Bcclient p
    WHERE p.bcName IS NULL
    AND p.expired = 0
    '
        );

        $alerts = $query->getResult();
        $count = count($query->getResult());

        $bcclients = $em->getRepository('AppBundle:Bcclient')->findAll();
//dump($bcclients,$alerts);
        return $this->render('bcclient/bcclient_sans_document.html.twig', array(
            'bcclients' => $alerts,
            'count' => $count,

        ));
    }

    /**
     * Lists all bcclient entities with nbjours restant < 20 
     * @Route("/bcclient_alerts", name="bcclient_alert")
     * @Method("GET")
     */
    public function alertAction()
    {
        $em = $this->getDoctrine()->getManager();

        $bcalerts = $em->createQuery(
            'SELECT p
    FROM AppBundle:Bcclient p
    WHERE (p.nbJrsR < :nbJrsR
    OR p.nbJrsR IS NULL)
    AND p.expired = 0'
        )->setParameter('nbJrsR', 30)->getResult();


        return $this->render('bcclient/bcclient_echus.html.twig', array(
            'bcclients' => $bcalerts,


        ));
    }

    /**
     * Creates a new bcclient entity.
     *
     * @Route("/new", name="bcclient_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {

//        dump($this->getUser()->getRoles());
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $bcclient = new Bcclient();
        $form = $this->createForm('AppBundle\Form\BcclientType', $bcclient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($bcclient);
            $em->flush();
            $bcclient1 = $em->getRepository('AppBundle:Bcclient')->find($bcclient->getId());
            $bcclient1->setNbjrsR($bcclient1->getNbJrs());
            $em->persist($bcclient1);
            $em->flush();

            return $this->redirectToRoute('bcclient_show', array('id' => $bcclient->getId()));
        }

        return $this->render('bcclient/new.html.twig', array(
            'bcclient' => $bcclient,
            'form' => $form->createView(),
        ));
    }

    /**
     * logs for bcclients
     * @Route("/log/{id}", name="bcclient_log")
     * @Method("GET")
     */
    public function LogObjectAction(Bcclient $object)
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
     * Finds and displays a bcclient entity.
     *
     * @Route("/{id}", name="bcclient_show")
     * @Method("GET")
     */
    public function showAction(Bcclient $bcclient)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $deleteForm = $this->createDeleteForm($bcclient);

        return $this->render('bcclient/show.html.twig', array(
            'bcclient' => $bcclient,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a bcclient entity.
     *
     * @Route("/{id}/expired", name="bcclient_expired")
     * @Method("GET")
     */
    public function expiredAction(Bcclient $bcclient)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $em = $this->getDoctrine()->getManager();
        if ($bcclient->getExpired()) {

            $bcclient->setExpired(false);
        } else {
            $bcclient->setExpired(true);

        }

        $em->persist($bcclient);
        $em->flush();
        return $this->redirectToRoute('bcclient_index');

    }

    /**
     * Displays a form to edit an existing bcclient entity.
     *
     * @Route("/{id}/edit", name="bcclient_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Bcclient $bcclient)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        //check role
        $operation = $this->get('app.operation');
        return $operation->checkAdmin();
        //end check
        $deleteForm = $this->createDeleteForm($bcclient);
        $editForm = $this->createForm('AppBundle\Form\EditBcclientType', $bcclient);
        $bcclient->setUpdatedAt(new \DateTime());
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bcclient_show', array('id' => $bcclient->getId()));
        }

        return $this->render('bcclient/edit.html.twig', array(
            'bcclient' => $bcclient,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a bcclient entity.
     *
     * @Route("/{id}", name="bcclient_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Bcclient $bcclient)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $form = $this->createDeleteForm($bcclient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {

                $em->remove($bcclient);
                $em->flush();
            } else {

                return $this->redirectToRoute('error_access');
            }

        }

        return $this->redirectToRoute('bcclient_index');
    }

    /**
     * Creates a form to delete a bcclient entity.
     *
     * @param Bcclient $bcclient The bcclient entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Bcclient $bcclient)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bcclient_delete', array('id' => $bcclient->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }


}
