<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Utils\KanaChecker;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class KanaIdeogramCodeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof KanaIdeogramCode) {
            throw new \InvalidArgumentException(sprintf('Expected instance of %s', KanaIdeogramCode::class));
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_scalar($value) && !$value instanceof \Stringable) {
            throw new UnexpectedValueException($value, 'string');
        }

        $value = (string) $value;

        if (!KanaChecker::isKana($value, $constraint->allowProlongedSoundMark)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
