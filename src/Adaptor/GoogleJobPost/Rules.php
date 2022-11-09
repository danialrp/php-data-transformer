<?php

return [
    'title' => ['required', 'string'],

    'datePosted' => ['required', 'dateTime'],

    'description' => ['required', 'string'],

    'hiringOrganization' => ['required', 'array', 'array_keys:name, sameAs'],

    'jobLocation' => [
        'required', 'array',
        'array_keys:streetAddress, addressLocality, addressRegion, postalCode, addressCountry'
    ],

    'employmentType' => ['in:FULL_TIME, PART_TIME, CONTRACTOR, TEMPORARY, INTERN, VOLUNTEER, PER_DIEM, OTHER'],

    'directApply' => ['boolean'],

    'validThrough' => ['dateTime'],

    'baseSalary' => [
        'array', 'array_keys:currency, minValue, maxValue, unitText',
        'unitText.in: HOUR, DAY, WEEK, MONTH, YEAR',
        'currency.in: EUR, USD, GBP'
    ]
];