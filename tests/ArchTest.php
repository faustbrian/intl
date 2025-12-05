<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

arch('globals')
    ->expect(['dd', 'dump'])
    ->not->toBeUsed();

// arch('Cline\Intl')
//     ->expect('Cline\Intl')
//     ->toUseStrictTypes()
//     ->toBeFinal()
//     ->ignoring('Cline\Intl\Enum');

// arch('Cline\Intl\Formatter')
//     ->expect('Cline\Intl\Formatter')
//     ->toUseStrictTypes()
//     ->toBeFinal()
//     ->toBeReadonly()
//     ->toHaveSuffix('Formatter');

// arch('Cline\Intl\Rule')
//     ->expect('Cline\Intl\Rule')
//     ->toHaveSuffix('Rule')
//     ->toUseStrictTypes()
//     ->toBeFinal()
//     ->toBeReadonly()
//     ->ignoring(Cline\Intl\Rules\PhoneNumberRule::class);

// arch('Cline\Intl\ValueObject')
//     ->expect('Cline\Intl\ValueObject')
//     ->toUseStrictTypes()
//     ->toBeFinal();
