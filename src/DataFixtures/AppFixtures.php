<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Card;
use App\Entity\CardImage;
use App\Entity\PokemonAttack;
use App\Entity\PokemonAttackCost;
use App\Entity\PokemonResistance;
use App\Entity\PokemonWeakness;
use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use stdClass;

final class AppFixtures extends Fixture
{
    /**
     * @var ArrayCollection<string, Type>
     */
    private readonly ArrayCollection $mappedTypes;

    public function __construct()
    {
        $this->mappedTypes = new ArrayCollection();
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getDefaultCardList() as $cardJson) {
            $cardData = (object) json_decode($cardJson, true);
            $this->addNewCard($manager, $cardData);
        }

        $manager->flush();
    }

    private function addNewCard(ObjectManager $manager, stdClass $cardData): void
    {
        $card = new Card();
        /* @phpstan-ignore argument.type */
        $card->setId($cardData->id);
        /* @phpstan-ignore argument.type */
        $card->setName($cardData->name);
        /* @phpstan-ignore argument.type */
        $card->setSupertype($cardData->supertype);
        $manager->persist($card);

        $types = (array) $cardData->types;
        foreach ($types as $type) {
            /* @phpstan-ignore argument.type */
            $cardType = $this->mappedTypes->offsetGet($type);
            if (is_null($cardType)) {
                $cardType = new Type();
                /* @phpstan-ignore argument.type */
                $cardType->setName($type);
                /* @phpstan-ignore argument.type */
                $this->mappedTypes->offsetSet($type, $cardType);
            }
            $cardType->addCard($card);
            $manager->persist($cardType);
        }

        $images = (array) $cardData->images;
        foreach ($images as $type => $url) {
            $cardImage = new CardImage();
            $cardImage->setType($type);
            /* @phpstan-ignore argument.type */
            $cardImage->setUrl($url);
            $cardImage->setCard($card);
            $manager->persist($cardImage);
        }

        if (isset($cardData->attacks)) {
            $attacks = (array) $cardData->attacks;
            foreach ($attacks as $attack) {
                $pokemonAttack = new PokemonAttack();
                /* @phpstan-ignore offsetAccess.nonOffsetAccessible, argument.type */
                $pokemonAttack->setName($attack['name']);
                /* @phpstan-ignore offsetAccess.nonOffsetAccessible */
                $costData = (array) $attack['cost'];
                foreach ($costData as $costItem) {
                    /* @phpstan-ignore argument.type */
                    $type = $this->mappedTypes->offsetGet($costItem);
                    if (is_null($type)) {
                        $type = new Type();
                        /* @phpstan-ignore argument.type */
                        $type->setName($costItem);
                        /* @phpstan-ignore argument.type */
                        $this->mappedTypes->offsetSet($costItem, $type);
                        $manager->persist($type);
                    }
                    $pokemonAttackCost = new PokemonAttackCost();
                    $pokemonAttackCost->setPokemonAttack($pokemonAttack);
                    $pokemonAttackCost->setType($type);
                    $pokemonAttack->addCost($pokemonAttackCost);
                    $type->addPokemonAttackCost($pokemonAttackCost);
                    $manager->persist($pokemonAttackCost);
                }
                /* @phpstan-ignore offsetAccess.nonOffsetAccessible, argument.type */
                $pokemonAttack->setConvertedEnergyCost($attack['convertedEnergyCost']);
                /* @phpstan-ignore offsetAccess.nonOffsetAccessible, argument.type */
                $pokemonAttack->setDamage($attack['damage']);
                /* @phpstan-ignore offsetAccess.nonOffsetAccessible, argument.type */
                $pokemonAttack->setText($attack['text']);
                $pokemonAttack->addCard($card);
                $manager->persist($pokemonAttack);
            }
        }

        if (isset($cardData->resistances)) {
            $resistances = (array) $cardData->resistances;
            foreach ($resistances as $resistance) {
                $pokemonResistance = new PokemonResistance();
                /* @phpstan-ignore offsetAccess.nonOffsetAccessible */
                $type = $resistance['type'];
                /* @phpstan-ignore argument.type */
                $pokemonResistanceType = $this->mappedTypes->offsetGet($type);
                if (is_null($pokemonResistanceType)) {
                    $pokemonResistanceType = new Type();
                    /* @phpstan-ignore argument.type */
                    $pokemonResistanceType->setName($type);
                    /* @phpstan-ignore argument.type */
                    $this->mappedTypes->offsetSet($type, $pokemonResistanceType);
                }
                $pokemonResistanceType->addPokemonResistance($pokemonResistance);
                $pokemonResistance->setType($pokemonResistanceType);
                $manager->persist($pokemonResistanceType);
                /* @phpstan-ignore offsetAccess.nonOffsetAccessible, argument.type */
                $pokemonResistance->setValue($resistance['value']);
                $pokemonResistance->addCard($card);
                $manager->persist($pokemonResistance);
            }
        }

        if (isset($cardData->weaknesses)) {
            $weaknesses = (array) $cardData->weaknesses;
            foreach ($weaknesses as $weakness) {
                $pokemonWeakness = new PokemonWeakness();
                /* @phpstan-ignore offsetAccess.nonOffsetAccessible */
                $weaknessType = $weakness['type'];
                /* @phpstan-ignore argument.type */
                $pokemonWeaknessType = $this->mappedTypes->offsetGet($weaknessType);
                if (is_null($pokemonWeaknessType)) {
                    $pokemonWeaknessType = new Type();
                    /* @phpstan-ignore argument.type */
                    $pokemonWeaknessType->setName($weaknessType);
                    /* @phpstan-ignore argument.type */
                    $this->mappedTypes->offsetSet($weaknessType, $pokemonWeaknessType);
                }
                $pokemonWeaknessType->addPokemonWeakness($pokemonWeakness);
                $pokemonWeakness->setType($pokemonWeaknessType);
                $manager->persist($pokemonWeaknessType);
                /* @phpstan-ignore offsetAccess.nonOffsetAccessible, argument.type */
                $pokemonWeakness->setValue($weakness['value']);
                $pokemonWeakness->addCard($card);
                $manager->persist($pokemonWeakness);
            }
        }
    }

    /**
     * @return array<mixed, string>
     */
    private function getDefaultCardList(): array
    {
        return [
            '{
              "id": "swsh4-25",
              "name": "Charizard",
              "supertype": "Pokémon",
              "subtypes": [
                "Stage 2"
              ],
              "hp": "170",
              "types": [
                "Fire"
              ],
              "evolvesFrom": "Charmeleon",
              "abilities": [
                {
                  "name": "Battle Sense",
                  "text": "Once during your turn, you may look at the top 3 cards of your deck and put 1 of them into your hand. Discard the other cards.",
                  "type": "Ability"
                }
              ],
              "attacks": [
                {
                  "name": "Royal Blaze",
                  "cost": [
                    "Fire",
                    "Fire"
                  ],
                  "convertedEnergyCost": 2,
                  "damage": "100+",
                  "text": "This attack does 50 more damage for each Leon card in your discard pile."
                }
              ],
              "weaknesses": [
                {
                  "type": "Water",
                  "value": "×2"
                }
              ],
              "retreatCost": [
                "Colorless",
                "Colorless",
                "Colorless"
              ],
              "convertedRetreatCost": 3,
              "set": {
                "id": "swsh4",
                "name": "Vivid Voltage",
                "series": "Sword & Shield",
                "printedTotal": 185,
                "total": 203,
                "legalities": {
                  "unlimited": "Legal",
                  "standard": "Legal",
                  "expanded": "Legal"
                },
                "ptcgoCode": "VIV",
                "releaseDate": "2020/11/13",
                "updatedAt": "2020/11/13 16:20:00",
                "images": {
                  "symbol": "https://images.pokemontcg.io/swsh4/symbol.png",
                  "logo": "https://images.pokemontcg.io/swsh4/logo.png"
                }
              },
              "number": "25",
              "artist": "Ryuta Fuse",
              "rarity": "Rare",
              "flavorText": "It spits fire that is hot enough to melt boulders. It may cause forest fires by blowing flames.",
              "nationalPokedexNumbers": [
                6
              ],
              "legalities": {
                "unlimited": "Legal",
                "standard": "Legal",
                "expanded": "Legal"
              },
              "images": {
                "small": "https://images.pokemontcg.io/swsh4/25.png",
                "large": "https://images.pokemontcg.io/swsh4/25_hires.png"
              },
              "tcgplayer": {
                "url": "https://prices.pokemontcg.io/tcgplayer/swsh4-25",
                "updatedAt": "2021/08/04",
                "prices": {
                  "normal": {
                    "low": 1.73,
                    "mid": 3.54,
                    "high": 12.99,
                    "market": 2.82,
                    "directLow": 3.93
                  },
                  "reverseHolofoil": {
                    "low": 3,
                    "mid": 8.99,
                    "high": 100,
                    "market": 3.89,
                    "directLow": 4.46
                  }
                }
              },
              "cardmarket": {
                "url": "https://prices.pokemontcg.io/cardmarket/swsh4-25",
                "updatedAt": "2021/08/04",
                "prices": {
                  "averageSellPrice": 9.38,
                  "lowPrice": 8.95,
                  "trendPrice": 10.29,
                  "germanProLow": null,
                  "suggestedPrice": null,
                  "reverseHoloSell": null,
                  "reverseHoloLow": null,
                  "reverseHoloTrend": null,
                  "lowPriceExPlus": 8.95,
                  "avg1": 9.95,
                  "avg7": 9.35,
                  "avg30": 11.31,
                  "reverseHoloAvg1": null,
                  "reverseHoloAvg7": null,
                  "reverseHoloAvg30": null
                }
            }
            }',
            '{
                "id": "xy1-1",
                "name": "Venusaur-EX",
                "supertype": "Pokémon",
                "subtypes": [
                  "Basic",
                  "EX"
                ],
                "hp": "180",
                "types": [
                  "Grass"
                ],
                "evolvesTo": [
                  "M Venusaur-EX"
                ],
                "rules": [
                  "Pokémon-EX rule: When a Pokémon-EX has been Knocked Out, your opponent takes 2 Prize cards."
                ],
                "attacks": [
                  {
                    "name": "Poison Powder",
                    "cost": [
                      "Grass",
                      "Colorless",
                      "Colorless"
                    ],
                    "convertedEnergyCost": 3,
                    "damage": "60",
                    "text": "Your opponent\'s Active Pokémon is now Poisoned."
                  },
                  {
                    "name": "Jungle Hammer",
                    "cost": [
                      "Grass",
                      "Grass",
                      "Colorless",
                      "Colorless"
                    ],
                    "convertedEnergyCost": 4,
                    "damage": "90",
                    "text": "Heal 30 damage from this Pokémon."
                  }
                ],
                "weaknesses": [
                  {
                    "type": "Fire",
                    "value": "×2"
                  }
                ],
                "retreatCost": [
                  "Colorless",
                  "Colorless",
                  "Colorless",
                  "Colorless"
                ],
                "convertedRetreatCost": 4,
                "set": {
                  "id": "xy1",
                  "name": "XY",
                  "series": "XY",
                  "printedTotal": 146,
                  "total": 146,
                  "legalities": {
                    "unlimited": "Legal",
                    "expanded": "Legal"
                  },
                  "ptcgoCode": "XY",
                  "releaseDate": "2014/02/05",
                  "updatedAt": "2018/03/04 10:35:00",
                  "images": {
                    "symbol": "https://images.pokemontcg.io/xy1/symbol.png",
                    "logo": "https://images.pokemontcg.io/xy1/logo.png"
                  }
                },
                "number": "1",
                    "artist": "Eske Yoshinob",
                    "rarity": "Rare Holo EX",
                    "nationalPokedexNumbers": [
                    3
                ],
                "legalities": {
                "unlimited": "Legal",
                  "expanded": "Legal"
                },
                "images": {
                  "small": "https://images.pokemontcg.io/xy1/1.png",
                  "large": "https://images.pokemontcg.io/xy1/1_hires.png"
                },
                "tcgplayer": {
                "url": "https://prices.pokemontcg.io/tcgplayer/xy1-1",
                  "updatedAt": "2021/07/09",
                  "prices": {
                    "holofoil": {
                        "low": 1.0,
                      "mid": 3.46,
                      "high": 12.95,
                      "market": 3.32,
                      "directLow": 2.95
                    }
                  }
                }
            }',
        ];
    }
}
