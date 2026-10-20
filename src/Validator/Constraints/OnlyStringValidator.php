<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Utils\KanaChecker;
use App\Utils\KanjiChecker;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class OnlyStringValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_scalar($value) && !$value instanceof \Stringable) {
            throw new UnexpectedValueException($value, 'string');
        }

        $value = (string) $value;

        if (
            KanjiChecker::isKanji($value)
            || KanaChecker::isKana($value)
            || is_numeric($value)
        ) {
            $this->context->buildViolation($constraint->message) // @phpstan-ignore-line
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
