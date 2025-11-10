<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case Created = 'created';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Failed = 'failed';
    case Incomplete = 'incomplete';

    /**
     * Human friendly label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::Created => 'Created',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
            self::Incomplete => 'Incomplete',
        };
    }

    /**
     * Tailwind CSS classes for the badge background + text color
     */
    public function color(): string
    {
        return match ($this) {
            self::Created => 'bg-gray-100 text-gray-800',
            self::InProgress => 'bg-yellow-100 text-yellow-800',
            self::Completed => 'bg-green-100 text-green-800',
            self::Failed => 'bg-red-100 text-red-800',
            self::Incomplete => 'bg-orange-100 text-orange-800',
        };
    }

    /**
     * Create a DocumentStatus from various string formats.
     * Normalizes hyphens/underscores and falls back to Created.
     */
    public static function fromString(string $value): self
    {
        $normalized = str_replace('-', '_', strtolower($value));

        return self::tryFrom($normalized) ?? self::Created;
    }
}
