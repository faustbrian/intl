## Table of Contents

1. [Overview](#doc-docs-readme) (`docs/README.md`)
2. [Casts And Rules](#doc-docs-casts-and-rules) (`docs/casts-and-rules.md`)
3. [Country](#doc-docs-country) (`docs/country.md`)
4. [Currency](#doc-docs-currency) (`docs/currency.md`)
5. [Language Locale](#doc-docs-language-locale) (`docs/language-locale.md`)
6. [Timezone](#doc-docs-timezone) (`docs/timezone.md`)
<a id="doc-docs-readme"></a>

Cline Intl provides type-safe internationalization value objects and validation rules for Laravel applications. Built on top of Symfony Intl components and Spatie Laravel Data, it offers a modern, elegant approach to handling countries, currencies, locales, timezones, phone numbers, postal codes, and addresses.

## Requirements

- **PHP 8.5+**
- **Laravel 12+**
- **Spatie Laravel Data 4.18+**

## Installation

Install the package via Composer:

```bash
composer require cline/intl
```

No configuration or service provider registration is required. The package is automatically discovered by Laravel.

## Key Features

### Type-Safe Value Objects

All internationalization data is wrapped in immutable value objects that extend Spatie's Laravel Data:

```php
use Cline\Intl\ValueObjects\Country;
use Cline\Intl\ValueObjects\Currency;
use Cline\Intl\ValueObjects\Language;
use Cline\Intl\ValueObjects\Locale;
use Cline\Intl\ValueObjects\TimeZone;
use Cline\Intl\ValueObjects\PhoneNumber;
use Cline\Intl\ValueObjects\PostalCode;
use Cline\Intl\ValueObjects\Address;
```

### Eloquent Model Casts

Seamlessly integrate value objects with your Eloquent models using built-in casts:

```php
use Cline\Intl\Data\Cast\CountryCast;
use Cline\Intl\Data\Cast\CurrencyCast;
use Cline\Intl\Data\Cast\LanguageCast;
use Cline\Intl\Data\Cast\LocaleCast;
use Cline\Intl\Data\Cast\TimeZoneCast;
use Cline\Intl\Data\Cast\PhoneNumberCast;
use Cline\Intl\Data\Cast\PostalCodeCast;

class User extends Model
{
    protected function casts(): array
    {
        return [
            'country' => CountryCast::class,
            'preferred_currency' => CurrencyCast::class,
            'language' => LanguageCast::class,
            'timezone' => TimeZoneCast::class,
        ];
    }
}
```

### Validation Rules

Validate user input with dedicated validation rules:

```php
use Cline\Intl\Rules\CountryRule;
use Cline\Intl\Rules\CurrencyRule;
use Cline\Intl\Rules\LanguageRule;
use Cline\Intl\Rules\LocaleRule;
use Cline\Intl\Rules\PhoneNumberRule;
use Cline\Intl\Rules\TimeZoneRule;

$request->validate([
    'country' => ['required', new CountryRule()],
    'currency' => ['required', new CurrencyRule()],
    'language' => ['required', new LanguageRule()],
    'phone' => ['required', new PhoneNumberRule()],
]);
```

## Quick Example

Here's a complete example showing the basic workflow:

```php
use Cline\Intl\ValueObjects\Country;
use Cline\Intl\ValueObjects\Currency;

// Create value objects from strings
$country = Country::createFromString('US');
$currency = Currency::createFromString('USD');

// Access properties
echo $country->localized;  // "United States"
echo $country->alpha2;     // "US"
echo $country->alpha3;     // "USA"

echo $currency->name;             // "US Dollar"
echo $currency->symbol;           // "$"
echo $currency->fractionDigits;   // 2

// Use in Eloquent models
$user = new User();
$user->country = 'US';  // Automatically cast to Country object
$user->save();

// Access as object
echo $user->country->localized;  // "United States"

// Compare instances
$other = Country::createFromString('US');
$country->isEqualTo($other);  // true

// Convert to string
(string) $country;  // "US"
```

## Underlying Libraries

Cline Intl builds on solid foundation libraries:

- **[Symfony Intl](https://symfony.com/doc/current/components/intl.html)** - Country, Currency, Language, Locale, and TimeZone data
- **[Brick PhoneNumber](https://github.com/brick/phonenumber)** - Phone number parsing and formatting
- **[Brick Postcode](https://github.com/brick/postcode)** - Postal code validation and formatting
- **[CommerceGuys Addressing](https://github.com/commerceguys/addressing)** - Address handling based on xAL standard
- **[Spatie Laravel Data](https://spatie.be/docs/laravel-data)** - Data transfer objects with casting support

## Next Steps

Explore the detailed documentation for each value object:

- **[Country](#doc-docs-country)** - ISO 3166-1 country codes with alpha-2 and alpha-3 support
- **[Currency](#doc-docs-currency)** - ISO 4217 currency codes with symbols and fraction digits
- **[Language & Locale](#doc-docs-language-locale)** - Language and locale handling
- **[TimeZone](#doc-docs-timezone)** - Timezone support with localized names
- **[Casts & Rules](#doc-docs-casts-and-rules)** - Complete guide to model casts and validation rules

<a id="doc-docs-casts-and-rules"></a>

Cline Intl provides both Eloquent model casts and Laravel validation rules for all internationalization value objects. This page provides a comprehensive guide to using both features together in your Laravel applications.

## Overview

Each value object in Cline Intl comes with two essential features:

1. **Eloquent Casts** - Automatically convert database values to/from value objects
2. **Validation Rules** - Validate user input before it reaches your models

This separation ensures that invalid data never enters your database while providing convenient object access in your application code.

## Eloquent Casts

### Available Casts

```php
use Cline\Intl\Data\Cast\CountryCast;
use Cline\Intl\Data\Cast\CurrencyCast;
use Cline\Intl\Data\Cast\LanguageCast;
use Cline\Intl\Data\Cast\LocaleCast;
use Cline\Intl\Data\Cast\TimeZoneCast;
use Cline\Intl\Data\Cast\PhoneNumberCast;
use Cline\Intl\Data\Cast\PostalCodeCast;
```

### Basic Usage

Define casts in your Eloquent model's `casts()` method:

```php
use Illuminate\Database\Eloquent\Model;
use Cline\Intl\Data\Cast\CountryCast;
use Cline\Intl\Data\Cast\CurrencyCast;
use Cline\Intl\Data\Cast\LanguageCast;
use Cline\Intl\Data\Cast\TimeZoneCast;

class User extends Model
{
    protected function casts(): array
    {
        return [
            'country' => CountryCast::class,
            'currency' => CurrencyCast::class,
            'language' => LanguageCast::class,
            'timezone' => TimeZoneCast::class,
        ];
    }
}
```

### How Casts Work

Casts automatically handle conversion between database strings and value objects:

```php
// Setting values (accepts strings)
$user = new User();
$user->country = 'US';          // Stored as "US" in database
$user->currency = 'USD';        // Stored as "USD" in database
$user->language = 'en';         // Stored as "en" in database
$user->timezone = 'UTC';        // Stored as "UTC" in database
$user->save();

// Retrieving values (returns value objects)
$user = User::find(1);
echo $user->country->localized;     // "United States"
echo $user->currency->symbol;       // "$"
echo $user->language->localized;    // "English"
echo $user->timezone->localized;    // "Coordinated Universal Time"
```

### Null Handling

All casts gracefully handle null values:

```php
class User extends Model
{
    protected function casts(): array
    {
        return [
            'country' => CountryCast::class,
        ];
    }
}

$user = new User();
$user->country = null;
$user->save();

$user->fresh();
var_dump($user->country);  // null
```

Ensure your database columns are nullable if you want to allow null values:

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('country', 2)->nullable();
});
```

## Validation Rules

### Available Rules

```php
use Cline\Intl\Rules\CountryRule;
use Cline\Intl\Rules\CurrencyRule;
use Cline\Intl\Rules\LanguageRule;
use Cline\Intl\Rules\LocaleRule;
use Cline\Intl\Rules\TimeZoneRule;
use Cline\Intl\Rules\PhoneNumberRule;
```

### Basic Usage

Use validation rules in form request classes:

```php
use Illuminate\Foundation\Http\FormRequest;
use Cline\Intl\Rules\CountryRule;
use Cline\Intl\Rules\CurrencyRule;
use Cline\Intl\Rules\LanguageRule;
use Cline\Intl\Rules\TimeZoneRule;

class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'country' => ['required', 'string', new CountryRule()],
            'currency' => ['required', 'string', new CurrencyRule()],
            'language' => ['required', 'string', new LanguageRule()],
            'timezone' => ['required', 'string', new TimeZoneRule()],
        ];
    }
}
```

### Controller Usage

```php
use Cline\Intl\Rules\CountryRule;

class UserController extends Controller
{
    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        // Values are already validated by the form request
        $user->update($request->validated());

        return redirect()->back()->with('success', 'Profile updated');
    }

    // Or validate directly in the controller
    public function updateQuick(Request $request)
    {
        $validated = $request->validate([
            'country' => ['required', 'string', new CountryRule()],
        ]);

        auth()->user()->update($validated);
    }
}
```

## Complete Integration Example

Here's a complete example showing casts and validation working together:

### Database Migration

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('country', 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('language', 2)->default('en');
            $table->string('timezone', 50)->default('UTC');
            $table->string('phone', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

### Eloquent Model

```php
use Illuminate\Database\Eloquent\Model;
use Cline\Intl\Data\Cast\CountryCast;
use Cline\Intl\Data\Cast\CurrencyCast;
use Cline\Intl\Data\Cast\LanguageCast;
use Cline\Intl\Data\Cast\TimeZoneCast;
use Cline\Intl\Data\Cast\PhoneNumberCast;

class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'country',
        'currency',
        'language',
        'timezone',
        'phone',
    ];

    protected function casts(): array
    {
        return [
            'country' => CountryCast::class,
            'currency' => CurrencyCast::class,
            'language' => LanguageCast::class,
            'timezone' => TimeZoneCast::class,
            'phone' => PhoneNumberCast::class,
        ];
    }
}
```

### Form Request

```php
use Illuminate\Foundation\Http\FormRequest;
use Cline\Intl\Rules\CountryRule;
use Cline\Intl\Rules\CurrencyRule;
use Cline\Intl\Rules\LanguageRule;
use Cline\Intl\Rules\TimeZoneRule;
use Cline\Intl\Rules\PhoneNumberRule;

class UpdateUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'country' => ['nullable', 'string', new CountryRule()],
            'currency' => ['required', 'string', new CurrencyRule()],
            'language' => ['required', 'string', new LanguageRule()],
            'timezone' => ['required', 'string', new TimeZoneRule()],
            'phone' => ['nullable', 'string', new PhoneNumberRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'country.required' => 'Please select a country.',
            'currency.required' => 'Please select a currency.',
            'language.required' => 'Please select a language.',
            'timezone.required' => 'Please select a timezone.',
        ];
    }
}
```

### Controller

```php
use App\Http\Requests\UpdateUserProfileRequest;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }

    public function update(UpdateUserProfileRequest $request)
    {
        $user = auth()->user();

        // All values are validated and will be automatically cast
        $user->update($request->validated());

        // Access as value objects
        logger()->info('Profile updated', [
            'user_id' => $user->id,
            'country' => $user->country?->alpha2,
            'currency' => $user->currency->code,
            'language' => $user->language->value,
            'timezone' => $user->timezone->value,
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profile updated successfully');
    }
}
```

## Advanced Validation: PhoneNumberRule

The `PhoneNumberRule` is more sophisticated and supports additional options for phone number validation:

### Basic Phone Number Validation

```php
use Cline\Intl\Rules\PhoneNumberRule;

$request->validate([
    'phone' => ['required', 'string', new PhoneNumberRule()],
]);
```

### Validation with Region Code

Provide a specific region code for validation:

```php
use Cline\Intl\Rules\PhoneNumberRule;

$request->validate([
    'phone' => ['required', 'string', new PhoneNumberRule('US')],
]);
```

### Validation with Dynamic Region Code

Use another field's value as the region code:

```php
use Cline\Intl\Rules\PhoneNumberRule;

$request->validate([
    'country' => ['required', 'string', new CountryRule()],
    'phone' => [
        'required',
        'string',
        new PhoneNumberRule(regionCodeReference: 'country'),
    ],
]);
```

This validates that the phone number is valid for the selected country.

### Strict Validation

By default, the rule checks if a phone number is "possible" (has valid length and format). Use strict mode to validate that the number is fully valid:

```php
use Cline\Intl\Rules\PhoneNumberRule;

$request->validate([
    'phone' => [
        'required',
        'string',
        new PhoneNumberRule(
            regionCode: 'US',
            shouldBeStrict: true
        ),
    ],
]);
```

### Complete Phone Number Example

```php
class CreateContactRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'country' => ['required', 'string', new CountryRule()],
            'phone' => [
                'required',
                'string',
                new PhoneNumberRule(
                    regionCodeReference: 'country',
                    shouldBeStrict: true
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Please provide a phone number.',
        ];
    }
}
```

## Custom Error Messages

Customize validation error messages in your form request:

```php
class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'country' => ['required', 'string', new CountryRule()],
            'currency' => ['required', 'string', new CurrencyRule()],
            'language' => ['required', 'string', new LanguageRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'country.required' => 'Please select your country.',
            'currency.required' => 'Please select your preferred currency.',
            'language.required' => 'Please select your language.',
        ];
    }

    public function attributes(): array
    {
        return [
            'country' => 'country',
            'currency' => 'preferred currency',
            'language' => 'language',
        ];
    }
}
```

## Working with Multiple Models

### Related Models with Casts

```php
use Cline\Intl\Data\Cast\CountryCast;
use Cline\Intl\Data\Cast\CurrencyCast;

class User extends Model
{
    protected function casts(): array
    {
        return [
            'country' => CountryCast::class,
            'currency' => CurrencyCast::class,
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

class Order extends Model
{
    protected function casts(): array
    {
        return [
            'shipping_country' => CountryCast::class,
            'billing_country' => CountryCast::class,
            'currency' => CurrencyCast::class,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isInternationalShipping(): bool
    {
        return !$this->shipping_country->isEqualTo($this->user->country);
    }
}
```

### Validation Across Models

```php
class CreateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'currency' => ['required', 'string', new CurrencyRule()],
            'shipping_country' => ['required', 'string', new CountryRule()],
            'billing_country' => ['required', 'string', new CountryRule()],
            'shipping_phone' => [
                'required',
                'string',
                new PhoneNumberRule(regionCodeReference: 'shipping_country'),
            ],
            'billing_phone' => [
                'required',
                'string',
                new PhoneNumberRule(regionCodeReference: 'billing_country'),
            ],
        ];
    }
}
```

## API Resources with Casts

When using API resources, the value objects are automatically serialized:

```php
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country' => [
                'code' => $this->country->alpha2,
                'name' => $this->country->localized,
            ],
            'currency' => [
                'code' => $this->currency->code,
                'symbol' => $this->currency->symbol,
                'name' => $this->currency->name,
            ],
            'language' => $this->language->value,
            'timezone' => $this->timezone->value,
        ];
    }
}
```

## Testing with Casts and Validation

### Feature Tests

```php
use Tests\TestCase;
use Cline\Intl\ValueObjects\Country;

class ProfileTest extends TestCase
{
    public function test_user_can_update_profile_with_valid_data()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'country' => 'US',
            'currency' => 'USD',
            'language' => 'en',
            'timezone' => 'America/New_York',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->fresh();
        $this->assertEquals('US', $user->country->alpha2);
        $this->assertEquals('USD', $user->currency->code);
    }

    public function test_validation_fails_with_invalid_country()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'country' => 'INVALID',
            'currency' => 'USD',
        ]);

        $response->assertSessionHasErrors(['country']);
    }
}
```

### Model Factories

```php
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'country' => fake()->randomElement(['US', 'GB', 'DE', 'FR', 'CA']),
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP', 'CAD']),
            'language' => fake()->randomElement(['en', 'fr', 'de', 'es']),
            'timezone' => fake()->randomElement([
                'America/New_York',
                'Europe/London',
                'Europe/Paris',
                'UTC',
            ]),
        ];
    }
}
```

## Best Practices

1. **Always validate before casting** - Use validation rules in form requests to prevent invalid data from reaching your models
2. **Use nullable appropriately** - Make database columns nullable only when the field is truly optional
3. **Provide defaults** - Set sensible defaults in migrations (e.g., `'UTC'` for timezone, `'en'` for language)
4. **Consistent validation** - Use the same validation rules everywhere you accept user input
5. **Leverage value object methods** - Use methods like `isEqualTo()`, `toString()`, and property access for business logic
6. **Test validation** - Write tests for both valid and invalid input scenarios
7. **Handle exceptions** - While validation should prevent most errors, handle exceptions gracefully in edge cases
8. **Use type hints** - Leverage PHP type hints with value objects in your application code

## Troubleshooting

### Cast Not Working

If casts aren't working as expected:

```php
// Check that the property is fillable or not guarded
protected $fillable = ['country', 'currency'];

// Or use guarded
protected $guarded = ['id'];

// Verify the cast is defined
protected function casts(): array
{
    return [
        'country' => CountryCast::class,
    ];
}
```

### Validation Passing Invalid Data

Ensure you're using the validation rules correctly:

```php
// Correct
new CountryRule()

// Not correct (missing parentheses)
new CountryRule

// Ensure string type is specified
'country' => ['required', 'string', new CountryRule()],
```

### Null Values Not Handled

Make sure your database column is nullable:

```php
$table->string('country', 2)->nullable();  // Correct
$table->string('country', 2);              // Will fail on null
```

## Related Resources

- [Country Value Object](#doc-docs-country)
- [Currency Value Object](#doc-docs-currency)
- [Language & Locale](#doc-docs-language-locale)
- [TimeZone Value Object](#doc-docs-timezone)
- [Laravel Validation Documentation](https://laravel.com/docs/validation)
- [Laravel Eloquent Casting Documentation](https://laravel.com/docs/eloquent-mutators#attribute-casting)

<a id="doc-docs-country"></a>

The `Country` value object provides type-safe handling of ISO 3166-1 country codes with support for both alpha-2 and alpha-3 formats, along with localized country names powered by Symfony Intl.

## Creating Country Instances

### From String

Create a country instance from an ISO 3166-1 alpha-2 country code:

```php
use Cline\Intl\ValueObjects\Country;

$country = Country::createFromString('US');
```

The factory method accepts alpha-2 codes (e.g., `US`, `GB`, `DE`) and automatically retrieves the corresponding alpha-3 code and localized name.

### From Enum

You can also use the `CountryCode` enum for type-safe country code handling:

```php
use Cline\Intl\Enums\CountryCode;

$countryCode = CountryCode::US;
```

## Properties

The `Country` value object provides three read-only properties:

```php
$country = Country::createFromString('US');

// Localized country name (based on application locale)
echo $country->localized;  // "United States"

// ISO 3166-1 alpha-2 code (2 letters)
echo $country->alpha2;  // "US"

// ISO 3166-1 alpha-3 code (3 letters)
echo $country->alpha3;  // "USA"
```

### Property Details

- **`localized`** (string) - The localized country name based on your application's current locale
- **`alpha2`** (string) - The ISO 3166-1 alpha-2 country code (2 characters)
- **`alpha3`** (string|null) - The ISO 3166-1 alpha-3 country code (3 characters)

## String Representation

The `Country` object implements `Stringable` and returns the alpha-2 code when cast to string:

```php
$country = Country::createFromString('GB');

echo (string) $country;  // "GB"
echo $country->toString();  // "GB"
echo $country;  // "GB" (implicit string cast)
```

## Equality Comparison

Compare two country instances for equality:

```php
$country1 = Country::createFromString('US');
$country2 = Country::createFromString('US');
$country3 = Country::createFromString('GB');

$country1->isEqualTo($country2);  // true
$country1->isEqualTo($country3);  // false
```

The comparison is based on the alpha-2 country code.

## Eloquent Model Integration

### Using CountryCast

Add country support to your Eloquent models using the `CountryCast`:

```php
use Illuminate\Database\Eloquent\Model;
use Cline\Intl\Data\Cast\CountryCast;

class User extends Model
{
    protected function casts(): array
    {
        return [
            'country' => CountryCast::class,
            'billing_country' => CountryCast::class,
            'shipping_country' => CountryCast::class,
        ];
    }
}
```

### Working with the Cast

```php
// Store as string, retrieve as Country object
$user = new User();
$user->country = 'US';
$user->save();

// Automatically cast to Country object
echo $user->country->localized;  // "United States"
echo $user->country->alpha2;     // "US"
echo $user->country->alpha3;     // "USA"

// Update with string
$user->country = 'GB';
$user->save();
```

### Database Schema

Store the country as a simple string column:

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('country', 2);  // alpha-2 code
    $table->string('billing_country', 2)->nullable();
    $table->string('shipping_country', 2)->nullable();
    $table->timestamps();
});
```

## Validation

Validate country input using the `CountryRule`:

```php
use Cline\Intl\Rules\CountryRule;
use Illuminate\Http\Request;

class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'country' => ['required', 'string', new CountryRule()],
            'billing_country' => ['nullable', 'string', new CountryRule()],
        ];
    }
}
```

The rule validates that the input is a valid ISO 3166-1 alpha-2 country code.

## Common Use Cases

### User Profile

```php
use Cline\Intl\ValueObjects\Country;
use Cline\Intl\Data\Cast\CountryCast;

class User extends Model
{
    protected function casts(): array
    {
        return [
            'country' => CountryCast::class,
        ];
    }

    public function getCountryDisplayName(): string
    {
        return $this->country?->localized ?? 'Not specified';
    }
}
```

### Order Shipping

```php
use Cline\Intl\ValueObjects\Country;
use Cline\Intl\Data\Cast\CountryCast;

class Order extends Model
{
    protected function casts(): array
    {
        return [
            'shipping_country' => CountryCast::class,
            'billing_country' => CountryCast::class,
        ];
    }

    public function isDomesticShipping(): bool
    {
        $shopCountry = Country::createFromString(config('shop.country'));

        return $this->shipping_country->isEqualTo($shopCountry);
    }

    public function getShippingZone(): string
    {
        return match ($this->shipping_country->alpha2) {
            'US' => 'domestic',
            'CA', 'MX' => 'north_america',
            default => 'international',
        };
    }
}
```

### Country Selection Dropdown

```php
use Symfony\Component\Intl\Countries;

class CountryController extends Controller
{
    public function index()
    {
        // Get all country codes and names for a dropdown
        $countries = collect(Countries::getNames())
            ->map(fn($name, $code) => [
                'code' => $code,
                'name' => $name,
            ])
            ->sortBy('name')
            ->values();

        return view('countries.index', compact('countries'));
    }
}
```

### Filtering by Region

```php
use Cline\Intl\ValueObjects\Country;

class Product extends Model
{
    protected function casts(): array
    {
        return [
            'available_countries' => 'array',
        ];
    }

    public function isAvailableInCountry(Country $country): bool
    {
        if (empty($this->available_countries)) {
            return true; // Available everywhere
        }

        return in_array($country->alpha2, $this->available_countries);
    }
}

// Usage
$product = Product::find(1);
$userCountry = Country::createFromString('US');

if ($product->isAvailableInCountry($userCountry)) {
    // Show product
}
```

## Exception Handling

The `createFromString()` method throws a `MissingResourceException` if the country code is invalid:

```php
use Symfony\Component\Intl\Exception\MissingResourceException;
use Cline\Intl\ValueObjects\Country;

try {
    $country = Country::createFromString('INVALID');
} catch (MissingResourceException $e) {
    // Handle invalid country code
    Log::error('Invalid country code provided', [
        'code' => 'INVALID',
        'message' => $e->getMessage(),
    ]);
}
```

Always validate user input using the `CountryRule` to prevent exceptions.

## Best Practices

1. **Always validate input** - Use `CountryRule` in form requests before creating Country objects
2. **Store alpha-2 codes** - Use 2-character columns in the database for consistency
3. **Use casts in models** - Let Eloquent handle the conversion automatically
4. **Leverage localization** - The `localized` property automatically respects your app's locale
5. **Consider nullable columns** - Not all records may require a country (e.g., optional billing address)

## Related Resources

- [Symfony Intl Countries Component](https://symfony.com/doc/current/components/intl.html)
- [ISO 3166-1 Standard](https://en.wikipedia.org/wiki/ISO_3166-1)
- [Validation Rules](#doc-docs-casts-and-rules)

<a id="doc-docs-currency"></a>

The `Currency` value object provides type-safe handling of ISO 4217 currency codes, including currency symbols, names, fraction digits, rounding increments, and numeric codes powered by Symfony Intl.

## Creating Currency Instances

### From String

Create a currency instance from an ISO 4217 currency code:

```php
use Cline\Intl\ValueObjects\Currency;

$currency = Currency::createFromString('USD');
```

The factory method accepts 3-letter ISO 4217 currency codes (e.g., `USD`, `EUR`, `GBP`) and automatically retrieves all currency metadata.

### From Enum

You can also use the `CurrencyCode` enum for type-safe currency code handling:

```php
use Cline\Intl\Enums\CurrencyCode;

$currencyCode = CurrencyCode::USD;
```

## Properties

The `Currency` value object provides comprehensive read-only properties:

```php
$currency = Currency::createFromString('USD');

// Currency code
echo $currency->code;  // "USD"

// Localized currency name
echo $currency->name;  // "US Dollar"

// Currency symbol
echo $currency->symbol;  // "$"

// Number of decimal places for standard transactions
echo $currency->fractionDigits;  // 2

// Rounding increment for standard transactions
echo $currency->roundingIncrement;  // 0

// Number of decimal places for cash transactions
echo $currency->cashFractionDigits;  // 2

// Rounding increment for cash transactions
echo $currency->cashRoundingIncrement;  // 0

// ISO 4217 numeric code (null if not available)
echo $currency->numericCode;  // 840
```

### Property Details

- **`code`** (string) - The ISO 4217 currency code (3 characters)
- **`name`** (string) - The localized currency name
- **`symbol`** (string) - The currency symbol (e.g., $, €, £)
- **`fractionDigits`** (int) - Default number of decimal places
- **`roundingIncrement`** (int) - Rounding increment for standard transactions
- **`cashFractionDigits`** (int) - Number of decimal places for cash transactions
- **`cashRoundingIncrement`** (int) - Rounding increment for cash transactions
- **`numericCode`** (int|null) - The ISO 4217 numeric code (if available)

## String Representation

The `Currency` object implements `Stringable` and returns the currency code when cast to string:

```php
$currency = Currency::createFromString('EUR');

echo (string) $currency;  // "EUR"
echo $currency->toString();  // "EUR"
echo $currency;  // "EUR" (implicit string cast)
```

## Equality Comparison

Compare two currency instances for equality:

```php
$currency1 = Currency::createFromString('USD');
$currency2 = Currency::createFromString('USD');
$currency3 = Currency::createFromString('EUR');

$currency1->isEqualTo($currency2);  // true
$currency1->isEqualTo($currency3);  // false
```

The comparison is based on the currency code.

## Understanding Fraction Digits

Different currencies have different decimal precision requirements:

```php
// Most currencies use 2 decimal places
$usd = Currency::createFromString('USD');
echo $usd->fractionDigits;  // 2 (e.g., $10.99)

$eur = Currency::createFromString('EUR');
echo $eur->fractionDigits;  // 2 (e.g., €10.99)

// Japanese Yen has no decimal places
$jpy = Currency::createFromString('JPY');
echo $jpy->fractionDigits;  // 0 (e.g., ¥1099)

// Bahraini Dinar uses 3 decimal places
$bhd = Currency::createFromString('BHD');
echo $bhd->fractionDigits;  // 3 (e.g., BD 10.995)
```

## Eloquent Model Integration

### Using CurrencyCast

Add currency support to your Eloquent models using the `CurrencyCast`:

```php
use Illuminate\Database\Eloquent\Model;
use Cline\Intl\Data\Cast\CurrencyCast;

class Product extends Model
{
    protected function casts(): array
    {
        return [
            'currency' => CurrencyCast::class,
        ];
    }
}
```

### Working with the Cast

```php
// Store as string, retrieve as Currency object
$product = new Product();
$product->currency = 'USD';
$product->price = 19.99;
$product->save();

// Automatically cast to Currency object
echo $product->currency->name;            // "US Dollar"
echo $product->currency->symbol;          // "$"
echo $product->currency->fractionDigits;  // 2

// Format price with currency
echo $product->currency->symbol . number_format(
    $product->price,
    $product->currency->fractionDigits
);  // "$19.99"

// Update with string
$product->currency = 'EUR';
$product->save();
```

### Database Schema

Store the currency as a simple string column:

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 10, 2);
    $table->string('currency', 3);  // ISO 4217 code
    $table->timestamps();
});
```

## Validation

Validate currency input using the `CurrencyRule`:

```php
use Cline\Intl\Rules\CurrencyRule;
use Illuminate\Http\Request;

class CreateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'currency' => ['required', 'string', new CurrencyRule()],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
```

The rule validates that the input is a valid ISO 4217 currency code.

## Common Use Cases

### E-commerce Product Pricing

```php
use Cline\Intl\ValueObjects\Currency;
use Cline\Intl\Data\Cast\CurrencyCast;

class Product extends Model
{
    protected function casts(): array
    {
        return [
            'currency' => CurrencyCast::class,
        ];
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->currency->symbol . number_format(
            $this->price,
            $this->currency->fractionDigits
        );
    }

    public function convertTo(Currency $targetCurrency, float $exchangeRate): float
    {
        return round(
            $this->price * $exchangeRate,
            $targetCurrency->fractionDigits
        );
    }
}

// Usage
$product = Product::find(1);
echo $product->formatted_price;  // "$19.99"

$targetCurrency = Currency::createFromString('EUR');
$convertedPrice = $product->convertTo($targetCurrency, 0.85);
```

### Multi-Currency Support

```php
use Cline\Intl\ValueObjects\Currency;
use Cline\Intl\Data\Cast\CurrencyCast;

class Order extends Model
{
    protected function casts(): array
    {
        return [
            'currency' => CurrencyCast::class,
            'customer_currency' => CurrencyCast::class,
        ];
    }

    public function getTotalInCustomerCurrency(): string
    {
        // Use an exchange rate service
        $rate = app(ExchangeRateService::class)
            ->getRate($this->currency, $this->customer_currency);

        $convertedTotal = $this->total * $rate;

        return $this->customer_currency->symbol . number_format(
            $convertedTotal,
            $this->customer_currency->fractionDigits
        );
    }

    public function needsCurrencyConversion(): bool
    {
        return !$this->currency->isEqualTo($this->customer_currency);
    }
}
```

### Currency Selector

```php
use Symfony\Component\Intl\Currencies;

class CurrencyController extends Controller
{
    public function index()
    {
        // Get all currencies for a dropdown
        $currencies = collect(Currencies::getNames())
            ->map(fn($name, $code) => [
                'code' => $code,
                'name' => $name,
                'symbol' => Currencies::getSymbol($code),
            ])
            ->sortBy('name')
            ->values();

        return view('currencies.index', compact('currencies'));
    }
}
```

### Price Formatting Service

```php
use Cline\Intl\ValueObjects\Currency;

class PriceFormatter
{
    public function format(float $amount, Currency $currency): string
    {
        $formatted = number_format(
            $amount,
            $currency->fractionDigits,
            '.',
            ','
        );

        return $currency->symbol . $formatted;
    }

    public function formatWithCode(float $amount, Currency $currency): string
    {
        $formatted = number_format(
            $amount,
            $currency->fractionDigits,
            '.',
            ','
        );

        return $formatted . ' ' . $currency->code;
    }
}

// Usage
$formatter = new PriceFormatter();
$currency = Currency::createFromString('USD');

echo $formatter->format(1234.56, $currency);          // "$1,234.56"
echo $formatter->formatWithCode(1234.56, $currency);  // "1,234.56 USD"
```

### Handling Zero-Decimal Currencies

```php
use Cline\Intl\ValueObjects\Currency;

class PaymentProcessor
{
    public function convertToSmallestUnit(float $amount, Currency $currency): int
    {
        // Stripe and many payment processors require amounts in smallest units
        // USD: 19.99 -> 1999 (cents)
        // JPY: 1999 -> 1999 (yen, no conversion needed)

        return (int) round($amount * (10 ** $currency->fractionDigits));
    }

    public function convertFromSmallestUnit(int $amount, Currency $currency): float
    {
        return $amount / (10 ** $currency->fractionDigits);
    }
}

// Usage
$processor = new PaymentProcessor();
$usd = Currency::createFromString('USD');
$jpy = Currency::createFromString('JPY');

// USD has 2 fraction digits
$processor->convertToSmallestUnit(19.99, $usd);  // 1999 cents

// JPY has 0 fraction digits
$processor->convertToSmallestUnit(1999, $jpy);  // 1999 yen
```

## Exception Handling

The `createFromString()` method throws a `MissingResourceException` if the currency code is invalid:

```php
use Symfony\Component\Intl\Exception\MissingResourceException;
use Cline\Intl\ValueObjects\Currency;

try {
    $currency = Currency::createFromString('INVALID');
} catch (MissingResourceException $e) {
    // Handle invalid currency code
    Log::error('Invalid currency code provided', [
        'code' => 'INVALID',
        'message' => $e->getMessage(),
    ]);
}
```

Always validate user input using the `CurrencyRule` to prevent exceptions.

## Best Practices

1. **Always validate input** - Use `CurrencyRule` in form requests before creating Currency objects
2. **Store ISO codes** - Use 3-character columns in the database for consistency
3. **Use fraction digits** - Respect the currency's `fractionDigits` when formatting amounts
4. **Handle zero-decimal currencies** - JPY, KRW, and others have no decimal places
5. **Consider cash rounding** - Some currencies have different rounding for cash vs. electronic transactions
6. **Use with money libraries** - Consider pairing with libraries like `moneyphp/money` or `brick/money` for advanced calculations

## Integration with Money Libraries

### Using with Brick Money

```php
use Brick\Money\Money;
use Cline\Intl\ValueObjects\Currency;

$currency = Currency::createFromString('USD');

// Create Money instance
$money = Money::of(19.99, $currency->code);

echo $money->formatTo('en_US');  // "$19.99"
```

## Related Resources

- [Symfony Intl Currencies Component](https://symfony.com/doc/current/components/intl.html)
- [ISO 4217 Standard](https://en.wikipedia.org/wiki/ISO_4217)
- [Validation Rules](#doc-docs-casts-and-rules)

<a id="doc-docs-language-locale"></a>

The `Language` and `Locale` value objects provide type-safe handling of language codes and locale identifiers, with localized names powered by Symfony Intl. These work together to enable proper internationalization support in your Laravel application.

## Language Value Object

The `Language` value object represents a language using ISO 639-1 language codes.

### Creating Language Instances

Create a language instance from an ISO 639-1 language code:

```php
use Cline\Intl\ValueObjects\Language;

$language = Language::createFromString('en');
```

The factory method accepts 2-letter ISO 639-1 language codes (e.g., `en`, `fr`, `de`, `es`).

### Language Properties

```php
$language = Language::createFromString('en');

// Language code
echo $language->value;  // "en"

// Localized language name
echo $language->localized;  // "English"
```

When you change your application's locale, the `localized` property will reflect the language name in that locale:

```php
app()->setLocale('fr');
$language = Language::createFromString('en');
echo $language->localized;  // "anglais"

app()->setLocale('de');
$language = Language::createFromString('en');
echo $language->localized;  // "Englisch"
```

### String Representation

The `Language` object implements `Stringable` and returns the language code:

```php
$language = Language::createFromString('fr');

echo (string) $language;  // "fr"
echo $language->toString();  // "fr"
```

### Equality Comparison

```php
$language1 = Language::createFromString('en');
$language2 = Language::createFromString('en');
$language3 = Language::createFromString('fr');

$language1->isEqualTo($language2);  // true
$language1->isEqualTo($language3);  // false
```

## Locale Value Object

The `Locale` value object represents a full locale identifier including language, region, script, and variant.

### Creating Locale Instances

Create a locale instance from a locale identifier:

```php
use Cline\Intl\ValueObjects\Locale;

$locale = Locale::createFromString('en_US');
```

The factory method accepts locale identifiers in various formats:
- `en_US` (language + region)
- `fr_FR`
- `de_DE`
- `pt_BR`
- `zh_CN`

### Locale Properties

```php
$locale = Locale::createFromString('en_US');

// Locale identifier
echo $locale->value;  // "en_US"

// Localized locale name
echo $locale->localized;  // "English (United States)"
```

The `localized` property respects your application's locale:

```php
app()->setLocale('fr');
$locale = Locale::createFromString('en_US');
echo $locale->localized;  // "anglais (États-Unis)"
```

### String Representation

```php
$locale = Locale::createFromString('fr_FR');

echo (string) $locale;  // "fr_FR"
echo $locale->toString();  // "fr_FR"
```

### Equality Comparison

```php
$locale1 = Locale::createFromString('en_US');
$locale2 = Locale::createFromString('en_US');
$locale3 = Locale::createFromString('en_GB');

$locale1->isEqualTo($locale2);  // true
$locale1->isEqualTo($locale3);  // false
```

## Eloquent Model Integration

### Using Language and Locale Casts

```php
use Illuminate\Database\Eloquent\Model;
use Cline\Intl\Data\Cast\LanguageCast;
use Cline\Intl\Data\Cast\LocaleCast;

class User extends Model
{
    protected function casts(): array
    {
        return [
            'language' => LanguageCast::class,
            'locale' => LocaleCast::class,
        ];
    }
}
```

### Working with the Casts

```php
// Store as strings, retrieve as value objects
$user = new User();
$user->language = 'en';
$user->locale = 'en_US';
$user->save();

// Automatically cast to value objects
echo $user->language->localized;  // "English"
echo $user->locale->localized;    // "English (United States)"

// Update with strings
$user->language = 'fr';
$user->locale = 'fr_FR';
$user->save();
```

### Database Schema

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('language', 2);  // ISO 639-1 code
    $table->string('locale', 10);   // Locale identifier
    $table->timestamps();
});
```

## Validation

### Language Validation

```php
use Cline\Intl\Rules\LanguageRule;

class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'language' => ['required', 'string', new LanguageRule()],
        ];
    }
}
```

### Locale Validation

```php
use Cline\Intl\Rules\LocaleRule;

class UpdatePreferencesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'locale' => ['required', 'string', new LocaleRule()],
        ];
    }
}
```

## Common Use Cases

### User Language Preference

```php
use Cline\Intl\ValueObjects\Language;
use Cline\Intl\Data\Cast\LanguageCast;

class User extends Model
{
    protected function casts(): array
    {
        return [
            'language' => LanguageCast::class,
        ];
    }

    public function getPreferredLanguage(): string
    {
        return $this->language->value;
    }

    public function setAppLocale(): void
    {
        app()->setLocale($this->language->value);
    }
}

// Usage
$user = auth()->user();
$user->setAppLocale();  // Set application locale to user's preference
```

### Content Localization

```php
use Cline\Intl\ValueObjects\Language;
use Cline\Intl\Data\Cast\LanguageCast;

class Article extends Model
{
    protected function casts(): array
    {
        return [
            'language' => LanguageCast::class,
        ];
    }

    public function scopeInLanguage($query, Language $language)
    {
        return $query->where('language', $language->value);
    }

    public function hasTranslation(Language $language): bool
    {
        return $this->translations()
            ->where('language', $language->value)
            ->exists();
    }
}

// Usage
$language = Language::createFromString('fr');
$articles = Article::inLanguage($language)->get();
```

### Locale-Based Formatting

```php
use Cline\Intl\ValueObjects\Locale;
use Cline\Intl\Data\Cast\LocaleCast;

class User extends Model
{
    protected function casts(): array
    {
        return [
            'locale' => LocaleCast::class,
        ];
    }

    public function formatDate(\DateTime $date): string
    {
        $formatter = new \IntlDateFormatter(
            $this->locale->value,
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE
        );

        return $formatter->format($date);
    }

    public function formatNumber(float $number): string
    {
        $formatter = new \NumberFormatter(
            $this->locale->value,
            \NumberFormatter::DECIMAL
        );

        return $formatter->format($number);
    }
}

// Usage
$user = User::find(1);
$user->locale = 'fr_FR';

echo $user->formatDate(new \DateTime());  // "15 décembre 2025"
echo $user->formatNumber(1234.56);        // "1 234,56"
```

### Language Selector

```php
use Symfony\Component\Intl\Languages;

class LanguageController extends Controller
{
    public function index()
    {
        // Get all languages for a dropdown
        $languages = collect(Languages::getNames())
            ->map(fn($name, $code) => [
                'code' => $code,
                'name' => $name,
            ])
            ->sortBy('name')
            ->values();

        return view('languages.index', compact('languages'));
    }
}
```

### Multi-Language Application

```php
use Cline\Intl\ValueObjects\Language;

class LocalizationMiddleware
{
    public function handle($request, Closure $next)
    {
        // Set locale from authenticated user
        if ($user = auth()->user()) {
            app()->setLocale($user->language->value);
        }
        // Or from session
        elseif ($language = session('language')) {
            app()->setLocale($language);
        }
        // Or from Accept-Language header
        else {
            $language = $request->getPreferredLanguage(['en', 'fr', 'de', 'es']);
            app()->setLocale($language);
        }

        return $next($request);
    }
}
```

### Translation Management

```php
use Cline\Intl\ValueObjects\Language;
use Cline\Intl\Data\Cast\LanguageCast;

class Translation extends Model
{
    protected function casts(): array
    {
        return [
            'language' => LanguageCast::class,
        ];
    }

    public function scopeForLanguage($query, Language $language)
    {
        return $query->where('language', $language->value);
    }
}

class TranslatableModel extends Model
{
    public function translations()
    {
        return $this->hasMany(Translation::class);
    }

    public function getTranslation(Language $language): ?string
    {
        return $this->translations()
            ->forLanguage($language)
            ->first()
            ?->content;
    }

    public function translate(Language $language, string $content): Translation
    {
        return $this->translations()->create([
            'language' => $language->value,
            'content' => $content,
        ]);
    }
}
```

## Language vs. Locale: When to Use Which

### Use Language When:
- You only need to identify the language without regional specifics
- Managing content translations
- Simple language preferences
- Language-specific content filtering

```php
// Simple language preference
$user->language = 'en';  // Just English, no regional variant
```

### Use Locale When:
- You need regional formatting (dates, numbers, currency)
- Full internationalization with regional differences
- Supporting multiple variants of the same language (en_US vs. en_GB)
- Using PHP's Intl extension for formatting

```php
// Full locale with regional formatting
$user->locale = 'en_US';  // English with US formatting
$user->locale = 'en_GB';  // English with UK formatting
```

## Exception Handling

Both value objects throw `MissingResourceException` for invalid codes:

```php
use Symfony\Component\Intl\Exception\MissingResourceException;
use Cline\Intl\ValueObjects\Language;
use Cline\Intl\ValueObjects\Locale;

try {
    $language = Language::createFromString('invalid');
} catch (MissingResourceException $e) {
    // Handle invalid language code
}

try {
    $locale = Locale::createFromString('invalid_LOCALE');
} catch (MissingResourceException $e) {
    // Handle invalid locale identifier
}
```

Always validate user input using validation rules to prevent exceptions.

## Best Practices

1. **Validate input** - Use `LanguageRule` and `LocaleRule` in form requests
2. **Store codes, not names** - Store language codes and locale identifiers, not localized names
3. **Use middleware** - Set application locale based on user preferences or request headers
4. **Consider fallbacks** - Have a default language/locale for users who haven't set preferences
5. **Respect regional differences** - Use full locales when regional formatting matters
6. **Cache language lists** - Cache the list of available languages for dropdowns
7. **Test with RTL languages** - If supporting Arabic or Hebrew, test right-to-left layouts

## Related Resources

- [Symfony Intl Languages Component](https://symfony.com/doc/current/components/intl.html)
- [Symfony Intl Locales Component](https://symfony.com/doc/current/components/intl.html)
- [ISO 639-1 Language Codes](https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes)
- [Validation Rules](#doc-docs-casts-and-rules)

<a id="doc-docs-timezone"></a>

The `TimeZone` value object provides type-safe handling of timezone identifiers with localized names powered by Symfony Intl. It simplifies working with timezones in your Laravel application, especially for user preferences and scheduled tasks.

## Creating TimeZone Instances

### From String

Create a timezone instance from a timezone identifier:

```php
use Cline\Intl\ValueObjects\TimeZone;

$timezone = TimeZone::createFromString('America/New_York');
```

The factory method accepts standard timezone identifiers from the [IANA Time Zone Database](https://www.iana.org/time-zones):
- `America/New_York`
- `Europe/London`
- `Asia/Tokyo`
- `Australia/Sydney`
- `UTC`

## Properties

The `TimeZone` value object provides two read-only properties:

```php
$timezone = TimeZone::createFromString('America/New_York');

// Timezone identifier
echo $timezone->value;  // "America/New_York"

// Localized timezone name
echo $timezone->localized;  // "Eastern Time (New York)"
```

### Property Details

- **`value`** (string) - The IANA timezone identifier
- **`localized`** (string) - The localized timezone name based on your application's current locale

The `localized` property respects your application's locale:

```php
app()->setLocale('en');
$timezone = TimeZone::createFromString('America/New_York');
echo $timezone->localized;  // "Eastern Time (New York)"

app()->setLocale('fr');
$timezone = TimeZone::createFromString('America/New_York');
echo $timezone->localized;  // "heure de l'Est (New York)"
```

## String Representation

The `TimeZone` object implements `Stringable` and returns the timezone identifier:

```php
$timezone = TimeZone::createFromString('Europe/Paris');

echo (string) $timezone;  // "Europe/Paris"
echo $timezone->toString();  // "Europe/Paris"
echo $timezone;  // "Europe/Paris" (implicit string cast)
```

## Equality Comparison

Compare two timezone instances for equality:

```php
$timezone1 = TimeZone::createFromString('America/New_York');
$timezone2 = TimeZone::createFromString('America/New_York');
$timezone3 = TimeZone::createFromString('Europe/London');

$timezone1->isEqualTo($timezone2);  // true
$timezone1->isEqualTo($timezone3);  // false
```

## Eloquent Model Integration

### Using TimeZoneCast

Add timezone support to your Eloquent models using the `TimeZoneCast`:

```php
use Illuminate\Database\Eloquent\Model;
use Cline\Intl\Data\Cast\TimeZoneCast;

class User extends Model
{
    protected function casts(): array
    {
        return [
            'timezone' => TimeZoneCast::class,
        ];
    }
}
```

### Working with the Cast

```php
// Store as string, retrieve as TimeZone object
$user = new User();
$user->timezone = 'America/New_York';
$user->save();

// Automatically cast to TimeZone object
echo $user->timezone->value;       // "America/New_York"
echo $user->timezone->localized;   // "Eastern Time (New York)"

// Update with string
$user->timezone = 'Europe/London';
$user->save();
```

### Database Schema

Store the timezone as a string column:

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('timezone', 50)->default('UTC');
    $table->timestamps();
});
```

## Validation

Validate timezone input using the `TimeZoneRule`:

```php
use Cline\Intl\Rules\TimeZoneRule;

class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'timezone' => ['required', 'string', new TimeZoneRule()],
        ];
    }
}
```

The rule validates that the input is a valid IANA timezone identifier.

## Common Use Cases

### User Timezone Preferences

```php
use Cline\Intl\ValueObjects\TimeZone;
use Cline\Intl\Data\Cast\TimeZoneCast;
use Carbon\Carbon;

class User extends Model
{
    protected function casts(): array
    {
        return [
            'timezone' => TimeZoneCast::class,
        ];
    }

    public function localTime(?Carbon $time = null): Carbon
    {
        $time = $time ?? now();

        return $time->setTimezone($this->timezone->value);
    }

    public function formatTimeInUserTimezone(Carbon $time): string
    {
        return $this->localTime($time)->format('Y-m-d H:i:s T');
    }
}

// Usage
$user = auth()->user();
$event = Event::find(1);

// Display event time in user's timezone
echo $user->formatTimeInUserTimezone($event->starts_at);
// "2025-12-15 14:30:00 EST"
```

### Scheduled Tasks with User Timezones

```php
use Cline\Intl\ValueObjects\TimeZone;
use Cline\Intl\Data\Cast\TimeZoneCast;
use Carbon\Carbon;

class ScheduledReport extends Model
{
    protected function casts(): array
    {
        return [
            'timezone' => TimeZoneCast::class,
            'scheduled_time' => 'datetime',
        ];
    }

    public function shouldRunNow(): bool
    {
        $now = now()->setTimezone($this->timezone->value);
        $scheduledTime = $this->scheduled_time->setTimezone($this->timezone->value);

        return $now->hour === $scheduledTime->hour
            && $now->minute === $scheduledTime->minute;
    }

    public function getNextRunTime(): Carbon
    {
        return $this->scheduled_time
            ->setTimezone($this->timezone->value)
            ->addDay();
    }
}
```

### Meeting Scheduler

```php
use Cline\Intl\ValueObjects\TimeZone;
use Cline\Intl\Data\Cast\TimeZoneCast;

class Meeting extends Model
{
    protected function casts(): array
    {
        return [
            'timezone' => TimeZoneCast::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function getStartTimeInTimezone(TimeZone $timezone): string
    {
        return $this->starts_at
            ->setTimezone($timezone->value)
            ->format('Y-m-d H:i:s T');
    }

    public function getTimezoneOffset(TimeZone $comparedTo): string
    {
        $meetingTz = new \DateTimeZone($this->timezone->value);
        $compareTz = new \DateTimeZone($comparedTo->value);

        $meetingOffset = $meetingTz->getOffset(new \DateTime());
        $compareOffset = $compareTz->getOffset(new \DateTime());

        $diffSeconds = $meetingOffset - $compareOffset;
        $diffHours = abs($diffSeconds / 3600);

        if ($diffSeconds > 0) {
            return "+{$diffHours} hours";
        } elseif ($diffSeconds < 0) {
            return "-{$diffHours} hours";
        }

        return "same timezone";
    }
}

// Usage
$meeting = Meeting::find(1);
$userTimezone = TimeZone::createFromString('America/Los_Angeles');

echo $meeting->getStartTimeInTimezone($userTimezone);
// "2025-12-15 11:00:00 PST"

echo $meeting->getTimezoneOffset($userTimezone);
// "+3 hours"
```

### Timezone Selector

```php
use Symfony\Component\Intl\Timezones;

class TimezoneController extends Controller
{
    public function index()
    {
        // Get all timezones grouped by region
        $timezones = collect(Timezones::getNames())
            ->map(fn($name, $id) => [
                'id' => $id,
                'name' => $name,
                'region' => explode('/', $id)[0] ?? 'Other',
            ])
            ->groupBy('region')
            ->map(fn($group) => $group->sortBy('name')->values());

        return view('timezones.index', compact('timezones'));
    }

    public function common()
    {
        // Common timezones for quick selection
        $common = [
            'UTC' => 'UTC',
            'America/New_York' => 'Eastern Time (US)',
            'America/Chicago' => 'Central Time (US)',
            'America/Denver' => 'Mountain Time (US)',
            'America/Los_Angeles' => 'Pacific Time (US)',
            'Europe/London' => 'London',
            'Europe/Paris' => 'Paris',
            'Asia/Tokyo' => 'Tokyo',
            'Australia/Sydney' => 'Sydney',
        ];

        return response()->json($common);
    }
}
```

### Displaying Times in Multiple Timezones

```php
use Cline\Intl\ValueObjects\TimeZone;
use Carbon\Carbon;

class TimeZoneConverter
{
    public function convertToMultipleTimezones(Carbon $time, array $timezoneIds): array
    {
        $results = [];

        foreach ($timezoneIds as $timezoneId) {
            $timezone = TimeZone::createFromString($timezoneId);
            $converted = $time->copy()->setTimezone($timezone->value);

            $results[] = [
                'timezone' => $timezone->localized,
                'time' => $converted->format('Y-m-d H:i:s'),
                'offset' => $converted->format('P'),
            ];
        }

        return $results;
    }
}

// Usage
$converter = new TimeZoneConverter();
$eventTime = Carbon::parse('2025-12-15 14:00:00', 'UTC');

$timezones = [
    'America/New_York',
    'Europe/London',
    'Asia/Tokyo',
    'Australia/Sydney',
];

$times = $converter->convertToMultipleTimezones($eventTime, $timezones);

/*
[
    ['timezone' => 'Eastern Time (New York)', 'time' => '2025-12-15 09:00:00', 'offset' => '-05:00'],
    ['timezone' => 'Greenwich Mean Time (London)', 'time' => '2025-12-15 14:00:00', 'offset' => '+00:00'],
    ['timezone' => 'Japan Time (Tokyo)', 'time' => '2025-12-15 23:00:00', 'offset' => '+09:00'],
    ['timezone' => 'Australian Eastern Time (Sydney)', 'time' => '2025-12-16 01:00:00', 'offset' => '+11:00'],
]
*/
```

### Notification Scheduling

```php
use Cline\Intl\ValueObjects\TimeZone;
use Cline\Intl\Data\Cast\TimeZoneCast;

class NotificationPreference extends Model
{
    protected function casts(): array
    {
        return [
            'timezone' => TimeZoneCast::class,
            'preferred_time' => 'datetime',
        ];
    }

    public function getNextNotificationTime(): Carbon
    {
        $now = now()->setTimezone($this->timezone->value);
        $preferred = $this->preferred_time->setTimezone($this->timezone->value);

        $next = $now->copy()
            ->setTime($preferred->hour, $preferred->minute);

        // If the preferred time has already passed today, schedule for tomorrow
        if ($next->isPast()) {
            $next->addDay();
        }

        return $next->setTimezone('UTC');
    }

    public function isNotificationTime(): bool
    {
        $now = now()->setTimezone($this->timezone->value);
        $preferred = $this->preferred_time->setTimezone($this->timezone->value);

        return $now->hour === $preferred->hour
            && $now->minute === $preferred->minute;
    }
}
```

## Integration with Carbon

The `TimeZone` value object works seamlessly with Laravel's Carbon:

```php
use Cline\Intl\ValueObjects\TimeZone;
use Carbon\Carbon;

$timezone = TimeZone::createFromString('America/New_York');

// Create Carbon instance in specific timezone
$time = Carbon::now($timezone->value);

// Convert existing time to timezone
$utcTime = Carbon::now('UTC');
$localTime = $utcTime->setTimezone($timezone->value);

// Format with timezone abbreviation
echo $localTime->format('Y-m-d H:i:s T');
// "2025-12-15 09:00:00 EST"
```

## Exception Handling

The `createFromString()` method throws a `MissingResourceException` if the timezone identifier is invalid:

```php
use Symfony\Component\Intl\Exception\MissingResourceException;
use Cline\Intl\ValueObjects\TimeZone;

try {
    $timezone = TimeZone::createFromString('Invalid/Timezone');
} catch (MissingResourceException $e) {
    // Handle invalid timezone identifier
    Log::error('Invalid timezone identifier', [
        'timezone' => 'Invalid/Timezone',
        'message' => $e->getMessage(),
    ]);
}
```

Always validate user input using the `TimeZoneRule` to prevent exceptions.

## Best Practices

1. **Always validate input** - Use `TimeZoneRule` in form requests before creating TimeZone objects
2. **Store UTC in database** - Store all timestamps in UTC and convert to user timezone for display
3. **Use IANA identifiers** - Avoid abbreviations like "EST" or "PST" - use full identifiers
4. **Consider DST** - IANA timezones automatically handle daylight saving time changes
5. **Default to UTC** - Provide UTC as a sensible default for users who don't set a timezone
6. **Test across timezones** - Test your application with users in different timezones
7. **Display timezone in UI** - Always show the timezone when displaying times to avoid confusion

## Common Timezone Identifiers

Here are some commonly used timezone identifiers:

### United States
- `America/New_York` - Eastern Time
- `America/Chicago` - Central Time
- `America/Denver` - Mountain Time
- `America/Los_Angeles` - Pacific Time
- `America/Anchorage` - Alaska Time
- `Pacific/Honolulu` - Hawaii Time

### Europe
- `Europe/London` - GMT/BST
- `Europe/Paris` - Central European Time
- `Europe/Berlin` - Central European Time
- `Europe/Rome` - Central European Time
- `Europe/Madrid` - Central European Time

### Asia
- `Asia/Tokyo` - Japan Standard Time
- `Asia/Shanghai` - China Standard Time
- `Asia/Hong_Kong` - Hong Kong Time
- `Asia/Singapore` - Singapore Time
- `Asia/Dubai` - Gulf Standard Time
- `Asia/Kolkata` - India Standard Time

### Australia
- `Australia/Sydney` - Australian Eastern Time
- `Australia/Melbourne` - Australian Eastern Time
- `Australia/Perth` - Australian Western Time

### Other
- `UTC` - Coordinated Universal Time
- `Pacific/Auckland` - New Zealand Time

## Related Resources

- [Symfony Intl Timezones Component](https://symfony.com/doc/current/components/intl.html)
- [IANA Time Zone Database](https://www.iana.org/time-zones)
- [PHP DateTimeZone](https://www.php.net/manual/en/class.datetimezone.php)
- [Laravel Carbon Documentation](https://carbon.nesbot.com/docs/)
- [Validation Rules](#doc-docs-casts-and-rules)
