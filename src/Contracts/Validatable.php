<?php

namespace DanialPanah\DataTransformer\Contracts;

interface Validatable
{
    public function passRequiredProperties(object $validatable): void;
}