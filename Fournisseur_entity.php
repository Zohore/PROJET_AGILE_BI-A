<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Fournisseur
 *
 * @ORM\Table(name="fournisseur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FournisseurRepository")
 * @Gedmo\Loggable
 */
class Fournisseur
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
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="tel", type="string", length=255, nullable=true)
     */
    private $tel;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="fax", type="string", length=255, nullable=true)
     */
    private $fax;

    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="adresse", type="text",nullable=true)
     */
    private $adresse;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="rc", type="string", length=255, nullable=true)
     */
    private $rc;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="ice", type="string", length=255, nullable=true)
     */
    private $ice;
/**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="iif", type="string", length=255, nullable=true)
     */
    private $iif;
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Mission", mappedBy="fournisseur",cascade={"persist", "remove"})
     */
    private $missions;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Bcfournisseur", mappedBy="fournisseur",cascade={"persist", "remove"})
     */
    private $bcfournisseurs;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\facturehorsprestation", mappedBy="fournisseur",cascade={"persist", "remove"})
     */
    private $facturehorsprestation;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Detailvirement", mappedBy="fournisseur",cascade={"persist", "remove"})
     */
    private $detailfournisseurs;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Facturefournisseur", mappedBy="fournisseur",cascade={"persist", "remove"})
     */
    private $facturefournisseurs;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Projetconsultant", mappedBy="fournisseur",cascade={"persist", "remove"})
     */
    private $projetconsultants;
    /**
     * @var string
     * @Gedmo\Versioned
     * @ORM\Column(name="rib", type="string", length=255, nullable=true)
     */
    private $rib;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Production", mappedBy="fournisseur",cascade={"persist", "remove"})
     */
    private $productions;

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
     * Set nom
     *
     * @param string $nom
     *
     * @return Fournisseur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set tel
     *
     * @param string $tel
     *
     * @return Fournisseur
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return Fournisseur
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Fournisseur
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set rib
     *
     * @param string $rib
     *
     * @return Fournisseur
     */
    public function setRib($rib)
    {
        $this->rib = $rib;

        return $this;
    }

    /**
     * Get rib
     *
     * @return string
     */
    public function getRib()
    {
        return $this->rib;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mission = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add mission
     *
     * @param \AppBundle\Entity\Mission $mission
     *
     * @return Fournisseur
     */
    public function addMission(\AppBundle\Entity\Mission $mission)
    {
        $this->mission[] = $mission;

        return $this;
    }

    /**
     * Remove mission
     *
     * @param \AppBundle\Entity\Mission $mission
     */
    public function removeMission(\AppBundle\Entity\Mission $mission)
    {
        $this->mission->removeElement($mission);
    }

    /**
     * Get mission
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMission()
    {
        return $this->mission;
    }

    public function __toString()
    {
        return $this->getNom();
    }

    /**
     * Set rc
     *
     * @param string $rc
     *
     * @return Fournisseur
     */
    public function setRc($rc)
    {
        $this->rc = $rc;

        return $this;
    }

    /**
     * Get rc
     *
     * @return string
     */
    public function getRc()
    {
        return $this->rc;
    }

    /**
     * Set ice
     *
     * @param string $ice
     *
     * @return Fournisseur
     */
    public function setIce($ice)
    {
        $this->ice = $ice;

        return $this;
    }

    /**
     * Get ice
     *
     * @return string
     */
    public function getIce()
    {
        return $this->ice;
    }

    /**
     * Set if
     *
     * @param string $if
     *
     * @return Fournisseur
     */
    public function setIf($if)
    {
        $this->if = $if;

        return $this;
    }

    /**
     * Get if
     *
     * @return string
     */
    public function getIf()
    {
        return $this->if;
    }

    /**
     * Add bcfournisseur
     *
     * @param \AppBundle\Entity\Bcfournisseur $bcfournisseur
     *
     * @return Fournisseur
     */
    public function addBcfournisseur(\AppBundle\Entity\Bcfournisseur $bcfournisseur)
    {
        $this->bcfournisseur[] = $bcfournisseur;

        return $this;
    }

    /**
     * Remove bcfournisseur
     *
     * @param \AppBundle\Entity\Bcfournisseur $bcfournisseur
     */
    public function removeBcfournisseur(\AppBundle\Entity\Bcfournisseur $bcfournisseur)
    {
        $this->bcfournisseur->removeElement($bcfournisseur);
    }

    /**
     * Get bcfournisseur
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBcfournisseur()
    {
        return $this->bcfournisseur;
    }

    /**
     * Get missions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMissions()
    {
        return $this->missions;
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
     * @return Fournisseur
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
     * Add detailfournisseur
     *
     * @param \AppBundle\Entity\Detailvirement $detailfournisseur
     *
     * @return Fournisseur
     */
    public function addDetailfournisseur(\AppBundle\Entity\Detailvirement $detailfournisseur)
    {
        $this->detailfournisseurs[] = $detailfournisseur;

        return $this;
    }

    /**
     * Remove detailfournisseur
     *
     * @param \AppBundle\Entity\Detailvirement $detailfournisseur
     */
    public function removeDetailfournisseur(\AppBundle\Entity\Detailvirement $detailfournisseur)
    {
        $this->detailfournisseurs->removeElement($detailfournisseur);
    }

    /**
     * Get detailfournisseurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDetailfournisseurs()
    {
        return $this->detailfournisseurs;
    }

    /**
     * Set iif
     *
     * @param string $iif
     *
     * @return Fournisseur
     */
    public function setIif($iif)
    {
        $this->iif = $iif;

        return $this;
    }

    /**
     * Get iif
     *
     * @return string
     */
    public function getIif()
    {
        return $this->iif;
    }

    /**
     * Add projetconsultant
     *
     * @param \AppBundle\Entity\Projetconsultant $projetconsultant
     *
     * @return Fournisseur
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
     * Add production
     *
     * @param \AppBundle\Entity\Production $production
     *
     * @return Fournisseur
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Fournisseur
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
     * @return Fournisseur
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
