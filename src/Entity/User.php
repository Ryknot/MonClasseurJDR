<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=180, maxMessage="email can contains max 180 characters !")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(min=5, minMessage="password must contains min 5 characters !")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=30, unique=true)
     * @Assert\Unique(message="this pseudo is already existing, choose an oser dude !")
     * @Assert\Length(min=5, minMessage="pseudo must contains min 5 characters !", max=30, maxMessage="pseudo can contain max 30 characters !")
     */
    private $pseudo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateRegister;

    /**
     * @ORM\OneToMany(targetEntity=FichePerso::class, mappedBy="user")
     */
    private $fichePersos;

    /**
     * @ORM\OneToMany(targetEntity=CarteMJ::class, mappedBy="user", orphanRemoval=true)
     */
    private $cartesMJ;

    public function __construct()
    {
        $this->fichePersos = new ArrayCollection();
        $this->cartesMJ = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getDateRegister(): ?\DateTimeInterface
    {
        return $this->dateRegister;
    }

    public function setDateRegister(\DateTimeInterface $dateRegister): self
    {
        $this->dateRegister = $dateRegister;

        return $this;
    }

    /**
     * @return Collection|FichePerso[]
     */
    public function getFichePersos(): Collection
    {
        return $this->fichePersos;
    }

    public function addFichePerso(FichePerso $fichePerso): self
    {
        if (!$this->fichePersos->contains($fichePerso)) {
            $this->fichePersos[] = $fichePerso;
            $fichePerso->setUser($this);
        }

        return $this;
    }

    public function removeFichePerso(FichePerso $fichePerso): self
    {
        if ($this->fichePersos->removeElement($fichePerso)) {
            // set the owning side to null (unless already changed)
            if ($fichePerso->getUser() === $this) {
                $fichePerso->setUser(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->pseudo;
    }

    /**
     * @return Collection|CarteMJ[]
     */
    public function getCartesMJ(): Collection
    {
        return $this->cartesMJ;
    }

    public function addCartesMJ(CarteMJ $cartesMJ): self
    {
        if (!$this->cartesMJ->contains($cartesMJ)) {
            $this->cartesMJ[] = $cartesMJ;
            $cartesMJ->setUser($this);
        }

        return $this;
    }

    public function removeCartesMJ(CarteMJ $cartesMJ): self
    {
        if ($this->cartesMJ->removeElement($cartesMJ)) {
            // set the owning side to null (unless already changed)
            if ($cartesMJ->getUser() === $this) {
                $cartesMJ->setUser(null);
            }
        }

        return $this;
    }
}
