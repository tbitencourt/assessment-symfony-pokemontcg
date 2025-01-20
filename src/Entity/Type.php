<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeRepository::class)]
#[ORM\Table(name: '`types`')]
class Type
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Card>|null
     */
    #[ORM\ManyToMany(targetEntity: Card::class, inversedBy: 'types')]
    #[ORM\JoinTable(name: 'card_type')]
    private ?Collection $cards = null;

    /**
     * @var Collection<int, PokemonAttackCost>|null
     */
    #[ORM\OneToMany(targetEntity: PokemonAttackCost::class, mappedBy: 'type')]
    private ?Collection $pokemonAttackCost = null;

    /**
     * @var Collection<int, PokemonWeakness>|null
     */
    #[ORM\OneToMany(targetEntity: PokemonWeakness::class, mappedBy: 'type')]
    private ?Collection $pokemonWeaknesses = null;

    /**
     * @var Collection<int, PokemonResistance>|null
     */
    #[ORM\OneToMany(targetEntity: PokemonResistance::class, mappedBy: 'type')]
    private ?Collection $pokemonResistances = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
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
     * @return Collection<int, Card>|null
     */
    public function getCards(): ?Collection
    {
        return $this->cards;
    }

    /**
     * @return $this
     */
    public function addCard(Card $card): static
    {
        if (is_null($this->cards)) {
            $this->cards = new ArrayCollection();
        }
        $this->cards->add($card);

        return $this;
    }

    /**
     * @return Collection<int, PokemonAttackCost>|null
     */
    public function getPokemonAttackCost(): ?Collection
    {
        return $this->pokemonAttackCost;
    }

    /**
     * @return $this
     */
    public function addPokemonAttackCost(PokemonAttackCost $pokemonAttackCost): static
    {
        if (is_null($this->pokemonAttackCost)) {
            $this->pokemonAttackCost = new ArrayCollection();
        }
        $this->pokemonAttackCost->add($pokemonAttackCost);

        return $this;
    }

    /**
     * @return Collection<int, PokemonWeakness>|null
     */
    public function getPokemonWeaknesses(): ?Collection
    {
        return $this->pokemonWeaknesses;
    }

    public function addPokemonWeakness(PokemonWeakness $pokemonWeakness): static
    {
        if (is_null($this->pokemonWeaknesses)) {
            $this->pokemonWeaknesses = new ArrayCollection();
        }
        $this->pokemonWeaknesses->add($pokemonWeakness);

        return $this;
    }

    /**
     * @return Collection<int, PokemonResistance>|null
     */
    public function getPokemonResistances(): ?Collection
    {
        return $this->pokemonResistances;
    }

    public function addPokemonResistance(PokemonResistance $pokemonResistance): static
    {
        if (is_null($this->pokemonResistances)) {
            $this->pokemonResistances = new ArrayCollection();
        }
        $this->pokemonResistances->add($pokemonResistance);

        return $this;
    }
}
