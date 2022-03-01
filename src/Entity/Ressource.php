<?php

namespace App\Entity;

use App\Repository\RessourceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RessourceRepository::class)
 */
class Ressource
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $label;

    /**
     * @ORM\Column(type="integer")
     */
    private $rangeMax;

    /**
     * @ORM\ManyToOne(targetEntity=FichePerso::class, inversedBy="ressources")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fichePerso;

    /**
     * @ORM\Column(type="integer")
     */
    private $sort;

    /**
     * @ORM\Column(type="integer")
     */
    private $ValeurGlissante;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getRangeMax(): ?int
    {
        return $this->rangeMax;
    }

    public function setRangeMax(int $rangeMax): self
    {
        $this->rangeMax = $rangeMax;

        return $this;
    }

    public function getFichePerso(): ?FichePerso
    {
        return $this->fichePerso;
    }

    public function setFichePerso(?FichePerso $fichePerso): self
    {
        $this->fichePerso = $fichePerso;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getValeurGlissante(): ?int
    {
        return $this->ValeurGlissante;
    }

    public function setValeurGlissante(int $ValeurGlissante): self
    {
        $this->ValeurGlissante = $ValeurGlissante;

        return $this;
    }
}
