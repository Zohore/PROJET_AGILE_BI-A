<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Client controller.
 *
 * @Route("client")
 */
class ClientController extends Controller
{
    /**
     * Lists all client entities.
     *
     * @Route("/", name="client_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clients = $em->getRepository('AppBundle:Client')->findAll();
        return $this->render('client/index.html.twig', array(
            'clients' => $clients,
        ));
    }

    /**
     * Creates a new client entity.
     *
     * @Route("/new", name="client_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $client = new Client();
        $form = $this->createForm('AppBundle\Form\ClientType', $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('client_show', array('id' => $client->getId()));
        }

        return $this->render('client/new.html.twig', array(
            'client' => $client,
            'form' => $form->createView(),
        ));
    }
    /**
     * Logs for client entity.
     * @Route("/log/{id}", name="client_log")
     * @Method("GET")
     */
    public function LogObjectAction(Client $object)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
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
     * Finds and displays a client entity.
     *
     * @Route("/{id}", name="client_show")
     * @Method("GET")
     */
    public function showAction(Client $client)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $deleteForm = $this->createDeleteForm($client);
        $missions =$client->getMission();

        return $this->render('client/show.html.twig', array(
            'client' => $client,
            'missions'=>$missions,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing client entity.
     *
     * @Route("/{id}/edit", name="client_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Client $client)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }

        $deleteForm = $this->createDeleteForm($client);
        $editForm = $this->createForm('AppBundle\Form\ClientType', $client);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('client_edit', array('id' => $client->getId()));
        }

        return $this->render('client/edit.html.twig', array(
            'client' => $client,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a client entity.
     *
     * @Route("/{id}", name="client_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Client $client)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $form = $this->createDeleteForm($client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {

                $em->remove($client);
                $em->flush();

            } else {

                return $this->redirectToRoute('error_access');
            }

          
        }

        return $this->redirectToRoute('client_index');
    }

    /**
     * Creates a form to delete a client entity.
     *
     * @param Client $client The client entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Client $client)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('client_delete', array('id' => $client->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
