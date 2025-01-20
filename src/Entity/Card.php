<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardRepository::class)]
#[ORM\Table(name: '`cards`')]
class Card
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $supertype = null;

    /**
     * @var Collection<int, CardImage>|null
     */
    #[ORM\OneToMany(targetEntity: CardImage::class, mappedBy: 'card')]
    private ?Collection $images = null;

    /**
     * @var Collection<int, Type>|null
     */
    #[ORM\ManyToMany(targetEntity: Type::class, mappedBy: 'cards')]
    private ?Collection $types = null;

    /**
     * @var Collection<int, PokemonAttack>|null
     */
    #[ORM\ManyToMany(targetEntity: PokemonAttack::class, mappedBy: 'cards')]
    private ?Collection $attacks = null;

    /**
     * @var Collection<int, PokemonWeakness>|null
     */
    #[ORM\ManyToMany(targetEntity: PokemonWeakness::class, mappedBy: 'cards')]
    private ?Collection $weaknesses = null;

    /**
     * @var Collection<int, PokemonResistance>|null
     */
    #[ORM\ManyToMany(targetEntity: PokemonResistance::class, mappedBy: 'cards')]
    private ?Collection $resistances = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
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

    public function getSupertype(): ?string
    {
        return $this->supertype;
    }

    public function setSupertype(string $supertype): static
    {
        $this->supertype = $supertype;

        return $this;
    }

    /**
     * @return Collection<int, CardImage>|null
     */
    public function getImages(): ?Collection
    {
        return $this->images;
    }

    public function addImage(CardImage $image): static
    {
        if (is_null($this->images)) {
            $this->images = new ArrayCollection();
        }
        $this->images->add($image);

        return $this;
    }

    public function getSmallImage(): ?CardImage
    {
        return $this->getImage('small');
    }

    public function getLargeImage(): ?CardImage
    {
        return $this->getImage('large');
    }

    public function getImage(string $type): ?CardImage
    {
        if (!is_null($this->images)) {
            /** CardImage $image */
            foreach ($this->images as $image) {
                if ($image->getType() === $type) {
                    return $image;
                }
            }
        }

        return null;
    }

    /**
     * @return Collection<int, Type>|null
     */
    public function getTypes(): ?Collection
    {
        return $this->types;
    }

    public function addType(Type $type): static
    {
        if (is_null($this->types)) {
            $this->types = new ArrayCollection();
        }
        $this->types->add($type);

        return $this;
    }

    /**
     * @return Collection<int, PokemonAttack>|null
     */
    public function getAttacks(): ?Collection
    {
        return $this->attacks;
    }

    /**
     * @return $this
     */
    public function addAttack(PokemonAttack $pokemonAttack): static
    {
        if (is_null($this->attacks)) {
            $this->attacks = new ArrayCollection();
        }
        $this->attacks->add($pokemonAttack);

        return $this;
    }

    /**
     * @return Collection<int, PokemonWeakness>|null
     */
    public function getWeaknesses(): ?Collection
    {
        return $this->weaknesses;
    }

    public function addWeakness(PokemonWeakness $pokemonWeakness): static
    {
        if (is_null($this->weaknesses)) {
            $this->weaknesses = new ArrayCollection();
        }
        $this->weaknesses->add($pokemonWeakness);

        return $this;
    }

    /**
     * @return Collection<int, PokemonResistance>|null
     */
    public function getResistances(): ?Collection
    {
        return $this->resistances;
    }

    public function addResistance(PokemonResistance $pokemonResistance): static
    {
        if (is_null($this->resistances)) {
            $this->resistances = new ArrayCollection();
        }
        $this->resistances->add($pokemonResistance);

        return $this;
    }
}
