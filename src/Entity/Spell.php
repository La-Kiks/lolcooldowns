<?php

namespace App\Entity;

use App\Repository\SpellRepository;
use DateTimeImmutable as TimeStamp;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: SpellRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Spell
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Champion::class, inversedBy: 'spells')]
    private Champion $champion;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $icon ;

    #[ORM\Column(type: 'json', length: 255, nullable: true)]
    private ?array $cooldowns = [];

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $affectedByCdr = true;

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

    public function getChampion(): Champion
    {
        return $this->champion;
    }

    public function setChampion(Champion $champion): self
    {
        $this->champion = $champion;
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

    public function getCooldowns(): ?array
    {
        return $this->cooldowns;
    }

    public function setCooldowns(?array $cooldowns): self
    {
        $this->cooldowns = $cooldowns;
        return $this;
    }

    public function getAffectedByCdr(): ?bool
    {
        return $this->affectedByCdr;
    }

    public function setAffectedByCdr(?bool $affectedByCdr): self
    {
        $this->affectedByCdr = $affectedByCdr;
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
