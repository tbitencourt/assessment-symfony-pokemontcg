<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PokemonAttackCostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokemonAttackCostRepository::class)]
class PokemonAttackCost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: PokemonAttack::class, inversedBy: 'cost')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PokemonAttack $pokemonAttack = null;

    #[ORM\ManyToOne(targetEntity: Type::class, inversedBy: 'pokemonAttackCost')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPokemonAttack(): ?PokemonAttack
    {
        return $this->pokemonAttack;
    }

    public function setPokemonAttack(?PokemonAttack $pokemonAttack): void
    {
        $this->pokemonAttack = $pokemonAttack;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): void
    {
        $this->type = $type;
    }
}
