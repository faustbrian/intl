<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\Rules\PhoneNumberRule;
use Illuminate\Support\Facades\Validator;

describe('PhoneNumberRule', function (): void {
    describe('Happy Paths', function (): void {
        test('validates international phone number without region code', function (): void {
            // Arrange
            $data = ['attribute' => '+33123456789'];
            $rules = ['attribute' => new PhoneNumberRule()];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->passes())->toBeTrue();
        });

        test('validates national phone number with explicit region code', function (): void {
            // Arrange
            $data = ['attribute' => '01 23 45 67 89'];
            $rules = ['attribute' => new PhoneNumberRule(regionCode: 'FR')];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->passes())->toBeTrue();
        });

        test('validates phone number with region code from data reference', function (): void {
            // Arrange
            $data = ['attribute' => '01 23 45 67 89', 'region' => ['code' => 'FR']];
            $rules = ['attribute' => new PhoneNumberRule(regionCodeReference: 'region.code')];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->passes())->toBeTrue();
        });

        test('validates possible phone number in non-strict mode', function (): void {
            // Arrange
            $data = ['attribute' => '+33123456789'];
            $rules = ['attribute' => new PhoneNumberRule(shouldBeStrict: false)];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->passes())->toBeTrue();
        });

        test('validates valid phone number in strict mode', function (): void {
            // Arrange
            $data = ['attribute' => '+12024561111']; // Valid US number
            $rules = ['attribute' => new PhoneNumberRule(regionCode: 'US', shouldBeStrict: true)];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->passes())->toBeTrue();
        });

        test('validates phone number with strict mode and region code reference', function (): void {
            // Arrange
            $data = ['attribute' => '+441234567890', 'region' => ['code' => 'GB']];
            $rules = ['attribute' => new PhoneNumberRule(regionCodeReference: 'region.code', shouldBeStrict: true)];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->passes())->toBeTrue();
        });
    });

    describe('Sad Paths', function (): void {
        test('rejects invalid phone number format', function (): void {
            // Arrange
            $data = ['attribute' => 'XX'];
            $rules = ['attribute' => new PhoneNumberRule()];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->first('attribute'))->toBe('The attribute is not a valid phone number.');
        });

        test('rejects phone number that fails strict validation', function (): void {
            // Arrange
            $data = ['attribute' => '+1999999999999999']; // Too long to be valid
            $rules = ['attribute' => new PhoneNumberRule(shouldBeStrict: true)];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->fails())->toBeTrue();
        });

        test('rejects empty phone number', function (): void {
            // Arrange
            $data = ['attribute' => ''];
            $rules = ['attribute' => ['required', new PhoneNumberRule()]];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->fails())->toBeTrue();
        });

        test('rejects phone number with invalid region code', function (): void {
            // Arrange
            $data = ['attribute' => '1234567890'];
            $rules = ['attribute' => new PhoneNumberRule(regionCode: 'INVALID')];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->fails())->toBeTrue();
        });

        test('rejects phone number when region code reference is missing', function (): void {
            // Arrange
            $data = ['attribute' => '01 23 45 67 89']; // No region code in data
            $rules = ['attribute' => new PhoneNumberRule(regionCodeReference: 'region.code')];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->fails())->toBeTrue();
        });

        test('rejects phone number in strict mode with wrong format', function (): void {
            // Arrange
            $data = ['attribute' => 'not-a-phone'];
            $rules = ['attribute' => new PhoneNumberRule(regionCode: 'US', shouldBeStrict: true)];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->fails())->toBeTrue();
        });
    });

    describe('Edge Cases', function (): void {
        test('handles phone number with special characters', function (): void {
            // Arrange
            $data = ['attribute' => '+1 (202) 456-1111'];
            $rules = ['attribute' => new PhoneNumberRule()];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->passes())->toBeTrue();
        });

        test('handles phone number with only spaces', function (): void {
            // Arrange
            $data = ['attribute' => '   '];
            $rules = ['attribute' => ['required', new PhoneNumberRule()]];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->fails())->toBeTrue();
        });

        test('handles numeric phone number value cast to string', function (): void {
            // Arrange
            $data = ['attribute' => 1_234_567_890];
            $rules = ['attribute' => new PhoneNumberRule(regionCode: 'US')];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->passes())->toBeTrue();
        });

        test('handles null region code reference path', function (): void {
            // Arrange
            $data = ['attribute' => '+33123456789', 'region' => null];
            $rules = ['attribute' => new PhoneNumberRule(regionCodeReference: 'region')];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->passes())->toBeTrue();
        });

        test('handles deeply nested region code reference', function (): void {
            // Arrange
            $data = [
                'attribute' => '01 23 45 67 89',
                'country' => ['contact' => ['region' => 'FR']],
            ];
            $rules = ['attribute' => new PhoneNumberRule(regionCodeReference: 'country.contact.region')];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->passes())->toBeTrue();
        });

        test('handles very long invalid phone number', function (): void {
            // Arrange
            $data = ['attribute' => str_repeat('1', 100)];
            $rules = ['attribute' => new PhoneNumberRule()];

            // Act
            $validator = Validator::make($data, $rules);

            // Assert
            expect($validator->fails())->toBeTrue();
        });
    });
});
