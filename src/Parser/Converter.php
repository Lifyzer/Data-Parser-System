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
use Lifyzer\Interpreter\Health\HealthStatus;
use Lifyzer\Parser\DbProductColumns as DbColumn;

class Converter
{
    public const FILENAME_EXPORT = 'food-database.xml';

    private const CSV_DELIMITER = '	';

    private const WANTED_DATA = [
        'product_name',
        'image_front_small_url',
        //'countries_en',
        'ingredients_text',
        //'allergens_en',
        //'additives_en',
        'sugars_100g',
        'carbohydrates_100g',
        //'cholesterol_100g',
        'saturated-fat_100g',
        'fiber_100g',
        'proteins_100g',
        //'casein_100g',
        'salt_100g',
        'sodium_100g',
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

    /** @var array */
    private $data = [];

    /**
     * Converter constructor.
     *
     * @param Reader $csvReader
     *
     * @throws \League\Csv\Exception
     */
    public function __construct(Reader $csvReader)
    {
        $csvReader->setDelimiter(self::CSV_DELIMITER);
        $csvReader->setHeaderOffset(0);

        $records = $csvReader->getRecords();
        foreach ($records as $offset => $data) {
            foreach ($data as $key => $val) {
                if ($this->isCsvKeyValid($key)) {
                    $this->data[$offset][$this->replaceKeys($key)] = $val ?? '';
                } else {
                    continue;
                }
            }

            $healthStatus = new HealthStatus($this->data, $offset);
            $this->data[$offset][DbColumn::IS_HEALTHY] = $healthStatus->isHealthy() ? '1' : '0';
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

    private function isCsvKeyValid(string $key): bool
    {
        return in_array($key, self::WANTED_DATA, true);
    }

    private function replaceKeys(string $keyName): string
    {
        $search = [
            'product_name',
            'image_front_small_url',
            'ingredients_text',
            'sugars_100g',
            'carbohydrates_100g',
            'salt_100g',
            'sodium_100g',
            'proteins_100g',
            'saturated-fat_100g',
            'fiber_100g',

            'alcohol_100g',
        ];

        $replace = [
            DbColumn::PRODUCT_NAME,
            DbColumn::IMAGE_URL,
            DbColumn::INGREDIENTS,
            DbColumn::SUGAR,
            DbColumn::CARBOHYDRATE,
            DbColumn::SALT,
            DbColumn::SODIUM,
            DbColumn::PROTEIN,
            DbColumn::SATURATED_FATS,
            DbColumn::DIETARY_FIBER,
            DbColumn::ALCOHOL,
        ];

        return str_replace($search, $replace, $keyName);
    }
}
