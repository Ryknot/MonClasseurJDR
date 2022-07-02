<?php

namespace App\Entity;

use App\Repository\CarteMJRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CarteMJRepository::class)
 */
class CarteMJ
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="cartesMJ")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $filtre;

    /**
     * @ORM\Column(type="integer")
     */
    private $PV;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $onBoard;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $qtyOnBoard;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFiltre(): ?string
    {
        return $this->filtre;
    }

    public function setFiltre(?string $filtre): self
    {
        $this->filtre = $filtre;

        return $this;
    }

    public function getPV(): ?int
    {
        return $this->PV;
    }

    public function setPV(int $PV): self
    {
        $this->PV = $PV;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getOnBoard(): ?bool
    {
        return $this->onBoard;
    }

    public function setOnBoard(?bool $onBoard): self
    {
        $this->onBoard = $onBoard;

        return $this;
    }

    public function getQtyOnBoard(): ?int
    {
        return $this->qtyOnBoard;
    }

    public function setQtyOnBoard(?int $qtyOnBoard): self
    {
        $this->qtyOnBoard = $qtyOnBoard;

        return $this;
    }
}
