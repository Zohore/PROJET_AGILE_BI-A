<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bcfournisseur;
use AppBundle\Entity\Facture;
use AppBundle\Entity\Facturefournisseur;
use AppBundle\Entity\FactureHsup;
use AppBundle\Entity\Mission;
use AppBundle\Entity\Production;
use AppBundle\Entity\Virement;
use AppBundle\Form\FactureHsupType;
use AppBundle\Service\SendMailService;
use DateTime;
use Doctrine\ORM\Query;
use Dompdf\Dompdf;
use Dompdf\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Facture controller.
 *
 * @Route("facture")
 */
class FactureController extends Controller
{
    /**
     * Lists all facture entities.
     *
     * @Route("/test", name="facture_generate_files",options={"expose"=true})
     * @Method("GET")
     */
    public function generateAllFactureFilesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $factures = $em->getRepository('AppBundle:Facture')->findAll();
        set_time_limit(5000); //

        foreach ($factures as $facture) {
            try {
                if ($facture->getMission()) $this->generateFacturePdf2($facture);

            } catch (Exception $exception) {
                echo $exception->getMessage();
            }
        }

        die('ok');

    }

    /**
     * Lists all facture entities.
     *
     * @Route("/", name="facture_index",options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

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

        $factures = $em->getRepository('AppBundle:Facture')->findAll();
        $missions = $em->getRepository('AppBundle:Mission')->findBy([
            'timesheet' => true
        ]);
        $factures_sans_timesheet = $em->getRepository('AppBundle:Facture')->findBy([
            'documentName' => null,
            'mission' => $missions
        ]);
//        dump($factures);
        $consultants = $em->getRepository('AppBundle:Consultant')->findBy([
            'active' => true
        ]);
        $missions = $em->getRepository('AppBundle:Mission')->findBy([
            'active' => true,
            'consultant' => $consultants
        ]);

        $date = new \DateTime('now');
        $mois = intval($date->format('m')) - 1;
        if ($mois == 0) {
            $mois = 12;
        }
        $day = intval($date->format('d'));
        if ($day >= 1) {

            foreach ($missions as $mission) {

                $facture = $em->getRepository('AppBundle:Facture')->findBy(

                    [
                        'mission' => $mission,
                        'mois' => $mois
                    ]


                );
                if ($facture != null) {

                    $missions_factured[] = $mission;
                    $diff = array_diff_assoc($missions, $missions_factured);
                    $nb_non_factured_missions = count($diff);
                } else {

                    $missions_factured[] = null;
                    $diff = array_diff_assoc($missions, $missions_factured);
                    $nb_non_factured_missions = count($diff);
                }

            }


            //  dump($diff, $missions, $missions_factured, count($diff));

            // $nb_non_factured_missions = count($diff);
        } else {

            $nb_non_factured_missions = null;
            //$diff = array_diff_assoc($missions, $missions_factured);
        }
        $opratationService = $this->get('app.operation');
        $mois_converted = $opratationService->mois_convert($mois);
        $bcclients = $em->createQuery('SELECT b FROM AppBundle:Bcclient b WHERE (b.nbJrsR < 30 OR b.nbJrsR IS NULL) AND b.expired = 0 ')
            ->getResult();
        return $this->render('facture/index_ajax.html.twig', array(
            'factures' => $factures,
            'nb_non_factured_missions' => $nb_non_factured_missions,
            'facture_sans_timesheet' => $factures_sans_timesheet,
            'mois' => $mois_converted,
            'bcclients' => $bcclients
        ,
        ));
    }

    /**
     * Creates a new virement entity.
     *
     * @Route("/getfacture", name="getfacture")
     * @Method({"GET", "POST"})
     */
    public function getfactureAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $factures = $em->getRepository('AppBundle:Facture')->findAll();

        // $bcfournisseurs = array_reverse($bcfournisseurs);


        if (!empty($factures)) {

            foreach ($factures as $key => $facture) {
                $arr[$key][0] = $facture->getNumero();

                if ($facture->getProjet() == null)
                    $arr[$key][1] = '';
                else
                    $arr[$key][1] = $facture->getProjet()->getNom();


                if ($facture->getClient() == null)
                    $arr[$key][2] = '';
                else
                    $arr[$key][2] = $facture->getClient()->getNom();


                if ($facture->getConsultant() == null)
                    $arr[$key][3] = '--';
                else
                    $arr[$key][3] = $facture->getConsultant()->getNom();


                if ($facture->getMois() == null)
                    $arr[$key][4] = '--';
                else
                    $arr[$key][4] = str_pad($facture->getMois(), 2, '0', STR_PAD_LEFT) . '-' . $facture->getYear();

                if ($facture->getNbjour() == null)
                    $arr[$key][5] = '';
                else
                    $arr[$key][5] = $facture->getNbjour();

                if ($facture->getDate() == null)
                    $arr[$key][6] = '';
                else
                    $arr[$key][6] = $facture->getDate()->format('d/m/Y');

                if ($facture->getTotalHT() == null)
                    $arr[$key][7] = '--';
                else
                    $arr[$key][7] = $facture->getTotalHT();

                if ($facture->getTotalTTC() == null)
                    $arr[$key][8] = '--';
                else
                    $arr[$key][8] = $facture->getTotalTTC();

                if ($facture->getEtat() == null) {
                    $arr[$key][9] = '--';
                } else {
                    if ($facture->getEtat() == 'non payé') {
                        $arr[$key][9] = '<span class="label label-warning arrowed arrowed-right">' . $facture->getEtat() . '</span>';
                    } else {
                        $arr[$key][9] = '<span class="label label-success arrowed arrowed-right">' . $facture->getEtat() . '</span>';
                    }
                }
                if ($facture->getDatePaiement() == null)
                    $arr[$key][10] = '';
                else
                    $arr[$key][10] = $facture->getDatePaiement()->format('d/m/Y');

                if ($facture->getMission() == null)
                    $arr[$key][11] = 'DH';
                else
                    $arr[$key][11] = $facture->getMission()->getDevise();
                if ($facture->getClient()->getEcheance() == null) {
                    $arr[$key][12] = '';

                } else {
                    if ($facture->getDate()) {

                        $datetime = $facture->getDate();
                        $echeance = '+' . $facture->getClient()->getEcheance() . " days";

                        $datetime->modify($echeance);

                        $arr[$key][12] = $datetime->format('d/m/Y');
                    } else {
                        $arr[$key][12] = null;

                    }


                }
                if ($facture->getTotalDH() == null) {
                    $arr[$key][13] = null;
                } else {
                    $arr[$key][13] = $facture->getTotalDH();
                }

                $arr[$key][14] = '<div class="hidden-sm hidden-xs action-buttons">';

                if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
                    if ($facture->getProjet() != null) {
                        $arr[$key][14] = $arr[$key][14] . ' <a class="pink" href="' . $request->getBaseUrl() . '/lignefacture/' . $facture->getId() . '/details"
                            title="Lignes">
                            <i class="ace-icon fa fa-list-alt bigger-130"></i>
                        </a>';
                    }
                }

                $arr[$key][14] = $arr[$key][14] . '<a class="blue"
                    href="' . $request->getBaseUrl() . '/facture/' . $facture->getId() . '" 
                    title="voir">
                    <i class="ace-icon fa fa-search-plus bigger-130"></i>
                </a>
                <a class="brown"
                    href="' . $request->getBaseUrl() . '/facture/' . $facture->getId() . '/edit_date"
                    title="Modifier les dates">
                    <i class="ace-icon fa fa-calendar bigger-130"></i>
                </a>
                <a class="blue"
                    href="' . $request->getBaseUrl() . '/facture/' . $facture->getId() . '/facture/avoir"
                    title="Ajouter un avoir">
                    <i class="ace-icon fa fa-sticky-note-o bigger-130"></i>
                </a>
                <a class="dark-10"
                    href="' . $request->getBaseUrl() . '/facture/log/' . $facture->getId() . '"
                    title="Historique">
                    <i class="ace-icon fa fa-history bigger-130"></i>
                </a>
                ';

                if ($facture->getDocumentName() == null) {
                    $arr[$key][14] = $arr[$key][14] . '<a class="red"
                        href="' . $request->getBaseUrl() . '/facture/' . $facture->getId() . '/addsheet"
                        title="Uploader le timesheet">
                        <i class="ace-icon fa fa-cloud-upload bigger-130"></i>
                    </a>';
                } else {
                    $arr[$key][14] = $arr[$key][14] . ' <a title="Télécharger le Timesheet" class="purple"
                        href="' . $request->getBaseUrl() . '/../uploads/documents/' . $facture->getDocumentName() . '">
                        <i class="ace-icon fa fa-download bigger-130"></i>
                    </a>';
                }

                if ($facture->getMission() != null && $facture->getNbjour() > 0) {
                    $arr[$key][14] = $arr[$key][14] . '<a class="grey"
                        href="' . $request->getBaseUrl() . '/facture/' . $facture->getId() . '/print"
                        title="Imprimer">
                        <i class="ace-icon fa fa-print bigger-130"></i>
                    </a>';
                } else {

                }

                if ($facture->getProjet() != null) {
                    $arr[$key][14] = $arr[$key][14] . '<a class="grey"
                        href="' . $request->getBaseUrl() . '/facture/' . $facture->getId() . '/print_projet"
                        title="Imprimer">
                        <i class="ace-icon fa fa-print bigger-130"></i>
                    </a>';
                }


                if ($facture->getMission() != null && $facture->getMission()->getDevise() != 'DH' && $facture->getTotalDH() == null) {
                    $arr[$key][14] = $arr[$key][14] . '
                    <a href="#my-moda' . $facture->getId() . '" role="button"
                    class=""
                    data-toggle="modal">
                        <i class="ace-icon fa fa-exchange bigger-100 purple"></i>
                    </a>
                    <div id="my-moda' . $facture->getId() . '" class="modal modal-test fade" style="display: none" tabindex="-1">
                       <form action="' . $request->getBaseUrl() . '/facture/convert/devise2' . '" method="post">
                        <div class="modal-dialog" style="width: 1000px">
                            <div class="modal-content" style="width: 850px">
                                <div class="modal-header">
                                    <button type="button" class="close"
                                            data-dismiss="modal"
                                            aria-hidden="true">&times;
                                    </button>
                                    <h3 class="smaller lighter blue no-margin">
                                    ' . $facture->getTotalTTC() . ' ' . $facture->getMission()->getDevise() . '
                                        -> DH </h3>
                                </div>

                                <div class="modal-body">


  <input type="hidden" class="form-control" value="' . $facture->getId() . '" name="id"/>
                                    <input type="text" placeholder="Total en DH" class="form-control" name="montant"/> 
                                        <input type="date" placeholder="Date Fin" class="form-control date-timepicker1" name="dateFin"/>

                                  

                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-sm btn-success ok pull-right" type="submit" >
                                        <i class="ace-icon fa fa-check"></i>
                                        Enregistrer
                                    </button>
                                    <button class="btn btn-sm btn-danger pull-left"
                                            data-dismiss="modal">
                                        <i class="ace-icon fa fa-times"></i>
                                        Close
                                    </button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                        </form>
                    </div>
                    ';
                }

                if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true) || in_array('ROLE_MANAGER', $this->getUser()->getRoles(), true)) {
                    $arr[$key][14] = $arr[$key][14] . ' <a class="red"
                        href="' . $request->getBaseUrl() . '/facture/' . $facture->getId() . '/delete"
                        title="delete">
                        <i class="ace-icon fa fa-trash bigger-130"></i>
                    </a>';

                    if ($facture->getProjet() == null && $facture->getEtat() != 'payé') {
                        if ($facture->getFacturehsups()->count() != 0) {
                            $arr[$key][14] = $arr[$key][14] . '<a class="orange"
                                href="' . $request->getBaseUrl() . '/facture/' . $facture->getId() . '/edit_hs"
                                title="modifier">
                                <i class="ace-icon fa fa-pencil bigger-130"></i>
                            </a>';
                            $arr[$key][14] = $arr[$key][14] . ' <a class="grey"
                        href="' . $request->getBaseUrl() . '/facture/' . $facture->getId() . '/print_hs"
                        title="Imprimer">
                        <i class="ace-icon fa fa-print bigger-130"></i>
                    </a>';
                        } else {
                            $arr[$key][14] = $arr[$key][14] . ' <a class="orange"
                                href="' . $request->getBaseUrl() . '/facture/' . $facture->getId() . '/edit"
                                title="modifier">
                                    <i class="ace-icon fa fa-pencil bigger-130"></i>
                                </a>
                            ';
                        }
                    } else {
                        if ($facture->getClient()->getNom() == 'MEDI TELECOM' && $facture->getEtat() != 'payé') {
                            $arr[$key][14] = $arr[$key][14] . '<a class="orange"
                                href="' . $request->getBaseUrl() . '/facture/' . $facture->getId() . '/edit_projet"
                                title="modifier">
                                <i class="ace-icon fa fa-pencil bigger-130"></i>
                            </a>
                            ';
                        }

                        if ($facture->getClient()->getNom() == 'PCS invest' && $facture->getEtat() != 'payé') {
                            $arr[$key][14] = $arr[$key][14] . '<a class="orange"
                                href="' . $request->getBaseUrl() . '/facture/' . $facture->getId() . '/edit_projet"
                                title="modifier">
                                <i class="ace-icon fa fa-pencil bigger-130"></i>
                            </a>
                            ';
                        }
                    }
                }

                $arr[$key][14] = $arr[$key][14] . '<div class="hidden-md hidden-lg">
                <div class="inline pos-rel">
                    <button class="btn btn-minier btn-yellow dropdown-toggle"
                            data-toggle="dropdown"
                            data-position="auto">
                        <i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
                        <li>
                            <a href="#" class="tooltip-info"
                               data-rel="tooltip"
                               title=""
                               data-original-title="View">
                                <span class="blue">
                                    <i class="ace-icon fa fa-search-plus bigger-120"></i>
                                </span>
                            </a>
                        </li>

                        <li>
                            <a href="#" class="tooltip-success"
                               data-rel="tooltip" title=""
                               data-original-title="Edit">
                                    <span class="green">
                                        <i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
                                    </span>
                            </a>
                        </li>';
                if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
                    $arr[$key][14] = $arr[$key][14] . '<li>
                                <a href="' . $request->getBaseUrl() . '/facture/' . $facture->getId() . '/delete"
                                   class="tooltip-error" data-rel="tooltip"
                                   title=""
                                   data-original-title="Delete">
                                    <span class="red">
                                        <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                    </span>
                                </a>
                            </li>';
                }


                $arr[$key][14] = $arr[$key][14] . '</ul>
                </div>
            </div></div>';


            }
        } else {
            $arr['draw'] = 1;

            $arr['data'] = [];
        }

        return new Response(json_encode(["data" => $arr]), 200, ['Content-Type' => 'application/json']); //tried this


    }

    /**
     * Lists all facture entities.
     *
     * @Route("/sans_timesheet", name="facture_sans_timesheet",options={"expose"=true})
     * @Method("GET")
     */
    public function sanstimesheetAction()
    {
        $em = $this->getDoctrine()->getManager();
        $missions = $em->getRepository('AppBundle:Mission')->findBy([
            'timesheet' => true
        ]);
        $factures = $em->getRepository('AppBundle:Facture')->findBy([
            'documentName' => null,
            'mission' => $missions
        ]);
        return $this->render('facture/sans_timesheet.twig', array(
            'factures' => $factures,


        ));
    }

    /**
     * Lists all facture entities.
     *
     * @Route("/mission_sans_facture", name="mission_sans_facture")
     * @Method("GET")
     */
    public function missionsSansFactureAction()
    {
        $em = $this->getDoctrine()->getManager();
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

        $factures = $em->getRepository('AppBundle:Facture')->findAll();
        $consultants = $em->getRepository('AppBundle:Consultant')->findBy([
            'active' => true
        ]);
        $missions = $em->getRepository('AppBundle:Mission')->findBy([
            'active' => true,
            'consultant' => $consultants
        ]);
        //dump($factures);
        $date = new \DateTime('now');
        $mois = intval($date->format('m')) - 1;
        if ($mois == 0) {
            $mois = 12;
        }
        $day = intval($date->format('d'));
        if ($day >= 10) {

            foreach ($missions as $mission) {

                $facture = $em->getRepository('AppBundle:Facture')->findBy(

                    [
                        'mission' => $mission,
                        'mois' => $mois
                    ]


                );
                if ($facture != null) {

                    $missions_factured[] = $mission;
                    $diff = array_diff_assoc($missions, $missions_factured);
                    $nb_non_factured_missions = count($diff);
                } else {

                    $missions_factured[] = null;
                    $diff = array_diff_assoc($missions, $missions_factured);
                    $nb_non_factured_missions = count($diff);
                }

            }


            // //dump($diff, $missions, $missions_factured, count($diff));

            // $nb_non_factured_missions = count($diff);
        } else {

            $nb_non_factured_missions = null;
            //$diff = array_diff_assoc($missions, $missions_factured);
        }


        return $this->render('facture/missionsansfacture.html.twig', array(
            'factures' => $factures,
            'mois' => mois_convert($mois),
            'missions' => $diff
        ));
    }

    /**
     * Creates a new facture entity.
     *
     * @Route("/new", name="facture_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $facture = new Facture();
        $facture->setAddedby($this->getUser());


        $bcfournisseur = new Bcfournisseur();
        $facturefournisseur = new Facturefournisseur();

        $date = new \DateTime('now');

        $em = $this->getDoctrine()->getManager();

        /* $nb = count($em->getRepository('AppBundle:Facture')->findBy(array(

             'mois'=>$mois,
             'year'=>$year
         )));*/


        $facturefournisseur->setEtat('non payé');


        $facture->setEtat('non payé');

        $form = $this->createForm('AppBundle\Form\FactureType', $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $facture->setEtat('non payé');
            $em->persist($facture);
            $em->flush();
//            dump($facture);
//            die();

            $mois = intval($facture->getDate()->format('m'));
            $year = intval($facture->getDate()->format('y'));
            $facture = $em->getRepository('AppBundle:Facture')->find($facture->getId());
//            $nb = count($em->getRepository('AppBundle:Facture')->findBy(array(
//
//                'mois' => $mois,
//                'year' => $year,
//            )));
            $nbb = $em->createQuery('
            
            SELECT COUNT(f) as total FROM AppBundle:Facture f 
            WHERE YEAR(f.date) = :annee 
            AND (f.devise = 0 OR f.devise IS NULL)
            AND (f.avoir = 0 OR f.avoir IS NULL)
            ')
                ->setParameters([

//                        'moi' => $mois,
                    'annee' => $year,

                ])->getResult();


            $nb = (int)$nbb[0]['total'];
            $mission = $facture->getMission();
            //dump($mission, $nb);
            $nb_facture = $nb + 1;
            $facture->setNumero('H3K-' . substr($year, -2) . '-' . str_pad($mois, 2, '0', STR_PAD_LEFT) . '-' . str_pad($nb, 3, '0', STR_PAD_LEFT));
            $facture->setBcclient($mission->getBcclient());
            $facture->setBcclient($mission->getBcclient());

            $facture->setClient($mission->getClient());
            $facture->setMission($mission);

            $prixAchatHT = $mission->getPrixAchat();
            $prixVenteHT = $mission->getPrixVente();
            $bcfournisseur->setMission($mission);
            $facturefournisseur->setMission($mission);
            $facture->setConsultant($mission->getConsultant());
            if ($mission->getDevise() == 'DH') {

                if ($mission->getType() == 'journaliere') {

                    $totalHT = $prixVenteHT * $facture->getNbjour();
                    $achatHT = $prixAchatHT * $facture->getNbjour();
                    $TVA = ($prixVenteHT * $facture->getNbjour()) * 0.2;
                    $TVA_Achat = $achatHT * 0.2;
                    $bcfournisseur->setAchatHT($achatHT);
                    $facturefournisseur->setAchatHT($achatHT);
                    if ($mission->getConsultant() != null and $mission->getConsultant()->getType() == 'entrepreneur') {
                        $bcfournisseur->setTaxe(0);
                        $facturefournisseur->setTaxe(0);
                        $bcfournisseur->setAchatTTC($achatHT);
                        $bcfournisseur->setVenteHT($totalHT);
                        $facturefournisseur->setAchatTTC($achatHT);

                    } else {

                        $bcfournisseur->setTaxe($TVA_Achat);
                        $facturefournisseur->setTaxe($TVA_Achat);
                        $bcfournisseur->setAchatTTC($achatHT + $TVA_Achat);
                        $bcfournisseur->setVenteHT($totalHT);
                        $facturefournisseur->setAchatTTC($achatHT + $TVA_Achat);
                    }

                    $facture->setTotalHT($totalHT);

                    $facture->setTaxe($TVA);
                    $facture->setTotalTTC($TVA + $totalHT);
                    $bcclient = $facture->getBcclient();
                    //dump($bcclient);
                    if ($bcclient != null) {

                        $bcclient->setNbJrsR($bcclient->getNbJrsR() - $facture->getNbjour());
                        $em->persist($bcclient);
                        $em->flush();
                    }
                } else {
                    $totalHT = $prixVenteHT;
                    $achatHT = $prixAchatHT;
                    $TVA_Achat = $achatHT * 0.2;
                    $bcfournisseur->setAchatHT($achatHT);
                    $facturefournisseur->setAchatHT($achatHT);
                    $bcfournisseur->setTaxe($TVA_Achat);
                    $facturefournisseur->setTaxe($TVA_Achat);
                    $bcfournisseur->setAchatTTC($achatHT + $TVA_Achat);
                    $bcfournisseur->setVenteHT($totalHT);
                    $facturefournisseur->setAchatTTC($achatHT + $TVA_Achat);
                    $TVA = ($prixVenteHT) * 0.2;
                    $facture->setTaxe($TVA);

                    $facture->setTotalHT($totalHT);
                    $facture->setTotalTTC($TVA + $totalHT);

                }


            } else {
                if ($mission->getType() == 'journaliere') {

                    $totalHT = $prixVenteHT * $facture->getNbjour();
                    $achatHT = $prixAchatHT * $facture->getNbjour();
                    $TVA = 0;
                    $TVA_Achat = 0;
                    $bcfournisseur->setAchatHT($achatHT);
                    $facturefournisseur->setAchatHT($achatHT);
                    $bcfournisseur->setTaxe($TVA_Achat);
                    $facturefournisseur->setTaxe($TVA_Achat);
                    $bcfournisseur->setAchatTTC($achatHT + $TVA_Achat);
                    $bcfournisseur->setVenteHT($totalHT);
                    $facturefournisseur->setAchatTTC($achatHT + $TVA_Achat);
                    $facture->setTotalHT($totalHT);
                    $facture->setTaxe($TVA);
                    $facture->setTotalTTC($TVA + $totalHT);
                    $bcclient = $facture->getBcclient();
                    //dump($bcclient);
                    if ($bcclient != null) {

                        $bcclient->setNbJrsR($bcclient->getNbJrsR() - $facture->getNbjour());
                        $em->persist($bcclient);
                        $em->flush();
                    }
                } else {
                    $totalHT = $prixVenteHT;
                    $achatHT = $prixAchatHT;
                    $TVA_Achat = 0;
                    $bcfournisseur->setAchatHT($achatHT);
                    $facturefournisseur->setAchatHT($achatHT);
                    $bcfournisseur->setTaxe($TVA_Achat);
                    $facturefournisseur->setTaxe($TVA_Achat);
                    $bcfournisseur->setAchatTTC($achatHT + $TVA_Achat);
                    $bcfournisseur->setVenteHT($totalHT);
                    $facturefournisseur->setAchatTTC($achatHT + $TVA_Achat);
                    $facturefournisseur->setBcfournisseur($bcfournisseur);
                    $TVA = 0;
                    $facture->setTaxe($TVA);

                    $facture->setTotalHT($totalHT);
                    $facture->setTotalTTC($TVA + $totalHT);

                }


            }
            $facture->setEtat('non payé');
            $em->persist($facture);
            $em->flush();


            $facture = $em->getRepository('AppBundle:Facture')->find($facture->getId());


            $bcfournisseur->setFournisseur($facture->getMission()->getFournisseur());
            $bcfournisseur->setNbjours($facture->getNbjour());
            $bcfournisseur->setMois($facture->getMois());
            $bcfournisseur->setYear($facture->getYear());
            $bcfournisseur->setDate(new \DateTime('now'));
            $facturefournisseur->setFournisseur($facture->getMission()->getFournisseur());
            $facturefournisseur->setNbjours($facture->getNbjour());
            $facturefournisseur->setMois($facture->getMois());
            $facturefournisseur->setYear($facture->getYear());
            $facturefournisseur->setDate(new \DateTime('now'));
            $facturefournisseur->setBcfournisseur($bcfournisseur);

            $em->persist($bcfournisseur);
            $em->flush();
            try {
                $this->generatePdf($bcfournisseur);

            } catch (Exception $exception) {
            }
            $em->persist($facturefournisseur);
            $em->flush();
            $heures = $form->get('facturehsups')->getData();
            if (!empty($heures)) {

                foreach ($heures as $heure) {
                    $nb_jour_sup = $heure->getNbheure() / 10;
                    $heure->setNbjour($nb_jour_sup);
                    $heure->setFacture($facture);
                    $heuresup = $heure->getHeuresup();
                    $heure->setTotalHT($nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $facture->getMission()->getVente());
                    $heure->setTotalTTC($heure->getTotalHT() * 1.2);

                    $em->persist($heure);
                    $em->flush();
                }
            }
//            dump($facture);
//            die();
            return $this->redirectToRoute('facture_show', array('id' => $facture->getId()));
        }

        return $this->render('facture/new.html.twig', array(
            'facture' => $facture,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing facture entity.
     *
     * @Route("/{id}/facture", name="facture_mission")
     * @Method({"GET", "POST"})
     */
    public function newfromMissionAction(Request $request, Mission $mission)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $operation = $this->get('app.operation');
        $facture = new Facture();
        $bcclientsNotExpired = $mission->getBcclientsNotExpired();
        if ($mission->getDevise() != 'DH') {

            $facture->setDevise(true);
//            dump($facture);die();
        }
        $facture->setAddedby($this->getUser());
        $bcfournisseur = new Bcfournisseur();
        $bcfournisseur->setFacture($facture);
        $facturefournisseur = new Facturefournisseur();
        $facturefournisseur->setFacture($facture);
        $date = new \DateTime('now');
        $mois = intval($date->format('m')) - 1;
        $year = intval($date->format('y')) - 1;
        $facturefournisseur->setEtat('non payé');
        $facture->setEtat('non payé');
//        $facture->setBcclient($mission->getBcclient());
        $facture->setClient($mission->getClient());
        $facture->setTjm($mission->getPrixVente());
        $facture->setMission($mission);
        /*dump($mission->getClient(), $facture);
        die();*/
        $prixAchatHT = $mission->getPrixAchat();
        $prixVenteHT = $mission->getPrixVente();
        $facture->setConsultant($mission->getConsultant());
        $form = $this->createForm('AppBundle\Form\FactureType', $facture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $bcclients_ids = $request->get('bcclient');
            $bcclients = $em->getRepository('AppBundle:Bcclient')->findBy([
                'id' => $bcclients_ids
            ], ['id' => 'ASC']);
            $mois = intval($facture->getDate()->format('m'));
            $year = intval($facture->getDate()->format('Y'));
            $yearmini = intval($facture->getDate()->format('y'));

            $nb = count($em->getRepository('AppBundle:Bcfournisseur')->findBy(array(

                'mois' => $facture->getMois(),
                'year' => $facture->getYear(),
            )));


            $mission = $facture->getMission();
            $bcfournisseur->setMission($mission);
            $bcfournisseur->setTjmAchat($mission->getPrixAchat());
            $facturefournisseur->setMission($mission);
            $bcfournisseur->setConsultant($mission->getConsultant());
            $facturefournisseur->setConsultant($mission->getConsultant());
            //dump($mission, $nb);
            $nb_facture = $nb + 1;
            $bcfournisseur->setCode('BC-' . substr($facture->getYear(), -2) . '-' . str_pad($facture->getMois(), 2, '0', STR_PAD_LEFT) . '-' . str_pad($nb + 1, 3, '0', STR_PAD_LEFT));
            if ($mission->getDevise() == 'DH') {
                $nbb = $em->createQuery('
            
            SELECT COUNT(f) as total FROM AppBundle:Facture f 
            WHERE MONTH(f.date) = :moi AND YEAR(f.date) = :annee
            AND (f.devise = 0 OR f.devise IS NULL)
            AND (f.avoir = 0 OR f.avoir IS NULL)
            
            ')
                    ->setParameters([

                        'moi' => $mois,
                        'annee' => $year,
                    ])->getResult();


                $count_factures = (int)$nbb[0]['total'] + 1;
                $facture->setNumero('H3K-' . substr($year, -2) . '-' . str_pad($mois, 2, '0', STR_PAD_LEFT) . '-' . str_pad($count_factures, 3, '0', STR_PAD_LEFT));
                if (!$this->checkNumFacture($facture->getNumero())) {

                    return $this->render('default/info.html.twig', [
                        'icon' => 'fa fa-info',
                        'code' => '403',
                        'msg' => ' le Numero de facture :' . $facture->getNumero() . '  existe déja  Merci de verifier et réessayer ! '
                    ]);
                }

                /*  dump($facture);
                  die();*/


                if ($mission->getType() == 'journaliere') {
                    $totalHT = $prixVenteHT * $facture->getNbjour();


                    $achatHT = $prixAchatHT * $facture->getNbjour();
//                    dump($prixVenteHT,$facture->getNbjour()) ; die();

                    $TVA = ($prixVenteHT * $facture->getNbjour()) * 0.2;
                    $TVA_Achat = $achatHT * 0.2;
                    if ($mission->getConsultant() != null and $mission->getConsultant()->getType() == 'entrepreneur') {

                        $bcfournisseur->setAchatHT($achatHT);
                        $bcfournisseur->setTaxe(0);
                        $bcfournisseur->setAchatTTC($achatHT);
                        $bcfournisseur->setVenteHT($totalHT);
                        $facturefournisseur->setAchatHT($achatHT);
                        $facturefournisseur->setTaxe(0);
                        $facturefournisseur->setAchatTTC($achatHT);
                    } else {
                        $bcfournisseur->setAchatHT($achatHT);
                        $bcfournisseur->setTaxe($TVA_Achat);
                        $bcfournisseur->setAchatTTC($achatHT + $TVA_Achat);
                        $bcfournisseur->setVenteHT($totalHT);
                        $facturefournisseur->setAchatHT($achatHT);
                        $facturefournisseur->setTaxe($TVA_Achat);
                        $facturefournisseur->setAchatTTC($achatHT + $TVA_Achat);

                    }

                    $facture->setTotalHT($totalHT);
                    $facture->setClient($mission->getClient());
                    $facture->setTaxe($TVA);
                    $facture->setTotalTTC($TVA + $totalHT);

                    $facture->setClient($mission->getClient());
                    //a suivre Bc client
                    $nbjours = $facture->getNbjour();
                    if ($bcclients != null) {

                        if (count($bcclients) == 1) {
                            $bcclient = $bcclients[0];
                            $bcclient->setNbJrsR($bcclient->getNbJrsR() - $nbjours);
                            $bcclient->addFacture($facture);
                            $em->persist($bcclient);
                            $em->flush();

                        }
                        if (count($bcclients) == 2) {
                            $bcclient0 = $bcclients[0];
                            $bcclient1 = $bcclients[1];
                            $bcclient1->setNbJrsR(($bcclient1->getNbJrsR() - $nbjours + $bcclient0->getNbJrsR()));
                            $bcclient0->setNbJrsR(0);
                            $bcclient0->addFacture($facture);
                            $bcclient1->addFacture($facture);

                            $em->persist($bcclient0);
                            $em->persist($bcclient1);
                            $em->flush();

                        }

                    }
                } else {

                    $totalHT = $prixVenteHT;
                    $achatHT = $prixAchatHT;
                    $TVA_Achat = $achatHT * 0.2;
                    if ($mission->getConsultant() != null and $mission->getConsultant()->getType() == 'entrepreneur') {
                        $bcfournisseur->setAchatHT($achatHT);
                        $bcfournisseur->setTaxe(0);
                        $bcfournisseur->setAchatTTC($achatHT);
                        $bcfournisseur->setVenteHT($totalHT);
                        $facturefournisseur->setAchatHT($achatHT);
                        $facturefournisseur->setTaxe(0);
                        $facturefournisseur->setAchatTTC($achatHT);

                    } else {
                        $bcfournisseur->setAchatHT($achatHT);
                        $bcfournisseur->setTaxe($TVA_Achat);
                        $bcfournisseur->setAchatTTC($achatHT + $TVA_Achat);
                        $bcfournisseur->setVenteHT($totalHT);
                        $facturefournisseur->setAchatHT($achatHT);
                        $facturefournisseur->setTaxe($TVA_Achat);
                        $facturefournisseur->setAchatTTC($achatHT + $TVA_Achat);

                    }

                    $TVA = ($prixVenteHT) * 0.2;
                    $facture->setTaxe($TVA);

                    $facture->setTotalHT($totalHT);
                    $facture->setTotalTTC($TVA + $totalHT);

                }


            } else {
                $nbb = $em->createQuery('
            
            SELECT COUNT(f) as total FROM AppBundle:Facture f 
            WHERE YEAR(f.date) = :annee AND f.devise = 1
            ')->setParameters([
//                        'moi' => $mois,
                    'annee' => $year,

                ])->getResult();


                $count_factures = (int)$nbb[0]['total'] + 1;
                $facture->setNumero('ET-' . substr($year, -2) . '-' . str_pad($count_factures, 3, '0', STR_PAD_LEFT));

                if ($mission->getType() == 'journaliere') {
                    $totalHT = $prixVenteHT * $facture->getNbjour();
                    $achatHT = $prixAchatHT * $facture->getNbjour();
                    $TVA = 0;
                    $TVA_Achat = 0;
                    $bcfournisseur->setAchatHT($achatHT);
                    $bcfournisseur->setTaxe($TVA_Achat);
                    $bcfournisseur->setAchatTTC($achatHT + $TVA_Achat);
                    $bcfournisseur->setVenteHT($totalHT);
                    $facturefournisseur->setAchatHT($achatHT);
                    $facturefournisseur->setTaxe($TVA_Achat);
                    $facturefournisseur->setAchatTTC($achatHT + $TVA_Achat);
                    $facture->setTotalHT($totalHT);
                    $facture->setTaxe($TVA);
                    $facture->setClient($mission->getClient());
                    $facture->setTotalTTC($TVA + $totalHT);
//                    $bcclient = $facture->getBcclient();
                    //dump($bcclient);
                    $nbjours = $facture->getNbjour();
                    if ($bcclients != null) {

                        if (count($bcclients) == 1) {
                            $bcclient = $bcclients[0];
                            $bcclient->setNbJrsR($bcclient->getNbJrsR() - $nbjours);
                            $bcclient->addFacture($facture);
                            $em->persist($bcclient);
                            $em->flush();

                        }
                        if (count($bcclients) == 2) {
                            $bcclient0 = $bcclients[0];
                            $bcclient1 = $bcclients[1];
                            $bcclient1->setNbJrsR(($bcclient1->getNbJrsR() - $nbjours + $bcclient0->getNbJrsR()));
                            $bcclient0->setNbJrsR(0);
                            $bcclient0->addFacture($facture);
                            $bcclient1->addFacture($facture);

                            $em->persist($bcclient0);
                            $em->persist($bcclient1);
                            $em->flush();

                        }


                    }
                } else {
                    $totalHT = $prixVenteHT;
                    $achatHT = $prixAchatHT;
                    $TVA_Achat = 0;
                    $bcfournisseur->setAchatHT($achatHT);
                    $bcfournisseur->setTaxe($TVA_Achat);
                    $bcfournisseur->setAchatTTC($achatHT + $TVA_Achat);
                    $bcfournisseur->setVenteHT($totalHT);
                    $facturefournisseur->setAchatHT($achatHT);
                    $facturefournisseur->setTaxe($TVA_Achat);
                    $facturefournisseur->setAchatTTC($achatHT + $TVA_Achat);
                    $TVA = 0;
                    $facture->setTaxe($TVA);

                    $facture->setTotalHT($totalHT);
                    $facture->setClient($mission->getClient());
                    $facture->setTotalTTC($TVA + $totalHT);
//                    dump($facture);die();
                }
            }
            $facture->setClient($mission->getClient());
            $facture->setEtat('non payé');

            $em->persist($facture);
            $em->flush();

            if ($mission->getDevise() == 'DH') {


                $totalHT_hs = null;
                $totalTTC_hs = null;
                $totalHT_hs_fournisseur = null;
                $totalTTC_hs_fournisseur = null;
                $nb_total_jrs = null;

                $heures = $form->get('facturehsups')->getData();
                if (!empty($heures)) {
                    $totalHT_hs = null;
                    $totalTTC_hs = null;
                    $totalHT_hs_fournisseur = null;
                    $totalTTC_hs_fournisseur = null;
                    $nb_total_jrs = null;
                    foreach ($heures as $heure) {

                        $nb_jour_sup = $heure->getNbheure() / 10;
                        $heure->setNbjour($nb_jour_sup);
                        $heure->setFacture($facture);
                        $heure->setBcfournisseur(null);
//                    $facture->addFacturehsup($heure);
                        $heuresup = $heure->getHeuresup();
                        $heure->setTotalHT($nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixVente());
                        $heure->setTotalTTC(($nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixVente()) * 1.2);
                        $totalHT_hs += ($nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixVente());
                        $totalTTC_hs += $nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixVente() * 1.2;
                        $totalHT_hs_fournisseur += ($nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixAchat());
                        $totalTTC_hs_fournisseur += $nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixAchat() * 1.2;
                        $nb_total_jrs += $heure->getNbheure() / 10;

                        $heure_bc = new FactureHsup();
                        $heure_bc->setBcfournisseur($bcfournisseur);
                        $heure_bc->setFacturefournisseur($facturefournisseur);
//                    $heure_bc->setFacture($facture);
                        $heure_bc->setNbjour($nb_jour_sup);
                        $heure_bc->setHeuresup($heure->getHeuresup());
                        $heure_bc->setTotalHT($nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixAchat());
                        $heure_bc->setTotalTTC(($nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixAchat()) * 1.2);
                        $heure_bc->setNbheure($heure->getNbheure());
                        $bcfournisseur->addHeure($heure_bc);
                        $facturefournisseur->addHeure($heure_bc);
                        $em->persist($heure);
                        $em->flush();

                    }
                } else {
                    $totalHT_hs = null;
                    $totalTTC_hs = null;
                    $totalHT_hs_fournisseur = null;
                    $totalTTC_hs_fournisseur = null;
                    $nb_total_jrs = null;

                }
                if ($nb_total_jrs) {
                    $facture->setNbjourT($nbjours + $nb_total_jrs);
                    $em->persist($facture);
                    $em->flush();
                }

//                die();
                $facture = $em->getRepository('AppBundle:Facture')->find($facture->getId());
                $facture->setTotalHT($facture->getTotalHT() + $totalHT_hs);
                $facture->setTaxe($facture->getTotalHT() * 0.2);
                $facture->setTotalTTC(($facture->getTotalHT()) * 1.2);

                $bcfournisseur->setFournisseur($facture->getMission()->getFournisseur());
//            $bcfournisseur->setNbjours($facture->getNbjour());
                $bcfournisseur->setMois($facture->getMois());
                $bcfournisseur->setYear($facture->getYear());
                $bcfournisseur->setDate(new \DateTime('now'));
                $facturefournisseur->setFournisseur($facture->getMission()->getFournisseur());
                $facturefournisseur->setNbjours($facture->getNbjour() + $nb_total_jrs);
                $bcfournisseur->setNbjours($facture->getNbjour() + $nb_total_jrs);

                $facturefournisseur->setMois($facture->getMois());
                $facturefournisseur->setYear($facture->getYear());
                $facturefournisseur->setDate(new \DateTime('now'));
                $facturefournisseur->setBcfournisseur($bcfournisseur);

                if ($prixAchatHT != null and $prixAchatHT != 0) {
                    $em->persist($bcfournisseur);
                    $em->flush();
                    $em->persist($facturefournisseur);
                    $em->flush();
                }

                $bcfournisseur->setVenteHT($facture->getTotalHT());
//            $facturefournisseur->setVenteHT($facture->getTotalHT());
                if ($mission->getConsultant() != null and $mission->getConsultant()->getType() == 'entrepreneur') {
                    $bcfournisseur->setAchatHT($bcfournisseur->getAchatHT() + $totalHT_hs_fournisseur);
                    $bcfournisseur->setAchatTTC($bcfournisseur->getAchatHT());
                    $facturefournisseur->setAchatHT($facturefournisseur->getAchatHT() + $totalHT_hs_fournisseur);
                    $facturefournisseur->setAchatTTC($facturefournisseur->getAchatHT() * 1);
                    $bcfournisseur->setTaxe(0);
                    $facturefournisseur->setTaxe(0);

                } else {

                    $bcfournisseur->setAchatHT($bcfournisseur->getAchatHT() + $totalHT_hs_fournisseur);
                    $bcfournisseur->setAchatTTC($bcfournisseur->getAchatTTC() + $totalTTC_hs_fournisseur);
                    $facturefournisseur->setAchatHT($facturefournisseur->getAchatHT() + $totalHT_hs_fournisseur);
                    $bcfournisseur->setAchatTTC($facturefournisseur->getAchatTTC() + $totalTTC_hs_fournisseur);
                    $facturefournisseur->setAchatTTC($facturefournisseur->getAchatHT() * 1.2);
                    $bcfournisseur->setTaxe($bcfournisseur->getAchatTTC() - $bcfournisseur->getAchatHT());
                    $facturefournisseur->setTaxe($facturefournisseur->getAchatTTC() - $facturefournisseur->getAchatHT());
                }
                if ($prixAchatHT != null and $prixAchatHT != 0) {
                    $em->persist($bcfournisseur);
                    $em->flush();
                    $em->persist($facturefournisseur);
                    $em->flush();
                }
                $production = new Production();
                $production->setConsultant($mission->getConsultant());
                $production->setClient($mission->getClient());
                $production->setNbjour($facturefournisseur->getNbjours());
                $production->setAchatHT($bcfournisseur->getAchatHT());
                $production->setAchatTTC($bcfournisseur->getAchatHT() * 1.2);
                $production->setFournisseur($bcfournisseur->getFournisseur());
                $production->setVenteHT($facture->getTotalHT());
                $production->setVenteTTC($facture->getTotalHT() * 1.2);
                $production->setMission($mission);

                $production->setMois($facture->getMois());
                $production->setTjmVente($mission->getPrixVente());
                $production->setTjmAchat($mission->getPrixAchat());
                $production->setYear($facture->getYear());
                $production->setFacture($facture);

                $em->persist($production);
                $em->flush();
                $facture->setEtat('non payé');
                $em->persist($facture);
                $em->flush();
                if ($prixAchatHT != null and $prixAchatHT != 0) {
                    $virement = new Virement();
                    $virement->setBcfournisseur($bcfournisseur);
                    $virement->setAchat($bcfournisseur->getAchatTTC());
                    $virement->setDate($bcfournisseur->getDate());
                    $virement->setConsultant($bcfournisseur->getConsultant());
                    $virement->setEtat('en attente');

                    $virement->setFacturefournisseur($facturefournisseur);
                    $em->persist($virement);
                    $em->flush();
                }
            }
            if (isset($nb_total_jrs)) {

                $bc = end($bcclients);
                $bc->setNbJrsR($bc->getNbJrsR() - $nb_total_jrs);
                $em->persist($bc);
                $em->flush();
            }
            // remove total hs from last bc client
//            dump();
//            die();
//            dump($facture, $bcfournisseur, $facturefournisseur, $production);

// convert to string
            $em->persist($facture);
            $em->flush();
            foreach ($bcclients as $bcclient) {

                $facture->addBcclient($bcclient);
            }
//            $facture_print = $em->getRepository('AppBundle:Facture')->find($facture->getId());

            $operation->convertTostring($facture);
            $operation->generateFacturePdf($facture);
//            die();
            /*  try {
                  if ($facture->getFacturehsups()->count() == 0) {
                      if ($facture->getDevise()) {

                          $this->generateFacturePdfDevise($facture);

                      } else {
                           dump($facture->getBcclients()->toArray());
                           die();
                       $this->generateFacturePdf($facture);
                      }

                  } else {
                      $this->generateFacturePdfHeureSup($facture);
                  }
              } catch
              (Exception $exception) {

              }
            */
            return $this->redirectToRoute('facture_show', array('id' => $facture->getId()));
        }
        return $this->render('facture/facture_mission.html.twig', array(
            'facture' => $facture,
            'mission' => $mission,
            'bcclientNotExpired' => $bcclientsNotExpired,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing facture entity.
     *
     * @Route("/{id}/facture/avoir", name="facture_avoir")
     * @Method({"GET", "POST"})
     */
    public function avoirAction(Request $request, Facture $facture)
    {
        if ($facture->getMission() == null) {
            return $this->render('default/info.html.twig', [
                'icon' => 'fa fa-warning',
                'code' => '403',
                'msg' => 'Vous pouvez pas créer un avoir pour une facture Projet !'
            ]);
        }
        $operation = $this->get('app.operation');

        $form = $this->createFormBuilder()
            ->add('nbjour', NumberType::class, array(
                'required' => true))
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'placeholder' => 'Date Facture',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'date-timepicker1'],
            ])
            ->getForm();
        $form->handleRequest($request);
        $avoir = new Facture();
        $avoir->setAvoir(true);
        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $mission = $facture->getMission();
            $prixVente = $mission->getPrixVente();
            $prixAchat = $mission->getPrixAchat();
            $nbjour = $form->get('nbjour')->getData();
            $date = $form->get('date')->getData();
            $venteHT = $prixVente * $nbjour * -1;
            $achatHT = $prixAchat * $nbjour * -1;
            //clone Avoir from Facture
            $avoir->setConsultant($facture->getConsultant());
            $avoir->setComptebancaire($facture->getComptebancaire());
            $avoir->setClient($facture->getClient());
            $avoir->setMission($facture->getMission());
            $avoir->setTjm($facture->getTjm());
            $avoir->setAddedby($this->getUser());
            $avoir->setNbjour($nbjour);
            $avoir->setYear($facture->getYear());
            $avoir->setMois($facture->getMois());
            $avoir->setFacture($facture);
            $avoir->setTotalHT($venteHT);
            $avoir->setTaxe($avoir->getTotalHT() * 0.2);
            $avoir->setTotalTTC($avoir->getTotalHT() * 1.2);
            $avoir->setDate($date);
            $year = intval($avoir->getDate()->format('Y'));
            $nbb = $em->createQuery('
            SELECT COUNT(f) as total FROM AppBundle:Facture f 
            WHERE YEAR(f.date) = :annee AND f.devise = 0 and f.avoir = 1
            ')->setParameters([
//
                'annee' => $year,

            ])->getResult();


            $count_factures = (int)$nbb[0]['total'] + 1;
            $avoir->setNumero('A-' . substr($year, -2) . '-' . str_pad($count_factures, 3, '0', STR_PAD_LEFT));
//create Bc fournisseur
            $bcfournisseur = new Bcfournisseur();
            $bcfournisseur->setAchatHT($achatHT);
            $bcfournisseur->setVenteHT($venteHT);
            $bcfournisseur->setNbjours($nbjour);
            $bcfournisseur->setMois($avoir->getMois());
            $bcfournisseur->setYear($avoir->getYear());
            $bcfournisseur->setTjmAchat($prixAchat);
            $bcfournisseur->setAchatTTC($bcfournisseur->getAchatHT() * 1.2);
            $bcfournisseur->setTaxe($bcfournisseur->getAchatHT() * 0.2);
            $bcfournisseur->setDate($avoir->getDate());
            $bcfournisseur->setMission($avoir->getMission());
            $bcfournisseur->setFacture($avoir);
            $bcfournisseur->setFournisseur($mission->getFournisseur());
            $bcfournisseur->setConsultant($avoir->getConsultant());
            $nb = count($em->getRepository('AppBundle:Bcfournisseur')->findBy(array(

                'mois' => $facture->getMois(),
                'year' => $facture->getYear(),
            )));
            $bcfournisseur->setCode('BC-' . substr($facture->getYear(), -2) . '-' . str_pad($facture->getMois(), 2, '0', STR_PAD_LEFT) . '-' . str_pad($nb + 1, 3, '0', STR_PAD_LEFT));
// creation facture fournisseur
            $facturefournisseur = new Facturefournisseur();
            $facturefournisseur->setConsultant($bcfournisseur->getConsultant())
                ->setFournisseur($bcfournisseur->getFournisseur())
                ->setFacture($avoir)
                ->setMission($bcfournisseur->getMission())
                ->setDate($bcfournisseur->getDate())
                ->setAchatHT($bcfournisseur->getAchatHT())
                ->setNbjours($bcfournisseur->getNbjours())
                ->setMois($bcfournisseur->getMois())
                ->setYear($bcfournisseur->getYear())
                ->setAchatTTC($bcfournisseur->getAchatTTC())
                ->setTaxe($bcfournisseur->getTaxe())
                ->setBcfournisseur($bcfournisseur);
            // creation Virement
            $virement = new Virement();
            $virement->setBcfournisseur($bcfournisseur)
                ->setDate($bcfournisseur->getDate())
                ->setEtat('en attente')
                ->setAchat($bcfournisseur->getAchatTTC())
                ->setFacturefournisseur($facturefournisseur)
                ->setConsultant($facturefournisseur->getConsultant());
            // creation production
            $production = new Production();
            $production->setConsultant($avoir->getConsultant())
                ->setMission($mission)
                ->setClient($avoir->getClient())
                ->setFacture($avoir)
                ->setFournisseur($mission->getFournisseur())
                ->setTjmAchat($prixAchat)
                ->setTjmVente($prixVente)
                ->setMois($avoir->getMois())
                ->setYear($avoir->getYear())
                ->setNbjour($avoir->getNbjour())
                ->setVenteHT($venteHT)
                ->setVenteTTC($avoir->getTotalTTC())
                ->setAchatHT($achatHT)
                ->setAchatTTC($bcfournisseur->getAchatTTC());
            $bcclients = $facture->getBcclients();
//            dump($bcclients);


            if ($bcclients->count() == 1) {
                $bcclients->first()->setNbJrsR($bcclients->first()->getNbJrsR() + $nbjour);
                $em->persist($bcclients->first());
                $em->flush();
            }

            if ($bcclients->count() == 2) {
                $bcclients->last()->setNbJrsR($bcclients->last()->getsetNbJrsR() + $nbjour);

                $em->persist($bcclients->last());
                $em->flush();

//                $operation->getLastVersion($bcclient);

            }
//            die();
            $em->persist($avoir);
            $em->persist($bcfournisseur);
            $em->persist($facturefournisseur);
            $em->persist($virement);
            $em->persist($production);
            $em->flush();
//            dump($production, $virement, $facturefournisseur, $avoir, $bcfournisseur);
            $operation->generateFacturePdf($avoir);
//
            return $this->redirectToRoute('facture_show', ['id' => $avoir->getId()]);
//            echo 'form submited';
//            die();

        }
        return $this->render('facture/new_avoir.html.twig', array(
            'facture' => $facture,
            'form' => $form->createView()

        ));
    }

    /**
     * @Route("/log/{id}", name="facture_log")
     * @Method("GET")
     */
    public function LogObjectAction(Facture $object)
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
     * Finds and displays a facture entity.
     *
     * @Route("/{id}", name="facture_show")
     * @Method("GET")
     */
    public function showAction(Facture $facture)
    {

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry'); // we use default log entry class
        $logs = $repo->getLogEntries($facture);
//        $test = $repo->revert($facture, 2/*version*/);
        // dump($facture->HasExecutedVirement());

        $fiche = $em->getRepository('AppBundle:Fiche')->find(1);

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

            }
        }


        $deleteForm = $this->createDeleteForm($facture);


        return $this->render('facture/show.html.twig', array(
            'facture' => $facture,
            'delete_form' => $deleteForm->createView(),
            'mois' => mois_convert($facture->getMois()),
            'fiche' => $fiche
        ));
    }

    /**
     * Finds and displays a facture entity.
     *
     * @Route("/{id}/paye", name="facture_payer")
     * @Method("GET")
     */
    public
    function setPayedAction(Facture $facture)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $em = $this->getDoctrine()->getManager();

        $facture->setEtat('payé');
        $em->persist($facture);
        $em->flush();


        return $this->redirectToRoute('facture_index');
    }

    /**
     * Finds and displays a facture entity.
     *
     * @Route("/{id}/change", name="facture_change",options={"expose"=true})
     * @Method("GET")
     */
    public
    function changeAction(Facture $facture)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $em = $this->getDoctrine()->getManager();

        $facture->setEtat('payé');
        $em->persist($facture);
        $em->flush();


        return $this->redirectToRoute('facture_index');
    }

    /**
     * Finds and displays a facture entity.
     *
     * @Route("/{id}/print", name="facture_print")
     * @Method("GET")
     */
    public function showfactureAction(Facture $facture)
    {
//        dump($facture->getBcclients()->count());
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository('AppBundle:Fiche')->find(1);
//dump($fiche);
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

            }
        }


        if ($facture->getDevise()) {
            $template = 'facture/print_devise.html.twig';


        } else {
            $template = 'facture/print.html.twig';


        }

        return $this->render($template, array(
            'facture' => $facture,
            'mois' => mois_convert($facture->getMois()),
            'fiche' => $fiche
        ));
    }

    /**
     * Finds and displays a facture entity.
     *
     * @Route("/{id}/print/pdf", name="facture_print_pdf")
     * @Method("GET")
     */
    public function showfacturePDFAction(Facture $facture)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository('AppBundle:Fiche')->find(1);

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

            }
        }


        $template = 'facture/pdf/print.html.twig';


//dompdf staffs
        $html = $this->render($template, array(
            'facture' => $facture,

            'mois' => mois_convert($facture->getMois()),
            'fiche' => $fiche
            // 'delete_form' => $deleteForm->createView(),
        ));
        $dompdf = new Dompdf();
        $path = $this->get('kernel')->getRootDir() . '/../web';

        $pathing_pv = 'test' . '.pdf';

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        //dump($dompdf);
        $dompdf->loadHtml($html->getContent());
        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();


        // Write file to the desired path
        file_put_contents($pathing_pv, $output);
//end dompdf
        return $this->render($template, array(
            'facture' => $facture,

            'mois' => mois_convert($facture->getMois()),
            'fiche' => $fiche
            // 'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a facture entity.
     *
     * @Route("/{id}/print_hs", name="facture_print_hs")
     * @Method("GET")
     */
    public function showhsfactureAction(Facture $facture)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository('AppBundle:Fiche')->find(1);

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

            }
        }


        return $this->render('facture/print_hs.html.twig', array(
            'facture' => $facture,

            'mois' => mois_convert($facture->getMois()),
            'fiche' => $fiche,

            // 'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a facture entity.
     *
     * @Route("/{id}/print_devise", name="facture_print_devise")
     * @Method("GET")
     */
    public
    function printDeviseAction(Facture $facture)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository('AppBundle:Fiche')->find(1);

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

            }
        }


        return $this->render('facture/print_devise.html.twig', array(
            'facture' => $facture,

            'mois' => mois_convert($facture->getMois()),
            'fiche' => $fiche
            // 'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a facture entity.
     *
     * @Route("/{id}/print_projet", name="facture_print_projet")
     * @Method("GET")
     */
    public function printFactureProjetAction(Facture $facture)
    {

//        dump($facture);
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository('AppBundle:Fiche')->find(1);
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

            }
        }


        // AND l.nbjour>0 AND l.totalHt>0
        // orange
        if ($facture->getProjet()->getClient()->getNom() == 'MEDI TELECOM') {
            $itemss = $em->createQuery('
          SELECT p as ligne,SUM (l.nbjourVente) AS nbjours, SUM(l.totalHt) as total,SUM(l.totalTTC) as totalTTC ,p.vente as tjm   
          From AppBundle:LigneFacture l
          JOIN AppBundle:Projetconsultant p
          WHERE l.facture = :facture
          AND l.projetconsultant = p.id
          GROUP BY p.job
         ')->setParameter('facture', $facture)->execute();
            $items = [];
            foreach ($itemss as $item) {
                if ($item['nbjours'] != 0) {
                    $items[] = $item;

                }

            }

//  dump($items);
//            die();

            return $this->render('facture/print_projet.html.twig', array(
                'facture' => $facture,

                'mois' => mois_convert($facture->getMois()),
                'fiche' => $fiche,

                'items' => $items

            ));
        }

        if ($facture->getProjet()->getClient()->getId() == 14) {
            //Pcs
            $itemss = $em->createQuery('
          SELECT IDENTITY(p.job) as job,p as ligne,SUM (l.nbjour) as nbjours, SUM (l.totalHt) as total,SUM (l.totalTTC) as totalTTC ,p.vente as tjm    
          From AppBundle:LigneFacture l
          JOIN AppBundle:Projetconsultant p        
          WHERE l.facture = :facture        
          AND l.projetconsultant = p.id
          AND l.nbjour>0 AND l.totalHt>0
          GROUP By tjm
                    
          ')->setParameter('facture', $facture)->execute();
            $items = [];
            foreach ($itemss as $item) {
                if ($item['nbjours'] != 0) {
                    $items[] = $item;
                }
            }
//            dump($items);
            return $this->render('facture/print_pcs.html.twig', array(
                'facture' => $facture,

                'mois' => mois_convert($facture->getMois()),
                'fiche' => $fiche,
                'items' => $items,

            ));
        } else {
            //Other clients
            $items = $em->createQuery('
          SELECT p as ligne,l.nbjour as nbjours, l.totalHt as total,l.totalTTC as totalTTC   
          From AppBundle:LigneFacture l
          JOIN AppBundle:Projetconsultant p
          WHERE l.facture = :facture
          AND l.projetconsultant = p.id             
          ')->setParameter('facture', $facture)->execute();
//            dump($items);
            return $this->render('facture/print_projet.html.twig', array(
                'facture' => $facture,

                'mois' => mois_convert($facture->getMois()),
                'fiche' => $fiche,
                'items' => $items
            ));
        }


    }

    /**
     * Displays a form to edit an existing facture entity.
     *
     * @Route("/{id}/edit_hs", name="facture_edit_hs")
     * @Method({"GET", "POST"})
     */
    public function editHsupAction(Request $request, Facture $facture)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        //check role
        $operation = $this->get('app.operation');
        return $operation->checkAdmin();
        //end check
        if ($facture->getId() << 140) {

            return $this->redirectToRoute('facture_index');
        }
        $editForm = $this->createForm('AppBundle\Form\FactureType', $facture);
        $editForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $totalHT_hs = null;
            $totalTTC_hs = null;
            $totalHT_hs_fournisseur = null;
            $totalTTC_hs_fournisseur = null;
            $nb_total_jrs = null;

            $heures = $editForm->get('facturehsups')->getData();
            $nbjours = $editForm->get('nbjour')->getData();
            if (!empty($heures)) {
                $totalHT_hs = null;
                $totalTTC_hs = null;
                $totalHT_hs_fournisseur = null;
                $totalTTC_hs_fournisseur = null;
                $nb_total_jrs = null;
                $mission = $facture->getMission();
                foreach ($heures as $heure) {

                    $nb_jour_sup = $heure->getNbheure() / 10;
                    $heure->setNbjour($nb_jour_sup);
                    $heure->setFacture($facture);
                    $heure->setBcfournisseur(null);
//                    $facture->addFacturehsup($heure);
                    $heuresup = $heure->getHeuresup();
                    $heure->setTotalHT($nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixVente());
                    $heure->setTotalTTC(($nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixVente()) * 1.2);
                    $totalHT_hs += ($nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixVente());
                    $totalTTC_hs += $nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixVente() * 1.2;
                    $totalHT_hs_fournisseur += ($nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixAchat());
                    $totalTTC_hs_fournisseur += $nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixAchat() * 1.2;
                    $nb_total_jrs += $heure->getNbheure() / 10;
                    $bcfournisseur = $em->getRepository('AppBundle:Bcfournisseur')->findOneBy(array(
                        'consultant' => $facture->getConsultant(),
                        'mois' => $facture->getMois(),
//                        'facture' => $facture,

                    ));
                    $facturefournisseur = $em->getRepository('AppBundle:Facturefournisseur')->findOneBy([
                        'consultant' => $facture->getConsultant(),
                        'mois' => $facture->getMois(),
                        'facture' => $facture,

                    ]);
                    $heure_bc = $em->getRepository('AppBundle:FactureHsup')->findOneBy([
                        //    'facture' => $facture,
                        'heuresup' => $heuresup,
                        'bcfournisseur' => $bcfournisseur,

                    ]);

                    $heure_bc->setBcfournisseur($bcfournisseur);
                    $heure_bc->setFacturefournisseur($facturefournisseur);
//                    $heure_bc->setFacture($facture);
                    $heure_bc->setNbjour($nb_jour_sup);
                    $heure_bc->setHeuresup($heure->getHeuresup());

                    $heure_bc->setTotalHT(0);
                    $heure_bc->setTotalHT($nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixAchat());
                    $heure_bc->setTotalTTC(($nb_jour_sup * ($heuresup->getPourcentage() / 100 + 1) * $mission->getPrixAchat()) * 1.2);
                    $heure_bc->setNbheure($heure->getNbheure());
                    $bcfournisseur->addHeure($heure_bc);
                    $facturefournisseur->addHeure($heure_bc);
                    $em->persist($heure);
                    $em->flush();

                }
            } else {
                $totalHT_hs = null;
                $totalTTC_hs = null;
                $totalHT_hs_fournisseur = null;
                $totalTTC_hs_fournisseur = null;
                $nb_total_jrs = null;

            }


            $facture = $em->getRepository('AppBundle:Facture')->find($facture->getId());
            $facture->setTotalHT($nbjours * $facture->getMission()->getPrixVente() + $totalHT_hs);
            $facture->setTotalTTC(($facture->getTotalHT()) * 1.2);

            $bcfournisseur->setFournisseur($facture->getMission()->getFournisseur());
//            $bcfournisseur->setNbjours($facture->getNbjour());
            $bcfournisseur->setMois($facture->getMois());
            $bcfournisseur->setYear($facture->getYear());
            $bcfournisseur->setDate(new \DateTime('now'));
            $facturefournisseur->setFournisseur($facture->getMission()->getFournisseur());
            $facturefournisseur->setNbjours($nbjours + $nb_total_jrs);
            $bcfournisseur->setNbjours($nbjours + $nb_total_jrs);

            $facturefournisseur->setMois($facture->getMois());
            $facturefournisseur->setYear($facture->getYear());
            $facturefournisseur->setDate(new \DateTime('now'));
            $facturefournisseur->setBcfournisseur($bcfournisseur);


            $em->persist($bcfournisseur);
            $em->flush();
            $em->persist($facturefournisseur);
            $em->flush();
            $bcfournisseur->setVenteHT($facture->getTotalHT());
//            $facturefournisseur->setVenteHT($facture->getTotalHT());
            if ($mission->getConsultant() != null and $mission->getConsultant()->getType() == 'entrepreneur') {
                $bcfournisseur->setAchatHT($nbjours * $facture->getMission()->getPrixAchat() + $totalHT_hs_fournisseur);
                $bcfournisseur->setAchatTTC($bcfournisseur->getAchatHT() * 1);

                $facturefournisseur->setAchatHT($nbjours * $facture->getMission()->getPrixVente() + $totalHT_hs_fournisseur);
                $facturefournisseur->setAchatTTC($facturefournisseur->getAchatHT() * 1);
//            $facturefournisseur->setAchatTTC(($facturefournisseur->getAchatHT() + $totalTTC_hs_fournisseur) * 1.2);
                $bcfournisseur->setTaxe($bcfournisseur->getAchatHT() * 0);
                $facturefournisseur->setTaxe($bcfournisseur->getAchatHT() * 0);
            } else {

                $bcfournisseur->setAchatHT($nbjours * $facture->getMission()->getPrixAchat() + $totalHT_hs_fournisseur);
                $bcfournisseur->setAchatTTC($bcfournisseur->getAchatHT() * 1.2);

                $facturefournisseur->setAchatHT($nbjours * $facture->getMission()->getPrixVente() + $totalHT_hs_fournisseur);
                $facturefournisseur->setAchatTTC($facturefournisseur->getAchatHT() * 1.2);
//            $facturefournisseur->setAchatTTC(($facturefournisseur->getAchatHT() + $totalTTC_hs_fournisseur) * 1.2);
                $bcfournisseur->setTaxe($bcfournisseur->getAchatHT() * 0.2);
                $facturefournisseur->setTaxe($bcfournisseur->getAchatHT() * 0.2);
            }

//            $facturefournisseur->setTaxe($facturefournisseur->getAchatTTC() - $facturefournisseur->getAchatHT());
            $em->persist($bcfournisseur);
            $em->flush();
            $em->persist($facturefournisseur);
            $em->flush();
            if ($facture->getProductions()->count() != 0) {

                $production = $facture->getProductions()->first();
                $production->setConsultant($mission->getConsultant());
                $production->setClient($mission->getClient());
                $production->setNbjour($facturefournisseur->getNbjours());
                $production->setAchatHT($bcfournisseur->getAchatHT());
                $production->setAchatTTC($bcfournisseur->getAchatHT() * 1.2);
                $production->setFournisseur($bcfournisseur->getFournisseur());
                $production->setVenteHT($facture->getTotalHT());
                $production->setVenteTTC($facture->getTotalHT() * 1.2);
                $production->setMission($mission);

                $production->setMois($facture->getMois());
                $production->setTjmVente($mission->getPrixVente());
                $production->setTjmAchat($mission->getPrixAchat());
                $production->setYear($facture->getYear());
                $production->setFacture($facture);

                $em->persist($production);
                $em->flush();
            }

//            $facture->setEtat('non payé');
            $em->persist($facture);
            $em->flush();
            $operation = $this->get('app.operation');
            $operation->convertTostring($facture);

        }
        return $this->render('facture/edit.html.twig', array(
            'facture' => $facture,
            'edit_form' => $editForm->createView(),

        ));

    }

    /**
     * Displays a form to edit an existing facture entity.
     *
     * @Route("/{id}/edit", name="facture_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Facture $facture)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        //check role
        $operation = $this->get('app.operation');
        return $operation->checkAdmin();
        //end check
        if ($facture->HasExecutedVirement() and !in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {

            return $this->render('default/info.html.twig', [
                'icon' => 'fa fa-warning',
                'code' => '403',
                'msg' => 'Vous pouvez pas Modifier une Facture liée avec des Virements Executés !'
            ]);
        }
        if ($facture->getId() < 140) {
            return $this->redirectToRoute('facture_index');
        }
//        dump($facture);
//        $deleteForm = $this->createDeleteForm($facture);
        $bcclients_mission = $facture->getMission()->getBcclients();
        $bcclientsNotExpired = $facture->getMission()->getBcclientsNotExpired();
        $editForm = $this->createForm('AppBundle\Form\FactureEditType', $facture);
        $editForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $mission = $facture->getMission();
            $bcclientsss = $request->get('bcclient');
            $bcclientss = $em->getRepository('AppBundle:Bcclient')->findBy([

                'id' => $bcclientsss
            ]);

//            dump($bcclients);
            foreach ($facture->getBcclients() as $bc) {

                $bc->removeFacture($facture);
            }

            if (!empty($bcclientss)) {

                foreach ($bcclientss as $bcclient) {

                    $bcclient->addFacture($facture);
                    $em->persist($bcclient);
                    $em->flush();


                }
            }
            $prixAchatHT = $mission->getPrixAchat();
            $prixVenteHT = $mission->getPrixVente();
            $bcfournisseur = $em->getRepository('AppBundle:Bcfournisseur')->findOneBy([
                'mission' => $mission,
//                'mois' => $facture->getMois(),
                'facture' => $facture

            ]);
            $facturefournisseur = $em->getRepository('AppBundle:Facturefournisseur')->findOneBy([
                'mission' => $mission,
//                'mois' => $facture->getMois(),
                'facture' => $facture

            ]);
            $production = $em->getRepository('AppBundle:Production')->findOneBy([
                'mission' => $mission,
//                'mois' => $facture->getMois(),
                'facture' => $facture

            ]);
            $virement = $em->getRepository('AppBundle:Virement')->findOneBy([


                'bcfournisseur' => $bcfournisseur,
                'etat' => 'en attente'

            ]);


            if (!$production) {

                $production = $em->getRepository('AppBundle:Production')->findOneBy([
                    'mission' => $mission,
                    'mois' => $facture->getMois(),
//                'facture' => $facture

                ]);
            }
//            dump($bcfournisseur, $facture, $facturefournisseur);
            if ($mission->getDevise() == 'DH') {

                if ($mission->getType() == 'journaliere') {
                    $totalHT = $prixVenteHT * $facture->getNbjour();
                    $achatHT = $prixAchatHT * $facture->getNbjour();
                    $TVA = ($prixVenteHT * $facture->getNbjour()) * 0.2;
                    $TVA_Achat = $achatHT * 0.2;
                    if ($mission->getConsultant() != null and $mission->getConsultant()->getType() == 'entrepreneur') {
                        $bcfournisseur->setAchatHT($achatHT);
                        $facturefournisseur->setAchatHT($achatHT);
                        $bcfournisseur->setTaxe(0);
                        $facturefournisseur->setTaxe(0);
                        $bcfournisseur->setAchatTTC($achatHT);
                        $bcfournisseur->setVenteHT($totalHT);
                        $bcfournisseur->setMois($facture->getMois());
                        $facturefournisseur->setMois($facture->getMois());
                        $bcfournisseur->setNbjours($facture->getNbjour());
                        $facturefournisseur->setNbjours($facture->getNbjour());
                        $facturefournisseur->setAchatTTC($achatHT);
                    } else {

                        $bcfournisseur->setAchatHT($achatHT);
                        $facturefournisseur->setAchatHT($achatHT);
                        $bcfournisseur->setTaxe($TVA_Achat);
                        $facturefournisseur->setTaxe($TVA_Achat);
                        $bcfournisseur->setMois($facture->getMois());
                        $facturefournisseur->setMois($facture->getMois());

                        $bcfournisseur->setAchatTTC($achatHT + $TVA_Achat);
                        $bcfournisseur->setVenteHT($totalHT);
                        $bcfournisseur->setNbjours($facture->getNbjour());
                        $facturefournisseur->setNbjours($facture->getNbjour());
                        $facturefournisseur->setAchatTTC($achatHT + $TVA_Achat);
                    }

                    $facture->setTotalHT($totalHT);
                    $facture->setTjm($prixVenteHT);
                    $facture->setTaxe($TVA);
                    $facture->setTotalTTC($TVA + $totalHT);

                } else {
                    $totalHT = $prixVenteHT;
                    $achatHT = $prixAchatHT;
                    $TVA_Achat = $achatHT * 0.2;
                    if ($mission->getConsultant() != null and $mission->getConsultant()->getType() == 'entrepreneur') {

                        $bcfournisseur->setAchatHT($achatHT);
                        $facturefournisseur->setAchatHT($achatHT);
                        $bcfournisseur->setTaxe(0);
                        $facturefournisseur->setTaxe(0);
                        $bcfournisseur->setAchatTTC($achatHT);
                        $bcfournisseur->setVenteHT($totalHT);
                        $facturefournisseur->setAchatTTC($achatHT);
                    } else {
                        $bcfournisseur->setAchatHT($achatHT);
                        $facturefournisseur->setAchatHT($achatHT);
                        $bcfournisseur->setTaxe($TVA_Achat);
                        $facturefournisseur->setTaxe($TVA_Achat);
                        $bcfournisseur->setAchatTTC($achatHT + $TVA_Achat);
                        $bcfournisseur->setVenteHT($totalHT);
                        $facturefournisseur->setAchatTTC($achatHT + $TVA_Achat);

                    }

                    $TVA = ($prixVenteHT) * 0.2;
                    $facture->setTaxe($TVA);

                    $facture->setTotalHT($totalHT);
                    $facture->setTotalTTC($TVA + $totalHT);

                }
                if ($virement != null) {

                    $virement->setAchat($bcfournisseur->getAchatTTC());

                }
//                dump($bcfournisseur,$facture,$virement,$facturefournisseur,$production);
//                die();
            } else {


                if ($mission->getType() == 'journaliere') {

                    $totalHT = $prixVenteHT * $facture->getNbjour();
                    $achatHT = $prixAchatHT * $facture->getNbjour();
                    $TVA = 0;
                    $TVA_Achat = 0;
                    /* $bcfournisseur->setAchatHT($achatHT);
                     $facturefournisseur->setAchatHT($achatHT);
                     $bcfournisseur->setTaxe($TVA_Achat);
                     $facturefournisseur->setTaxe($TVA_Achat);
                     $bcfournisseur->setAchatTTC($achatHT + $TVA_Achat);
                     $bcfournisseur->setVenteHT($totalHT);
                     $facturefournisseur->setAchatTTC($achatHT + $TVA_Achat);*/
                    $facture->setTotalHT($totalHT);
                    $facture->setTaxe($TVA);
                    $facture->setTotalTTC($TVA + $totalHT);
//                    $bcclient = $facture->getBcclient();
                    //dump($bcclient);
                    /*  if ($bcclient != null) {

                          $bcclient->setNbJrsR($bcclient->getNbJrsR() - $facture->getNbjour());
                          $em->persist($bcclient);
                          $em->flush();
                      }*/
//                    dump($facture);die();

                } else {
                    $totalHT = $prixVenteHT;
                    $achatHT = $prixAchatHT;
                    $TVA_Achat = 0;
                    /*$bcfournisseur->setAchatHT($achatHT);
                    $facturefournisseur->setAchatHT($achatHT);
                    $bcfournisseur->setTaxe($TVA_Achat);
                    $facturefournisseur->setTaxe($TVA_Achat);
                    $bcfournisseur->setAchatTTC($achatHT + $TVA_Achat);
                    $bcfournisseur->setVenteHT($totalHT);
                    $facturefournisseur->setAchatTTC($achatHT + $TVA_Achat);
                    $facturefournisseur->setBcfournisseur($bcfournisseur);*/
                    $TVA = 0;
                    $facture->setTaxe($TVA);

                    $facture->setTotalHT($totalHT);
                    $facture->setTotalTTC($TVA + $totalHT);
//                    dump($facture);die();
                }


            }


            if ($bcfournisseur) {
                $em->persist($bcfournisseur);
                $em->flush();
            }
            if ($facturefournisseur) {
                $em->persist($facturefournisseur);
                $em->flush();
            }
            if ($virement) {
                $em->persist($virement);
                $em->flush();
            }

            if ($production) {

                $production->setConsultant($mission->getConsultant());
                $production->setClient($mission->getClient());
                $production->setNbjour($facture->getNbjour());
                $production->setAchatHT($bcfournisseur->getAchatHT());
                $production->setAchatTTC($bcfournisseur->getAchatHT() * 1.2);
                $production->setFournisseur($bcfournisseur->getFournisseur());
                $production->setVenteHT($facture->getTotalHT());
                $production->setVenteTTC($facture->getTotalHT() * 1.2);
                $production->setMission($mission);

                $production->setMois($facture->getMois());
                $production->setTjmVente($mission->getPrixVente());
                $production->setTjmAchat($mission->getPrixAchat());
                $production->setYear($facture->getYear());
                $production->setFacture($facture);

                $em->persist($production);
                $em->flush();
            }


            $facture->setEditedby($this->getUser());
            $facture->setUpdatedAt(new \DateTime());
//            dump($facture, $bcfournisseur, $facturefournisseur, $mission, $production);

            $this->getDoctrine()->getManager()->flush();
            try {
                $this->generateFacturePdfEdit($facture);

            } catch (Exception $exception) {

            }
            $operation = $this->get('app.operation');
            $operation->convertTostring($facture);
            return $this->redirectToRoute('facture_edit', array('id' => $facture->getId()));
        }

        return $this->render('facture/edit.html.twig', array(
            'facture' => $facture,
            'bcclientNotExpired' => $bcclientsNotExpired,
            'bcclients' => $bcclients_mission,
            'edit_form' => $editForm->createView(),

        ));
    }

    /**
     * Displays a form to edit an existing facture entity.
     *
     * @Route("/{id}/edit_date", name="facture_edit_date")
     * @Method({"GET", "POST"})
     */
    public
    function editDateAction(Request $request, Facture $facture)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $editForm = $this->createForm('AppBundle\Form\FactureEditDatesType', $facture);
        $editForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($editForm->isSubmitted() && $editForm->isValid()) {


            $facture->setEditedby($this->getUser());
            $facture->setUpdatedAt(new \DateTime());
//            dump($facture, $bcfournisseur, $facturefournisseur, $mission, $production);

            $this->getDoctrine()->getManager()->flush();


            return $this->redirectToRoute('facture_index');
        }

        return $this->render('facture/edit_date.html.twig', array(
            'facture' => $facture,
            'edit_form' => $editForm->createView(),

        ));
    }

    /**
     * Displays a form to edit an existing facture entity.
     *
     * @Route("/{id}/edit_projet2", name="facture_edit_projet2")
     * @Method({"GET", "POST"})
     */
    public
    function editProjetOrange2Action(Request $request, Facture $facture)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        //check role
        $operation = $this->get('app.operation');
        return $operation->checkAdmin();
        //end check
        if ($facture->getId() << 140) {

            return $this->redirectToRoute('facture_index');
        }
        $em = $this->getDoctrine()->getManager();
        $projet = $facture->getProjet();
//        dump($projet);
//        $bcfournisseurs=$projet->getBcfournisseurs();
//dump($facture);
        $editForm = $this->createForm('AppBundle\Form\Facture2Type', $facture);
        $editForm->handleRequest($request);
        $totalHt = null;
        if ($editForm->isSubmitted() && $editForm->isValid()) {

            foreach ($facture->getLignes() as $ligne) {

                $totalHt += $ligne->getNbjour() * $ligne->getProjetconsultant()->getVente();
//
                $ligne->setNbjourVente($ligne->getNbjour());

                $ligne->setTotalHT($ligne->getNbjour() * $ligne->getProjetconsultant()->getVente());
                $ligne->setTotalTTC($ligne->getNbjour() * $ligne->getProjetconsultant()->getVente() * 1.2);

                if ($ligne->getProductions()->count() != 0) {

                    $production = $ligne->getProductions()->first();
                    $production->setVenteHT($ligne->getTotalHT());
                    $production->setVenteTTC($ligne->getTotalTTC());
                    $production->setNbjour($ligne->getNbjour());
                    $production->setAchatHT($ligne->getNbjour() * $ligne->getProjetconsultant()->getAchat());
                    $production->setAchatTTC($production->getAchatHT() * 1.2);

                    $em->persist($production);
                    $em->flush();

                }


//                dump($production);die();

            }


            $taxe = $totalHt * 0.2;

            $facture->setTotalHT($totalHt);
            $facture->setTaxe($taxe);
            $facture->setTotalTTC($taxe + $totalHt);
            $facture->setProjet($projet);
//            $em->persist($facture);
//            $em->flush();


            $this->getDoctrine()->getManager()->flush();
            $bcfournisseurs = $em->createQuery('
             SELECT l,IDENTITY (p.consultant) as consultant ,SUM ( l.nbjour),SUM (l.totalHt)
             FROM AppBundle:LigneFacture l
             JOIN AppBundle:Projetconsultant p

             WHERE l.facture = :id 
             AND l.projetconsultant = p.id 
            
             GROUP BY p.id
             ')
                ->setParameter('id', $facture)
                ->getResult();
//            dump($bcfournisseurs);die();
            foreach ($bcfournisseurs as $bcfournisseur) {


                $bc = $em->getRepository('AppBundle:Bcfournisseur')->findOneBy(array(
                    'consultant' => intval($bcfournisseur['consultant']),
                    'mois' => $facture->getMois(),
                    'facture' => $facture,
                    'projet' => $facture->getProjet()
                ));
//                dump($bc);die();
                $facturefournisseur = $em->getRepository('AppBundle:Facturefournisseur')->findOneBy([
                    'consultant' => intval($bcfournisseur['consultant']),
                    'mois' => $facture->getMois(),
                    'facture' => $facture,
                    'projet' => $facture->getProjet()
                ]);
//update bc fournisseur

                $nb_jours = floatval($bcfournisseur[1]);
                $tjm_achat = $bcfournisseur[0]->getProjetconsultant()->getAchat();
                $totalVente = floatval($bcfournisseur[2]);
                if ($bc) {
                    $bc->setNbjours($nb_jours);
                    $bc->setAchatHt($tjm_achat * $nb_jours);
                    $bc->setTaxe($tjm_achat * $nb_jours * 0.2);
                    $bc->setAchatTTC($tjm_achat * $nb_jours * 1.2);
                    $bc->setVenteHt($totalVente);
                    $em->persist($bc);
                    $em->flush();
                }
                if ($facturefournisseur) {
                    $facturefournisseur->setNbjours($nb_jours);
                    $facturefournisseur->setAchatHt($tjm_achat * $nb_jours);
                    $facturefournisseur->setTaxe($tjm_achat * $nb_jours * 0.2);
                    $facturefournisseur->setAchatTTC($tjm_achat * $nb_jours * 1.2);
                    $em->persist($facturefournisseur);
                    $em->flush();
                }


//                dump($bcfournisseur[0], $bc, $facturefournisseur, $tjm_achat, $nb_jours, $totalVente);
            }
            $facture->setEditedby($this->getUser());
            $facture->setUpdatedAt(new \DateTime());

            $this->getDoctrine()->getManager()->flush();

            /*  dump($facture, $facture->getLignes(), $totalHt, $bcfournisseurs);
              die();*/

            return $this->redirectToRoute('facture_edit', array('id' => $facture->getId()));
        }

        return $this->render('facture/edit_projet.html.twig', array(
            'projet' => $facture->getProjet(),
            'facture' => $facture,
            'form' => $editForm->createView(),

        ));
    }

    /**
     * Displays a form to edit an existing facture entity.
     *
     * @Route("/{id}/edit_projet", name="facture_edit_projet")
     * @Method({"GET", "POST"})
     */
    public function editProjetOrangeAction(Request $request, Facture $facture)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $projet = $facture->getProjet();
        //check role
        $operation = $this->get('app.operation');
//        return $operation->checkAdmin();
        //end check
        if ($facture->getId() << 140) {

            return $this->redirectToRoute('facture_index');
        }
        $em = $this->getDoctrine()->getManager();

        $bcclientsNotExpired = $facture->getProjet()->getBcclients();
        dump($facture);
//        $bcfournisseurs=$projet->getBcfournisseurs();
//        dump($facture->getLignes()->toArray());
        $editForm = $this->createForm('AppBundle\Form\Facture3Type', $facture);
        $editForm->handleRequest($request);
        $totalHt = null;
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $bcclientsss = $request->get('bcclient');
            $bcclientss = $em->getRepository('AppBundle:Bcclient')->findBy([

                'id' => $bcclientsss
            ]);

//            dump($bcclients);
            foreach ($facture->getBcclients() as $bcc) {

                $bcc->removeFacture($facture);
            }

            if (!empty($bcclientss)) {

                foreach ($bcclientss as $bcclient) {

                    $bcclient->addFacture($facture);
                    $em->persist($bcclient);
                    $em->flush();


                }
            }
            foreach ($facture->getLignes() as $ligne) {

                $totalHt += $ligne->getNbjourVente() * $ligne->getProjetconsultant()->getVente();
                $ligne->setNbjourVente($ligne->getNbjourVente());
                $ligne->setNbjour($ligne->getNbjourVente());

                $ligne->setTotalHT($ligne->getNbjourVente() * $ligne->getProjetconsultant()->getVente());
                $ligne->setTotalTTC($ligne->getNbjourVente() * $ligne->getProjetconsultant()->getVente() * 1.2);

                $bc = $ligne->getBcfournisseur();

                if ($bc) {
                    $bc->setNbjours($ligne->getNbjourVente());
                    $bc->setAchatHt($ligne->getNbjourVente() * $ligne->getProjetconsultant()->getAchat());
                    if ($ligne->getProjetconsultant()->getConsultant() != null and $ligne->getProjetconsultant()->getConsultant()->getType() == 'entrepreneur') {
                        $bc->setTaxe(0);
                        $bc->setAchatTTC($bc->getAchatHt() * 1);
                    } else {
                        $bc->setTaxe($bc->getAchatHt() * 0.2);
                        $bc->setAchatTTC($bc->getAchatHt() * 1.2);

                    }

                    $bc->setVenteHt($ligne->getNbjourVente() * $ligne->getProjetconsultant()->getVente());
                    $em->persist($bc);
                    $em->flush();
                    $facturefournisseur = $bc->getFacturefournisseurs()->last();

                    if ($facturefournisseur) {
                        $facturefournisseur->setNbjours($bc->getNbjours());
                        $facturefournisseur->setAchatHt($bc->getAchatHt());
                        if ($ligne->getProjetconsultant()->getConsultant() != null and $ligne->getProjetconsultant()->getConsultant()->getType() == 'entrepreneur') {
                            $facturefournisseur->setTaxe(0);
                            $facturefournisseur->setAchatTTC($bc->getAchatHt() * 1);

                        } else {

                            $facturefournisseur->setTaxe($bc->getTaxe());
                            $facturefournisseur->setAchatTTC($bc->getAchatHt() * 1.2);
                        }

                        $em->persist($facturefournisseur);
                        $em->flush();
                    }
                }
                $virement = $em->getRepository('AppBundle:Virement')->findOneBy([
                    'bcfournisseur' => $bc
                ]);
//                $virement = new Virement();
                if ($virement) {
                    $virement->setFacturefournisseur($facturefournisseur);
                    $virement->setAchat($bc->getAchatTTC());
                    $virement->setDate($bc->getDate());
                    $virement->setConsultant($bc->getConsultant());
                    $em->persist($virement);
                    $em->flush();
                }

                if ($ligne->getProductions()->count() != 0) {
                    $production = $ligne->getProductions()->first();
                    $production->setVenteHT($ligne->getTotalHT());
                    $production->setVenteTTC($ligne->getTotalTTC());
                    $production->setTjmVente($ligne->getProjetconsultant()->getVente());
                    $production->setTjmAchat($ligne->getProjetconsultant()->getAchat());
                    $production->setNbjour($ligne->getNbjour());
                    $production->setAchatHT($ligne->getNbjour() * $ligne->getProjetconsultant()->getAchat());
                    $production->setAchatTTC($production->getAchatHT() * 1.2);
                    $em->persist($production);
                    $em->flush();
                }
            }
            $taxe = $totalHt * 0.2;
            $facture->setTotalHT($totalHt);
            $facture->setTaxe($taxe);
            $facture->setTotalTTC($taxe + $totalHt);
            $facture->setProjet($projet);
            $facture->setEditedby($this->getUser());
            $facture->setUpdatedAt(new \DateTime());
            $em->flush();
//            dump($facture);
//            die();

            try {
                $this->generateFacturePdfEditProjet($facture);

            } catch (Exception $exception) {

            }
//            dump($facture);
//            die();

            $operation = $this->get('app.operation');
            $operation->convertTostring($facture);
            return $this->redirectToRoute('facture_edit_projet', array('id' => $facture->getId()));
        }

        return $this->render('facture/edit_projet.html.twig', array(
            'projet' => $facture->getProjet(),
            'facture' => $facture,
            'bcclientNotExpired' => $bcclientsNotExpired,
            'form' => $editForm->createView(),

        ));
    }

    /**
     * Displays a form to edit an existing facture entity.
     *
     * @Route("/{id}/addsheet", name="facture_sheet")
     * @Method({"GET", "POST"})
     */
    public
    function addsheetAction(Request $request, Facture $facture)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
//        dump($facture);
        $deleteForm = $this->createDeleteForm($facture);
        $editForm = $this->createForm('AppBundle\Form\FacturesheetType', $facture);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('facture_index');
        }

        return $this->render('facture/edit_sheet.html.twig', array(
            'facture' => $facture,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a facture entity.
     *
     * @Route("/{id}", name="facture_delete")
     * @Method("DELETE")
     */
    public
    function deleteAction(Request $request, Facture $facture)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $form = $this->createDeleteForm($facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {

                $em->remove($facture);
                $em->flush();
            } else {

                return $this->redirectToRoute('error_access');
            }

        }

        return $this->redirectToRoute('facture_index');
    }

    /**
     * Deletes a facture entity.
     *
     * @Route("/{id}/delete", name="facture_remove")
     * @Method("GET")
     */
    public function supprimerAction(Request $request, Facture $facture)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $em = $this->getDoctrine()->getManager();


        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {

            $em->remove($facture);
            $em->flush();
        } else {

            return $this->redirectToRoute('error_access');
        }

        return $this->redirectToRoute('facture_index');
    }

    /**
     * Creates a form to delete a facture entity.
     *
     * @param Facture $facture The facture entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private
    function createDeleteForm(Facture $facture)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('facture_delete', array('id' => $facture->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     *
     * @Route("/test", name="route_to_retrieve_mission",options={"expose"=true})
     ** @Method({"GET", "POST"})
     */
    public function getNiveau(Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $Id = $request->get('idMission');
        $year = $request->get('year');
        /*  $Id = 6;
          $year = 2020;*/
        $em = $this->getDoctrine()->getManager();
        $mission = $em->getRepository('AppBundle:Mission')->find($Id);
        $factures = $em->getRepository('AppBundle:Facture')->findBy(

            [
                'mission' => $mission,
                'year' => $year
            ]


        );
        if ($mission->getType()) {
            $type = $mission->getType();
        } else {

            $type = null;
        }


        if ($factures != null) {
            foreach ($factures as $facture) {

                $output[] = array($facture->getMois());
            }

            $response = json_encode(array('data' => $type, 'mois' => $output));
        } else {
            $response = json_encode(array('data' => $type, 'mois' => null));
        }


        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));

    }

    /**
     *
     * @Route("/convert/devise", name="route_to_convert_devise", options={"expose"=true})
     ** @Method({"GET","POST"})
     */
    public
    function convertDevise(Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $id = $request->get('id');
//        $id = 453;
        $montant = $request->get('montant');
        $date = $request->get('dateFin');
//        $montant = 2000;
        $datego = DateTime::createFromFormat('Y-m-d H:i', $date);
        $datego ? $datego->format('Y-m-d H:i') : false;
        $em = $this->getDoctrine()->getManager();

        $facture = $em->getRepository('AppBundle:Facture')->find($id);
        $facture->setTotalDH($montant);
        $facture->setUpdatedAt(new \DateTime('now'));
        $facture->setDatepaiement($datego);
        $facture->setEtat('payé');
        $em->persist($facture);
        $em->flush();
//        dump($facture);
//die();
        // get taux
        $taux_object = $em->getRepository('AppBundle:Parametrage')->findOneBy([

            'motif' => 'taux_devise'
        ]);

        if ($taux_object != null) {

            $taux = floatval($taux_object->getValeur());
        } else {

            $taux = 0.95;
        }

        // creation bc fournisseur & facture fournisseur
//        $facture = new Facture();
        $bcfournisseur = new Bcfournisseur();
        $bcfournisseur->setMois($facture->getMois());
        $bcfournisseur->setYear($facture->getYear());
        $bcfournisseur->setUpdatedAt(new \DateTime());
        $bcfournisseur->setNbjours($facture->getNbjour());
        $bcfournisseur->setFacture($facture);
        $bcfournisseur->setConsultant($facture->getConsultant());
        $bcfournisseur->setFournisseur($facture->getMission()->getFournisseur());
        $bcfournisseur->setDate($facture->getDate());
        $bcfournisseur->setMission($facture->getMission());
        $bcfournisseur->setVenteHT($facture->getTotalDH());
        $achatTTC = round(($facture->getTotalDH() * $taux), 2);
        $bcfournisseur->setAchatTTC($achatTTC);
        $achatHT = round(($bcfournisseur->getAchatTTC() / 1.2), 2);

        $taxe = round(($bcfournisseur->getAchatHT() * 0.2), 2);
        $bcfournisseur->setAchatHT($achatHT);
        $bcfournisseur->setTaxe($taxe);
        $nb = count($em->getRepository('AppBundle:Bcfournisseur')->findBy(array(

            'mois' => $facture->getMois(),
            'year' => $facture->getYear(),
        )));
        $bcfournisseur->setCode('BC-' . substr($facture->getYear(), -2) . '-' . str_pad($facture->getMois(), 2, '0', STR_PAD_LEFT) . '-' . str_pad($nb + 1, 3, '0', STR_PAD_LEFT));


        $em->persist($bcfournisseur);
        $em->flush();
        $facturefournisseur = new Facturefournisseur();
        $facturefournisseur->setBcfournisseur($bcfournisseur);
        $facturefournisseur->setMois($facture->getMois());
        $facturefournisseur->setYear($facture->getYear());
        $facturefournisseur->setUpdatedAt(new \DateTime());
        $facturefournisseur->setNbjours($facture->getNbjour());
        $facturefournisseur->setFacture($facture);
        $facturefournisseur->setConsultant($facture->getConsultant());
        $facturefournisseur->setFournisseur($facture->getMission()->getFournisseur());
        $facturefournisseur->setDate($facture->getDate());
        $facturefournisseur->setMission($facture->getMission());
//        $facturefournisseur->setVenteHT($facture->getTotalDH());
        $facturefournisseur->setAchatTTC($facture->getTotalDH() * $taux);
        $facturefournisseur->setAchatHT($facturefournisseur->getAchatTTC() / 1.2);
        $facturefournisseur->setTaxe($facturefournisseur->getAchatHT() * 0.2);
        $em->persist($facturefournisseur);
//        dump($facture,$bcfournisseur,$facturefournisseur);die();
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
        $response = json_encode(array('data' => 'ok'));

        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));
    }

    /**
     *
     * @Route("/convert/devise2", name="route_to_convert_devise2")
     ** @Method({"POST"})
     */
    public
    function convertDevise2(Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }

        $id = $request->get('id');
//        $id = 453;
        $montant = $request->get('montant');
        $date = $request->get('dateFin');

//        $montant = 2000;
        $datego = DateTime::createFromFormat('Y-m-d', $date);
        $datego ? $datego->format('Y-m-d H:i') : false;
//        dump($date,$datego, $id, $montant);
//        die();
        $em = $this->getDoctrine()->getManager();

        $facture = $em->getRepository('AppBundle:Facture')->find($id);
        $facture->setTotalDH($montant);
        $facture->setUpdatedAt(new \DateTime('now'));
        $facture->setDatepaiement($datego);
        $facture->setEtat('payé');
        $em->persist($facture);
        $em->flush();
//        dump($facture);
//die();
        // get taux
        $taux_object = $em->getRepository('AppBundle:Parametrage')->findOneBy([

            'motif' => 'taux_devise'
        ]);

        if ($taux_object != null) {

            $taux = floatval($taux_object->getValeur());
        } else {

            $taux = 0.95;
        }

        // creation bc fournisseur & facture fournisseur
//        $facture = new Facture();
        $bcfournisseur = new Bcfournisseur();
        $bcfournisseur->setMois($facture->getMois());
        $bcfournisseur->setYear($facture->getYear());
        $bcfournisseur->setUpdatedAt(new \DateTime());
        $bcfournisseur->setNbjours($facture->getNbjour());
        $bcfournisseur->setFacture($facture);
        $bcfournisseur->setConsultant($facture->getConsultant());
        $bcfournisseur->setFournisseur($facture->getMission()->getFournisseur());
        $bcfournisseur->setDate($facture->getDate());
        $bcfournisseur->setMission($facture->getMission());
        $bcfournisseur->setVenteHT($facture->getTotalDH());
        $achatTTC = round(($facture->getTotalDH() * $taux), 2);
        $bcfournisseur->setAchatTTC($achatTTC);
        $achatHT = round(($bcfournisseur->getAchatTTC() / 1.2), 2);

        $taxe = round(($bcfournisseur->getAchatHT() * 0.2), 2);
        $bcfournisseur->setAchatHT($achatHT);
        $bcfournisseur->setTaxe($taxe);
        $nb = count($em->getRepository('AppBundle:Bcfournisseur')->findBy(array(

            'mois' => $facture->getMois(),
            'year' => $facture->getYear(),
        )));
        $bcfournisseur->setCode('BC-' . substr($facture->getYear(), -2) . '-' . str_pad($facture->getMois(), 2, '0', STR_PAD_LEFT) . '-' . str_pad($nb + 1, 3, '0', STR_PAD_LEFT));


        $em->persist($bcfournisseur);
        $em->flush();
        $facturefournisseur = new Facturefournisseur();
        $facturefournisseur->setBcfournisseur($bcfournisseur);
        $facturefournisseur->setMois($facture->getMois());
        $facturefournisseur->setYear($facture->getYear());
        $facturefournisseur->setUpdatedAt(new \DateTime());
        $facturefournisseur->setNbjours($facture->getNbjour());
        $facturefournisseur->setFacture($facture);
        $facturefournisseur->setConsultant($facture->getConsultant());
        $facturefournisseur->setFournisseur($facture->getMission()->getFournisseur());
        $facturefournisseur->setDate($facture->getDate());
        $facturefournisseur->setMission($facture->getMission());
//        $facturefournisseur->setVenteHT($facture->getTotalDH());
        $facturefournisseur->setAchatTTC($facture->getTotalDH() * $taux);
        $facturefournisseur->setAchatHT($facturefournisseur->getAchatTTC() / 1.2);
        $facturefournisseur->setTaxe($facturefournisseur->getAchatHT() * 0.2);
        $em->persist($facturefournisseur);
//        dump($facture,$bcfournisseur,$facturefournisseur);die();
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
        $response = json_encode(array('data' => 'ok'));
        return $this->redirectToRoute('facture_show', ['id' => $facture->getId()]);

    }

    /**
     *
     * @Route("/test/{id}", name="route_test")
     ** @Method({"GET"})
     */
    public
    function Test(Facture $facture)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $em = $this->getDoctrine()->getManager();

        $mois = intval($facture->getDate()->format('m'));
        $year = intval($facture->getDate()->format('Y'));
        $yearmini = intval($facture->getDate()->format('y'));


        $nb = count($em->getRepository('AppBundle:Facture')->findBy(array(

            'mois' => $mois,
            'year' => $year,
        )));

        $nbb = $em->createQuery('
            
            SELECT COUNT(f) as total FROM AppBundle:Facture f 
            WHERE MONTH(f.date) = :moi AND YEAR(f.date) = :annee
            ')
            ->setParameters([

                'moi' => $mois,
                'annee' => $year,
            ])->getResult();
        $nbb2 = $em->createQuery('
            
            SELECT COUNT(f) as total FROM AppBundle:Facture f 
            WHERE MONTH(f.date) = :moi AND YEAR(f.date) = :annee
            ')
            ->setParameters([

                'moi' => $mois,
                'annee' => $year,
            ])->getResult();


    }

    public function mois_convert($m)
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

        }
    }


    public function generateFacturePdf(Facture $facture)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository('AppBundle:Fiche')->find(1);

        $template = 'facture/pdf/print.html.twig';


//dompdf staffs
        $html = $this->render($template, array(
            'facture' => $facture,

            'mois' => $this->mois_convert($facture->getMois()),
            'fiche' => $fiche
        ));
        $dompdf = new Dompdf();
        $path_facture = $this->get('kernel')->getRootDir() . '/../web/factures/' . $facture->getClient()->getNom() . '/' . $facture->getYear() . '/' . $this->mois_convert($facture->getMois()) . '/';
        if (!file_exists($path_facture)) {
            mkdir($path_facture, 0777, true);
        }
        $pathing_pv = $path_facture . $facture->getNumero() . '.pdf';
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        //dump($dompdf);
        $dompdf->loadHtml($html->getContent());
        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();
        // Write file to the desired path
        file_put_contents($pathing_pv, $output);
        // service send mail
        $mailer = $this->get('app.sendmail');

        $mailer->sendMail(
            'Nouvelle Facture ' . $facture->getNumero(),
            'rfo.mobile@hope3k.net', 'aaaimad.backup@gmail.com',
            'facture_created',
            ['facture' => $facture],
            $pathing_pv

        );
        return true;
    }

    public function generateFacturePdfDevise(Facture $facture)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository('AppBundle:Fiche')->find(1);

        $template = 'facture/pdf/print_devise.html.twig';


//dompdf staffs
        $html = $this->render($template, array(
            'facture' => $facture,

            'mois' => $this->mois_convert($facture->getMois()),
            'fiche' => $fiche
        ));
        $dompdf = new Dompdf();
        $path_facture = $this->get('kernel')->getRootDir() . '/../web/factures/' . $facture->getClient()->getNom() . '/' . $facture->getYear() . '/' . $this->mois_convert($facture->getMois()) . '/';
        if (!file_exists($path_facture)) {
            mkdir($path_facture, 0777, true);
        }
        $pathing_pv = $path_facture . $facture->getNumero() . '.pdf';
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        //dump($dompdf);
        $dompdf->loadHtml($html->getContent());
        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();
        // Write file to the desired path
        file_put_contents($pathing_pv, $output);
        $mailer = $this->get('app.sendmail');

        $mailer->sendMail(
            'Nouvelle Facture ' . $facture->getNumero(),
            'rfo.mobile@hope3k.net', 'aaaimad.backup@gmail.com',
            'facture_created',
            ['facture' => $facture],
            $pathing_pv

        );
        return true;
    }

    public
    function generateFacturePdfHeureSup(Facture $facture)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository('AppBundle:Fiche')->find(1);

        $template = 'facture/pdf/print.html.twig';


//dompdf staffs
        $html = $this->render($template, array(
            'facture' => $facture,
            'mois' => $this->mois_convert($facture->getMois()),
            'fiche' => $fiche
        ));
        $dompdf = new Dompdf();
        $path_facture = $this->get('kernel')->getRootDir() . '/../web/factures/' . $facture->getClient()->getNom() . '/' . $facture->getYear() . '/' . $this->mois_convert($facture->getMois()) . '/';
        if (!file_exists($path_facture)) {
            mkdir($path_facture, 0777, true);
        }
        $pathing_pv = $path_facture . $facture->getNumero() . '.pdf';
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        //dump($dompdf);
        $dompdf->loadHtml($html->getContent());
        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();
        // Write file to the desired path
        file_put_contents($pathing_pv, $output);
        // service send mail
        $mailer = $this->get('app.sendmail');

        $mailer->sendMail(
            'Nouvelle Facture ' . $facture->getNumero(),
            'rfo.mobile@hope3k.net', 'aaaimad.backup@gmail.com',
            'facture_created',
            ['facture' => $facture],
            $pathing_pv

        );
        return true;
    }

    public function generateFacturePdfEdit(Facture $facture)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository('AppBundle:Fiche')->find(1);

        $template = 'facture/pdf/print.html.twig';


//dompdf staffs
        $html = $this->render($template, array(
            'facture' => $facture,

            'mois' => $this->mois_convert($facture->getMois()),
            'fiche' => $fiche
        ));
        $dompdf = new Dompdf();
        $path_facture = $this->get('kernel')->getRootDir() . '/../web/factures/' . $facture->getClient()->getNom() . '/' . $facture->getYear() . '/' . $this->mois_convert($facture->getMois()) . '/';
        if (!file_exists($path_facture)) {
            mkdir($path_facture, 0777, true);
        }
        $pathing_pv = $path_facture . $facture->getNumero() . '_copy_' . date('d_m_Y_H_i') . '.pdf';
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        //dump($dompdf);
        $dompdf->loadHtml($html->getContent());
        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();
        // Write file to the desired path
        file_put_contents($pathing_pv, $output);
        // service send mail
        $mailer = $this->get('app.sendmail');

        $mailer->sendMail(
            'Facture Modifiée ' . $facture->getNumero(),
            'rfo.mobile@hope3k.net', 'aaaimad.backup@gmail.com',
            'facture_created',
            ['facture' => $facture],
            $pathing_pv

        );
        return true;
    }

    public function generateFacturePdfEditProjet(Facture $facture)
    {

//        dump($facture);
//        die();
        $em = $this->getDoctrine()->getManager();
        // AND l.nbjour>0 AND l.totalHt>0
        // orange
        if ($facture->getProjet()->getClient()->getNom() == 'MEDI TELECOM') {
            $itemss = $em->createQuery('
          SELECT p as ligne,SUM (l.nbjourVente) AS nbjours, SUM(l.totalHt) as total,SUM(l.totalTTC) as totalTTC ,p.vente as tjm   
          From AppBundle:LigneFacture l
          JOIN AppBundle:Projetconsultant p
          WHERE l.facture = :facture
          AND l.projetconsultant = p.id
          GROUP BY p.job
         ')->setParameter('facture', $facture)->execute();
            $items = [];
            foreach ($itemss as $item) {
                if ($item['nbjours'] != 0) {
                    $items[] = $item;

                }

            }

//  dump($items);
//            die();

            $template = 'facture/pdf/print_projet.html.twig';
        } else {

            if ($facture->getProjet()->getClient()->getId() == 14) {
                //Pcs
                $itemss = $em->createQuery('
          SELECT IDENTITY(p.job) as job,p as ligne,SUM (l.nbjour) as nbjours, SUM (l.totalHt) as total,SUM (l.totalTTC) as totalTTC ,p.vente as tjm    
          From AppBundle:LigneFacture l
          JOIN AppBundle:Projetconsultant p       
          WHERE l.facture = :facture      
          AND l.projetconsultant = p.id
          AND l.nbjour>0 AND l.totalHt>0
          GROUP By tjm
                    
          ')->setParameter('facture', $facture)->execute();
                $items = [];
                foreach ($itemss as $item) {
                    if ($item['nbjours'] != 0) {
                        $items[] = $item;

                    }

                }

//            dump($items);


            } else {
                //Other clients
                $items = $em->createQuery('
          SELECT p as ligne,l.nbjour as nbjours, l.totalHt as total,l.totalTTC as totalTTC   From AppBundle:LigneFacture l
          JOIN AppBundle:Projetconsultant p
          WHERE l.facture = :facture
          AND l.nbjour > 0
          AND l.projetconsultant = p.id             
          ')->setParameter('facture', $facture)->execute();
            }
        }

        $fiche = $em->getRepository('AppBundle:Fiche')->find(1);
        $template = 'facture/pdf/print_projet.html.twig';
        //dompdf staffs
        $html = $this->render($template, array(
            'facture' => $facture,

            'items' => $items,

            'mois' => $this->mois_convert($facture->getMois()),
            'fiche' => $fiche
        ));
        $dompdf = new Dompdf();
        $path_facture = $this->get('kernel')->getRootDir() . '/../web/factures/' . $facture->getClient()->getNom() . '/' . $facture->getYear() . '/' . $this->mois_convert($facture->getMois()) . '/';
        if (!file_exists($path_facture)) {
            mkdir($path_facture, 0777, true);
        }
        $pathing_pv = $path_facture . $facture->getNumero() . '_copy_' . date('d_m_Y_H_i') . '.pdf';
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        //dump($dompdf);
        $dompdf->loadHtml($html->getContent());
        // Render the HTML as PDF
        $dompdf->render();
        // Store PDF Binary Data
        $output = $dompdf->output();
        // Write file to the desired path
        file_put_contents($pathing_pv, $output);
        // service send mail
        $mailer = $this->get('app.sendmail');
        $mailer->sendMail(
            'Facture Modifiée ' . $facture->getNumero(),
            'rfo.mobile@hope3k.net', 'aaaimad.backup@gmail.com',
            'facture_created',
            ['facture' => $facture],
            $pathing_pv

        );
//        dump($facture);
//        die();
        return true;
    }

    public function generateFacturePdf2(Facture $facture)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository('AppBundle:Fiche')->find(1);
        $template = 'facture/pdf/print.html.twig';
//dompdf staffs
        $html = $this->render($template, array(
            'facture' => $facture,
            'mois' => $this->mois_convert($facture->getMois()),
            'fiche' => $fiche
        ));
        $dompdf = new Dompdf();
        $path_facture = $this->get('kernel')->getRootDir() . '/../web/factures/' . $facture->getClient()->getNom() . '/' . $facture->getYear() . '/' . $this->mois_convert($facture->getMois()) . '/';
        if (!file_exists($path_facture)) {
            mkdir($path_facture, 0777, true);
        }
        $pathing_pv = $path_facture . $facture->getNumero() . '.pdf';
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        //dump($dompdf);
        $dompdf->loadHtml($html->getContent());
        // Render the HTML as PDF
        $dompdf->render();
        // Store PDF Binary Data
        $output = $dompdf->output();
        // Write file to the desired path
        file_put_contents($pathing_pv, $output);
        // service send mail
        $mailer = $this->get('app.sendmail');
        $mailer->sendMail(
            'Nouvelle Facture ' . $facture->getNumero(),
            'rfo.mobile@hope3k.net', 'aaaimad.backup@gmail.com',
            'facture_created',
            ['facture' => $facture],
            $pathing_pv
        );
        return true;
    }

    public
    function generatePdf(Bcfournisseur $bcfournisseur)
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
            $html = $this->render('bcfournisseur/pdf/print.html.twig', array(
                'bcfournisseur' => $bcfournisseur,
                'fiche' => $fiche,
                'mois' => mois_convert($bcfournisseur->getMois()),
                'nb' => $nb,
            ));

        } else {
            $html = $this->render('bcfournisseur/pdf/print_projet.html.twig', array(
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

    private function checkNumFacture($numero)
    {

        $em = $this->getDoctrine()->getManager();
        $factureNum = $em->getRepository('AppBundle:Facture')->findBy([
            'numero' => $numero
        ]);
//        dump($numero, count($factureNum));
        if (count($factureNum) == 0) {
            return true;


        } else {

            return false;
        }

    }


}
