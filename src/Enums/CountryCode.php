<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Intl\Enums;

use Symfony\Component\Intl\Countries;
use Throwable;

use function mb_strlen;

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @see \Symfony\Component\Intl\Countries::getNames()
 */
enum CountryCode: string
{
    case AF = 'AF'; // Afghanistan
    case AX = 'AX'; // Åland Islands
    case AL = 'AL'; // Albania
    case DZ = 'DZ'; // Algeria
    case AS = 'AS'; // American Samoa
    case AD = 'AD'; // Andorra
    case AO = 'AO'; // Angola
    case AI = 'AI'; // Anguilla
    case AQ = 'AQ'; // Antarctica
    case AG = 'AG'; // Antigua & Barbuda
    case AR = 'AR'; // Argentina
    case AM = 'AM'; // Armenia
    case AW = 'AW'; // Aruba
    case AU = 'AU'; // Australia
    case AT = 'AT'; // Austria
    case AZ = 'AZ'; // Azerbaijan
    case BS = 'BS'; // Bahamas
    case BH = 'BH'; // Bahrain
    case BD = 'BD'; // Bangladesh
    case BB = 'BB'; // Barbados
    case BY = 'BY'; // Belarus
    case BE = 'BE'; // Belgium
    case BZ = 'BZ'; // Belize
    case BJ = 'BJ'; // Benin
    case BM = 'BM'; // Bermuda
    case BT = 'BT'; // Bhutan
    case BO = 'BO'; // Bolivia
    case BA = 'BA'; // Bosnia & Herzegovina
    case BW = 'BW'; // Botswana
    case BV = 'BV'; // Bouvet Island
    case BR = 'BR'; // Brazil
    case IO = 'IO'; // British Indian Ocean Territory
    case VG = 'VG'; // British Virgin Islands
    case BN = 'BN'; // Brunei
    case BG = 'BG'; // Bulgaria
    case BF = 'BF'; // Burkina Faso
    case BI = 'BI'; // Burundi
    case KH = 'KH'; // Cambodia
    case CM = 'CM'; // Cameroon
    case CA = 'CA'; // Canada
    case CV = 'CV'; // Cape Verde
    case BQ = 'BQ'; // Caribbean Netherlands
    case KY = 'KY'; // Cayman Islands
    case CF = 'CF'; // Central African Republic
    case TD = 'TD'; // Chad
    case CL = 'CL'; // Chile
    case CN = 'CN'; // China
    case CX = 'CX'; // Christmas Island
    case CC = 'CC'; // Cocos (Keeling) Islands
    case CO = 'CO'; // Colombia
    case KM = 'KM'; // Comoros
    case CG = 'CG'; // Congo - Brazzaville
    case CD = 'CD'; // Congo - Kinshasa
    case CK = 'CK'; // Cook Islands
    case CR = 'CR'; // Costa Rica
    case CI = 'CI'; // Côte d’Ivoire
    case HR = 'HR'; // Croatia
    case CU = 'CU'; // Cuba
    case CW = 'CW'; // Curaçao
    case CY = 'CY'; // Cyprus
    case CZ = 'CZ'; // Czechia
    case DK = 'DK'; // Denmark
    case DJ = 'DJ'; // Djibouti
    case DM = 'DM'; // Dominica
    case DO = 'DO'; // Dominican Republic
    case EC = 'EC'; // Ecuador
    case EG = 'EG'; // Egypt
    case SV = 'SV'; // El Salvador
    case GQ = 'GQ'; // Equatorial Guinea
    case ER = 'ER'; // Eritrea
    case EE = 'EE'; // Estonia
    case SZ = 'SZ'; // Eswatini
    case ET = 'ET'; // Ethiopia
    case FK = 'FK'; // Falkland Islands
    case FO = 'FO'; // Faroe Islands
    case FJ = 'FJ'; // Fiji
    case FI = 'FI'; // Finland
    case FR = 'FR'; // France
    case GF = 'GF'; // French Guiana
    case PF = 'PF'; // French Polynesia
    case TF = 'TF'; // French Southern Territories
    case GA = 'GA'; // Gabon
    case GM = 'GM'; // Gambia
    case GE = 'GE'; // Georgia
    case DE = 'DE'; // Germany
    case GH = 'GH'; // Ghana
    case GI = 'GI'; // Gibraltar
    case GR = 'GR'; // Greece
    case GL = 'GL'; // Greenland
    case GD = 'GD'; // Grenada
    case GP = 'GP'; // Guadeloupe
    case GU = 'GU'; // Guam
    case GT = 'GT'; // Guatemala
    case GG = 'GG'; // Guernsey
    case GN = 'GN'; // Guinea
    case GW = 'GW'; // Guinea-Bissau
    case GY = 'GY'; // Guyana
    case HT = 'HT'; // Haiti
    case HM = 'HM'; // Heard & McDonald Islands
    case HN = 'HN'; // Honduras
    case HK = 'HK'; // Hong Kong SAR China
    case HU = 'HU'; // Hungary
    case IS = 'IS'; // Iceland
    case IN = 'IN'; // India
    case ID = 'ID'; // Indonesia
    case IR = 'IR'; // Iran
    case IQ = 'IQ'; // Iraq
    case IE = 'IE'; // Ireland
    case IM = 'IM'; // Isle of Man
    case IL = 'IL'; // Israel
    case IT = 'IT'; // Italy
    case JM = 'JM'; // Jamaica
    case JP = 'JP'; // Japan
    case JE = 'JE'; // Jersey
    case JO = 'JO'; // Jordan
    case KZ = 'KZ'; // Kazakhstan
    case KE = 'KE'; // Kenya
    case KI = 'KI'; // Kiribati
    case KW = 'KW'; // Kuwait
    case KG = 'KG'; // Kyrgyzstan
    case LA = 'LA'; // Laos
    case LV = 'LV'; // Latvia
    case LB = 'LB'; // Lebanon
    case LS = 'LS'; // Lesotho
    case LR = 'LR'; // Liberia
    case LY = 'LY'; // Libya
    case LI = 'LI'; // Liechtenstein
    case LT = 'LT'; // Lithuania
    case LU = 'LU'; // Luxembourg
    case MO = 'MO'; // Macao SAR China
    case MG = 'MG'; // Madagascar
    case MW = 'MW'; // Malawi
    case MY = 'MY'; // Malaysia
    case MV = 'MV'; // Maldives
    case ML = 'ML'; // Mali
    case MT = 'MT'; // Malta
    case MH = 'MH'; // Marshall Islands
    case MQ = 'MQ'; // Martinique
    case MR = 'MR'; // Mauritania
    case MU = 'MU'; // Mauritius
    case YT = 'YT'; // Mayotte
    case MX = 'MX'; // Mexico
    case FM = 'FM'; // Micronesia
    case MD = 'MD'; // Moldova
    case MC = 'MC'; // Monaco
    case MN = 'MN'; // Mongolia
    case ME = 'ME'; // Montenegro
    case MS = 'MS'; // Montserrat
    case MA = 'MA'; // Morocco
    case MZ = 'MZ'; // Mozambique
    case MM = 'MM'; // Myanmar (Burma)
    case NA = 'NA'; // Namibia
    case NR = 'NR'; // Nauru
    case NP = 'NP'; // Nepal
    case NL = 'NL'; // Netherlands
    case NC = 'NC'; // New Caledonia
    case NZ = 'NZ'; // New Zealand
    case NI = 'NI'; // Nicaragua
    case NE = 'NE'; // Niger
    case NG = 'NG'; // Nigeria
    case NU = 'NU'; // Niue
    case NF = 'NF'; // Norfolk Island
    case KP = 'KP'; // North Korea
    case MK = 'MK'; // North Macedonia
    case MP = 'MP'; // Northern Mariana Islands
    case NO = 'NO'; // Norway
    case OM = 'OM'; // Oman
    case PK = 'PK'; // Pakistan
    case PW = 'PW'; // Palau
    case PS = 'PS'; // Palestinian Territories
    case PA = 'PA'; // Panama
    case PG = 'PG'; // Papua New Guinea
    case PY = 'PY'; // Paraguay
    case PE = 'PE'; // Peru
    case PH = 'PH'; // Philippines
    case PN = 'PN'; // Pitcairn Islands
    case PL = 'PL'; // Poland
    case PT = 'PT'; // Portugal
    case PR = 'PR'; // Puerto Rico
    case QA = 'QA'; // Qatar
    case RE = 'RE'; // Réunion
    case RO = 'RO'; // Romania
    case RU = 'RU'; // Russia
    case RW = 'RW'; // Rwanda
    case WS = 'WS'; // Samoa
    case SM = 'SM'; // San Marino
    case ST = 'ST'; // São Tomé & Príncipe
    case SA = 'SA'; // Saudi Arabia
    case SN = 'SN'; // Senegal
    case RS = 'RS'; // Serbia
    case SC = 'SC'; // Seychelles
    case SL = 'SL'; // Sierra Leone
    case SG = 'SG'; // Singapore
    case SX = 'SX'; // Sint Maarten
    case SK = 'SK'; // Slovakia
    case SI = 'SI'; // Slovenia
    case SB = 'SB'; // Solomon Islands
    case SO = 'SO'; // Somalia
    case ZA = 'ZA'; // South Africa
    case GS = 'GS'; // South Georgia & South Sandwich Islands
    case KR = 'KR'; // South Korea
    case SS = 'SS'; // South Sudan
    case ES = 'ES'; // Spain
    case LK = 'LK'; // Sri Lanka
    case BL = 'BL'; // St. Barthélemy
    case SH = 'SH'; // St. Helena
    case KN = 'KN'; // St. Kitts & Nevis
    case LC = 'LC'; // St. Lucia
    case MF = 'MF'; // St. Martin
    case PM = 'PM'; // St. Pierre & Miquelon
    case VC = 'VC'; // St. Vincent & Grenadines
    case SD = 'SD'; // Sudan
    case SR = 'SR'; // Suriname
    case SJ = 'SJ'; // Svalbard & Jan Mayen
    case SE = 'SE'; // Sweden
    case CH = 'CH'; // Switzerland
    case SY = 'SY'; // Syria
    case TW = 'TW'; // Taiwan
    case TJ = 'TJ'; // Tajikistan
    case TZ = 'TZ'; // Tanzania
    case TH = 'TH'; // Thailand
    case TL = 'TL'; // Timor-Leste
    case TG = 'TG'; // Togo
    case TK = 'TK'; // Tokelau
    case TO = 'TO'; // Tonga
    case TT = 'TT'; // Trinidad & Tobago
    case TN = 'TN'; // Tunisia
    case TR = 'TR'; // Türkiye
    case TM = 'TM'; // Turkmenistan
    case TC = 'TC'; // Turks & Caicos Islands
    case TV = 'TV'; // Tuvalu
    case UM = 'UM'; // U.S. Outlying Islands
    case VI = 'VI'; // U.S. Virgin Islands
    case UG = 'UG'; // Uganda
    case UA = 'UA'; // Ukraine
    case AE = 'AE'; // United Arab Emirates
    case GB = 'GB'; // United Kingdom
    case US = 'US'; // United States
    case UY = 'UY'; // Uruguay
    case UZ = 'UZ'; // Uzbekistan
    case VU = 'VU'; // Vanuatu
    case VA = 'VA'; // Vatican City
    case VE = 'VE'; // Venezuela
    case VN = 'VN'; // Vietnam
    case WF = 'WF'; // Wallis & Futuna
    case EH = 'EH'; // Western Sahara
    case YE = 'YE'; // Yemen
    case ZM = 'ZM'; // Zambia
    case ZW = 'ZW'; // Zimbabwe

    public static function tryFromString(?string $code): ?self
    {
        if ($code === null) {
            return null;
        }

        // ISO 3166-1 alpha-2
        if (mb_strlen($code) === 2) {
            return self::tryFrom($code);
        }

        // ISO 3166-1 alpha-3
        try {
            return self::tryFrom(Countries::getAlpha2Code($code));
        } catch (Throwable) {
            return null;
        }
    }
}
