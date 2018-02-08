<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

namespace Lifyzer\Parser;

class DbProductColumns
{
    public const PRODUCT_NAME = 'name';
    public const IMAGE_URL = 'image';
    public const INGREDIENTS = 'ingredients';
    public const CALORIES = 'calories';
    public const IS_ORGANIC = 'isOrganic';
    public const IS_HEALTHY = 'isHealthy';
    public const VITAMINS = 'vitamin';
    public const FAT = 'fat';
    public const SATURATED_FATS = 'saturatedFats';
    public const PROTEIN = 'protein';
    public const SUGAR = 'sugar';
    public const SALT = 'salt';
    public const CARBOHYDRATE = 'carbohydrate';
    public const DIETARY_FIBER = 'dietaryFiber';
    public const SODIUM = 'sodium';
    public const ALCOHOL = 'alcohol';

    public const COLUMNS = [
        self::PRODUCT_NAME,
        self::INGREDIENTS,
        self::IMAGE_URL,
        self::SATURATED_FATS,
        self::CARBOHYDRATE,
        self::SUGAR,
        self::DIETARY_FIBER,
        self::PROTEIN,
        self::SALT,
        self::SODIUM,
        self::ALCOHOL,
        self::IS_HEALTHY
    ];
}
