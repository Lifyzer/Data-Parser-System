<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

namespace Lifyzer\Parser;

final class DbProductColumns
{
    public const PRODUCT_ID = 'id';
    public const BARCODE = 'barcode_id';
    public const PRODUCT_NAME = 'product_name';
    public const COMPANY_NAME = 'company_name';
    public const IMAGE_URL = 'product_image';
    public const INGREDIENTS = 'ingredients';
    public const CALORIES = 'calories';
    public const IS_ORGANIC = 'is_organic';
    public const IS_HEALTHY = 'is_healthy';
    public const VITAMINS = 'vitamin';
    public const FAT = 'fat_amount';
    public const SATURATED_FATS = 'saturated_fats';
    public const PROTEIN = 'protein';
    public const SUGAR = 'sugar';
    public const SALT = 'salt';
    public const CARBOHYDRATE = 'carbohydrate';
    public const DIETARY_FIBER = 'dietary_fiber';
    public const SODIUM = 'sodium';
    public const ALCOHOL = 'alcohol';

    public const COLUMNS = [
        self::BARCODE,
        self::PRODUCT_NAME,
        self::INGREDIENTS,
        self::IMAGE_URL,
        self::FAT,
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
