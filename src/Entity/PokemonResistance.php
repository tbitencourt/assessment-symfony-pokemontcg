<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PokemonResistanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokemonResistanceRepository::class)]
class PokemonResistance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Type::class, inversedBy: 'pokemon_resistances')]
    private ?Type $type = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    /**
     * @var Collection<int, Card>|null
     */
    #[ORM\ManyToMany(targetEntity: Card::class, inversedBy: 'resistances')]
    #[ORM\JoinTable(name: 'card_pokemon_resistance')]
    private ?Collection $cards = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

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
}
