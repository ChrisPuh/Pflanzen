<?php

declare(strict_types=1);

namespace App\Enums\Area;

use App\Traits\HasValues;
use Illuminate\Support\Collection;

enum AreaTypeEnum: string
{
    use HasValues;

    case FlowerBed = 'flower_bed';
    case VegetableBed = 'vegetable_bed';
    case HerbBed = 'herb_bed';
    case Lawn = 'lawn';
    case Meadow = 'meadow';
    case Terrace = 'terrace';
    case Patio = 'patio';
    case House = 'house';
    case Greenhouse = 'greenhouse';
    case Pool = 'pool';
    case Pond = 'pond';
    case Shed = 'shed';
    case Compost = 'compost';
    case Pathway = 'pathway';
    case Rockery = 'rockery';
    case TreeArea = 'tree_area';
    case Playground = 'playground';
    case Seating = 'seating';
    case Storage = 'storage';
    case Other = 'other';

    public static function options(): Collection
    {
        return collect(self::cases())->map(fn (self $case): array => [
            'value' => $case->value,
            'label' => $case->getLabel(),
            'description' => $case->description(),
            'category' => $case->category(),
        ]);
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::FlowerBed => 'Blumenbeet',
            self::VegetableBed => 'Gemüsebeet',
            self::HerbBed => 'Kräuterbeet',
            self::Lawn => 'Rasen',
            self::Meadow => 'Blumenwiese',
            self::Terrace => 'Terrasse',
            self::Patio => 'Innenhof',
            self::House => 'Haus',
            self::Greenhouse => 'Gewächshaus',
            self::Pool => 'Pool',
            self::Pond => 'Teich',
            self::Shed => 'Schuppen',
            self::Compost => 'Kompost',
            self::Pathway => 'Weg',
            self::Rockery => 'Steingarten',
            self::TreeArea => 'Baumbereich',
            self::Playground => 'Spielplatz',
            self::Seating => 'Sitzbereich',
            self::Storage => 'Lagerbereich',
            self::Other => 'Sonstiges',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::FlowerBed => 'Ein Bereich mit Zierpflanzen und Blumen',
            self::VegetableBed => 'Ein Bereich zum Anbau von Gemüse',
            self::HerbBed => 'Ein spezieller Bereich für Kräuter und Gewürze',
            self::Lawn => 'Eine gepflegte Rasenfläche',
            self::Meadow => 'Eine natürliche Wiese mit Wildblumen',
            self::Terrace => 'Eine befestigte Außenfläche',
            self::Patio => 'Ein umschlossener Außenbereich',
            self::House => 'Das Hauptgebäude',
            self::Greenhouse => 'Ein Gewächshaus für geschützten Anbau',
            self::Pool => 'Ein Schwimmbecken',
            self::Pond => 'Ein Gartenteich oder Wasserspiel',
            self::Shed => 'Ein Schuppen für Gartengeräte',
            self::Compost => 'Ein Kompostbereich für organische Abfälle',
            self::Pathway => 'Wege und Pfade im Garten',
            self::Rockery => 'Ein Steingarten mit alpinen Pflanzen',
            self::TreeArea => 'Ein Bereich mit Bäumen und Sträuchern',
            self::Playground => 'Ein Spielbereich für Kinder',
            self::Seating => 'Sitzgelegenheiten und Ruhebereiche',
            self::Storage => 'Lager- und Aufbewahrungsbereiche',
            self::Other => 'Andere nicht kategorisierte Bereiche',
        };
    }

    public function category(): string
    {
        return match ($this) {
            self::FlowerBed, self::VegetableBed, self::HerbBed => 'Pflanzbereich',
            self::Lawn, self::Meadow => 'Grünfläche',
            self::Terrace, self::Patio, self::Seating => 'Aufenthaltsbereich',
            self::House, self::Greenhouse, self::Shed, self::Storage => 'Gebäude',
            self::Pool, self::Pond => 'Wasserelement',
            self::Compost, self::Pathway, self::Rockery => 'Funktionsbereich',
            self::TreeArea => 'Gehölz',
            self::Playground => 'Freizeit',
            self::Other => 'Sonstiges',
        };
    }

    public function isPlantingArea(): bool
    {
        return in_array($this, [
            self::FlowerBed,
            self::VegetableBed,
            self::HerbBed,
            self::Meadow,
            self::Rockery,
            self::TreeArea,
        ]);
    }

    public function isWaterFeature(): bool
    {
        return in_array($this, [
            self::Pool,
            self::Pond,
        ]);
    }

    public function isBuilding(): bool
    {
        return in_array($this, [
            self::House,
            self::Greenhouse,
            self::Shed,
            self::Storage,
        ]);
    }
}
