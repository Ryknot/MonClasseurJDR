<?php

namespace App\Entity;

use App\Repository\FichePersoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FichePersoRepository::class)
 */
class FichePerso
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(max=100, maxMessage="pseudo can contain max 100 characters !")
     */
    private $pseudo;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="fichePersos")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbChamps1;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbChamps2;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbChamps3;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbChamps4;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbChamps5;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbChamps6;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbChamps7;

    /**
     * @ORM\OneToMany(targetEntity=Ressource::class, mappedBy="fichePerso", orphanRemoval=true)
     */
    private $ressources;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbRessource;

    /**
     * @ORM\OneToMany(targetEntity=Champs::class, mappedBy="fichePerso", orphanRemoval=true)
     */
    private $champs;

    public function __construct()
    {
        $this->champs = new ArrayCollection();
        $this->ressources = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return Collection|Champs[]
     */
    public function getChamps(): Collection
    {
        return $this->champs;
    }

    public function addChamp(Champs $champ): self
    {
        if (!$this->champs->contains($champ)) {
            $this->champs[] = $champ;
            $champ->setFichePerso($this);
        }

        return $this;
    }

    public function removeChamp(Champs $champ): self
    {
        if ($this->champs->removeElement($champ)) {
            // set the owning side to null (unless already changed)
            if ($champ->getFichePerso() === $this) {
                $champ->setFichePerso(null);
            }
        }

        return $this;
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

    public function __toString()
    {
        return $this->pseudo;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getNbChamps1(): ?int
    {
        return $this->nbChamps1;
    }

    public function setNbChamps1(int $nbChamps1): self
    {
        $this->nbChamps1 = $nbChamps1;

        return $this;
    }

    public function getNbChamps2(): ?int
    {
        return $this->nbChamps2;
    }

    public function setNbChamps2(int $nbChamps2): self
    {
        $this->nbChamps2 = $nbChamps2;

        return $this;
    }

    public function getNbChamps3(): ?int
    {
        return $this->nbChamps3;
    }

    public function setNbChamps3(int $nbChamps3): self
    {
        $this->nbChamps3 = $nbChamps3;

        return $this;
    }

    public function getNbChamps4(): ?int
    {
        return $this->nbChamps4;
    }

    public function setNbChamps4(int $nbChamps4): self
    {
        $this->nbChamps4 = $nbChamps4;

        return $this;
    }

    public function getNbChamps5(): ?int
    {
        return $this->nbChamps5;
    }

    public function setNbChamps5(int $nbChamps5): self
    {
        $this->nbChamps5 = $nbChamps5;

        return $this;
    }

    public function getNbChamps6(): ?int
    {
        return $this->nbChamps6;
    }

    public function setNbChamps6(int $nbChamps6): self
    {
        $this->nbChamps6 = $nbChamps6;

        return $this;
    }

    public function getNbChamps7(): ?int
    {
        return $this->nbChamps7;
    }

    public function setNbChamps7(int $nbChamps7): self
    {
        $this->nbChamps7 = $nbChamps7;

        return $this;
    }

    /**
     * @return Collection|Ressource[]
     */
    public function getRessources(): Collection
    {
        return $this->ressources;
    }

    public function addRessource(Ressource $ressource): self
    {
        if (!$this->ressources->contains($ressource)) {
            $this->ressources[] = $ressource;
            $ressource->setFichePerso($this);
        }

        return $this;
    }

    public function removeRessource(Ressource $ressource): self
    {
        if ($this->ressources->removeElement($ressource)) {
            // set the owning side to null (unless already changed)
            if ($ressource->getFichePerso() === $this) {
                $ressource->setFichePerso(null);
            }
        }

        return $this;
    }

    public function getNbRessource(): ?int
    {
        return $this->nbRessource;
    }

    public function setNbRessource(int $nbRessource): self
    {
        $this->nbRessource = $nbRessource;

        return $this;
    }


}
