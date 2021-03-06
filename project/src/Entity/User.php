<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Todolist::class, mappedBy="user", orphanRemoval=true)
     */
    private $todolists;

    /**
     * @ORM\ManyToMany(targetEntity=Todolist::class, mappedBy="invited")
     */
    private $invited;

    public function __construct()
    {
        $this->todolists = new ArrayCollection();
        $this->invited = new ArrayCollection();
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
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
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
     * @see PasswordAuthenticatedUserInterface
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

    /**
     * @return Collection|Todolist[]
     */
    public function getTodolists(): Collection
    {
        return $this->todolists;
    }

    public function addTodolist(Todolist $todolist): self
    {
        if (!$this->todolists->contains($todolist)) {
            $this->todolists[] = $todolist;
            $todolist->setUser($this);
        }

        return $this;
    }

    public function removeTodolist(Todolist $todolist): self
    {
        if ($this->todolists->removeElement($todolist)) {
            // set the owning side to null (unless already changed)
            if ($todolist->getUser() === $this) {
                $todolist->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Todolist[]
     */
    public function getInvited(): Collection
    {
        return $this->invited;
    }

    public function addInvited(Todolist $invited): self
    {
        if (!$this->invited->contains($invited)) {
            $this->invited[] = $invited;
            $invited->addInvited($this);
        }

        return $this;
    }

    public function removeInvited(Todolist $invited): self
    {
        if ($this->invited->removeElement($invited)) {
            $invited->removeInvited($this);
        }

        return $this;
    }
}
