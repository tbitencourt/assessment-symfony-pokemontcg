<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SetRepository::class)]
#[ORM\Table(name: '`sets`')]
class Set
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $series = null;

    #[ORM\Column(length: 255)]
    private ?string $ptcgo_code = null;

    /**
     * @var Collection<int, Card>|null
     */
    #[ORM\OneToMany(targetEntity: Card::class, mappedBy: 'set')]
    private ?Collection $cards = null;

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

    public function getSeries(): ?string
    {
        return $this->series;
    }

    public function setSeries(string $series): static
    {
        $this->series = $series;

        return $this;
    }

    public function getPtcgoCode(): ?string
    {
        return $this->ptcgo_code;
    }

    public function setPtcgoCode(string $ptcgo_code): static
    {
        $this->ptcgo_code = $ptcgo_code;

        return $this;
    }

    /**
     * @return Collection<int, Card>|null
     */
    public function getCards(): ?Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): static
    {
        if (is_null($this->cards)) {
            $this->cards = new ArrayCollection();
        }
        $this->cards->add($card);

        return $this;
    }
}
