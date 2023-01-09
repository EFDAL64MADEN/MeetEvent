<?php

namespace App\Entity;

use App\Repository\EventRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 * @ORM\Table(name="event", indexes={@ORM\Index(columns={"name_of_event", "zip_code", "city", "description"}, flags={"fulltext"})})
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nameOfEvent;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startEvent;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endEvent;

    /**
     * @ORM\Column(type="integer")
     */
    private $numberOfPlaces;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="event", orphanRemoval=true, cascade={"persist"})
     */
    private $pictures;

    /**
     * @ORM\ManyToOne(targetEntity=Theme::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $theme;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="events")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="subscribedEvents")
     */
    private $users;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="event", orphanRemoval=true)
     */
    private $comments;


    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameOfEvent(): ?string
    {
        return $this->nameOfEvent;
    }

    public function setNameOfEvent(string $nameOfEvent): self
    {
        $this->nameOfEvent = $nameOfEvent;

        return $this;
    }

    public function getStartEvent(): ?\DateTimeInterface
    {
        return $this->startEvent;
    }

    public function setStartEvent(\DateTimeInterface $startEvent): self
    {
        $this->startEvent = $startEvent;

        return $this;
    }

    public function getEndEvent(): ?\DateTimeInterface
    {
        return $this->endEvent;
    }

    public function setEndEvent(\DateTimeInterface $endEvent): self
    {
        $this->endEvent = $endEvent;

        return $this;
    }

    public function getNumberOfPlaces(): ?int
    {
        return $this->numberOfPlaces;
    }

    public function setNumberOfPlaces(int $numberOfPlaces): self
    {
        $this->numberOfPlaces = $numberOfPlaces;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setEvent($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getEvent() === $this) {
                $picture->setEvent(null);
            }
        }

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): self
    {
        $this->theme = $theme;

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
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);
        
        return $this;
    }

    public function __toString()
    {
        return $this->nameOfEvent;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setEvent($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getEvent() === $this) {
                $comment->setEvent(null);
            }
        }

        return $this;
    }
}
