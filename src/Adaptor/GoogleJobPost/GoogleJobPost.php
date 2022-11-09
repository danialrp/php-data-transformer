<?php

namespace DanialPanah\DataTransformer\Adaptor\GoogleJobPost;

use DanialPanah\DataTransformer\Adaptor\JobPost;
use DanialPanah\DataTransformer\Contracts\AdaptorInterface;
use DanialPanah\DataTransformer\Validations\Validation;

class GoogleJobPost extends JobPost implements AdaptorInterface
{
    /**
     * @var Validation
     * Google job posting validation
     */
    protected Validation $validation;

    /**
     * @var array
     * Validation Rules
     */
    public $rules;

    /**
     * @var array
     * Package Configuration
     */
    public $config;

    /**
     * @var string
     * DateTime
     */
    private string $datePosted;

    /**
     * @var string
     * Text
     */
    private string $description;

    /**
     * @var string
     * Organization
     */
    private array $hiringOrganization;

    /**
     * @var string
     * Place
     */
    private array $jobLocation;

    /**
     * @var string
     * Text
     */
    private string $title;


    /**
     * @var string|array
     * Text or Array of Enums
     */
    private string|array $employmentType;

    /**
     * @var mixed
     * Boolean
     */
    private $directApply;

    /**
     * @var string
     * DateTime
     */
    private $validThrough;

    /**
     * @var array
     * MonetaryAmount
     */
    private $baseSalary;


    public function __construct(Validation $validation)
    {
        $this->validation = $validation;
        $this->rules = include 'Rules.php';
        $this->config = include 'Config.php';
    }

    public function __set($attribute, $value)
    {
        $this->$attribute = $value;
    }

    public function __get($attribute)
    {
        if (isset($this->$attribute)) return $this->$attribute;
    }

    public function excludedJsonSerializeProperties(): array
    {
        return ['validation', 'rules', 'config'];
    }

    public function setTitle(string $value): self
    {
        $this->validation->passes('title', $value, $this->rules['title']);

        $this->title = $value;

        return $this;
    }

    public function setDatePosted(string $value): self
    {
        $this->validation->passes('datePosted', $value, $this->rules['datePosted']);

        $this->datePosted = $value;

        return $this;
    }

    public function setDescription(string $value): self
    {
        $this->validation->passes('description', $value, $this->rules['description']);

        $this->description = $value;
        return $this;
    }

    public function setHiringOrganization(array $value): self
    {
        $this->validation->passes('hiringOrganization', $value, $this->rules['hiringOrganization']);

        $this->hiringOrganization = [
            "@type" => "Organization",
            "name" => $value['name'],
            "sameAs" => $value['sameAs'],
            "logo" => $value['logo'] ?? ''
        ];

        return $this;
    }

    public function setJobLocation(array $value): self
    {
        $this->validation->passes('jobLocation', $value, $this->rules['jobLocation']);

        $this->jobLocation = [
            "@type" => "Place",
            "address" => [
                "@type" => "PostalAddress",
                "streetAddress" => $value['streetAddress'],
                "addressLocality" => $value['addressLocality'],
                "addressRegion" => $value['addressRegion'],
                "postalCode" => $value['postalCode'],
                "addressCountry" => $value['addressCountry']
            ]
        ];

        return $this;
    }

    public function setEmploymentType(array|string $value): self
    {
        $this->validation->passes('employmentType', $value, $this->rules['employmentType']);

        $this->employmentType = $value;

        return $this;
    }

    public function setDirectApply($value): self
    {
        $this->validation->passes('directApply', $value, $this->rules['directApply']);

        $trueValues = ['true', 1, '1', 'on', 'yes'];
        $falseValues = ['false', 0, '0', 'off', 'no'];

        if (in_array($value, $trueValues))
            $this->directApply = true;
        elseif (in_array($value, $falseValues))
            $this->directApply = false;
        else
            $this->directApply = $value;

        return $this;
    }

    public function setValidThrough(string $value): self
    {
        $this->validation->passes('validThrough', $value, $this->rules['validThrough']);

        $this->validThrough = $value;

        return $this;
    }

    public function setBaseSalary(array $value): self
    {
        $this->validation->passes('baseSalary', $value, $this->rules['baseSalary']);

        $this->baseSalary = [
            "@type" => "MonetaryAmount",
            "currency" => $value['currency'],
            "value" => [
                "@type" => "QuantitativeValue",
                "minValue" => $value['minValue'],
                "maxValue" => $value['maxValue'],
                "unitText" => $value['unitText']
            ]
        ];

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        $this->passRequiredProperties($this);

        $properties = get_object_vars($this);

        foreach ($properties as $key => $property)
            if (!isset($property)) unset($properties[$key]);

        $excludedProperties = array_flip($this->excludedJsonSerializeProperties());

        return array_diff_key($properties, $excludedProperties);
    }

    public function embedToStructuredHtml(): string
    {
        $htmlOutput = "";
        $embeddingData = $this->jsonSerialize();
        $embeddingData['@context'] = 'https://schema.org/';
        $embeddingData['@type'] = 'JobPosting';

        $htmlOutput .= "<html><head><title>{$embeddingData['title']}</title>";
        $htmlOutput .= "<script type={$this->config['html']['application_type']}>";
        $htmlOutput .= json_encode($embeddingData);
        $htmlOutput .= "</script></head><body></body></html>";

        return $htmlOutput;
    }
}