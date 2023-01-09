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
    private $roles = ["ROLE_USER"];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=30)
     */ 
    private $nickname;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateOfBirth;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profilePicture;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="user", orphanRemoval=true)
     */
    private $events;

    /**
     * @ORM\ManyToMany(targetEntity=Event::class, mappedBy="users")
     */
    private $subscribedEvents;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="sender", orphanRemoval=true)
     */
    private $sent;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="receiver", orphanRemoval=true)
     */
    private $received;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="followers")
     */
    private $follows;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="follows")
     */
    private $followers;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="user", orphanRemoval=true)
     */
    private $comments;


    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->subscribedEvents = new ArrayCollection();
        $this->sent = new ArrayCollection();
        $this->received = new ArrayCollection();
        $this->follows = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setUser($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getUser() === $this) {
                $event->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getSubscribedEvents(): Collection
    {
        return $this->subscribedEvents;
    }

    public function addSubscribedEvent(Event $subscribedEvent): self
    {
        if (!$this->subscribedEvents->contains($subscribedEvent)) {
            $this->subscribedEvents[] = $subscribedEvent;
            $subscribedEvent->addUser($this);
        }

        return $this;
    }

    public function removeSubscribedEvent(Event $subscribedEvent): self
    {
        if ($this->subscribedEvents->removeElement($subscribedEvent)) {
            $subscribedEvent->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSent(): Collection
    {
        return $this->sent;
    }

    public function addSent(Message $sent): self
    {
        if (!$this->sent->contains($sent)) {
            $this->sent[] = $sent;
            $sent->setSender($this);
        }

        return $this;
    }

    public function removeSent(Message $sent): self
    {
        if ($this->sent->removeElement($sent)) {
            // set the owning side to null (unless already changed)
            if ($sent->getSender() === $this) {
                $sent->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceived(): Collection
    {
        return $this->received;
    }

    public function addReceived(Message $received): self
    {
        if (!$this->received->contains($received)) {
            $this->received[] = $received;
            $received->setReceiver($this);
        }

        return $this;
    }

    public function removeReceived(Message $received): self
    {
        if ($this->received->removeElement($received)) {
            // set the owning side to null (unless already changed)
            if ($received->getReceiver() === $this) {
                $received->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFollows(): Collection
    {
        return $this->follows;
    }

    public function addFollow(self $follow): self
    {
        if (!$this->follows->contains($follow)) {
            $this->follows[] = $follow;
        }

        return $this;
    }

    public function removeFollow(self $follow): self
    {
        $this->follows->removeElement($follow);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(self $follower): self
    {
        if (!$this->followers->contains($follower)) {
            $this->followers[] = $follower;
            $follower->addFollow($this);
        }

        return $this;
    }

    public function removeFollower(self $follower): self
    {
        if ($this->followers->removeElement($follower)) {
            $follower->removeFollow($this);
        }

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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }
}
