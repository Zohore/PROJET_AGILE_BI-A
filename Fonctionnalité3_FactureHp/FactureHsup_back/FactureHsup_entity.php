<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * FactureHsup
 *
 * @ORM\Table(name="facture_hsup")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactureHsupRepository")
 * @Gedmo\Loggable
 */
class FactureHsup
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
     * @ORM\Column(name="nbjour", type="float", nullable=true)
     */
    private $nbjour;

    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="nbheure", type="float", nullable=true)
     */
    private $nbheure;

    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="totalHT", type="float", nullable=true)
     */
    private $totalHT;

    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="totalTTC", type="float", nullable=true)
     */
    private $totalTTC;

    /**
     * @ORM\ManyToOne(targetEntity="Facture", inversedBy="facturehsups",cascade={"persist"})
     * @ORM\JoinColumn(name="id_facture", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $facture;
    /**
     * @ORM\ManyToOne(targetEntity="Bcfournisseur", inversedBy="heures",cascade={"persist"})
     * @ORM\JoinColumn(name="id_bcfournisseur", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $bcfournisseur;
    /**
     * @ORM\ManyToOne(targetEntity="Facturefournisseur", inversedBy="heures",cascade={"persist"})
     * @ORM\JoinColumn(name="id_facture_fournisseur", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $facturefournisseur;
    /**
     * @ORM\ManyToOne(targetEntity="Heuresup", inversedBy="heures")
     * @ORM\JoinColumn(name="id_heuresup", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $heuresup;
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nbjour
     *
     * @param float $nbjour
     *
     * @return FactureHsup
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
     * Set nbheure
     *
     * @param float $nbheure
     *
     * @return FactureHsup
     */
    public function setNbheure($nbheure)
    {
        $this->nbheure = $nbheure;

        return $this;
    }

    /**
     * Get nbheure
     *
     * @return float
     */
    public function getNbheure()
    {
        return $this->nbheure;
    }

    /**
     * Set totalHT
     *
     * @param float $totalHT
     *
     * @return FactureHsup
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
     * @return FactureHsup
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
     * Set facture
     *
     * @param \AppBundle\Entity\Facture $facture
     *
     * @return FactureHsup
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
     * Set heuresup
     *
     * @param \AppBundle\Entity\Heuresup $heuresup
     *
     * @return FactureHsup
     */
    public function setHeuresup(\AppBundle\Entity\Heuresup $heuresup = null)
    {
        $this->heuresup = $heuresup;

        return $this;
    }

    /**
     * Get heuresup
     *
     * @return \AppBundle\Entity\Heuresup
     */
    public function getHeuresup()
    {
        return $this->heuresup;
    }

    /**
     * Set bcfournisseur
     *
     * @param \AppBundle\Entity\Bcfournisseur $bcfournisseur
     *
     * @return FactureHsup
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
     * Set facturefournisseur
     *
     * @param \AppBundle\Entity\Facturefournisseur $facturefournisseur
     *
     * @return FactureHsup
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return FactureHsup
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
     * @return FactureHsup
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

    public function __toString()
    {
        return 'id: ' . $this->getId() .
            ', nbheure: ' . $this->getNbheure() . ', nbjour: ' . $this->getNbjour() . ', totalht: '
            . $this->getTotalHt() . ', totalTTC: ' . $this->getTotalTTC() ;
    }
}
