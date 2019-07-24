<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace Lifyzer\Interpreter\Health\Data;

final class Ingredients
{
    public const BAD_INGREDIENTS = [
        'emulsifier|émulsifiant' => 3,
        'additive' => 3,
        'stabiliser' => 2,
        'flavor enhancer|flavour enhancer' => 1,
        'dextrose' => 2,
        'palm oil' => 2.5, // palm oil is carcinogenic (EFSA source)
        'oil' => 1,
        'firming agent' => 0.7,
        'calcium chloride' => 0.7, // https://www.livestrong.com/article/457871-how-does-calcium-chloride-work/
        'aspartame|acesulfame' => 0.4,
        'syrup|sirop' => 1.5,
        'dextrose|maltose|fructose|glucose' => 1.5,
        'dextrin|dextrine' => 1.5,
        'maltodextrin|maltodextrine' => 1.5,
        'sucrose|saccharose' => 1.5,
    ];

    public const GOOD_INGREDIENTS = [
        'apples' => 1, // plural, should be more than one
        'broccoli' => 2.5,
        'lentil' => 2,
        'spinach' => 1,
        'celery' => 0.8,
        'walnuts' => 1, // plural, should be more than one
        'chestnuts' => 1, // plural, should be more than one
        'avocados' => 1, // plural, should be more than one
        'lemon' => 1,
        'garlic' => 0.5,
        'antioxidant' => 2,
        'sesame' => 1,
        'curcuma' => 1,
        'spirulina|spiruline' => 1,
        'chia' => 1.2, // https://draxe.com/chia-seeds-benefits-side-effects/
        'kale' => 0.3,
        'goji' => 0.4, // https://www.nhs.uk/Livewell/superfoods/Pages/are-goji-berries-a-superfood.aspx
        'zucchini|courgette' => 0.4,
    ];
}
