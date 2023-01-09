<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ThemeRepository::class)
 */
class Theme
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
    private $themeName;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="theme", orphanRemoval=true)
     */
    private $events;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $color;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getThemeName(): ?string
    {
        return $this->themeName;
    }
    
    public function setThemeName(string $themeName): self
    {
        $this->themeName = $themeName;
        
        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }
    
    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;
        
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }
    
    public function setColor(string $color): self
    {
        $this->color = $color;
        
        return $this;
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
            $event->setTheme($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getTheme() === $this) {
                $event->setTheme(null);
            }
        }

        return $this;
    }
    
    public function __toString()
    {
        return $this->themeName;
    }
}
