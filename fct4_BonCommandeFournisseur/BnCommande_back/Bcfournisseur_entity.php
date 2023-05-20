<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Bcfournisseur
 *
 * @ORM\Table(name="bcfournisseur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BcfournisseurRepository")
 * @Vich\Uploadable
 * @Gedmo\Loggable
 */
class Bcfournisseur
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
     * @Gedmo\Versioned
     * @ORM\Column(name="venteHT", type="float", nullable=true)
     */
    private $venteHT;
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
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="tjmAchat", type="float", nullable=true)
     */
    private $tjmAchat;
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
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="factureFournisseur", type="string", length=255, nullable=true)
     */
    private $factureFournisseur;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;


    /**
     * @var \DateTime
     * @Gedmo\Versioned
     * @ORM\Column(name="date", type="date",nullable=true)
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="Fournisseur", inversedBy="bcfournisseurs")
     * @ORM\JoinColumn(name="id_fournisseur", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $fournisseur;
    /**
     * @ORM\ManyToOne(targetEntity="Consultant", inversedBy="bcfournisseurs")
     * @ORM\JoinColumn(name="id_consultant", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $consultant;
    /**
     * @ORM\ManyToOne(targetEntity="Mission", inversedBy="bcfournisseurs")
     * @ORM\JoinColumn(name="id_mission", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $mission;
    /**
     * @ORM\ManyToOne(targetEntity="Projet", inversedBy="bcfournisseurs")
     * @ORM\JoinColumn(name="id_projet", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $projet;
    /**
     * @ORM\ManyToOne(targetEntity="Facture", inversedBy="bcfournisseurs")
     * @ORM\JoinColumn(name="id_facture", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $facture;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Virement", mappedBy="bcfournisseur",cascade={"persist", "remove"})
     */
    private $virements;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Facturefournisseur", mappedBy="bcfournisseur",cascade={"persist", "remove"})
     */
    private $facturefournisseurs;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FactureHsup", mappedBy="bcfournisseur",cascade={"persist", "remove"})
     */
    private $heures;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LigneFacture", mappedBy="bcfournisseur",cascade={"persist", "remove"})
     */
    private $lignes;

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
     * @param float $achat
     *
     * @return Bcfournisseur
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
     * @return Bcfournisseur
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
     * @return Bcfournisseur
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
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_ceation", type="datetime", nullable=true)
     */
    private $createdAt;
    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     *
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Bcfournisseur
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
     * @return Bcfournisseur
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
     * @return Bcfournisseur
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

        $this->createdAt = new \DateTime('now');
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * Add virment
     *
     * @param \AppBundle\Entity\Virement $virment
     *
     * @return Bcfournisseur
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

            return 'BC_F_' . '--' . $this->getMois() . '/' . $this->getYear();

        }

    }

    /**
     * Set achatHT
     *
     * @param float $achatHT
     *
     * @return Bcfournisseur
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
     * @return Bcfournisseur
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
     * @return Bcfournisseur
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
     * Set nbjours
     *
     * @param float $nbjours
     *
     * @return Bcfournisseur
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
     * @return Bcfournisseur
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
     * @return Bcfournisseur
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
     * @return Bcfournisseur
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
     * Add virement
     *
     * @param \AppBundle\Entity\Virement $virement
     *
     * @return Bcfournisseur
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
     * Add facturefournisseur
     *
     * @param \AppBundle\Entity\Bcfournisseur $facturefournisseur
     *
     * @return Bcfournisseur
     */
    public function addFacturefournisseur(\AppBundle\Entity\Bcfournisseur $facturefournisseur)
    {
        $this->facturefournisseurs[] = $facturefournisseur;

        return $this;
    }

    /**
     * Remove facturefournisseur
     *
     * @param \AppBundle\Entity\Bcfournisseur $facturefournisseur
     */
    public function removeFacturefournisseur(\AppBundle\Entity\Bcfournisseur $facturefournisseur)
    {
        $this->facturefournisseurs->removeElement($facturefournisseur);
    }

    /**
     * Get facturefournisseurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFacturefournisseurs()
    {
        return $this->facturefournisseurs;
    }

    /**
     * Set venteHT
     *
     * @param float $venteHT
     *
     * @return Bcfournisseur
     */
    public function setVenteHT($venteHT)
    {
        $this->venteHT = $venteHT;

        return $this;
    }

    /**
     * Get venteHT
     *
     * @return float
     */
    public function getVenteHT()
    {
        return $this->venteHT;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Bcfournisseur
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set consultant
     *
     * @param \AppBundle\Entity\Consultant $consultant
     *
     * @return Bcfournisseur
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
     * @return Bcfournisseur
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
     * @return Bcfournisseur
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
     * Set facture
     *
     * @param \AppBundle\Entity\Facture $facture
     *
     * @return Bcfournisseur
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
     * Add ligne
     *
     * @param \AppBundle\Entity\LigneFacture $ligne
     *
     * @return Bcfournisseur
     */
    public function addLigne(\AppBundle\Entity\LigneFacture $ligne)
    {
        $this->lignes[] = $ligne;

        return $this;
    }

    /**
     * Remove ligne
     *
     * @param \AppBundle\Entity\LigneFacture $ligne
     */
    public function removeLigne(\AppBundle\Entity\LigneFacture $ligne)
    {
        $this->lignes->removeElement($ligne);
    }

    /**
     * Get lignes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLignes()
    {
        return $this->lignes;
    }

    /**
     * Set tjmAchat
     *
     * @param float $tjmAchat
     *
     * @return Bcfournisseur
     */
    public function setTjmAchat($tjmAchat)
    {
        $this->tjmAchat = $tjmAchat;

        return $this;
    }

    /**
     * Get tjmAchat
     *
     * @return float
     */
    public function getTjmAchat()
    {
        return $this->tjmAchat;
    }

    public function getCountVirementsExecutes()
    {

        return $this->getVirements()->filter(function (Virement $virement) {

            return $virement->getEtat() == 'executé';
        });
    }
}
