<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\lettragefournisseur;
use AppBundle\Entity\Virement;
use AppBundle\Entity\Facturefournisseur;
use AppBundle\Entity\Fournisseur;
use AppBundle\Entity\Consultant;
use DateTime;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Query;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


/**
 * LettrageFournisseur controller.
 *
 * @Route("lettragefournisseur")
 */
class LettreageFournisseurController extends Controller
{
    /**
     * @Route("/index",name="lettragefournisseur_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lettragefournisseurs = $em->getRepository('AppBundle:lettragefournisseur')->findAll();

        return $this->render('LettreageFournisseur/index.html.twig', array(
            'lettragefournisseurs' => $lettragefournisseurs,
        ));
    }

    
    
   
}
