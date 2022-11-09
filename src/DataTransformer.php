<?php

namespace DanialPanah\DataTransformer;

use DanialPanah\DataTransformer\Container\AppContainer;
use DanialPanah\DataTransformer\Exceptions\NotFoundException;

/**
 * @property string title
 * @property string datePosted
 * @property string description
 * @property array hiringOrganization
 * @property array jobLocation
 * @property string|array employmentType
 * @property mixed directApply
 * @property string validThrough
 * @property array baseSalary
 */
class DataTransformer
{
    /**
     * @var AppContainer
     * Container Object
     */
    private AppContainer $appContainer;

    /**
     * @var object
     * Adaptor Object
     */
    private object $transformer;


    public function __construct(string $transformer)
    {
        $this->appContainer = new AppContainer();

        $adaptorTransformer = "DanialPanah\DataTransformer\Adaptor\\{$transformer}\\{$transformer}";
        $this->transformer = $this->appContainer->make($adaptorTransformer);
    }

    public function __set($attribute, $value)
    {
        $transformerMethods = get_class_methods($this->transformer);
        $transformerSetter = 'set' . ucfirst($attribute);

        if (in_array($transformerSetter, $transformerMethods)) {
            $this->transformer->$transformerSetter($value);
            return;
        }

        $this->transformer->$attribute = $value;
    }

    public function __get($attribute)
    {
        if (isset($this->transformer->$attribute))
            return $this->transformer->$attribute;
        else
            throw new NotFoundException("'{$attribute}' property doesn't exists in this context.");
    }

    public function formatToArray(): array
    {
        return $this->transformer->jsonSerialize();
    }

    public function formatToJson(): string
    {
        return json_encode($this->transformer->jsonSerialize());
    }

    public function formatToHtml(): string
    {
        return $this->transformer->embedToStructuredHtml();
    }
}