<?php

namespace Nomenclature\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Details
 *
 * @ORM\Table(name="details")
 * @ORM\Entity
 */
class Details
{
    /**
     * @var integer
     *
     * @ORM\Column(name="detailId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $detailId;

    /**
     * @var string
     *
     * @ORM\Column(name="detailCode", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $detailCode;

    /**
     * @var string
     *
     * @ORM\Column(name="detailName", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $detailName;

    /**
     * @var integer
     *
     * @ORM\Column(name="detailPattern", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $detailPattern;

    /**
     * @var integer
     *
     * @ORM\Column(name="detailModel", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $detailModel;

    /**
     * @var integer
     *
     * @ORM\Column(name="detailProject", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $detailProject;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="detailDateReceiving", type="date", precision=0, scale=0, nullable=false, unique=false)
     */
    private $detailDateReceiving;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="detailDateStart", type="date", precision=0, scale=0, nullable=false, unique=false)
     */
    private $detailDateStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="detailDateExercise", type="date", precision=0, scale=0, nullable=false, unique=false)
     */
    private $detailDateExercise;


    /**
     * Get detailId
     *
     * @return integer
     */
    public function getId()
    {
        return $this->detailId;
    }

    /**
     * Set detailCode
     *
     * @param string $detailCode
     *
     * @return Details
     */
    public function setCode($detailCode)
    {
        $this->detailCode = $detailCode;

        return $this;
    }

    /**
     * Get detailCode
     *
     * @return string
     */
    public function getCode()
    {
        return $this->detailCode;
    }

    /**
     * Set detailName
     *
     * @param string $detailName
     *
     * @return Details
     */
    public function setName($detailName)
    {
        $this->detailName = $detailName;

        return $this;
    }

    /**
     * Get detailName
     *
     * @return string
     */
    public function getName()
    {
        return $this->detailName;
    }

    /**
     * Set detailPattern
     *
     * @param integer $detailPattern
     *
     * @return Details
     */
    public function setPattern($detailPattern)
    {
        $this->detailPattern = $detailPattern;

        return $this;
    }

    /**
     * Get detailPattern
     *
     * @return integer
     */
    public function getPattern()
    {
        return $this->detailPattern;
    }

    /**
     * Set detailModel
     *
     * @param integer $detailModel
     *
     * @return Details
     */
    public function setModel($detailModel)
    {
        $this->detailModel = $detailModel;

        return $this;
    }

    /**
     * Get detailModel
     *
     * @return integer
     */
    public function getModel()
    {
        return $this->detailModel;
    }

    /**
     * Set detailProject
     *
     * @param integer $detailProject
     *
     * @return Details
     */
    public function setProject($detailProject)
    {
        $this->detailProject = $detailProject;

        return $this;
    }

    /**
     * Get detailProject
     *
     * @return integer
     */
    public function getProject()
    {
        return $this->detailProject;
    }

    /**
     * Set detailDateReceiving
     *
     * @param \DateTime $detailDateReceiving
     *
     * @return Details
     */
    public function setDateReceiving($detailDateReceiving)
    {
        $this->detailDateReceiving = $detailDateReceiving;

        return $this;
    }

    /**
     * Get detailDateReceiving
     *
     * @return \DateTime
     */
    public function getDateReceiving()
    {
        return $this->detailDateReceiving;
    }

    /**
     * Set detailDateStart
     *
     * @param \DateTime $detailDateStart
     *
     * @return Details
     */
    public function setDateStart($detailDateStart)
    {
        $this->detailDateStart = $detailDateStart;

        return $this;
    }

    /**
     * Get detailDateStart
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->detailDateStart;
    }

    /**
     * Set detailDateExercise
     *
     * @param \DateTime $detailDateExercise
     *
     * @return Details
     */
    public function setDateExercise($detailDateExercise)
    {
        $this->detailDateExercise = $detailDateExercise;

        return $this;
    }

    /**
     * Get detailDateExercise
     *
     * @return \DateTime
     */
    public function getDateExercise()
    {
        return $this->detailDateExercise;
    }
}

