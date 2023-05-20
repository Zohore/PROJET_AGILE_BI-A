<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bcfournisseur;
use AppBundle\Entity\Facturefournisseur;
use AppBundle\Entity\Virement;
use AppBundle\Entity\lettragefournisseur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Handler\DownloadHandler;


/**
 * Facturefournisseur controller.
 *
 * @Route("facturefournisseur")
 */
class FacturefournisseurController extends Controller
{
    /**
     * Lists all facturefournisseur entities.
     *
     * @Route("/", name="facturefournisseur_index",options={"expose"=true}))
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $facturefournisseurs = $em->getRepository('AppBundle:Facturefournisseur')->findAll();
//        dump($facturefournisseurs);
        $facturefournisseurs_sans_facture = $em->getRepository('AppBundle:Facturefournisseur')->findBy([

            'documentName' => null
        ]);

        return $this->render('facturefournisseur/index_ajax.html.twig', array(
            'facturefournisseurs' => $facturefournisseurs,
            'facturefournisseurs_sans_facture' => $facturefournisseurs_sans_facture,
        ));
    }

    /**
     * Creates a new virement entity.
     *
     * @Route("/getfacturefournisseur", name="getfacturefournisseur")
     * @Method({"GET", "POST"})
     */
    public function getfacturefournisseurAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $facturefournisseurs = $em->getRepository('AppBundle:Facturefournisseur')->findAll();

        // $bcfournisseurs = array_reverse($bcfournisseurs);


        if (!empty($facturefournisseurs)) {

            foreach ($facturefournisseurs as $key => $facturefournisseur) {
                $arr[$key][0] = ' <label class="pos-rel center">
                <input type="checkbox" class="ace" value="' . $facturefournisseur->getId() . '" data-id="' . $facturefournisseur->getVirements()->count() . '">
                <span class="lbl"></span>
            </label>';


                $arr[$key][1] = $facturefournisseur->getNumero();

                if ($facturefournisseur->getFournisseur() == null)
                    $arr[$key][2] = '';
                else
                    $arr[$key][2] = $facturefournisseur->getFournisseur()->getNom();


                if ($facturefournisseur->getConsultant() == null)
                    $arr[$key][3] = '';
                else
                    $arr[$key][3] = $facturefournisseur->getConsultant()->getNom();

                if ($facturefournisseur->getDate() == null)
                    $arr[$key][4] = '';
                else
                    $arr[$key][4] = $facturefournisseur->getDate()->format('Y-m-d');

                if ($facturefournisseur->getMois() == null)
                    $arr[$key][5] = '--';
                else
                    $arr[$key][5] = str_pad($facturefournisseur->getMois(), 2, '0', STR_PAD_LEFT) . '/' . $facturefournisseur->getYear();

                if ($facturefournisseur->getNbjours() == null)
                    $arr[$key][6] = '';
                else
                    $arr[$key][6] = $facturefournisseur->getNbjours();


                if ($facturefournisseur->getAchatHT() == null)
                    $arr[$key][7] = '';
                else
                    $arr[$key][7] = number_format($facturefournisseur->getAchatHT(), 2, ',', ' ');

                if ($facturefournisseur->getAchatTTC() == null)
                    $arr[$key][8] = '--';
                else
                    $arr[$key][8] = number_format($facturefournisseur->getAchatTTC(), 2, ',', ' ');

                $arr[$key][9] = $facturefournisseur->getEtat();


                $arr[$key][10] = '<div class="hidden-sm hidden-xs action-buttons">
                <a class="blue"
                   href="' . $request->getBaseUrl() . '/facturefournisseur/' . $facturefournisseur->getId() . '"

                   title="voir">
                    <i class="ace-icon fa fa-search-plus bigger-160"></i>
                </a>
                 <a class="dark-10"
                   href="' . $request->getBaseUrl() . '/facturefournisseur/log/' . $facturefournisseur->getId() . '"

                   title="Historique">
                    <i class="ace-icon fa fa-history bigger-160"></i>
                </a>
                
                ';

                if (in_array('ROLE_MANAGER', $this->getUser()->getRoles(), true)) {
                    $arr[$key][10] = $arr[$key][10] . ' 
                    <a class="orange"
                    href="' . $request->getBaseUrl() . '/facturefournisseur/' . $facturefournisseur->getId() . '/edit"
                    title="modifier">
                     <i class="ace-icon fa fa-pencil bigger-160"></i>
                    </a>';
                }

                if ($facturefournisseur->getDocumentName() == null) {
                    $arr[$key][10] = $arr[$key][10] . ' <a title="Uploader Facture fournisseur" class="red"
                  
                    href="' . $request->getBaseUrl() . '/facturefournisseur/' . $facturefournisseur->getId() . '/add_facture">

                     <i class="ace-icon fa fa-cloud-upload bigger-160"></i>
                    </a>';
                } else {
                    $arr[$key][10] = $arr[$key][10] . ' <a title="Télécharger Facture fournisseur" class="purple"
             
                    href="' . $request->getBaseUrl() . '/../uploads/documents/' . $facturefournisseur->getDocumentName() . '">

                     <i class="ace-icon fa fa-download bigger-160"></i>
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
     * Lists all facturefournisseur entities.
     *
     * @Route("/sans_facture", name="facturefournisseur_sans")
     * @Method("GET")
     */
    public function sansfactureAction()
    {
        $em = $this->getDoctrine()->getManager();


        $facturefournisseurs = $em->getRepository('AppBundle:Facturefournisseur')->findBy([

            'documentName' => null
        ]);

        return $this->render('facturefournisseur/sans_facture.html.twig', array(
            'facturefournisseurs' => $facturefournisseurs,

        ));
    }


    /**
     * Creates a new facturefournisseur entity.
     *
     * @Route("/new", name="facturefournisseur_new")
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
        $facturefournisseur = new Facturefournisseur();
        $facturefournisseur->setYear($year);
        $facturefournisseur->setMois($mois);

        $form = $this->createForm('AppBundle\Form\FacturefournisseurType', $facturefournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $facturefournisseur->setEtat('non payé');
            $bcfournisseur = new Bcfournisseur();
            $virement = new Virement();
            $lettragefournisseur = new lettragefournisseur();
            $bcfournisseur->setFournisseur($facturefournisseur->getFournisseur());
            $bcfournisseur->setNbjours($bcfournisseur->getNbjours());
            $bcfournisseur->setConsultant($facturefournisseur->getConsultant());
            $bcfournisseur->setDate($facturefournisseur->getDate());
            $bcfournisseur->setMois($facturefournisseur->getMois());
            $bcfournisseur->setYear($facturefournisseur->getYear());
            $bcfournisseur->setAchatHT($facturefournisseur->getAchatHT());
            $bcfournisseur->setTaxe($facturefournisseur->getTaxe());
            $bcfournisseur->setAchatTTC($facturefournisseur->getAchatTTC());
            $facturefournisseur->setBcfournisseur($bcfournisseur);
            $lettragefournisseur->setFournisseur($facturefournisseur->getFournisseur());
            $lettragefournisseur->setNbjours($bcfournisseur->getNbjours());
            $lettragefournisseur->setEtat($facturefournisseur->getEtat());
            $lettragefournisseur->setnumerofacture($facturefournisseur->getnumero());
            $lettragefournisseur->setConsultant($facturefournisseur->getConsultant());
            $lettragefournisseur->setMois($facturefournisseur->getMois());
            $lettragefournisseur->setYear($facturefournisseur->getYear());
            $lettragefournisseur->setAchatHT($facturefournisseur->getAchatHT());
            $lettragefournisseur->setTaxe($facturefournisseur->getTaxe());
            $lettragefournisseur->setAchatTTC($facturefournisseur->getAchatTTC());
            $lettragefournisseur = $em->merge($lettragefournisseur);
            $virement->setEtat('en attente');
            $virement->setFacturefournisseur($facturefournisseur);
            
            $virement->setAchat($bcfournisseur->getAchatTTC());
            $virement->setDate($bcfournisseur->getDate());
            $virement->setConsultant($bcfournisseur->getConsultant());
            $virement->setBcfournisseur($bcfournisseur);
            
            $virement->setlettragefournisseur($lettragefournisseur);
            $lettragefournisseur->setdatefacture($virement->getDate());
            //$lettragefournisseur->setdatepaiement($virement->getVirementf()->getDate()->format('d/m/Y'));
            $em->persist($facturefournisseur);
            $em->flush();
            $em->persist($bcfournisseur);
            $em->flush();
            $em->persist($virement);
            $em->flush();
            $em->persist($lettragefournisseur);
            $em->flush();


            return $this->redirectToRoute('facturefournisseur_show', array('id' => $facturefournisseur->getId()));
        }

        return $this->render('facturefournisseur/new.html.twig', array(
            'facturefournisseur' => $facturefournisseur,
            'form' => $form->createView(),
        ));
    }

    public function downloadImageAction(File $image, DownloadHandler $downloadHandler): Response
    {
        $fileName = 'foo.png';

        return $downloadHandler->downloadObject($image, $fileField = 'imageFile', $objectClass = null, $fileName);
    }

    /**
     * Creates a new facturefournisseur entity.
     *
     * @Route("/ajouter", name="facturefournisseur_ajouter")
     * @Method({"GET", "POST"})
     */
    public function ajouterAction(Request $request)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $form = $this->createFormBuilder()
            ->add('numero')
            ->add('consultant', EntityType::class, array(
                'class' => 'AppBundle:Consultant',
                'multiple' => false,
                'placeholder' => '--',
                'required' => false,
                'label' => 'Consultant',
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false

                )
            ))
            ->add('fournisseur', EntityType::class, array(
                'class' => 'AppBundle:Fournisseur',
                'multiple' => false,
                'placeholder' => '--',
                'required' => true,
                'label' => 'Fournisseur',
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false
                )
            ))->add('nbjour')
            ->add('achatHT')
            ->add('taxe')
            ->add('achatTTC')
            ->add('documentFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => true,
                'label' => 'Facture Fournisseur'
                //   'delete_label' => 'form.label.delete',

            ])->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $facturefournisseur = new Facturefournisseur();
            $file = $form->get('documentFile');
//            $facturefournisseur->setDocumentFile($file);
//            $em->persist($facturefournisseur);
//            $em->flush();
//            dump($form->get('documentFile'), $facturefournisseur);
            die();
//            return $this->redirectToRoute('facturefournisseur_show', array('id' => $facturefournisseur->getId()));
        }

        return $this->render('facturefournisseur/ajouter.html.twig', array(
//            'facturefournisseur' => $facturefournisseur,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/log/{id}", name="facturefournisseur_log")
     * @Method("GET")
     */
    public function LogObjectAction(Facturefournisseur $object)
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
     * Finds and displays a facturefournisseur entity.
     *
     * @Route("/{id}", name="facturefournisseur_show")
     * @Method("GET")
     */
    public function showAction(Facturefournisseur $facturefournisseur)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $deleteForm = $this->createDeleteForm($facturefournisseur);

        return $this->render('facturefournisseur/show.html.twig', array(
            'facturefournisseur' => $facturefournisseur,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing facturefournisseur entity.
     *
     * @Route("/{id}/edit", name="facturefournisseur_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Facturefournisseur $facturefournisseur)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true) or in_array('ROLE_MANAGER', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');

        }
        //check role
        $operation = $this->get('app.operation');
        return $operation->checkAdmin();
        //end check
        $deleteForm = $this->createDeleteForm($facturefournisseur);
        $editForm = $this->createForm('AppBundle\Form\FacturefournisseurType', $facturefournisseur);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('facturefournisseur_edit', array('id' => $facturefournisseur->getId()));
        }


        return $this->render('facturefournisseur/edit.html.twig', array(
            'facturefournisseur' => $facturefournisseur,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing facturefournisseur entity.
     *
     * @Route("/{id}/add_facture", name="facturefournisseur_update")
     * @Method({"GET", "POST"})
     */
    public function updateAction(Request $request, Facturefournisseur $facturefournisseur)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true) or in_array('ROLE_MANAGER', $this->getUser()->getRoles(), true) or in_array('ROLE_MANAGER', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $deleteForm = $this->createDeleteForm($facturefournisseur);
        $editForm = $this->createForm('AppBundle\Form\Facturefournisseur1Type', $facturefournisseur);
        $editForm->handleRequest($request);
        $facturefournisseur->setEtat('Payé');
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('facturefournisseur_index');
        }

        return $this->render('facturefournisseur/edit.html.twig', array(
            'facturefournisseur' => $facturefournisseur,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a facturefournisseur entity.
     *
     * @Route("/{id}", name="facturefournisseur_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Facturefournisseur $facturefournisseur)
    {
        if (in_array('ROLE_COMPTABLE', $this->getUser()->getRoles(), true) or in_array('ROLE_CONS', $this->getUser()->getRoles(), true)) {
            return $this->redirectToRoute('error_access');
        }
        $form = $this->createDeleteForm($facturefournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {

                $em->remove($facturefournisseur);
                $em->flush();
            } else {

                return $this->redirectToRoute('error_access');
            }

        }

        return $this->redirectToRoute('facturefournisseur_index');
    }

    /**
     * Creates a form to delete a facturefournisseur entity.
     *
     * @param Facturefournisseur $facturefournisseur The facturefournisseur entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Facturefournisseur $facturefournisseur)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('facturefournisseur_delete', array('id' => $facturefournisseur->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
