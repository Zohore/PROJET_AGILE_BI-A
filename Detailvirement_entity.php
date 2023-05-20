<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Detailvirement
 * @Gedmo\Loggable
 * @ORM\Table(name="detailvirement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DetailvirementRepository")
 */
class Detailvirement
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
     * @var int
     * @Gedmo\Versioned
     * @ORM\Column(name="priorite", type="integer", nullable=true)
     */
    private $priorite;
    /**
     * @var float
     * @Gedmo\Versioned
     * @ORM\Column(name="total", type="float", nullable=true)
     */
    private $total;

    /**
     * @ORM\ManyToOne(targetEntity="Fournisseur", inversedBy="detailfournisseurs")
     * @ORM\JoinColumn(name="id_fournisseur", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $fournisseur;
    /**
     * @ORM\ManyToOne(targetEntity="Virementf", inversedBy="detailfournisseurs")
     * @ORM\JoinColumn(name="id_virementf", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $virementf;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Detailvirement
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
     * Set priorite
     *
     * @param integer $priorite
     *
     * @return Detailvirement
     */
    public function setPriorite($priorite)
    {
        $this->priorite = $priorite;

        return $this;
    }

    /**
     * Get priorite
     *
     * @return int
     */
    public function getPriorite()
    {
        return $this->priorite;
    }

    /**
     * Set total
     *
     * @param float $total
     *
     * @return Detailvirement
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set fournisseur
     *
     * @param \AppBundle\Entity\Fournisseur $fournisseur
     *
     * @return Detailvirement
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
     * Set virementf
     *
     * @param \AppBundle\Entity\Virementf $virementf
     *
     * @return Detailvirement
     */
    public function setVirementf(\AppBundle\Entity\Virementf $virementf = null)
    {
        $this->virementf = $virementf;

        return $this;
    }

    /**
     * Get virementf
     *
     * @return \AppBundle\Entity\Virementf
     */
    public function getVirementf()
    {
        return $this->virementf;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Detailvirement
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
     * @return Detailvirement
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

    public function __construct()
    {

        $this->setCreatedAt(new \DateTime());
    }

    public function __toString()
    {
        if ($this->getFournisseur() != null) {
            $string = $this->getFournisseur()->getNom();
        } else {
            $string = 'detail virement';
        }
        return $string;
    }
}
