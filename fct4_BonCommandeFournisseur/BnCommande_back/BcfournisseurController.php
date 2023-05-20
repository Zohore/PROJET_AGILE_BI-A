<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bcfournisseur;
use AppBundle\Entity\Facturefournisseur;
use AppBundle\Entity\Production;
use AppBundle\Entity\Projet;
use AppBundle\Entity\Projetconsultant;
use AppBundle\Entity\Virement;
use Dompdf\Dompdf;
use http\Exception\BadConversionException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bcfournisseur controller.
 *
 * @Route("bcfournisseur")
 */
class BcfournisseurController extends Controller
{

    /**
     * Lists all bcfournisseur entities.
     *
     * @Route("/", name="bcfournisseur_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $bcfournisseurs = $em->getRepository('AppBundle:Bcfournisseur')->findAll();
        if (empty($bcfournisseurs)) {
            $bcfournisseurs = [];
        }
        return $this->render('bcfournisseur/index_ajax.html.twig', array(
            'bcfournisseurs' => array_reverse($bcfournisseurs),
        ));
    }

    /**
     * Lists all bcfournisseur entities with ajax.
     *
     * @Route("/getbcfournisseur", name="getbcfournisseur")
     * @Method({"GET", "POST"})
     */
    public function getbcfournisseurAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $bcfournisseurs = $em->getRepository('AppBundle:Bcfournisseur')->findAll();
        if (!empty($bcfournisseurs)) {
            foreach ($bcfournisseurs as $key => $bcfournisseur) {
                $arr[$key][0] = $bcfournisseur->getCode();

                if ($bcfournisseur->getFournisseur() == null)
                    $arr[$key][1] = '--';
                else
                    $arr[$key][1] = $bcfournisseur->getFournisseur()->getNom();

                if ($bcfournisseur->getConsultant() == null)
                    $arr[$key][2] = '--';
                else
                    $arr[$key][2] = $bcfournisseur->getConsultant()->getNom();

                if ($bcfournisseur->getDate() == null)
                    $arr[$key][3] = '';
                else
                    $arr[$key][3] = $bcfournisseur->getDate()->format('Y-m-d');

                if ($bcfournisseur->getMois() == null)
                    $arr[$key][4] = '--';
                else
                    $arr[$key][4] = str_pad($bcfournisseur->getMois(), 2, '0', STR_PAD_LEFT) . '/' . $bcfournisseur->getYear();

                if ($bcfournisseur->getNbjours() == null)
                    $arr[$key][5] = '--';
                else
                    $arr[$key][5] = $bcfournisseur->getNbjours();
                //tjm achat
                if ($bcfournisseur->getTjmAchat() != null) {
                    $arr[$key][6] = $bcfournisseur->getTjmAchat();
                } else {
                    if ($bcfournisseur->getNbjours() != 0) {
                        $tjm = $bcfournisseur->getAchatHT() / $bcfournisseur->getNbjours();
                        $tjm = number_format($tjm, 2, '.', ' ');
                        $arr[$key][6] = $tjm;
                    } else {
                        $arr[$key][6] = '';
                    }
                }

                if ($bcfournisseur->GetAchatHT() == null)
                    $arr[$key][7] = '--';
                else
                    $arr[$key][7] = $bcfournisseur->GetAchatHT();

                if ($bcfournisseur->GetAchatTTC() == null)
                    $arr[$key][8] = '--';
                else
                    $arr[$key][8] = $bcfournisseur->GetAchatTTC();
                if ($bcfournisseur->getFacture())
                    $arr[$key][9] = $bcfournisseur->getFacture()->getNumero();
                else
                    $arr[$key][9] = '--';

                $arr[$key][10] = '<a class="blue" href="' . $request->getBaseUrl() . '/bcfournisseur/' . $bcfournisseur->getId() . '" title="voir">
                 <i class="ace-icon fa fa-search-plus bigger-130"></i>
             </a>
             <a class="orange"
                href="' . $request->getBaseUrl() . '/bcfournisseur/' . $bcfournisseur->getId() . '/edit"
                title="modifier">
                 <i class="ace-icon fa fa-pencil bigger-130"></i>
             </a>
             <a class="orange2"
                href="' . $request->getBaseUrl() . '/bcfournisseur/' . $bcfournisseur->getId() . '/editTjm"
                title="modifier TJM">
                 <i class="ace-icon fa fa-edit bigger-130"></i>
             </a>
             <a class="orange"
                href="' . $request->getBaseUrl() . '/bcfournisseur/' . $bcfournisseur->getId() . '/print' . '"
                title="imprimer">
                 <i class="ace-icon fa fa-print bigger-130"></i>
             </a>
             <a class="dark-10"
                href="' . $request->getBaseUrl() . '/bcfournisseur/log/' . $bcfournisseur->getId() . '' . '"
                title="History">
                 <i class="ace-icon fa fa-history bigger-130"></i>
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
     * Creates a new bcfournisseur entity.
     *
     * @Route("/new", name="bcfournisseur_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $bcfournisseur = new Bcfournisseur();
        $bcfournisseur->setYear(intval((new \DateTime('now'))->format('Y')));
        $bcfournisseur->setMois(intval((new \DateTime('now'))->format('m')));
        $form = $this->createForm('AppBundle\Form\BcfournisseurType', $bcfournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $projet = $bcfournisseur->getProjet();

            if ($projet and $projet->getProjetconsultants()->count() == 1) {

                if ($projet->getProjetconsultants()->count() == 1) {
                    $projet->getFactures()->count() == 1 ? $facture = $projet->getFactures()->last() : $facture = $bcfournisseur->getFacture();
                    $projet_consultant = $projet->getProjetconsultants()->first();
                    $tjm_achat = $projet_consultant->getAchat();
                    $tjm_vente = $projet_consultant->getVente();
                    $totalAchat = $bcfournisseur->getNbjours() * $tjm_achat;
                    $bc_client = $projet_consultant->getBcclient();
                    $bc_client->setNbJrsR($bc_client->getNbJrsR() - $bcfournisseur->getNbjours());
                    //bc_fournisseur
                    $bcfournisseur->setConsultant($projet_consultant->getConsultant());
                    $bcfournisseur->setFacture($facture);
                    $bcfournisseur->setAchatHT(round($totalAchat, 2));
                    $bcfournisseur->setAchatTTC(round(($bcfournisseur->getAchatHT() * 1.2), 2));
                    $bcfournisseur->setTaxe(round(($bcfournisseur->getAchatHT() * 0.2), 2));
                    //set code to bcfournisseur
                    $nb = count($em->getRepository('AppBundle:Bcfournisseur')->findBy(
                        array(
                        'mois' => $facture->getMois(),
                        'year' => $facture->getYear(),
                        )

                    ));
                    $bcfournisseur->setCode('BC-' . substr($facture->getYear(), -2) . '-' . str_pad($facture->getMois(), 2, '0', STR_PAD_LEFT) . '-' . str_pad($nb + 1, 3, '0', STR_PAD_LEFT));
                    //end bc_fournisseur
                    //facture_fournisseur
                    $facturefournisseur = new Facturefournisseur();
                    $facturefournisseur->setBcfournisseur($bcfournisseur);
                    $facturefournisseur->setYear($bcfournisseur->getYear());
                    $facturefournisseur->setDate($bcfournisseur->getDate());
                    $facturefournisseur->setTaxe($bcfournisseur->getTaxe());
                    $facturefournisseur->setAchatTTC($bcfournisseur->getAchatTTC());
                    $facturefournisseur->setAchatHT($bcfournisseur->getAchatHT());
                    $facturefournisseur->setNbjours($bcfournisseur->getNbjours());
                    $facturefournisseur->setMois($bcfournisseur->getMois());
                    $facturefournisseur->setFacture($facture);
                    $facturefournisseur->setProjet($projet);
                    $facturefournisseur->setAchatHT($totalAchat);
                    $facturefournisseur->setAchatTTC($facturefournisseur->getAchatHT() * 1.2);
                    $facturefournisseur->setTaxe($facturefournisseur->getAchatHT() * 0.2);
                    $facturefournisseur->setFournisseur($bcfournisseur->getFournisseur());
                    $facturefournisseur->setConsultant($projet_consultant->getConsultant());

//                    $em->persist($facturefournisseur);
                    $virement = new Virement();
                    $virement->setBcfournisseur($bcfournisseur);
                    $virement->setAchat($bcfournisseur->getAchatTTC());
                    $virement->setDate($bcfournisseur->getDate());
                    $virement->setEtat('en attente');

                    $virement->setConsultant($bcfournisseur->getConsultant());
                    $virement->setFacturefournisseur($facturefournisseur);
//                    $em->persist($virement);
                    $em->flush();
//                    $em->flush();
                    $projet = new Projet();
                    $projet->setClient();

// end facture_fournisseur
                } else {

                    return $this->redirectToRoute('bcfournisseur_index');
                }

            }
            $nb = count($em->getRepository('AppBundle:Bcfournisseur')->findBy(array(

                'mois' => $bcfournisseur->getMois(),
                'year' => $bcfournisseur->getYear(),
            )));
            $bcfournisseur->setCode('BC-' . substr($bcfournisseur->getYear(), -2) . '-' . str_pad($bcfournisseur->getMois(), 2, '0', STR_PAD_LEFT) . '-' . str_pad($nb + 1, 3, '0', STR_PAD_LEFT));


            $em->persist($bcfournisseur);
            $em->flush();
            $facturefournisseur = new Facturefournisseur();
            $facturefournisseur->setBcfournisseur($bcfournisseur);
            $facturefournisseur->setYear($bcfournisseur->getYear());
            $facturefournisseur->setDate($bcfournisseur->getDate());
            $facturefournisseur->setTaxe($bcfournisseur->getTaxe());
            $facturefournisseur->setAchatTTC($bcfournisseur->getAchatTTC());
            $facturefournisseur->setAchatHT($bcfournisseur->getAchatHT());
            $facturefournisseur->setNbjours($bcfournisseur->getNbjours());
            $facturefournisseur->setMois($bcfournisseur->getMois());
            $facturefournisseur->setProjet($projet);
            $facturefournisseur->setAchatHT($bcfournisseur->getAchatHT());
            $facturefournisseur->setAchatTTC($facturefournisseur->getAchatHT() * 1.2);
            $facturefournisseur->setTaxe($facturefournisseur->getAchatHT() * 0.2);
            $facturefournisseur->setFournisseur($bcfournisseur->getFournisseur());
            $facturefournisseur->setConsultant($bcfournisseur->getConsultant());

            $em->persist($facturefournisseur);
            $em->flush();
            $virement = new Virement();
            $virement->setBcfournisseur($bcfournisseur);
            $virement->setAchat($bcfournisseur->getAchatTTC());
            $virement->setDate($bcfournisseur->getDate());
            $virement->setEtat('en attente');

            $virement->setConsultant($bcfournisseur->getConsultant());
            $virement->setFacturefournisseur($facturefournisseur);
            $em->persist($virement);
            $em->flush();

            return $this->redirectToRoute('bcfournisseur_show', array('id' => $bcfournisseur->getId()));
        }

        return $this->render('bcfournisseur/new.html.twig', array(
            'bcfournisseur' => $bcfournisseur,
            'form' => $form->createView(),
        ));
    }

    /**
     * verif bcclient nbj restants pour valider le bcfournisseur.
     * @Route("/get_bc_infos", name="route_bc_getinfo",options={"expose"=true})
     ** @Method({"GET", "POST"})
     */
    public function validateAction(Request $request)

    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $em = $this->getDoctrine()->getManager();

        $nbjour = $request->get('nbjour');

        $id_mission = $request->get('id_mission');
        $id_projet = $request->get('id_project');
//        $id_projet = 5;

//            $mission = null;
        $projet = null;
        $bc_client = null;
        $achatHT = null;
        $achatTTC = null;
        $taxe = null;
        $facture_id = null;
        if ($id_projet) {
            /**
             * @var $projet Projet
             */
            $projet = $em->getRepository('AppBundle:Projet')->find($id_projet);
            $total_consultant = $projet->getProjetconsultants()->count();

            if ($total_consultant == 1) {
                /**
                 * @var $projetconsultant Projetconsultant
                 */
                $projetconsultant = $projet->getProjetconsultants()->first();
                $bc_client = $projet->getBcclients()->first();
                $nbjours_r = $bc_client->getNbJrsR();
                $nbjours_r_maj = $nbjours_r - $nbjour;
                $achatHT = round(($projetconsultant->getAchat() * $nbjour), 2);
                $achatTTC = round(($achatHT * 1.2), 2);
                $taxe = $achatHT * 0.2;
                $facture_id = $projet->getFactures()->last()->getId();
            }
        }


        $response = json_encode([
            'data' => [
                'nbjour' => $nbjour,
                'bcclient' => $bc_client->getCode(),
                'nbjour_r' => $bc_client->getNbJrsR(),
                'nbjour_r_maj' => $nbjours_r_maj,
                'achatHT' => $achatHT,
                'achatTTC' => $achatTTC,
                'taxe' => $taxe,
                'facture_id' => $facture_id

            ]


        ]);

        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));
    }

    /**
     * Logs for bcfournisseur ( tracabilité )
     * @Route("/log/{id}", name="bcfournisseur_log")
     * @Method("GET")
     */
    public function LogObjectAction(Bcfournisseur $object)
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
     * Finds and displays a bcfournisseur entity.
     *
     * @Route("/{id}", name="bcfournisseur_show")
     * @Method("GET")
     */
    public function showAction(Bcfournisseur $bcfournisseur)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $deleteForm = $this->createDeleteForm($bcfournisseur);
        $virementExecutes = $bcfournisseur->getCountVirementsExecutes();
        return $this->render('bcfournisseur/show.html.twig', array(
            'bcfournisseur' => $bcfournisseur,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Print a bcfournisseur entity.
     *
     * @Route("/{id}/print", name="bcfournisseur_print")
     * @Method("GET")
     */
    public function printAction(Bcfournisseur $bcfournisseur)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository('AppBundle:Fiche')->find(1);
        if (!empty($bcfournisseur->getHeures())) {
            $nb = null;
            foreach ($bcfournisseur->getHeures() as $heure)
                $nb += $heure->getNbjour();
        } else {
            $nb = 0;

        }
        function mois_convert($m)
        {

            switch ($m) {
                case 1:
                    return "Janvier";
                    break;
                case 2:
                    return "Février";
                    break;
                case 3:
                    return "Mars";
                    break;
                case 4:
                    return "Avril";
                    break;
                case 5:
                    return "Mai";
                    break;
                case 6:
                    return "Juin";
                    break;
                case 7:
                    return "Juillet";
                    break;
                case 8:
                    return "Aout";
                    break;
                case 9:
                    return "Septembre";
                    break;
                case 10:
                    return "Octobre";
                    break;
                case 11:
                    return "Novembre";
                    break;
                case 12:
                    return "Décembre";
                    break;
                case 0:
                    return "Décembre";
                    break;

            }
        }

        if ($bcfournisseur->getMission()) {

            return $this->render('bcfournisseur/print.html.twig', array(
                'bcfournisseur' => $bcfournisseur,
                'fiche' => $fiche,
                'mois' => mois_convert($bcfournisseur->getMois()),
                'nb' => $nb,
            ));
        } else {
            return $this->render('bcfournisseur/print_projet.html.twig', array(
                'bcfournisseur' => $bcfournisseur,
                'fiche' => $fiche,
                'mois' => mois_convert($bcfournisseur->getMois()),
                'nb' => $nb,
            ));

        }

    }

    /**
     * Displays a form to edit an existing bcfournisseur entity.
     *
     * @Route("/{id}/edit", name="bcfournisseur_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Bcfournisseur $bcfournisseur)
    {
//        dump($this->getUser()->getRoles(),$bcfournisseur->getCountVirementsExecutes()->count());
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        //check role
        $operation = $this->get('app.operation');
//        return $operation->checkAdmin();
        //end check
//        dump($bcfournisseur->getCountVirementsExecutes()->count(),in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true));
        /*if ($bcfournisseur->getCountVirementsExecutes()->count() > 0 or in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {

            return $this->render('default/info.html.twig', [
                'icon' => 'fa fa-warning',
                'code' => '403',
                'msg' => 'Vous pouvez pas Modifier un Bc fournisseur avec des Virements Executés !'
            ]);
        }*/
        $deleteForm = $this->createDeleteForm($bcfournisseur);
        $editForm = $this->createForm('AppBundle\Form\BcfournisseurType', $bcfournisseur);
        $editForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $facturefournisseur = $em->getRepository('AppBundle:Facturefournisseur')->findOneBy([
                'bcfournisseur' => $bcfournisseur
            ]);
            $virement = $em->getRepository('AppBundle:Virement')->findOneBy([

                'bcfournisseur' => $bcfournisseur
            ]);
//            $facturefournisseur = new Facturefournisseur();
            $facturefournisseur->setDate($bcfournisseur->getDate());
            $facturefournisseur->setNbjours($bcfournisseur->getNbjours());
            $facturefournisseur->setFournisseur($bcfournisseur->getFournisseur());
            $facturefournisseur->setAchatHT($bcfournisseur->getAchatHT());
            $facturefournisseur->setTaxe($bcfournisseur->getTaxe());
            $facturefournisseur->setAchatTTC($bcfournisseur->getAchatTTC());
//            $virement = new Virement();

            if ($virement) {

                $virement->setDate($bcfournisseur->getDate());
                $virement->setAchat($bcfournisseur->getAchatTTC());
                $virement->setEtat('en attente');
                $virement->setFacturefournisseur($facturefournisseur);
                $em->persist($virement);
            }


            $this->getDoctrine()->getManager()->persist($facturefournisseur);


            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bcfournisseur_edit', array('id' => $bcfournisseur->getId()));
        }

        return $this->render('bcfournisseur/edit.html.twig', array(
            'bcfournisseur' => $bcfournisseur,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit tjm  an existing bcfournisseur entity.
     *
     * @Route("/{id}/editTjm", name="bcfournisseur_edit_tjm")
     * @Method({"GET", "POST"})
     */
    public function editTjmAction(Request $request, Bcfournisseur $bcfournisseur)
    {
//        dump($this->getUser()->getRoles(),$bcfournisseur->getCountVirementsExecutes()->count());
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        //check role
        $operation = $this->get('app.operation');
//        return $operation->checkAdmin();
        //end check
//        dump($bcfournisseur->getCountVirementsExecutes()->count(),in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true));
        /*if ($bcfournisseur->getCountVirementsExecutes()->count() > 0 or in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {

            return $this->render('default/info.html.twig', [
                'icon' => 'fa fa-warning',
                'code' => '403',
                'msg' => 'Vous pouvez pas Modifier un Bc fournisseur avec des Virements Executés !'
            ]);
        }*/
        $deleteForm = $this->createDeleteForm($bcfournisseur);
        $editForm = $this->createForm('AppBundle\Form\BcfournisseurEditType', $bcfournisseur);
        $editForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('bcfournisseur_edit_tjm', array('id' => $bcfournisseur->getId()));
        }

        return $this->render('bcfournisseur/edit.html.twig', array(
            'bcfournisseur' => $bcfournisseur,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a bcfournisseur entity.
     *
     * @Route("/{id}", name="bcfournisseur_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Bcfournisseur $bcfournisseur)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $form = $this->createDeleteForm($bcfournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {

                $em->remove($bcfournisseur);
                $em->flush();
            } else {

                return $this->redirectToRoute('error_access');
            }

        }

        return $this->redirectToRoute('bcfournisseur_index');
    }

    /**
     * Creates a form to delete a bcfournisseur entity.
     *
     * @param Bcfournisseur $bcfournisseur The bcfournisseur entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Bcfournisseur $bcfournisseur)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bcfournisseur_delete', array('id' => $bcfournisseur->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Deletes a bcfournisseur entity.
     *
     * @Route("/{id}/remove", name="bcfournisseur_remove")
     * @Method("GET")
     */
    public function removeAction(Request $request, Bcfournisseur $bcfournisseur)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }

        $em = $this->getDoctrine()->getManager();
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            $em->remove($bcfournisseur);
            $em->flush();

        } else {

            return $this->redirectToRoute('error_access');
        }


        return $this->redirectToRoute('bcfournisseur_index');
    }
/*
* generation fichier PDF pour Bcfournisseur
**/
    public function generatePdf(Bcfournisseur $bcfournisseur)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository('AppBundle:Fiche')->find(1);
        if (!empty($bcfournisseur->getHeures())) {
            $nb = null;
            foreach ($bcfournisseur->getHeures() as $heure)
                $nb += $heure->getNbjour();
        } else {
            $nb = 0;

        }
        function mois_convert($m)
        {

            switch ($m) {
                case 1:
                    return "Janvier";
                    break;
                case 2:
                    return "Février";
                    break;
                case 3:
                    return "Mars";
                    break;
                case 4:
                    return "Avril";
                    break;
                case 5:
                    return "Mai";
                    break;
                case 6:
                    return "Juin";
                    break;
                case 7:
                    return "Juillet";
                    break;
                case 8:
                    return "Aout";
                    break;
                case 9:
                    return "Septembre";
                    break;
                case 10:
                    return "Octobre";
                    break;
                case 11:
                    return "Novembre";
                    break;
                case 12:
                    return "Décembre";
                    break;
                case 0:
                    return "Décembre";
                    break;

            }
        }

        if ($bcfournisseur->getMission()) {
            $html = $this->render('bcfournisseur/print.html.twig', array(
                'bcfournisseur' => $bcfournisseur,
                'fiche' => $fiche,
                'mois' => mois_convert($bcfournisseur->getMois()),
                'nb' => $nb,
            ));

        } else {
            $html = $this->render('bcfournisseur/print_projet.html.twig', array(
                'bcfournisseur' => $bcfournisseur,
                'fiche' => $fiche,
                'mois' => mois_convert($bcfournisseur->getMois()),
                'nb' => $nb,
            ));

        }

        $dompdf = new Dompdf();
        $path = $this->get('kernel')->getRootDir() . '/../web/bcfournisseurs';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $pathing_pv = $path . '/' . $bcfournisseur->getCode() . '.pdf';

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        //dump($dompdf);
        $dompdf->loadHtml($html->getContent());
        return true;

    }
}
