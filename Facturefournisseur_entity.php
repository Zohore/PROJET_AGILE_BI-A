<?php

namespace AppBundle\Entity;

use AppBundle\Controller\FactureController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Facturefournisseur
 * @Gedmo\Loggable
 * @ORM\Table(name="facture_fournisseur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FacturefournisseurRepository")
 * @Vich\Uploadable
 */
class Facturefournisseur
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
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="achatHT", type="float", nullable=true)
     */
    private $achatHT;
    /**
     * @var float
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="nb_jours", type="float", nullable=true)
     */
    private $nbjours;
    /**
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="mois", type="integer", nullable=true)
     */
    private $mois;
    /**
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    private $year;
    /**
     * @var float
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="achatTTC", type="float", nullable=true)
     */
    private $achatTTC;
    /**
     * @var float
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="taxe", type="float", nullable=true)
     */
    private $taxe;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="factureFournisseur", type="string", length=255, nullable=true)
     */
    private $factureFournisseur;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="etat", type="string", length=255, nullable=true)
     */
    private $etat;
    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="numero", type="string", length=255, nullable=true)
     */
    private $numero;

    /**
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="date", type="date",nullable=true)
     */
    private $date;

    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Fournisseur", inversedBy="facturefournisseurs")
     * @ORM\JoinColumn(name="id_fournisseur", referencedColumnName="id")
     */
    private $fournisseur;
    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Facture", inversedBy="facturefournisseurs")
     * @ORM\JoinColumn(name="id_facture", referencedColumnName="id")
     */
    private $facture;
    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Bcfournisseur", inversedBy="facturefournisseurs",cascade={"persist"})
     * @ORM\JoinColumn(name="id_bcfournisseur", referencedColumnName="id")
     */
    private $bcfournisseur;
    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Mission", inversedBy="facturefournisseurs")
     * @ORM\JoinColumn(name="id_mission", referencedColumnName="id")
     */
    private $mission;
    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Projet", inversedBy="facturefournisseurs")
     * @ORM\JoinColumn(name="id_projet", referencedColumnName="id")
     */
    private $projet;
    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Consultant", inversedBy="facturefournisseurs")
     * @ORM\JoinColumn(name="id_consultant", referencedColumnName="id")
     */
    private $consultant;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Virement", mappedBy="facturefournisseur",cascade={"persist", "remove"})
     */
    private $virements;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FactureHsup", mappedBy="facturefournisseur",cascade={"persist", "remove"})
     */
    private $heures;

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
     * Set achat
     *
     * @ float $achat
     *
     * @return Facturefournisseur
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
     * Set factureFournisseur
     *
     * @param string $factureFournisseur
     *
     * @return Facturefournisseur
     */
    public function setFacturefournisseur($factureFournisseur)
    {
        $this->factureFournisseur = $factureFournisseur;

        return $this;
    }

    /**
     * Get factureFournisseur
     *
     * @return string
     */
    public function getFacturefournisseur()
    {
        return $this->factureFournisseur;
    }

    /**
     * Set etat
     *
     * @param string $etat
     *
     * @return Facturefournisseur
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Facturefournisseur
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="document_path", fileNameProperty="documentName")
     *
     * @var File
     */
    private $documentFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $documentName;


    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $document
     */
    public function setDocumentFile(?File $document = null): void
    {
        $this->documentFile = $document;

        if (null !== $document) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getDocumentFile(): ?File
    {
        return $this->documentFile;
    }

    public function setDocumentName(?string $documentName): void
    {
        $this->documentName = $documentName;
    }

    public function getDocumentName(): ?string
    {
        return $this->documentName;
    }

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_ceation", type="datetime", nullable=true)
     */
    private $createdAt;


    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Facturefournisseur
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Facturefournisseur
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set fournisseur
     *
     * @param \AppBundle\Entity\Fournisseur $fournisseur
     *
     * @return Facturefournisseur
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
     * Constructor
     */
    public function __construct()
    {
        $this->virments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->heures = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setEtat('non payé');
        $this->setCreatedAt(new \DateTime());
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
     * Get virments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVirments()
    {
        return $this->virments;
    }

    public function __toString()
    {
        if ($this->getFournisseur()) {
            return $this->getFournisseur()->getNom() . '--' . $this->getMois() . '/' . $this->getYear();


        } else {
            return 'Facture_F_' . '--' . $this->getMois() . '/' . $this->getYear();


            
        }
    }

    /**
     * Set achatHT
     *
     * @param float $achatHT
     *
     * @return Facturefournisseur
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
     * @return Facturefournisseur
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
     * @return Facturefournisseur
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
     * Set mois
     *
     * @param integer $mois
     *
     * @return Facturefournisseur
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
     * @return Facturefournisseur
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
     * Set mission
     *
     * @param \AppBundle\Entity\Mission $mission
     *
     * @return Facturefournisseur
     */
    public function setMission(\AppBundle\Entity\Mission $mission = null)
    {
        $this->mission = $mission;

        return $this;
    }

    /**
     * Get mission
     *
     * @return \AppBundle\Entity\Mission
     */
    public function getMission()
    {
        return $this->mission;
    }


    /**
     * Set bcfournisseur
     *
     * @param \AppBundle\Entity\Bcfournisseur $bcfournisseur
     *
     * @return Facturefournisseur
     */
    public function setBcfournisseur(\AppBundle\Entity\Bcfournisseur $bcfournisseur = null)
    {
        $this->bcfournisseur = $bcfournisseur;

        return $this;
    }

    /**
     * Get bcfournisseur
     *
     * @return \AppBundle\Entity\Bcfournisseur
     */
    public function getBcfournisseur()
    {
        return $this->bcfournisseur;
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return Facturefournisseur
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set consultant
     *
     * @param \AppBundle\Entity\Consultant $consultant
     *
     * @return Facturefournisseur
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
     * Set projet
     *
     * @param \AppBundle\Entity\Projet $projet
     *
     * @return Facturefournisseur
     */
    public function setProjet(\AppBundle\Entity\Projet $projet = null)
    {
        $this->projet = $projet;

        return $this;
    }

    /**
     * Get projet
     *
     * @return \AppBundle\Entity\Projet
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * Add heure
     *
     * @param \AppBundle\Entity\FactureHsup $heure
     *
     * @return Facturefournisseur
     */
    public function addHeure(\AppBundle\Entity\FactureHsup $heure)
    {
        $this->heures[] = $heure;

        return $this;
    }

    /**
     * Remove heure
     *
     * @param \AppBundle\Entity\FactureHsup $heure
     */
    public function removeHeure(\AppBundle\Entity\FactureHsup $heure)
    {
        $this->heures->removeElement($heure);
    }

    /**
     * Get heures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHeures()
    {
        return $this->heures;
    }

    /**
     * Add virement
     *
     * @param \AppBundle\Entity\Virement $virement
     *
     * @return Facturefournisseur
     */
    public function addVirement(\AppBundle\Entity\Virement $virement)
    {
        $this->virements[] = $virement;

        return $this;
    }

    /**
     * Remove virement
     *
     * @param \AppBundle\Entity\Virement $virement
     */
    public function removeVirement(\AppBundle\Entity\Virement $virement)
    {
        $this->virements->removeElement($virement);
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

    /**
     * Set facture
     *
     * @param \AppBundle\Entity\Facture $facture
     *
     * @return Facturefournisseur
     */
    public function setFacture(\AppBundle\Entity\Facture $facture = null)
    {
        $this->facture = $facture;

        return $this;
    }

    /**
     * Get facture
     *
     * @return \AppBundle\Entity\Facture
     */
    public function getFacture()
    {
        return $this->facture;
    }

    /**
     * Set nbjours
     *
     * @param float $nbjours
     *
     * @return Facturefournisseur
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
}
