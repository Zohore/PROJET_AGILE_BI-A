<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bcclient;
use AppBundle\Entity\Mission;
use AppBundle\Entity\Projet;
use DateTime;
use FontLib\TrueType\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mission controller.
 *
 * @Route("mission")
 */
class MissionController extends Controller
{

    /**
     *
     *
     * @Route("/missions_archived" , name="missions_archived")
     * @Method("GET")
     */
    public function getMissionArchived()
    {


        $em = $this->getDoctrine()->getManager();
        $missions = $em->getRepository('AppBundle:Mission')->findBy([
            'active' => false

        ]);

//        dump($missions);
        return $this->render('mission/missions_archived.html.twig', [

            'missions' => $missions
        ]);
    }

    /**
     * Lists all mission entities.
     *
     * @Route("/", name="mission_index",options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $missions = $em->createQuery('SELECT m FROM AppBundle:Mission m WHERE m.active = 1 OR m.active IS NULL')->getResult();
        $missions_sans_contratF = $em->getRepository('AppBundle:Mission')->findBy([
            'contratFName' => null,
            'active' => true

        ]);
        $missions_sans_contratC = $em->getRepository('AppBundle:Mission')->findBy([
            'contratCName' => null,
            'active' => true

        ]);

        $nb_sans_contratF = count($missions_sans_contratF);
        $nb_sans_contratC = count($missions_sans_contratC);


        return $this->render('mission/index.html.twig', array(
            'missions' => $missions,
            'nb_F' => $nb_sans_contratF,
            'nb_C' => $nb_sans_contratC,


        ));
    }

    /**
     * Lists all mission entities.
     *
     * @Route("/mission_sans_contract_client", name="mission_sans_contrat_client")
     * @Method("GET")
     */
    public function missionssanscontracclientAction()
    {
        $em = $this->getDoctrine()->getManager();

        $missions = $em->getRepository('AppBundle:Mission')->findAll();

        $missions_sans_contratC = $em->getRepository('AppBundle:Mission')->findBy([
            'contratCName' => null,
            'active' => true

        ]);
        return $this->render('mission/missions_sans_contract_client.html.twig', array(
            'missions' => $missions_sans_contratC,


        ));
    }

    /**
     * Lists all mission entities.
     *
     * @Route("/mission_termine", name="mission_termine")
     * @Method("GET")
     */
    public function missionstermineAction()
    {

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT m
    FROM AppBundle:Mission m
    WHERE m.statut == :statut'
        )->setParameter('statut', 'Terminée');

        $missions = $query->getResult();


        return $this->render('mission/missions_sans_contract_client.html.twig', array(
            'missions' => $missions,


        ));
    }

    /**
     * Lists all mission entities.
     *
     * @Route("/mission_sans_contract_fournisseur", name="mission_sans_contrat_fournisseur")
     * @Method("GET")
     */
    public function missionssanscontratfournisseurAction()
    {
        $em = $this->getDoctrine()->getManager();

        $missions_sans_contratF = $em->getRepository('AppBundle:Mission')->findBy([
            'contratFName' => null,
            'active' => true

        ]);


        return $this->render('mission/missions_sans_contract_fournisseur.html.twig', array(
            'missions' => $missions_sans_contratF,


        ));
    }

    /**
     * Lists all mission entities.
     *
     * @Route("/mission_sans_bc_client", name="mission_sans_bc_client")
     * @Method("GET")
     */
    public function missionssansbcclientAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('
SELECT m FROM AppBundle:Mission m 
JOIN AppBundle:Client c 
WHERE m.client = c.id AND m.bcName IS NULL AND c.contratCadre IS null 
        
        ')->execute();


        $missions_sans_BC = $em->getRepository('AppBundle:Mission')->findBy([
            'bcName' => null

        ]);


        return $this->render('mission/missions_sans_bc_client.html.twig', array(
            'missions' => $query,


        ));
    }


    /**
     * Creates a new mission entity.
     *
     * @Route("/new", name="mission_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $mission = new Mission();
//        $bcclient= new Bcclient();
//        $mission->addBcclient($bcclient);
        $form = $this->createForm('AppBundle\Form\MissionType', $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $bcclients = $request->get('bcclient');


            $em = $this->getDoctrine()->getManager();
            $bcclientss = $em->getRepository('AppBundle:Bcclient')->findBy([

                'id' => $bcclients
            ]);
            if (!empty($bcclientss)) {

                foreach ($bcclientss as $bcclient) {

                    $bcclient->setMission($mission);
                    $bcclient->setClient($mission->getClient())
                        ->setConsultant($mission->getConsultant());


                }
            }

//            dump($bcclients, $bcclientss);

            $em->persist($mission);


//            dump($request->request->get('bcclients'));
//            dump($mission);
//            dump($mission->getBcclients()->count());


            $cc = $request->request->get('switch-field-1');

//die();
//            die();

            $em->flush();
            $miss = $em->getRepository('AppBundle:Mission')->find($mission->getId());

            if ($cc == 'on') {

                $miss->setcontratCName($miss->getClient()->getContratCadre());
                $em->persist($miss);
                $em->flush();
                /*dump($miss);
                die();*/
            } else {
//                $p = 'ok';
//                dump($p);
//                die();

            }
            return $this->redirectToRoute('mission_show', array('id' => $mission->getId()));
        }

        return $this->render('mission/new.html.twig', array(
            'mission' => $mission,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/log/{id}", name="mission_log")
     * @Method("GET")
     */
    public function LogObjectAction(Mission $object)
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
     * Finds and displays a mission entity.
     *
     * @Route("/{id}", name="mission_show")
     * @Method({"GET","POST"})
     * @param Mission $mission
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response|null
     */
    public function showAction(Mission $mission, Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $deleteForm = $this->createDeleteForm($mission);
        $bcclient = new Bcclient();
        $bcclient->setMission($mission)
            ->setConsultant($mission->getConsultant())
            ->setClient($mission->getClient());
        $form = $this->createForm('AppBundle\Form\Bclient1Type', $bcclient);
        $form->handleRequest($request);
        if ($form->isValid() and $form->isSubmitted()) {
            $bcclient->setNbJrsR($bcclient->getNbJrs());
            $em = $this->getDoctrine()->getManager();
            $em->persist($bcclient);
            $em->flush();
            return $this->redirectToRoute('mission_show', array('id' => $mission->getId()));

        }
//        dump($mission->getBcclientsNotExpired()->toArray(), $mission->getBcclients()->toArray());
        return $this->render('mission/show.html.twig', array(
            'mission' => $mission,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a mission entity.
     *
     * @Route("/{id}/end", name="mission_end")
     * @Method("GET")
     */
    public function terminerAction(Mission $mission)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $mission->setStatut('Terminé');
        $mission->setClosedAt(new \DateTime('now'));
        $mission->setUpdatedAt(new \DateTime('now'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($mission);
        $em->flush();

        return $this->redirectToRoute('mission_show', array('id' => $mission->getId()));

    }

    /**
     * Displays a form to edit an existing mission entity.
     *
     * @Route("/{id}/edit", name="mission_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Mission $mission)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        //check role
        $operation = $this->get('app.operation');
//        return $operation->checkAdmin();
        //end check
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($mission);
        $editForm = $this->createForm('AppBundle\Form\MissionType', $mission);
        $editForm->handleRequest($request);
        $bcclients = $em->getRepository('AppBundle:Bcclient')->findAll();
        if ($editForm->isSubmitted()) {
            $bcclientsss = $request->get('bcclient');
            $em = $this->getDoctrine()->getManager();
            $bcclientss = $em->getRepository('AppBundle:Bcclient')->findBy([
                'id' => $bcclientsss
            ]);
            foreach ($mission->getBcclients() as $bc) {
                $bc->setMission(null);
            }
            $mission->clearBcclient();
            if (!empty($bcclientss)) {
                foreach ($bcclientss as $bcclient) {
                    $bcclient->setMission($mission);
                    if ($bcclient->getClient() == null or $bcclient->getClient()->getId() == $mission->getClient()->getId()) {
                        $bcclient->setClient($mission->getClient());
                    }
                    if ($bcclient->getConsultant() == null) {
                        $bcclient->setConsultant($mission->getConsultant());
                    }
                }
            }
            $em->persist($mission);
            $em->flush();
            return $this->redirectToRoute('mission_edit', array('id' => $mission->getId()));
        }

        return $this->render('mission/edit.html.twig', array(
            'mission' => $mission,
            'form' => $editForm->createView(),
            'bcclients' => $bcclients,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing mission entity.
     *
     * @Route("/{id}/upload", name="mission_upload")
     * @Method({"GET", "POST"})
     */
    public function uploadAction(Request $request, Mission $mission)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $deleteForm = $this->createDeleteForm($mission);
        $editForm = $this->createForm('AppBundle\Form\MissionType', $mission);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('mission_show', array('id' => $mission->getId()));
        }

        return $this->render('mission/upload.html.twig', array(
            'mission' => $mission,
            'form' => $editForm->createView(),

        ));
    }

    /**
     * Deletes a mission entity.
     *
     * @Route("/{id}", name="mission_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Mission $mission)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $form = $this->createDeleteForm($mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {

                $em->remove($mission);
                $em->flush();
            } else {

                return $this->redirectToRoute('error_access');
            }

        }

        return $this->redirectToRoute('mission_index');
    }

    /**
     * Deletes a mission entity.
     *
     * @Route("/{id}/remove/mission", name="mission_remove")
     * @Method("GET")
     */
    public function removeAction(Request $request, Mission $mission)
    {


        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($mission);
            $em->flush();
        } else {

            return $this->redirectToRoute('error_access');
        }

        return $this->redirectToRoute('mission_index');
    }

    /**
     * Creates a form to delete a mission entity.
     *
     * @param Mission $mission The mission entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Mission $mission)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mission_delete', array('id' => $mission->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     *
     * @Route("/dep", name="route_to_retrieve_departement",options={"expose"=true})
     ** @Method({"GET", "POST"})
     */
    public function getDepartement(Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $Id = $request->get('idClient');
        $departement_exist = false;
        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository('AppBundle:Client')->find($Id);

        if ($client != null) {
            $departements = $client->getDepartements();

            if ($client->getDepartements() != null and $client->getDepartements()->count() >> 0) {
                $departement_exist = true;
                $count = $client->getDepartements()->count();

            } else {

                $departement_exist = false;
                $count = 0;
            }
        } else {

            $departements = null;
        }


        $response = json_encode(array('exist' => $departement_exist,
            'count' => $count,
            'departements' => $departements

        ));


        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));

    }

    /**
     *
     * @Route("/bc_date_fin", name="route_to_retrieve_date_fin",options={"expose"=true})
     ** @Method({"GET", "POST"})
     */
    public function getDateFin(Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $Id = $request->get('idBClient');
        //  $departement_exist = false;
        $em = $this->getDoctrine()->getManager();
        $bclient = $em->getRepository('AppBundle:Bcclient')->find($Id);

        if ($bclient != null) {

            $nbr_joursR = $bclient->getNbJrsR();

        } else {

            $nbr_joursR = null;
        }


        $response = json_encode(array('nb_jrs' => $nbr_joursR,


        ));


        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));

    }

    /**
     *
     * @Route("{id}/reactivate/missions" ,name="mission_open")
     * @Method("GET")
     */
    public function reactivateMission(Mission $mission)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $em = $this->getDoctrine()->getManager();
        $mission->setActive(!$mission->getActive());
        $mission->setUpdatedAt(new \DateTime('now'));

        $em->persist($mission);
        $em->flush();

        return $this->redirectToRoute('mission_index');
    }

    /**
     *
     * @Route("/desactivate/mission" ,name="mission_desactivate", options={"expose"=true})
     ** @Method({"GET","POST"})
     */
    public function desactivateMission(Request $request)
    {
        $id = $request->get('id');
//        $id = 3;
        $date = $request->get('dateFin');


        $em = $this->getDoctrine()->getManager();
        $mission = $em->getRepository('AppBundle:Mission')->find($id);
        $datego = DateTime::createFromFormat('Y-m-d H:i', $date);
        $datego ? $datego->format('Y-m-d H:i') : false;

        $consultant = $mission->getConsultant();
        $consultant ? $consultant->setActive(false)->setDateFin($datego) : true;
        if ($mission->getActive()) {
            $mission->setDateFin($datego);
            $mission->setActive(!$mission->getActive());

        }
        $em->persist($mission, $consultant);
        $em->flush();
//        dump($id, $date, $mission); die();
        $response = json_encode(array('data' => $date));

        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));

    }

    /**
     *
     * @Route("/getbc/mission" ,name="getBcforMission", options={"expose"=true})
     ** @Method({"GET","POST"})
     */
    public function getBcforMission(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //request variables
        $id = $request->get('id');
        $nbjours = $request->get('nbjours');
        $bcclients_ids = $request->get('bcclients');
        //getMission Object
        /**
         * @var $mission Mission
         */
        $mission = $em->getRepository('AppBundle:Mission')->find($id);
        if ($bcclients_ids) {

            $bcclients = $em->getRepository('AppBundle:Bcclient')->findBy(['id' => $bcclients_ids]);
            $nb = 0;
            foreach ($bcclients as $bcclient) {
                $nb += $bcclient->getNbJrsR();

            }
            if ($nb >= $nbjours) {

                $data['verif'] = true;
                $data['msg'] = 'entrée Valide ';
            } else {

                $data['verif'] = false;
                $data['msg'] = 'Nombre de jours BC insuffisantes  ';
            }


        } else {

            $data['verif'] = false;
            $data['msg'] = 'Aucun Bc client Choisit ! ';
        }


        $data['nbjour'] = $nbjours;
        $data['id'] = $id;

        return new JsonResponse($data);

    }

    /**
     *
     * @Route("/getbc/projet" ,name="getBcforProjet", options={"expose"=true})
     ** @Method({"GET","POST"})
     */
    public function getBcforProjet(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //request variables
        $id = $request->get('id');
        $nbjours = $request->get('nbjours');
        $bcclients_ids = $request->get('bcclients');
        //getMission Object
        /**
         * @var $projet Projet
         */
        $projet = $em->getRepository('AppBundle:Projet')->find($id);
        if ($bcclients_ids) {

            $bcclients = $em->getRepository('AppBundle:Bcclient')->findBy(['id' => $bcclients_ids]);
            $nb = 0;
            foreach ($bcclients as $bcclient) {
                $nb += $bcclient->getNbJrsR();

            }
            if ($nb >= $nbjours) {

                $data['verif'] = true;
                $data['msg'] = 'entrée Valide ';
            } else {

                $data['verif'] = false;
                $data['msg'] = 'Nombre de jours BC insuffisantes  ';
            }


        } else {

            $data['verif'] = false;
            $data['msg'] = 'Aucun Bc client Choisit ! ';
        }


        $data['nbjour'] = $nbjours;
        $data['id'] = $id;

        return new JsonResponse($data);

    }
}
