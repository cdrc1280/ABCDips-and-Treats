<?php

namespace App\Domains\Analytics\Enums;

/** The dashboard's date-range filter. Daily buckets up to 90 days, monthly for the year. */
enum DashboardPeriod: string
{
    case Week = '7d';
    case Month = '30d';
    case Quarter = '90d';
    case Year = '12m';

    public const DEFAULT = self::Month;

    public static function fromQuery(mixed $value): self
    {
        return is_string($value) ? (self::tryFrom($value) ?? self::DEFAULT) : self::DEFAULT;
    }

    public function label(): string
    {
        return match ($this) {
            self::Week => 'Last 7 days',
            self::Month => 'Last 30 days',
            self::Quarter => 'Last 90 days',
            self::Year => 'Last 12 months',
        };
    }

    public function monthly(): bool
    {
        return $this === self::Year;
    }

    /** Number of buckets (days, or months for the year view). */
    public function buckets(): int
    {
        return match ($this) {
            self::Week => 7,
            self::Month => 30,
            self::Quarter => 90,
            self::Year => 12,
        };
    }

    /** @return list<array{value: string, label: string}> */
    public static function options(): array
    {
        return array_map(fn (self $p) => ['value' => $p->value, 'label' => $p->label()], self::cases());
    }
}
