<?php

namespace DanialPanah\DataTransformer\Contracts;

interface AdaptorInterface
{
    public function excludedJsonSerializeProperties(): array;
}