<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace Lifyzer\Parser;

use League\Csv\Reader;

class Converter
{
    private const EXPORT_FILENAME = 'food-database.sql';
    private const MAXIMUM_HEALTHY_SUGAR = 20;
    private const MAXIMUM_HEALTHY_SALT = 20;
    private const DANGER_LEVEL = 5;
    private const MINIMUM_INGREDIENT_LENGTH = 5;

    private const WANTED_DATA = [
        'product_name',
        'countries_en',
        'ingredients_text',
        'allergens_en',
        'additives_en',
        'sugars_100g',
        'cholesterol_100g',
        'saturated-fat_100g',
        'fiber_100g',
        'proteins_100g',
        'casein_100g',
        'salt_100g',
        'sodium_100g',
        'alcohol_100g',
        'vitamin-a_100g',
        'vitamin-c_100g',
        'vitamin-d_100g',
        'vitamin-e_100g',
        'vitamin-k_100g',
        'calcium_100g',
        'magnesium_100g',
        'iron_100g',
        'zinc_100g',
        'cacao_100g',
        'fruits-vegetables-nuts_100g',
        'caffeine_100g',
    ];

    private const BAD_INGREDIENTS = [
        'emulsifier' => 3,
        'additive' => 3,
        'stabiliser' => 2,
        'aspartame' => 5,
        'dextrose' => 2,
    ];

    /** @var array */
    private $data = [];

    /**
     * @param CsvFile $file
     */
    public function __construct(CsvFile $file)
    {
        $path = $file->getValue();

        $csvReader = Reader::createFromPath($path, 'r+');

        $records = $csvReader->getRecords(self::WANTED_DATA);
        foreach ($records as $offset => $data) {
            foreach ($data as $key => $val) {
                $this->data[$offset][$this->replaceKeys($key)] = $val;
            }

            $isHealthyValue = $this->isItHealthy($offset) ? 1 : 0;
            $this->data[$offset]['isHealthy'] = $isHealthyValue;
        }
    }

    public function asArray(): array
    {
        return $this->data;
    }

    public function exportToCsv(): void
    {

    }

    private function isItHealthy(int $offset): bool
    {
        return $this->areManyBadIngredients($offset) && $this->isTooMuchSugar($offset) && $this->isTooMuchSalt($offset) && $this->isAlcohol($offset);
    }

    private function isTooMuchSugar(int $offset): bool
    {
        return (int)$this->data[$offset]['sugar'] > self::MAXIMUM_HEALTHY_SUGAR;
    }

    private function areManyBadIngredients(int $offset): bool
    {
        if (empty($this->data[$offset]['ingredients_text']) || strlen($this->data[$offset]['ingredients_text']) <= self::MINIMUM_INGREDIENT_LENGTH) {
            return false;
        }

        $dangerLevel = 0;

        foreach (self::BAD_INGREDIENTS as $name => $level) {
            if (stripos($this->data[$offset]['ingredients_text'], $name) !== false) {
                $dangerLevel += $level;
            }
        }

        return $dangerLevel >= self::DANGER_LEVEL;
    }

    private function isTooMuchSalt(int $offset): bool
    {
        return (int)$this->data[$offset]['salt'] > self::MAXIMUM_HEALTHY_SALT;
    }

    private function isAlcohol(int $offset): bool
    {
        return (int)$this->data[$offset]['alcohol'] === 0;
    }

    private function replaceKeys(string $keyName): string
    {
        $search = [
            'product_name',
            'sugars_100g',
            'salt_100g',
            'alcohol_100g',
        ];

        $replace = [
            'name',
            'sugar',
            'salt',
            'alcohol',
        ];

        return str_replace($search, $replace, $keyName);
    }
}
