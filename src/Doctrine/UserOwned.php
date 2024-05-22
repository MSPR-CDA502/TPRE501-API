<?php

namespace App\Doctrine;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class UserOwned
{
    public function __construct(
        protected string $property = 'owner',
    ) {
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function setProperty(string $property): self
    {
        $this->property = $property;

        return $this;
    }
}
