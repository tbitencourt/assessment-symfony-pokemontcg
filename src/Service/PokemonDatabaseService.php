<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Card;
use App\Entity\CardImage;
use App\Entity\PokemonAttack;
use App\Entity\PokemonAttackCost;
use App\Entity\PokemonResistance;
use App\Entity\PokemonWeakness;
use App\Entity\Set;
use App\Entity\Type;
use App\Repository\CardRepository;
use App\Repository\PokemonAttackRepository;
use App\Repository\PokemonResistanceRepository;
use App\Repository\PokemonWeaknessRepository;
use App\Repository\SetRepository;
use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use Pokemon\Models\Attack as PokemonCardAttack;
use Pokemon\Models\Card as PokemonCard;
use Pokemon\Models\CardImages as PokemonCardImages;
use Pokemon\Models\Resistance;
use Pokemon\Models\Set as PokemonSet;
use Pokemon\Models\Weakness;
use Pokemon\Pokemon;

final class PokemonDatabaseService
{
    /**
     * @var Collection<string, Type>
     */
    private Collection $mappedTypes;

    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly SetRepository $setRepository,
        private readonly CardRepository $cardRepository,
        private readonly TypeRepository $typeRepository,
        private readonly PokemonAttackRepository $attackRepository,
        private readonly PokemonResistanceRepository $resistanceRepository,
        private readonly PokemonWeaknessRepository $weaknessRepository,
    ) {
        $this->mapAllTypes();
    }

    public function initiate(): void
    {
        Pokemon::Options(['verify' => true]);
        /* @phpstan-ignore argument.type */
        Pokemon::ApiKey($_ENV['POKEMON_API_KEY']);
    }

    /**
     * @return Collection<int, Set>
     */
    public function getAllSets(): Collection
    {
        $sets = new ArrayCollection();
        $pokemonSets = Pokemon::Set()->all();
        /** @var PokemonSet $pokemonSet */
        foreach ($pokemonSets as $pokemonSet) {
            $sets->add($this->updatePokemonSet($pokemonSet));
        }
        $this->manager->flush();

        return $sets;
    }

    /**
     * @return Collection<int, Card>
     *
     * @throws GuzzleException
     */
    public function getCardsBySet(Set $set): Collection
    {
        $cards = new ArrayCollection();
        /* @phpstan-ignore method.notFound, method.nonObject */
        $pagination = Pokemon::Card()->where([
            'set.id' => $set->getId(),
        ])->pageSize(100)->pagination();
        /* @phpstan-ignore method.nonObject */
        $page = $pagination->getPage();
        do {
            /* @phpstan-ignore method.nonObject */
            if (0 === $pagination->getTotalCount()) {
                break;
            }
            /* @phpstan-ignore-next-line method.notFound, method.nonObject */
            $pokemonCards = Pokemon::Card()->where([
                'set.id' => $set->getId(),
            ])->page($page)->pageSize(100)->all();
            /* @phpstan-ignore foreach.nonIterable */
            foreach ($pokemonCards as $pokemonCard) {
                /* @phpstan-ignore argument.type */
                $card = $this->updatePokemonCard($pokemonCard, $set);
                /* @phpstan-ignore argument.type, method.nonObject */
                $this->updatePokemonCardTypes($card, $pokemonCard->getTypes());
                /* @phpstan-ignore argument.type, method.nonObject */
                $this->updatePokemonCardImages($card, $pokemonCard->getImages());
                /* @phpstan-ignore argument.type, method.nonObject */
                $this->updatePokemonCardAttack($card, $pokemonCard->getAttacks());
                /* @phpstan-ignore argument.type, method.nonObject */
                $this->updatePokemonCardResistances($card, $pokemonCard->getResistances());
                /* @phpstan-ignore argument.type, method.nonObject */
                $this->updatePokemonCardWeaknesses($card, $pokemonCard->getWeaknesses());

                $cards->add($card);
            }
            $this->manager->flush();
            /* @phpstan-ignore method.nonObject, preInc.type */
        } while (++$page <= $pagination->getTotalPages());

        return $cards;
    }

    private function mapAllTypes(): void
    {
        $this->mappedTypes = new ArrayCollection();
        $persistentTypes = $this->typeRepository->findAll();
        foreach ($persistentTypes as $persistentType) {
            $this->mappedTypes->offsetSet($persistentType->getName(), $persistentType);
        }
    }

    private function updatePokemonSet(PokemonSet $pokemonSet): Set
    {
        $set = $this->setRepository->find($pokemonSet->getId());
        if (is_null($set)) {
            $set = new Set();
            $set->setId($pokemonSet->getId());
        }
        $set->setName($pokemonSet->getName());
        $set->setSeries($pokemonSet->getSeries());
        $set->setPtcgoCode($pokemonSet->getPtcgoCode() ?? 'N/A');
        $this->manager->persist($set);

        return $set;
    }

    private function updatePokemonCard(PokemonCard $pokemonCard, Set $set): Card
    {
        $card = $this->cardRepository->find($pokemonCard->getId());
        if (is_null($card)) {
            $card = new Card();
            $card->setId($pokemonCard->getId());
        }
        $card->setName($pokemonCard->getName());
        /* @phpstan-ignore argument.type */
        $card->setSupertype($pokemonCard->getSupertype());
        $card->setSet($set);
        $this->manager->persist($card);

        return $card;
    }

    /**
     * @param array<int, string>|null $pokemonCardTypes
     */
    private function updatePokemonCardTypes(Card $card, ?array $pokemonCardTypes): void
    {
        foreach ($pokemonCardTypes ?? [] as $pokemonCardType) {
            $cardType = $this->mappedTypes->offsetGet($pokemonCardType);
            if (is_null($cardType)) {
                $cardType = new Type();
                $cardType->setName($pokemonCardType);
                $this->mappedTypes->offsetSet($pokemonCardType, $cardType);
            }
            if ($card->checkType($cardType)) {
                continue;
            }
            $cardType->addCard($card);
            $this->manager->persist($cardType);
        }
    }

    private function updatePokemonCardImages(Card $card, PokemonCardImages $cardImages): void
    {
        $cardImageSmall = $card->getSmallImage();
        if (is_null($cardImageSmall)) {
            $cardImageSmall = new CardImage();
        }
        $cardImageSmall->setType('small');
        /* @phpstan-ignore argument.type */
        $cardImageSmall->setUrl($cardImages->getSmall());
        $cardImageSmall->setCard($card);
        $this->manager->persist($cardImageSmall);

        $cardImageLarge = $card->getLargeImage();
        if (is_null($cardImageLarge)) {
            $cardImageLarge = new CardImage();
        }
        $cardImageLarge->setType('large');
        /* @phpstan-ignore argument.type */
        $cardImageLarge->setUrl($cardImages->getSmall());
        $cardImageLarge->setCard($card);
        $this->manager->persist($cardImageLarge);
    }

    /**
     * @param array<int, PokemonCardAttack>|null $pokemonCardAttacks
     */
    private function updatePokemonCardAttack(Card $card, ?array $pokemonCardAttacks): void
    {
        foreach ($pokemonCardAttacks ?? [] as $pokemonCardAttack) {
            $attack = $this->attackRepository->findOneByCardAndName($card, $pokemonCardAttack->getName());
            if (is_null($attack)) {
                $attack = new PokemonAttack();
                $attack->setName($pokemonCardAttack->getName());
            }
            $attack->setConvertedEnergyCost($pokemonCardAttack->getConvertedEnergyCost());
            $attack->setDamage($pokemonCardAttack->getDamage());
            $attack->setText($pokemonCardAttack->getText());
            $this->manager->persist($attack);
            /* @phpstan-ignore argument.type */
            $this->updatePokemonCardAttackCost($attack, $pokemonCardAttack->getCost());
        }
    }

    /**
     * @param array<int, string> $pokemonCardAttackCost
     */
    private function updatePokemonCardAttackCost(PokemonAttack $attack, array $pokemonCardAttackCost): void
    {
        foreach ($pokemonCardAttackCost as $pokemonCardAttackCostItem) {
            $itemCounts = array_count_values($pokemonCardAttackCost);
            if ($attack->checkCost($pokemonCardAttackCostItem) <= $itemCounts[$pokemonCardAttackCostItem]) {
                continue;
            }
            $cardType = $this->mappedTypes->offsetGet($pokemonCardAttackCostItem);
            if (is_null($cardType)) {
                $cardType = new Type();
                $cardType->setName($pokemonCardAttackCostItem);
                $this->mappedTypes->offsetSet($pokemonCardAttackCostItem, $cardType);
                $this->manager->persist($cardType);
            }
            $pokemonAttackCost = new PokemonAttackCost();
            $pokemonAttackCost->setPokemonAttack($attack);
            $pokemonAttackCost->setType($cardType);
            $attack->addCost($pokemonAttackCost);
            $cardType->addPokemonAttackCost($pokemonAttackCost);
            $this->manager->persist($pokemonAttackCost);
        }
    }

    /**
     * @param array<int, Resistance>|null $pokemonCardResistances
     */
    private function updatePokemonCardResistances(Card $card, ?array $pokemonCardResistances): void
    {
        foreach ($pokemonCardResistances ?? [] as $pokemonCardResistance) {
            $resistances = $this->resistanceRepository->findByCardAndTypeAndValue(
                $card,
                $pokemonCardResistance->getType(),
                $pokemonCardResistance->getValue()
            );
            if ([] !== $resistances) {
                continue;
            }
            $pokemonResistance = new PokemonResistance();
            $type = $pokemonCardResistance->getType();
            $pokemonResistanceType = $this->mappedTypes->offsetGet($type);
            if (is_null($pokemonResistanceType)) {
                $pokemonResistanceType = new Type();
                $pokemonResistanceType->setName($type);
                $this->mappedTypes->offsetSet($type, $pokemonResistanceType);
            }
            $pokemonResistanceType->addPokemonResistance($pokemonResistance);
            $pokemonResistance->setType($pokemonResistanceType);
            $this->manager->persist($pokemonResistanceType);
            $pokemonResistance->setValue($pokemonCardResistance->getValue());
            $pokemonResistance->addCard($card);
            $this->manager->persist($pokemonResistance);
        }
    }

    /**
     * @param array<int, Weakness>|null $pokemonCardWeaknesses
     */
    private function updatePokemonCardWeaknesses(Card $card, ?array $pokemonCardWeaknesses): void
    {
        foreach ($pokemonCardWeaknesses ?? [] as $pokemonCardWeakness) {
            $weakness = $this->weaknessRepository->findByCardAndTypeAndValue(
                $card,
                $pokemonCardWeakness->getType(),
                $pokemonCardWeakness->getValue()
            );
            if ([] !== $weakness) {
                continue;
            }
            $pokemonWeakness = new PokemonWeakness();
            $type = $pokemonCardWeakness->getType();
            $pokemonWeaknessType = $this->mappedTypes->offsetGet($type);
            if (is_null($pokemonWeaknessType)) {
                $pokemonWeaknessType = new Type();
                $pokemonWeaknessType->setName($type);
                $this->mappedTypes->offsetSet($type, $pokemonWeaknessType);
            }
            $pokemonWeaknessType->addPokemonWeakness($pokemonWeakness);
            $pokemonWeakness->setType($pokemonWeaknessType);
            $this->manager->persist($pokemonWeaknessType);
            $pokemonWeakness->setValue($pokemonCardWeakness->getValue());
            $pokemonWeakness->addCard($card);
            $this->manager->persist($pokemonWeakness);
        }
    }
}
