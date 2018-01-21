<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace Lifyzer\Parser;

use League\Csv\Reader;
use League\Csv\XMLConverter;
use Lifyzer\Parser\DbProductColumns as DbColumn;

class Converter
{
    public const FILENAME_EXPORT = 'food-database.xml';

    private const CSV_DELIMITER = '	';
    private const MAXIMUM_HEALTHY_SUGAR = 30;
    private const MAXIMUM_HEALTHY_SALT = 12;
    private const MAXIMUM_HEALTHY_FAT = 20;
    private const DANGER_LEVEL = 5;
    private const MINIMUM_INGREDIENT_LENGTH = 5;

    private const WANTED_DATA = [
        'product_name',
        'image_front_small_url',
        //'countries_en',
        'ingredients_text',
        //'allergens_en',
        //'additives_en',
        'sugars_100g',
        //'cholesterol_100g',
        'saturated-fat_100g',
        'fiber_100g',
        'proteins_100g',
        //'casein_100g',
        'salt_100g',
        //'sodium_100g',
        'alcohol_100g',
        //'vitamin-a_100g',
        //'vitamin-c_100g',
        //'vitamin-d_100g',
        //'vitamin-e_100g',
        //'vitamin-k_100g',
        //'calcium_100g',
        //'magnesium_100g',
        //'iron_100g',
        //'zinc_100g',
        //'cacao_100g',
        //'fruits-vegetables-nuts_100g',
        //'caffeine_100g',
    ];

    private const BAD_INGREDIENTS = [
        'emulsifier' => 3,
        'additive' => 3,
        'stabiliser' => 2,
        'aspartame' => 5,
        'dextrose' => 2,
        'palm oil' => 2.5, // palm oil is carcinogenic (EFSA source)
    ];

    private const GOOD_INGREDIENTS = [
        'apples' => 1, // should be more than one
        'broccoli' => 3,
        'lentil' => 1,
        'spinach' => 1,
        'walnuts' => 1, // should be more than one
        'chestnuts' => 1, // should be more than one
        'avocados' => 1, // should be more than one
        'lemon' => 1,
        'antioxidant' => 1,
    ];

    /** @var array */
    private $data = [];

    public function __construct(Reader $csvReader)
    {
        $csvReader->setDelimiter(self::CSV_DELIMITER);
        $csvReader->setHeaderOffset(0);
        $records = $csvReader->getRecords();

        foreach ($records as $offset => $data) {
            foreach ($data as $key => $val) {
                if (in_array($key, self::WANTED_DATA, true)) {
                    $this->data[$offset][$this->replaceKeys($key)] = $val ?? '';
                } else {
                    continue;
                }
            }

            $this->data[$offset][DbColumn::IS_HEALTHY] = $this->isNotHealthy($offset) ? '0' : '1';
        }
    }

    public function asArray(): array
    {
        return $this->data;
    }

    public function asXml(): string
    {
        $converter = (new XMLConverter())
            ->rootElement('csv')
            ->recordElement('record', 'offset')
            ->fieldElement('field', 'name');

        $dom = $converter->convert($this->data);
        $dom->formatOutput = true;

        return $dom->saveXML();
    }


    private function isNotHealthy(int $offset): bool
    {
        return $this->areBadIngredients($offset) || $this->isTooMuchSugar($offset) || $this->isTooFat($offset) || $this->isTooMuchSalt($offset) || $this->isAlcohol($offset);
    }

    private function areBadIngredients(int $offset): bool
    {
        if (empty($this->data[$offset][DbColumn::INGREDIENTS]) || strlen($this->data[$offset][DbColumn::INGREDIENTS]) <= self::MINIMUM_INGREDIENT_LENGTH) {
            return false;
        }

        $dangerLevel = 0; // neutral level

        // Increase the danger lever if "dangerous" ingredients are found
        foreach (self::BAD_INGREDIENTS as $name => $level) {
            if (stripos($this->data[$offset][DbColumn::INGREDIENTS], $name) !== false) {
                $dangerLevel += $level;
            }
        }

        // Decrease the danger lever if "healthy" ingredients are found
        foreach (self::GOOD_INGREDIENTS as $name => $level) {
            if (stripos($this->data[$offset][DbColumn::INGREDIENTS], $name) !== false) {
                if ($dangerLevel === 0) {
                    break; // Cannot go under zero
                }

                $dangerLevel -= $level;
            }
        }

        return $dangerLevel >= self::DANGER_LEVEL;
    }

    private function isTooMuchSugar(int $offset): bool
    {
        return (int)$this->data[$offset][DbColumn::SUGAR] > self::MAXIMUM_HEALTHY_SUGAR;
    }

    private function isTooMuchSalt(int $offset): bool
    {
        return (int)$this->data[$offset][DbColumn::SALT] > self::MAXIMUM_HEALTHY_SALT;
    }

    private function isTooFat(int $offset): bool
    {
        return (int)$this->data[$offset][DbColumn::SATURATED_FATS] > self::MAXIMUM_HEALTHY_FAT;
    }

    private function isAlcohol(int $offset): bool
    {
        return (int)$this->data[$offset][DbColumn::ALCOHOL] === 0;
    }

    private function replaceKeys(string $keyName): string
    {
        $search = [
            'product_name',
            'image_front_small_url',
            'ingredients_text',
            'sugars_100g',
            'salt_100g',
            'alcohol_100g',
            'proteins_100g',
            'saturated-fat_100g',
            'fiber_100g',
        ];

        $replace = [
            DbColumn::PRODUCT_NAME,
            DbColumn::IMAGE_URL,
            DbColumn::INGREDIENTS,
            DbColumn::SUGAR,
            DbColumn::SALT,
            DbColumn::ALCOHOL,
            DbColumn::PROTEIN,
            DbColumn::SATURATED_FATS,
            DbColumn::DIETARY_FIBER,
        ];

        return str_replace($search, $replace, $keyName);
    }
}
