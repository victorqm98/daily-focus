<?php

declare(strict_types=1);

namespace DailyFocus\Shared\Domain\ValueObjects;

use Ramsey\Uuid\Uuid;
use InvalidArgumentException;

final class Id
{
    private string $value;

    public function __construct(string $value)
    {
        $this->ensureIsValidUuid($value);
        $this->value = $value;
    }

    public static function random(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Id $other): bool
    {
        return $this->value === $other->value;
    }

    private function ensureIsValidUuid(string $value): void
    {
        if (!Uuid::isValid($value)) {
            throw new InvalidArgumentException('Invalid UUID format');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
