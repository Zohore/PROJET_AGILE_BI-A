<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

// gedmo annotations

/**
 * Bcclient
 * @Gedmo\Loggable
 * @Vich\Uploadable
 * @ORM\Table(name="bcclient")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BcclientRepository")
 */
class Bcclient
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
     * @Gedmo\Versioned
     * @ORM\Column(name="code", type="string", length=255,nullable=true)
     */
    private $code;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="type", type="string", length=255,nullable=true)
     */
    private $type;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="n_contrat", type="string", length=255,nullable=true)
     */
    private $ncontrat;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="application", type="string", length=255,nullable=true)
     */
    private $application;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="avenant", type="string", length=255,nullable=true)
     */
    private $avenant;

    /**
     * @var \DateTime
     * @Gedmo\Versioned
     * @ORM\Column(name="date", type="date",nullable=true)
     */
    private $date;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="nbJrs", type="float", nullable=true)
     */
    private $nbJrs;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="expired", type="boolean", nullable=true)
     */
    private $expired = false;

    /**
     * @var int
     * @Gedmo\Versioned
     * @ORM\Column(name="nbJrsR", type="float", nullable=true)
     */
    private $nbJrsR;

    /**
     * @ORM\ManyToOne(targetEntity="Mission", inversedBy="bcclients")
     * @ORM\JoinColumn(name="id_mission", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $mission;
    /**
     * @ORM\ManyToOne(targetEntity="Projet", inversedBy="bcclients")
     * @ORM\JoinColumn(name="id_projet", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $projet;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Projetconsultant", mappedBy="bcclient",cascade={"persist", "remove"})
     */
    private $projetconsultants;
    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="bcclients")
     * @ORM\JoinColumn(name="id_client", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="Consultant", inversedBy="bcclients")
     * @ORM\JoinColumn(name="id_consultant", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $consultant;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     *
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
     * @var \DateTime
     * @Gedmo\Timestampable(on="change",field={"nbJrs"})
     * @ORM\Column(name="nbjr_changed_At",type="datetime",nullable=true)
     */
    private $nbrJoursChanged;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Facture", inversedBy="bcclients",cascade={"persist"})
     */
    private $factures;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="document_path", fileNameProperty="bcName")
     *
     * @var File
     */
    private $bcFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $bcName;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $bc
     */
    public function setBcFile(?File $bc = null): void
    {
        $this->bcFile = $bc;

        if (null !== $bc) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getBcFile(): ?File
    {
        return $this->bcFile;
    }

    public function setBcName(?string $bcName): void
    {
        $this->bcName = $bcName;
    }

    public function getBcName(): ?string
    {
        return $this->bcName;
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
     * Set code
     *
     * @param string $code
     *
     * @return Bcclient
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Bcclient
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
     * Set nbJrs
     *
     * @param float $nbJrs
     *
     * @return Bcclient
     */
    public function setNbJrs($nbJrs)
    {
        $this->nbJrs = $nbJrs;

        return $this;
    }

    /**
     * Get nbJrs
     *
     * @return float
     */
    public function getNbJrs()
    {
        return $this->nbJrs;
    }

    /**
     * Set nbJrsR
     *
     * @param float $nbJrsR
     *
     * @return Bcclient
     */
    public function setNbJrsR($nbJrsR)
    {
        $this->nbJrsR = $nbJrsR;

        return $this;
    }

    /**
     * Get nbJrsR
     *
     * @return float
     */
    public function getNbJrsR()
    {
        return $this->nbJrsR;
    }


    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Bcclient
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
     * Set consultant
     *
     * @param \AppBundle\Entity\Consultant $consultant
     *
     * @return Bcclient
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

    public function __toString()
    {
        $string = '';

        if ($this->getCode()) {

            $string = $this->getCode();
        } else {
            if ($this->getApplication()) {

                $string = $this->getApplication();
            }
            if ($this->getNcontrat()) {

                $string = $this->getNcontrat();
            }
            if ($this->getAvenant()) {

                $string = $this->getAvenant();
            }

        }


        return $string;

    }


    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Bcclient
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
     * @return Bcclient
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
     * Set type
     *
     * @param string $type
     *
     * @return Bcclient
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set ncontrat
     *
     * @param string $ncontrat
     *
     * @return Bcclient
     */
    public function setNcontrat($ncontrat)
    {
        $this->ncontrat = $ncontrat;

        return $this;
    }

    /**
     * Get ncontrat
     *
     * @return string
     */
    public function getNcontrat()
    {
        return $this->ncontrat;
    }

    /**
     * Set application
     *
     * @param string $application
     *
     * @return Bcclient
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get application
     *
     * @return string
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set avenant
     *
     * @param string $avenant
     *
     * @return Bcclient
     */
    public function setAvenant($avenant)
    {
        $this->avenant = $avenant;

        return $this;
    }

    /**
     * Get avenant
     *
     * @return string
     */
    public function getAvenant()
    {
        return $this->avenant;
    }

    /**
     * Add projetconsultant
     *
     * @param \AppBundle\Entity\Projetconsultant $projetconsultant
     *
     * @return Bcclient
     */
    public function addProjetconsultant(\AppBundle\Entity\Projetconsultant $projetconsultant)
    {
        $this->projetconsultants[] = $projetconsultant;

        return $this;
    }

    /**
     * Remove projetconsultant
     *
     * @param \AppBundle\Entity\Projetconsultant $projetconsultant
     */
    public function removeProjetconsultant(\AppBundle\Entity\Projetconsultant $projetconsultant)
    {
        $this->projetconsultants->removeElement($projetconsultant);
    }

    /**
     * Get projetconsultants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjetconsultants()
    {
        return $this->projetconsultants;
    }

    /**
     * Set nbrJoursChanged
     *
     * @param \DateTime $nbrJoursChanged
     *
     * @return Bcclient
     */
    public function setNbrJoursChanged($nbrJoursChanged)
    {
        $this->nbrJoursChanged = $nbrJoursChanged;

        return $this;
    }

    /**
     * Get nbrJoursChanged
     *
     * @return \DateTime
     */
    public function getNbrJoursChanged()
    {
        return $this->nbrJoursChanged;
    }


    /**
     * Set projet
     *
     * @param \AppBundle\Entity\Projet $projet
     *
     * @return Bcclient
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
     * Set mission
     *
     * @param \AppBundle\Entity\Mission $mission
     *
     * @return Bcclient
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
     * Add facture
     *
     * @param \AppBundle\Entity\Facture $facture
     *
     * @return Bcclient
     */
    public function addFacture(\AppBundle\Entity\Facture $facture)
    {
        if (!$this->factures->contains($facture)) {
            $this->factures[] = $facture;
        }
        return $this;
    }

    /**
     * Remove facture
     *
     * @param \AppBundle\Entity\Facture $facture
     */
    public function removeFacture(\AppBundle\Entity\Facture $facture)
    {
        $this->factures->removeElement($facture);
    }

    /**
     * Get factures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFactures()
    {
        return $this->factures;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->projetconsultants = new \Doctrine\Common\Collections\ArrayCollection();
        $this->factures = new \Doctrine\Common\Collections\ArrayCollection();
        $this->nbJrsR = $this->nbJrs;
        $this->type = 'DIRECT';
        $this->expired = false;

    }


    /**
     * Set expired
     *
     * @param boolean $expired
     *
     * @return Bcclient
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean
     */
    public function getExpired()
    {
        return $this->expired;
    }
}
