<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Consultant;
use AppBundle\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Consultant controller.
 *
 * @Route("consultant")
 */
class ConsultantController extends Controller
{
    private $userManager;

    /**
     * ConsultantController constructor.
     * @param $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;

    }

    /**
     * Lists all consultant entities.
     *
     * @Route("/create/users", name="consultant_users")
     * @Method("GET")
     */
    public function createUsersAction()
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $em = $this->getDoctrine()->getManager();
        $consultants = $em->getRepository('AppBundle:Consultant')->findAll();

        foreach ($consultants as $consultant) {
            $email = $consultant->getEmail();
            if ($email) {
// creation utilisateur pour consultant :

//                $user = new User();
                $user = $this->userManager->createUser();
                $user->setEnabled(true);
                $user->setConsultant($consultant);
                $user->setUsername($email);
                $user->setEmail($email);
                $user->setUsernameCanonical($email);
                $user->setPlainPassword('123456');
                $user->addRole('ROLE_CONS');

                $this->userManager->updateUser($user);

            }

        }
        die();
//        dump($consultants[0]->calculePoids());
        return $this->render('consultant/index.html.twig', array());
    }

    /**
     * Lists all consultant entities.
     *
     * @Route("/", name="consultant_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $consultants = $em->getRepository('AppBundle:Consultant')->findAll();
//        dump($consultants[0]->calculePoids());
        return $this->render('consultant/index.html.twig', array(
            'consultants' => $consultants,
        ));
    }

    /**
     * Lists all consultant entities.
     *
     * @Route("/update/poids", name="consultant_update_poids")
     * @Method("GET")
     */
    public function updateAction()
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $em = $this->getDoctrine()->getManager();

        $consultants = $em->getRepository('AppBundle:Consultant')->findAll();
        foreach ($consultants as $consultant) {
            $consultant->setPoids($consultant->calculePoids());
            $em->persist($consultant);
            $em->flush();
        }
//        dump($consultants[0]->calculePoids());
        return $this->redirectToRoute('consultant_index');
    }

    /**
     * Creates a new facturefournisseur entity.
     *
     * @Route("/getData", name="consultant_data")
     * @Method({"GET", "POST"})
     */
    public function getDataAction(Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $em = $this->getDoctrine()->getManager();

        $facturefournisseurs = $em->getRepository('AppBundle:Consultant')->findAll();

        foreach ($facturefournisseurs as $key => $facturefournisseur) {

//            $array[$key][]= $facturefournisseur->getId();
            $array[$key][] = $facturefournisseur->getNom();
            $array[$key][] = $facturefournisseur->getEmail();
            $array[$key][] = $facturefournisseur->getAdresse();
            $array[$key][] = $facturefournisseur->getTel();
            $array[$key][] = $facturefournisseur->getType();
            $array[$key][] = $facturefournisseur->getRib();
            if ($facturefournisseur->getEcheance()) {
                $array[$key][] = $facturefournisseur->getEcheance()->getNom();

            } else {
                $array[$key][] = null;
            }

            if ($facturefournisseur->getAutoVirement()) {
                $array[$key][] = '--';

            } else {
                if ($facturefournisseur->getAutoVirement() == 1) {
                    $array[$key][] = '<div class="text-center"><label class="text-center"><input id="gritter-light" value="' . $facturefournisseur->getAutoVirement() . '" type="checkbox" checked="checked" data-id="' . $facturefournisseur->getId() . '" class="ace ace-switch ace-switch-5 switch"> <span class="lbl middle"></span></label></div>';

                } else {
                    $array[$key][] = '<div class="text-center"><label class="text-center"><input id="gritter-light" value="' . $facturefournisseur->getAutoVirement() . '" type="checkbox" data-id="' . $facturefournisseur->getId() . '" class="ace ace-switch ace-switch-5 switch"> <span class="lbl middle"></span></label></div>';

                }

            }

            $array[$key][] = $facturefournisseur->getAnciennte();
            $array[$key][] = $facturefournisseur->getPoids();
            $array[$key][] = $facturefournisseur->getPoids();


        }

        $response = json_encode(array('data' => $array));

        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));
    }

    /**
     * Creates a new consultant entity.
     *
     * @Route("/new", name="consultant_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $consultant = new Consultant();
        $form = $this->createForm('AppBundle\Form\ConsultantType', $consultant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $email = $consultant->getEmail();
            if ($email) {
// creation utilisateur pour consultant :

//                $user = new User();
                $user = $this->userManager->createUser();
                $user->setEnabled(true);
                $user->setConsultant($consultant);
                $user->setUsername($email);
                $user->setEmail($email);
                $user->setUsernameCanonical($email);
                $user->setPlainPassword('123456');
                $user->addRole('ROLE_CONS');

                $this->userManager->updateUser($user);

            }

            $consultant->setPoids($consultant->calculePoids());
            $em->persist($consultant);
            $em->flush();

            return $this->redirectToRoute('consultant_show', array('id' => $consultant->getId()));
        }

        return $this->render('consultant/new.html.twig', array(
            'consultant' => $consultant,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/log/{id}", name="consultant_log")
     * @Method("GET")
     */
    public function LogObjectAction(Consultant $object)
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
     * Finds and displays a consultant entity.
     *
     * @Route("/{id}", name="consultant_show")
     * @Method("GET")
     */
    public function showAction(Consultant $consultant)
    {
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $deleteForm = $this->createDeleteForm($consultant);

        return $this->render('consultant/show.html.twig', array(
            'consultant' => $consultant,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a consultant entity.
     *
     * @Route("/{id}/activate", name="consultant_activate")
     * @Method("GET")
     */
    public function activateAction(Consultant $consultant)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $em = $this->getDoctrine()->getManager();
        $consultant->setActive(!$consultant->getActive());
        $em->persist($consultant);
        $em->flush();

        return $this->redirectToRoute('consultant_index');
    }

    /**
     * Displays a form to edit an existing consultant entity.
     *
     * @Route("/{id}/edit", name="consultant_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Consultant $consultant)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $deleteForm = $this->createDeleteForm($consultant);
        $editForm = $this->createForm('AppBundle\Form\ConsultantType', $consultant);
        $editForm->handleRequest($request);
        $consultant->setPoids($consultant->calculePoids());

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('consultant_index');
        }

        return $this->render('consultant/edit.html.twig', array(
            'consultant' => $consultant,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a consultant entity.
     *
     * @Route("/{id}", name="consultant_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Consultant $consultant)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $form = $this->createDeleteForm($consultant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {

                $em->remove($consultant);

                $em->flush();

            } else {

                return $this->redirectToRoute('error_access');
            }

        }

        return $this->redirectToRoute('consultant_index');
    }

    /**
     * Deletes a consultant entity.
     *
     * @Route("/{id}/setAutoVirement", name="make_autovirement_true_or_false",options={"expose"=true}))
     * @Method({"GET", "POST"})
     */
    public function autovirementAction(Request $request, Consultant $consultant)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $consultant->setAutoVirement(!$consultant->getAutoVirement());
        $em = $this->getDoctrine()->getManager();
        $em->persist($consultant);
        $em->flush();


        return new JsonResponse('ok');
    }

    /**
     * Creates a form to delete a consultant entity.
     *
     * @param Consultant $consultant The consultant entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Consultant $consultant)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('consultant_delete', array('id' => $consultant->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function getPaiementForCons(Consultant $consultant)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }

        $em = $this->getDoctrine()->getManager();
        $data = $em->createQuery('      
         SELECT b.code as numero,fac.mois as mois , f.datetimesheet as date_ts , e.max as date_paiement,fac.etat as statut FROM AppBundle:Facturefournisseur fac 
        JOIN fac.consultant c
        JOIN fac.facture f  
        JOIN fac.bcfournisseur b 
        JOIN AppBundle:Echeance e
        WHERE fac.consultant = c.id
        AND fac.facture = f.id
        AND e.id = c.echeance
        AND fac.bcfournisseur = b.id
        AND c.id = :cons
        ORDER BY fac.id DESC 
        
        
        
        ')->setParameters(['cons' => $consultant])->getResult();

        return $data;
    }

    /**
     *
     * @Route("/desactivate/consultant" ,name="consultant_desactivate", options={"expose"=true})
     ** @Method({"GET","POST"})
     */
    public function desactivateConsultant(Request $request)
    {
        $id = $request->get('id');
//        $id = 3;
        $date = $request->get('dateFin');


        $em = $this->getDoctrine()->getManager();
        $consultant = $em->getRepository('AppBundle:Consultant')->find($id);
        $datego = DateTime::createFromFormat('Y-m-d H:i', $date);
        $datego ? $datego->format('Y-m-d H:i') : false;


        if ($consultant->getActive()) {
            $consultant->setDateFin($datego);
            $consultant->setActive(!$consultant->getActive());

        }
        $em->persist($consultant);
        $em->flush();
//        dump($id, $date, $mission); die();
        $response = json_encode(array('data' => $date));

        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));

    }
}
