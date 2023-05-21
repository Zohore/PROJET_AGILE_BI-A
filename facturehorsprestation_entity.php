<?php

namespace AppBundle\Entity;

use AppBundle\Controller\FactureController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * facturehorsprestation
 * @Gedmo\Loggable
 * @ORM\Table(name="facturehorsprestation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\facturehorsprestationRepository")
 * @Vich\Uploadable
 */
class facturehorsprestation
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
     *
     * @ORM\Column(name="achatHT", type="float", nullable=true)
     */
    private $achatHT;

    /**
     * @var float
     *
     * @ORM\Column(name="nbjours", type="float", nullable=true)
     */
    private $nbjours;

    /**
     * @var int
     *
     * @ORM\Column(name="mois", type="integer", nullable=true)
     */
    private $mois;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    private $year;
    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var float
     *
     * @ORM\Column(name="achatTTC", type="float", nullable=true)
     */
    private $achatTTC;

    /**
     * @var float
     *
     * @ORM\Column(name="taxe", type="float", nullable=true)
     */
    private $taxe;

    
    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=255, nullable=true)
     */
    private $etat;

    

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=255, nullable=true)
     */
    private $numero;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=500, nullable=true)
     */
    private $designation;
    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Fournisseur", inversedBy="facturehorsprestation")
     * @ORM\JoinColumn(name="id_fournisseur", referencedColumnName="id")
     */
    private $fournisseur;
    
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Virement", mappedBy="facturehorsprestation",cascade={"persist", "remove"})
     */
    private $virements;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set achatHT
     *
     * @param float $achatHT
     *
     * @return facturehorsprestation
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
     * Set nbjours
     *
     * @param float $nbjours
     *
     * @return facturehorsprestation
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
     * @return facturehorsprestation
     */
    public function setMois($mois)
    {
        $this->mois = $mois;

        return $this;
    }

    /**
     * Get mois
     *
     * @return int
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
     * @return facturehorsprestation
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set achatTTC
     *
     * @param float $achatTTC
     *
     * @return facturehorsprestation
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
     * Set fournisseur
     *
     * @param \AppBundle\Entity\Fournisseur $fournisseur
     *
     * @return facturehorsprestation
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
     * Set taxe
     *
     * @param float $taxe
     *
     * @return facturehorsprestation
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
     * Set facturehorsprestation
     *
     * @param string $facturehorsprestation
     *
     * @return facturehorsprestation
     */
    public function setfacturehorsprestation($facturehorsprestation)
    {
        $this->facturehorsprestation= $facturehorsprestation;

        return $this;
    }

    /**
     * Get facturehorsprestation
     *
     * @return string
     */
    public function getfacturehorsprestation()
    {
        return $this->facturehorsprestation;
    }

    /**
     * Set etat
     *
     * @param string $etat
     *
     * @return facturehorsprestation
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
     * Set numero
     *
     * @param string $numero
     *
     * @return facturehorsprestation
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return facturehorsprestation
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
     * Set designation
     *
     * @param string $designation
     *
     * @return facturehorsprestation
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get designation
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
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
     * @return facturehorsprestation
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
     *Constructor
     */
    public function __construct()
    {
        $this->virments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setEtat('non payé');
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * Add virment
     *
     * @param \AppBundle\Entity\Virement $virment
     *
     * @return facturehorsprestation
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
    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_creation", type="datetime", nullable=true)
     */
    private $createdAt;
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return facturehorsprestation
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
    
   


}

