<?php

namespace App\Entity;

use App\Repository\ChampionRepository;
use DateTimeImmutable as TimeStamp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ChampionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Champion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank]
    private ?string $key;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private ?string $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private ?string $icon;

    #[ORM\OneToMany(targetEntity: Spell::class, mappedBy: 'champion', fetch: 'EAGER', orphanRemoval: true)]
    private Collection $spells;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull]
    private TimeStamp $updatedAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull]
    private TimeStamp $createdAt;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->spells = new ArrayCollection();
        $this->createdAt = new TimeStamp();
        $this->updatedAt = new TimeStamp();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new TimeStamp();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(?string $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function getSpells(): Collection
    {
        return $this->spells;
    }

    public function addSpell(Spell $spell): self
    {
        if(!$this->spells->contains($spell)){
            $this->spells[] = $spell;
            $spell->setChampion($this);
        }
       return $this;
    }

    public function removeSpell(Spell $spell): self
    {
        if ($this->spells->removeElement($spell)) {
            // set the owning side to null (unless already changed)
            if ($spell->getChampion() === $this) {
                $spell->setChampion(null);
            }
        }
        return $this;
    }

    public function getUpdatedAt(): TimeStamp
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(TimeStamp $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCreatedAt(): TimeStamp
    {
        return $this->createdAt;
    }

    public function setCreatedAt(TimeStamp $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }


}
