<?php

namespace App\Entity;

use App\Repository\ChampsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChampsRepository::class)
 */
class Champs
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
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $valeurTexte;

    /**
     * @ORM\ManyToOne(targetEntity=TypeInfo::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeInfo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typeChamp;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $valeurArea;

    /**
     * @ORM\Column(type="integer")
     */
    private $sort = 0;

    /**
     * @ORM\ManyToOne(targetEntity=FichePerso::class, inversedBy="champs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fichePerso;

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

    public function getValeurTexte(): ?string
    {
        return $this->valeurTexte;
    }

    public function setValeurTexte(string $valeurTexte): self
    {
        $this->valeurTexte = $valeurTexte;

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

    public function getTypeInfo(): ?TypeInfo
    {
        return $this->typeInfo;
    }

    public function setTypeInfo(?TypeInfo $typeInfo): self
    {
        $this->typeInfo = $typeInfo;

        return $this;
    }

    public function getTypeChamp(): ?string
    {
        return $this->typeChamp;
    }

    public function setTypeChamp(string $typeChamp): self
    {
        $this->typeChamp = $typeChamp;

        return $this;
    }

    public function getValeurArea(): ?string
    {
        return $this->valeurArea;
    }

    public function setValeurArea(?string $valeurArea): self
    {
        $this->valeurArea = $valeurArea;

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
}
