<?php

namespace App\Entity;

use App\Repository\TodolistRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TodolistRepository::class)
 * @ApiResource
 */
class Todolist
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="list", orphanRemoval=true)
     */
    private $tasks;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dead_line;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="todolists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="invited")
     */
    private $invited;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->invited = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDeadLine(): ?\DateTimeInterface
    {
        return $this->dead_line;
    }

    public function setDeadLine(?\DateTimeInterface $dead_line): self
    {
        $this->dead_line = $dead_line;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setList($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getList() === $this) {
                $task->setList(null);
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

    /**
     * @return Collection|User[]
     */
    public function getInvited(): Collection
    {
        return $this->invited;
    }

    public function addInvited(User $invited): self
    {
        if (!$this->invited->contains($invited)) {
            $this->invited[] = $invited;
        }

        return $this;
    }

    public function removeInvited(User $invited): self
    {
        $this->invited->removeElement($invited);

        return $this;
    }
}
