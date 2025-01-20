<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PokemonAttackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokemonAttackRepository::class)]
class PokemonAttack
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, PokemonAttackCost>|null
     */
    #[ORM\OneToMany(targetEntity: PokemonAttackCost::class, mappedBy: 'pokemonAttack')]
    private ?Collection $cost = null;

    #[ORM\Column]
    private ?int $convertedEnergyCost = null;

    #[ORM\Column(length: 255)]
    private ?string $damage = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\ManyToOne(targetEntity: Card::class, inversedBy: 'attacks')]
    private ?Card $card = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, PokemonAttackCost>|null
     */
    public function getCost(): ?Collection
    {
        return $this->cost;
    }

    /**
     * @return $this
     */
    public function addCost(PokemonAttackCost $cost): static
    {
        if (is_null($this->cost)) {
            $this->cost = new ArrayCollection();
        }
        $this->cost->add($cost);

        return $this;
    }

    public function checkCost(string $type): int
    {
        if (
            is_null($this->cost)
            || $this->cost->isEmpty()) {
            return 0;
        }
        $count = 0;
        foreach ($this->cost as $cost) {
            if ($cost->getType()?->getName() === $type) {
                ++$count;
            }
        }

        return $count;
    }

    public function getConvertedEnergyCost(): ?int
    {
        return $this->convertedEnergyCost;
    }

    public function setConvertedEnergyCost(int $convertedEnergyCost): static
    {
        $this->convertedEnergyCost = $convertedEnergyCost;

        return $this;
    }

    public function getDamage(): ?string
    {
        return $this->damage;
    }

    public function setDamage(?string $damage): static
    {
        $this->damage = $damage;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getCards(): ?Card
    {
        return $this->card;
    }

    /**
     * @return $this
     */
    public function setCard(Card $card): static
    {
        $this->card = $card;

        return $this;
    }
}
