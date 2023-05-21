<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Facture
 *
 * @ORM\Table(name="facture")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactureRepository")
 * @Vich\Uploadable
 * @Gedmo\Loggable
 */
class Facture
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
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=255, nullable=true)
     */
    private $etat;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="numero", type="string", length=255, nullable=true)
     */
    private $numero;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="total_lettre", type="string", length=255, nullable=true)
     */
    private $totalLettre;
    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="devise", type="boolean", nullable=true)
     */
    private $devise;
    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="avoir", type="boolean", nullable=true)
     */
    private $avoir;
    /**
     * @var integer
     * @Gedmo\Versioned
     * @ORM\Column(name="nb_jours", type="float", nullable=true)
     */
    private $nbjour;
    /**
     * @var integer
     * @Gedmo\Versioned
     * @ORM\Column(name="nb_jours_T", type="float", nullable=true)
     */
    private $nbjourT;

    /**
     * @var integer
     * @Gedmo\Versioned
     * @ORM\Column(name="mois", type="integer", nullable=true)
     */
    private $mois;
    /**
     * @var integer
     * @Gedmo\Versioned
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    private $year;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="total_HT", type="float", nullable=true)
     */
    private $totalHT;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="total_TTC", type="float", nullable=true)
     */
    private $totalTTC;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="total_DH", type="float", nullable=true)
     */
    private $totalDH;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="taxe", type="float", nullable=true)
     */
    private $taxe;

    /**
     * @ORM\ManyToOne(targetEntity="Consultant", inversedBy="factures")
     * @ORM\JoinColumn(name="id_consultant", referencedColumnName="id")
     */
    private $consultant;
    /**
     * @ORM\ManyToOne(targetEntity="Comptebancaire", inversedBy="factures")
     * @ORM\JoinColumn(name="id_compte_bancaire", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $comptebancaire;

    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="factures")
     * @ORM\JoinColumn(name="id_client", referencedColumnName="id")
     */
    private $client;
    /**
     * @ORM\ManyToOne(targetEntity="Projet", inversedBy="factures")
     * @ORM\JoinColumn(name="id_projet", referencedColumnName="id")
     */
    private $projet;
    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Mission", inversedBy="factures")
     * @ORM\JoinColumn(name="id_mission", referencedColumnName="id")
     */
    private $mission;
    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Facture", inversedBy="avoirs")
     * @ORM\JoinColumn(name="id_facture", referencedColumnName="id")
     */
    private $facture;

    /**
     * @var \DateTime
     * @Gedmo\Versioned
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;
    /**
     * @var \DateTime
     * @Gedmo\Versioned
     * @ORM\Column(name="date_depot", type="date", nullable=true)
     */
    private $datedepot;
    /**
     * @var \DateTime
     * @Gedmo\Versioned
     * @ORM\Column(name="date_paiement", type="date", nullable=true)
     */
    private $datepaiement;
    /**
     * @var \DateTime
     * @Gedmo\Versioned
     * @ORM\Column(name="date_timesheet", type="date", nullable=true)
     */
    private $datetimesheet;
    /**
     * @var \DateTime
     * @Gedmo\Versioned
     * @ORM\Column(name="date_debut", type="date", nullable=true)
     */
    private $dateDebut;
    /**
     * @var \DateTime
     * @Gedmo\Versioned
     * @ORM\Column(name="date_fin", type="date", nullable=true)
     */
    private $dateFin;

    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="User", inversedBy="facturesajoutes")
     * @ORM\JoinColumn(name="id_added_by", referencedColumnName="id")
     */
    private $addedby;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="tjm", type="string", length=255, nullable=true)
     */
    private $tjm;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="facturesmodifies")
     * @ORM\JoinColumn(name="id_edited_by", referencedColumnName="id")
     */
    private $editedby;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_ceation", type="datetime", nullable=true)
     */
    private $createdAt;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LigneFacture", mappedBy="facture",cascade={"persist", "remove"})
     */
    private $lignes;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Facture", mappedBy="facture",cascade={"persist", "remove"})
     */
    private $avoirs;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Production", mappedBy="facture",cascade={"persist", "remove"})
     */
    private $productions;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Bcfournisseur", mappedBy="facture",cascade={"persist", "remove"})
     */
    private $bcfournisseurs;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Facturefournisseur", mappedBy="facture",cascade={"persist", "remove"})
     */
    private $facturefournisseurs;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FactureHsup", mappedBy="facture",cascade={"persist", "remove"})
     */
    private $facturehsups;
    /**
     * @ORM\ManyToMany (targetEntity="AppBundle\Entity\Bcclient",mappedBy="factures")
     */
    private $bcclients;
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set etat
     *
     * @param string $etat
     *
     * @return Facture
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
     * Set mois
     *
     * @param string $mois
     *
     * @return Facture
     */
    public function setMois($mois)
    {
        $this->mois = $mois;

        return $this;
    }

    /**
     * Get mois
     *
     * @return string
     */
    public function getMois()
    {
        return $this->mois;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Facture
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
     * Set consultant
     *
     * @param \AppBundle\Entity\Consultant $consultant
     *
     * @return Facture
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
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Facture
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set totalHT
     *
     * @param float $totalHT
     *
     * @return Facture
     */
    public function setTotalHT($totalHT)
    {
        $this->totalHT = $totalHT;

        return $this;
    }

    /**
     * Get totalHT
     *
     * @return float
     */
    public function getTotalHT()
    {
        return $this->totalHT;
    }

    /**
     * Set totalTTC
     *
     * @param float $totalTTC
     *
     * @return Facture
     */
    public function setTotalTTC($totalTTC)
    {
        $this->totalTTC = $totalTTC;

        return $this;
    }

    /**
     * Get totalTTC
     *
     * @return float
     */
    public function getTotalTTC()
    {
        return $this->totalTTC;
    }


    /**
     * Set mission
     *
     * @param \AppBundle\Entity\Mission $mission
     *
     * @return Facture
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
     * Set year
     *
     * @param string $year
     *
     * @return Facture
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set taxe
     *
     * @param float $taxe
     *
     * @return Facture
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
     * Set numero
     *
     * @param string $numero
     *
     * @return Facture
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Facture
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
     * @return Facture
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
     * Set totalDH
     *
     * @param float $totalDH
     *
     * @return Facture
     */
    public function setTotalDH($totalDH)
    {
        $this->totalDH = $totalDH;

        return $this;
    }

    /**
     * Get totalDH
     *
     * @return float
     */
    public function getTotalDH()
    {
        return $this->totalDH;
    }

    /**
     * Set comptebancaire
     *
     * @param \AppBundle\Entity\Comptebancaire $comptebancaire
     *
     * @return Facture
     */
    public function setComptebancaire(\AppBundle\Entity\Comptebancaire $comptebancaire = null)
    {
        $this->comptebancaire = $comptebancaire;

        return $this;
    }

    /**
     * Get comptebancaire
     *
     * @return \AppBundle\Entity\Comptebancaire
     */
    public function getComptebancaire()
    {
        return $this->comptebancaire;
    }


    /**
     * Add ligne
     *
     * @param \AppBundle\Entity\LigneFacture $ligne
     *
     * @return Facture
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
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return Facture
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return Facture
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }


    /**
     * Set projet
     *
     * @param \AppBundle\Entity\Projet $projet
     *
     * @return Facture
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

    public function __toString()
    {
        return '' . $this->getNumero();

    }


    /**
     * Add facturehsup
     *
     * @param \AppBundle\Entity\FactureHsup $facturehsup
     *
     * @return Facture
     */
    public function addFacturehsup(\AppBundle\Entity\FactureHsup $facturehsup)
    {
        $this->facturehsups[] = $facturehsup;

        return $this;
    }

    /**
     * Remove facturehsup
     *
     * @param \AppBundle\Entity\FactureHsup $facturehsup
     */
    public function removeFacturehsup(\AppBundle\Entity\FactureHsup $facturehsup)
    {
        $this->facturehsups->removeElement($facturehsup);
    }

    /**
     * Get facturehsups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFacturehsups()
    {
        return $this->facturehsups;
    }

    /**
     * Set nbjour
     *
     * @param float $nbjour
     *
     * @return Facture
     */
    public function setNbjour($nbjour)
    {
        $this->nbjour = $nbjour;

        return $this;
    }

    /**
     * Get nbjour
     *
     * @return float
     */
    public function getNbjour()
    {
        return $this->nbjour;
    }

    /**
     * Add production
     *
     * @param \AppBundle\Entity\Production $production
     *
     * @return Facture
     */
    public function addProduction(\AppBundle\Entity\Production $production)
    {
        $this->productions[] = $production;

        return $this;
    }

    /**
     * Remove production
     *
     * @param \AppBundle\Entity\Production $production
     */
    public function removeProduction(\AppBundle\Entity\Production $production)
    {
        $this->productions->removeElement($production);
    }

    /**
     * Get productions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductions()
    {
        return $this->productions;
    }

    /**
     * Add bcfournisseur
     *
     * @param \AppBundle\Entity\Bcfournisseur $bcfournisseur
     *
     * @return Facture
     */
    public function addBcfournisseur(\AppBundle\Entity\Bcfournisseur $bcfournisseur)
    {
        if (!$this->bcfournisseurs->contains($bcfournisseur)) {
            $this->bcfournisseurs[] = $bcfournisseur;
        }


        return $this;
    }

    /**
     * Remove bcfournisseur
     *
     * @param \AppBundle\Entity\Bcfournisseur $bcfournisseur
     */
    public function removeBcfournisseur(\AppBundle\Entity\Bcfournisseur $bcfournisseur)
    {
        $this->bcfournisseurs->removeElement($bcfournisseur);
    }

    /**
     * Get bcfournisseurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBcfournisseurs()
    {
        return $this->bcfournisseurs;
    }

    /**
     * Add facturefournisseur
     *
     * @param \AppBundle\Entity\Facturefournisseur $facturefournisseur
     *
     * @return Facture
     */
    public function addFacturefournisseur(\AppBundle\Entity\Facturefournisseur $facturefournisseur)
    {
        $this->facturefournisseurs[] = $facturefournisseur;

        return $this;
    }

    /**
     * Remove facturefournisseur
     *
     * @param \AppBundle\Entity\Facturefournisseur $facturefournisseur
     */
    public function removeFacturefournisseur(\AppBundle\Entity\Facturefournisseur $facturefournisseur)
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
     * Set addedby
     *
     * @param \AppBundle\Entity\User $addedby
     *
     * @return Facture
     */
    public function setAddedby(\AppBundle\Entity\User $addedby = null)
    {
        $this->addedby = $addedby;

        return $this;
    }

    /**
     * Get addedby
     *
     * @return \AppBundle\Entity\User
     */
    public function getAddedby()
    {
        return $this->addedby;
    }

    /**
     * Set editedby
     *
     * @param \AppBundle\Entity\User $editedby
     *
     * @return Facture
     */
    public function setEditedby(\AppBundle\Entity\User $editedby = null)
    {
        $this->editedby = $editedby;

        return $this;
    }

    /**
     * Get editedby
     *
     * @return \AppBundle\Entity\User
     */
    public function getEditedby()
    {
        return $this->editedby;
    }

    /**
     * Set datedepot
     *
     * @param \DateTime $datedepot
     *
     * @return Facture
     */
    public function setDatedepot($datedepot)
    {
        $this->datedepot = $datedepot;

        return $this;
    }

    /**
     * Get datedepot
     *
     * @return \DateTime
     */
    public function getDatedepot()
    {
        return $this->datedepot;
    }

    /**
     * Set datepaiement
     *
     * @param \DateTime $datepaiement
     *
     * @return Facture
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
     * Set datetimesheet
     *
     * @param \DateTime $datetimesheet
     *
     * @return Facture
     */
    public function setDatetimesheet($datetimesheet)
    {
        $this->datetimesheet = $datetimesheet;

        return $this;
    }

    /**
     * Get datetimesheet
     *
     * @return \DateTime
     */
    public function getDatetimesheet()
    {
        return $this->datetimesheet;
    }

    /**
     * Set devise
     *
     * @param boolean $devise
     *
     * @return Facture
     */
    public function setDevise($devise)
    {
        $this->devise = $devise;

        return $this;
    }

    /**
     * Get devise
     *
     * @return boolean
     */
    public function getDevise()
    {
        return $this->devise;
    }

    /**
     * Set tjm
     *
     * @param string $tjm
     *
     * @return Facture
     */
    public function setTjm($tjm)
    {
        $this->tjm = $tjm;

        return $this;
    }

    /**
     * Get tjm
     *
     * @return string
     */
    public function getTjm()
    {
        return $this->tjm;
    }

    /**
     * Add bcclient
     *
     * @param \AppBundle\Entity\Bcclient $bcclient
     *
     * @return Facture
     */
    public function addBcclient(\AppBundle\Entity\Bcclient $bcclient)
    {
        if (!$this->bcclients->contains($bcclient)) {
            $this->bcclients[] = $bcclient;
        }
        return $this;
    }

    /**
     * Remove bcclient
     *
     * @param \AppBundle\Entity\Bcclient $bcclient
     */
    public function removeBcclient(\AppBundle\Entity\Bcclient $bcclient)
    {
        $this->bcclients->removeElement($bcclient);
    }

    /**
     * Get bcclients
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBcclients()
    {
        return $this->bcclients;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lignes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->avoirs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->bcfournisseurs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->facturefournisseurs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->facturehsups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->bcclients = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = new \DateTime();

        $this->etat = 'non payé';
        $this->devise = false;
        $this->avoir = false;
    }


    /**
     * Set nbjourT
     *
     * @param float $nbjourT
     *
     * @return Facture
     */
    public function setNbjourT($nbjourT)
    {
        $this->nbjourT = $nbjourT;

        return $this;
    }

    /**
     * Get nbjourT
     *
     * @return float
     */
    public function getNbjourT()
    {
        return $this->nbjourT;
    }

    public function HasExecutedVirement()
    {
        $bcfournisseurs = $this->getBcfournisseurs()->filter(function (Bcfournisseur $bcfournisseur) {

            return $bcfournisseur->getCountVirementsExecutes()->count() >> 0;
        });

        if ($bcfournisseurs->count() >> 0) return true;
        else return false;


    }

    /**
     * Set totalLettre
     *
     * @param string $totalLettre
     *
     * @return Facture
     */
    public function setTotalLettre($totalLettre)
    {
        $this->totalLettre = $totalLettre;

        return $this;
    }

    /**
     * Get totalLettre
     *
     * @return string
     */
    public function getTotalLettre()
    {
        return $this->totalLettre;
    }

    /**
     * Set facture
     *
     * @param \AppBundle\Entity\Facture $facture
     *
     * @return Facture
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
     * Add avoir
     *
     * @param \AppBundle\Entity\Facture $avoir
     *
     * @return Facture
     */
    public function addAvoir(\AppBundle\Entity\Facture $avoir)
    {
        $this->avoirs[] = $avoir;

        return $this;
    }

    /**
     * Remove avoir
     *
     * @param \AppBundle\Entity\Facture $avoir
     */
    public function removeAvoir(\AppBundle\Entity\Facture $avoir)
    {
        $this->avoirs->removeElement($avoir);
    }

    /**
     * Get avoirs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAvoirs()
    {
        return $this->avoirs;
    }

    /**
     * Set avoir
     *
     * @param boolean $avoir
     *
     * @return Facture
     */
    public function setAvoir($avoir)
    {
        $this->avoir = $avoir;

        return $this;
    }

    /**
     * Get avoir
     *
     * @return boolean
     */
    public function getAvoir()
    {
        return $this->avoir;
    }
}
