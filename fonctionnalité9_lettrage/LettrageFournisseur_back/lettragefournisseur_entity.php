<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * lettragefournisseur
 *
 * @ORM\Table(name="lettragefournisseur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LettrageFournisseurRepository")
 * @Gedmo\Loggable
 */
class lettragefournisseur
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Fournisseur", inversedBy="lettragefournisseurs")
     * @ORM\JoinColumn(name="id_fournisseur", referencedColumnName="id")
     */
    private $fournisseur;
    /**

     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Consultant", inversedBy="lettragefournisseurs")
     * @ORM\JoinColumn(name="id_consultant", referencedColumnName="id")
     */
    private $consultant;
    /**

     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="facturehorsprestation", inversedBy="lettragefournisseurs")
     * @ORM\JoinColumn(name="id_facturehorsprestation", referencedColumnName="id")
     */
    private $facturehorsprestation;
 
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Virement", mappedBy="lettragefournisseur",cascade={"persist", "remove"})
     */
    private $virements;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="achatHT", type="float", nullable=true)
     */
    private $achatHT;
    
    /**
     * @var integer
     * @Gedmo\Versioned
     * @ORM\Column(name="nb_jours", type="float", nullable=true)
     */
    private $nbjours;
    /**
     * @var integer
     * @Gedmo\Versioned
     * @ORM\Column(name="mois", type="integer", nullable=true)
     */
    private $mois;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="etat", type="string", nullable=true)
     */
    private $etat;
    /**
     * @var integer
     * @Gedmo\Versioned
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    private $year;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="designation", type="string", nullable=true)
     */
    private $designation;
     /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="achatTTC", type="float", nullable=true)
     */
    private $achatTTC;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="taxe", type="float", nullable=true)
     */
    private $taxe;

    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Facturefournisseur", inversedBy="lettragefournisseurs")
     * @ORM\JoinColumn(name="id_facturefournisseur", referencedColumnName="id")
     */
    private $facturefournisseur;
    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="numerofacture", type="string", length=255, nullable=true)
     */
    private $numerofacture;

    /**
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="datefacture", type="date",nullable=true)
     */
    private $datefacture;

    /**
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="datepaiement", type="date",nullable=true)
     */
    private $datepaiement;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Set fournisseur
     *
     * @param \AppBundle\Entity\Fournisseur $fournisseur
     *
     * @return lettragefournisseur
     */
    public function setFournisseur(\AppBundle\Entity\Fournisseur $fournisseur = null)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return \AppBundle\Entity\Fournisseur
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }
    /**
     * Set consultant
     *
     * @param \AppBundle\Entity\Consultant $consultant
     *
     * @return lettragefournisseur
     */
    public function setConsultant(\AppBundle\Entity\Consultant $consultant = null)
    {
        $this->consultant = $consultant;

        return $this;
    }

    /**
     * Get consultant
     *
     * @return \AppBundle\Entity\Consultant
     */
    public function getConsultant()
    {
        return $this->consultant;
    }
     /**
     * Set facturefournisseur
     *
     * @param \AppBundle\Entity\Facturefournisseur $facturefournisseur
     *
     * @return lettragefournisseur
     */
    public function setFacturefournisseur(\AppBundle\Entity\Facturefournisseur $facturefournisseur = null)
    {
        $this->facturefournisseur = $facturefournisseur;

        return $this;
    }

    /**
     * Get facturefournisseur
     *
     * @return \AppBundle\Entity\Facturefournisseur
     */
    public function getFacturefournisseur()
    {
        return $this->facturefournisseur;
    }
     /**
     * Set achat
     *
     * @param float $achat
     *
     * @return lettragefournisseur
     */
    public function setAchat($achat)
    {
        $this->achat = $achat;

        return $this;
    }

    /**
     * Get achat
     *
     * @return float
     */
    public function getAchat()
    {
        return $this->achat;
    }

    /**
     * Set etat
     *
     * @param string $etat
     *
     * @return lettragefournisseur
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set datefacture
     *
     * @param \DateTime $datefacture
     *
     * @return lettragefournisseur
     */
    public function Setdatefacture($datefacture)
    {
        $this->datefacture = $datefacture;

        return $this;
    }

    /**
     * Get datefacture
     *
     * @return \DateTime
     */
    public function getDatefacture()
    {
        return $this->datefacture;
    }

    /**
     * Set datepaiement
     *
     * @param \DateTime $datepaiement
     *
     * @return lettragefournisseur
     */
    public function setDatepaiement($datepaiement)
    {
        $this->datepaiement = $datepaiement;

        return $this;
    }

    /**
     * Get datepaiement
     *
     * @return \DateTime
     */
    public function getDatepaiement()
    {
        return $this->datepaiement;
    }
    /**
     * Set achatHT
     *
     * @param float $achatHT
     *
     * @return lettragefournisseur
     */
    public function setAchatHT($achatHT)
    {
        $this->achatHT = $achatHT;

        return $this;
    }

    /**
     * Get achatHT
     *
     * @return float
     */
    public function getAchatHT()
    {
        return $this->achatHT;
    }

    /**
     * Set achatTTC
     *
     * @param float $achatTTC
     *
     * @return lettragefournisseur
     */
    public function setAchatTTC($achatTTC)
    {
        $this->achatTTC = $achatTTC;

        return $this;
    }

    /**
     * Get achatTTC
     *
     * @return float
     */
    public function getAchatTTC()
    {
        return $this->achatTTC;
    }

    /**
     * Set taxe
     *
     * @param float $taxe
     *
     * @return lettragefournisseur
     */
    public function setTaxe($taxe)
    {
        $this->taxe = $taxe;

        return $this;
    }

    /**
     * Get taxe
     *
     * @return float
     */
    public function getTaxe()
    {
        return $this->taxe;
    }
    /**
     * Set designation
     *
     * @param string $designation
     *
     * @return lettragefournisseur
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get designation
     *
     * @return designation
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set nbjours
     *
     * @param float $nbjours
     *
     * @return lettragefournisseur
     */
    public function setNbjours($nbjours)
    {
        $this->nbjours = $nbjours;

        return $this;
    }

    /**
     * Get nbjours
     *
     * @return float
     */
    public function getNbjours()
    {
        return $this->nbjours;
    }

    /**
     * Set mois
     *
     * @param integer $mois
     *
     * @return lettragefournisseur
     */
    public function setMois($mois)
    {
        $this->mois = $mois;

        return $this;
    }

    /**
     * Get mois
     *
     * @return integer
     */
    public function getMois()
    {
        return $this->mois;
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return lettragefournisseur
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }
    /**
     * Set numerofacture
     *
     * @param string $numerofacture
     *
     * @return lettragefournisseur
     */
    public function setNumerofacture($numerofacture)
    {
        $this->numerofacture = $numerofacture;

        return $this;
    }

    /**
     * Get numerofacture
     *
     * @return string
     */
    public function getNumerofacture()
    {
        return $this->numerofacture;
    }
    /**
     * Add virment
     *
     * @param \AppBundle\Entity\Virement $virment
     *
     * @return Facturefournisseur
     */
    public function addVirment(\AppBundle\Entity\Virement $virment)
    {
        $this->virments[] = $virment;

        return $this;
    }

    /**
     * Remove virment
     *
     * @param \AppBundle\Entity\Virement $virment
     */
    public function removeVirment(\AppBundle\Entity\Virement $virment)
    {
        $this->virments->removeElement($virment);
    }

    /**
     * Get virements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVirements()
    {
        return $this->virements;
    }
}

