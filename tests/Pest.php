<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Struct\Metadata\MetadataFactory;
use Cline\Struct\Metadata\PropertyMetadata;
use Tests\Fakes\PropertyMetadataData;
use Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function dummyPropertyMetadata(): PropertyMetadata
{
    static $property;

    if ($property instanceof PropertyMetadata) {
        return $property;
    }

    foreach ((new MetadataFactory())->for(PropertyMetadataData::class)->properties as $candidate) {
        if ($candidate->name === 'value') {
            return $property = $candidate;
        }
    }

    throw new RuntimeException('Failed to resolve dummy property metadata.');
}
