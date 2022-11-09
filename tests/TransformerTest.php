<?php

namespace DanialPanah\DataTransformer\Tests;

use DanialPanah\DataTransformer\DataTransformer;

class TransformerTest extends PhpDataTransformerTestCase
{
    protected DataTransformer $transformer;

    public function setUp(): void
    {
        $this->transformer = new DataTransformer('GoogleJobPost');
    }

    public function test__construct(): void
    {
        $this->assertInstanceOf('DanialPanah\DataTransformer\DataTransformer', $this->transformer);
    }

    public function test_setTitle(): void
    {
        $this->transformer->title = 'PHP Backend Developer';

        $this->assertIsString($this->transformer->title);
        $this->assertEquals('PHP Backend Developer', $this->transformer->title);
    }

    public function test_setFormattedDatePosted(): void
    {
        $this->transformer->datePosted = '2022-02-18';

        $this->assertIsString($this->transformer->datePosted);
        $this->assertEquals('2022-02-18', $this->transformer->datePosted);
    }

    public function test_setAtomicDatePosted(): void
    {
        $this->transformer->datePosted = '2012-12-25T15:30:45+00:00';

        $this->assertIsString($this->transformer->datePosted);
        $this->assertEquals('2012-12-25T15:30:45+00:00', $this->transformer->datePosted);
    }

    public function test_setDescription(): void
    {
        $this->transformer->description = '<p>Some description about this Job.</p>';

        $this->assertIsString($this->transformer->description);
        $this->assertEquals('<p>Some description about this Job.</p>', $this->transformer->description);
    }

    public function test_setHiringOrganization(): void
    {
        $this->transformer->hiringOrganization = [
            'name' => 'MagsRUs Wheel Company',
            'sameAs' => 'http://www.magsruswheelcompany.com',
            'logo' => 'http://www.example.com/images/logo.png'
        ];

        $this->assertIsArray($this->transformer->hiringOrganization);

        $this->assertArrayHasKey('name', $this->transformer->hiringOrganization);
        $this->assertArrayHasKey('sameAs', $this->transformer->hiringOrganization);

        $this->assertEquals('MagsRUs Wheel Company', $this->transformer->hiringOrganization['name']);
        $this->assertEquals('http://www.magsruswheelcompany.com', $this->transformer->hiringOrganization['sameAs']);
        $this->assertEquals('http://www.example.com/images/logo.png', $this->transformer->hiringOrganization['logo']);
    }

    public function test_setJobLocation(): void
    {
        $this->transformer->jobLocation = [
            'streetAddress' => '555 Clancy St',
            'addressLocality' => 'Detroit',
            'addressRegion' => 'MI',
            'postalCode' => '48201',
            'addressCountry' => 'US'
        ];

        $this->assertIsArray($this->transformer->jobLocation);

        $this->assertArrayHasKey('streetAddress', $this->transformer->jobLocation);
        $this->assertArrayHasKey('addressLocality', $this->transformer->jobLocation);
        $this->assertArrayHasKey('addressRegion', $this->transformer->jobLocation);
        $this->assertArrayHasKey('postalCode', $this->transformer->jobLocation);
        $this->assertArrayHasKey('addressCountry', $this->transformer->jobLocation);

        $this->assertEquals('555 Clancy St', $this->transformer->jobLocation['streetAddress']);
        $this->assertEquals('Detroit', $this->transformer->jobLocation['addressLocality']);
        $this->assertEquals('MI', $this->transformer->jobLocation['addressRegion']);
        $this->assertEquals('48201', $this->transformer->jobLocation['postalCode']);
        $this->assertEquals('US', $this->transformer->jobLocation['addressCountry']);
    }

    public function test_setMultipleEmploymentType(): void
    {
        $this->transformer->employmentType = ['FULL_TIME', 'PART_TIME'];

        $this->assertIsArray($this->transformer->employmentType);

        $this->assertArrayInArray($this->transformer->employmentType, ['FULL_TIME', 'PART_TIME']);
    }

    public function test_setDirectApply(): void
    {
        $this->transformer->directApply = false;

        $this->assertIsBool($this->transformer->directApply);

        $this->assertEquals($this->transformer->directApply, false);
    }

    public function test_setBaseSalary(): void
    {
        $this->transformer->baseSalary = [
            'currency' => 'USD',
            'minValue' => 100000.00,
            'maxValue' => 150000.00,
            'unitText' => 'YEAR'
        ];

        $this->assertIsArray($this->transformer->baseSalary);

        $this->assertArrayHasKey('currency', $this->transformer->baseSalary);
        $this->assertArrayHasKey('minValue', $this->transformer->baseSalary);
        $this->assertArrayHasKey('maxValue', $this->transformer->baseSalary);
        $this->assertArrayHasKey('unitText', $this->transformer->baseSalary);

        $this->assertIsFloat($this->transformer->baseSalary['minValue']);
        $this->assertIsFloat($this->transformer->baseSalary['maxValue']);

        $this->assertEquals('USD', $this->transformer->baseSalary['currency']);
        $this->assertEquals(100000.00, $this->transformer->baseSalary['minValue']);
        $this->assertEquals(150000.00, $this->transformer->baseSalary['maxValue']);
        $this->assertEquals('YEAR', $this->transformer->baseSalary['unitText']);
    }

    public function test_setFormattedValidThrough(): void
    {
        $this->transformer->validThrough = '2022-02-18';

        $this->assertIsString($this->transformer->validThrough);
        $this->assertEquals('2022-02-18', $this->transformer->validThrough);
    }

    public function test_setAtomicValidThrough(): void
    {
        $this->transformer->validThrough = '2012-12-25T15:30:45+00:00';

        $this->assertIsString($this->transformer->validThrough);
        $this->assertEquals('2012-12-25T15:30:45+00:00', $this->transformer->validThrough);
    }
}