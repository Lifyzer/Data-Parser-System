<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace Lifyzer\Parser;

use League\Csv\Reader;
use League\Csv\Writer;
use League\Csv\XMLConverter;
use Lifyzer\Interpreter\Health\HealthStatus;
use Lifyzer\Parser\DbProductColumns as DbColumn;
use Lifyzer\Parser\DbProductTable as DbTable;

class Converter
{
    public const FILENAME_PROVIDER = 'en.openfoodfacts.org.products.csv';
    public const FILENAME_EXPORT = 'food-database.sql';
    public const SPLIT_SQL_FILENAME_EXPORT = 'food-database-%d-%d';
    public const FILENAME_EXPORT_EXT = '.sql';

    private const NUM_QUERY_SPLIT = 100;
    private const OFFSET_START = 1;
    private const CSV_DELIMITER = '	';

    private const WANTED_DATA = [
        'code',
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
    private $validData = [];

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
            if ($this->isProductNameValid($data)) {
                foreach ($data as $key => $val) {
                    if ($this->isCsvKeyValid($key)) {
                        $this->validData[$offset][$this->replaceKeys($key)] = $val ?? '';
                    }
                }
                $this->addHealthyField($offset);
            }
        }
    }

    public function asArray(): array
    {
        return $this->validData;
    }

    public function asXml(): string
    {
        $converter = (new XMLConverter())
            ->rootElement('resultset')
            ->recordElement('row')
            ->fieldElement('field', 'name');

        $dom = $converter->convert($this->validData);
        $dom->formatOutput = true;

        return $dom->saveXML();
    }

    /**
     * Later one, the output can be converted as SQL file
     * with tools such as http://convertcsv.com/csv-to-sql.htm
     *
     * @return string
     *
     * @throws \League\Csv\CannotInsertRecord
     * @throws \TypeError
     */
    public function asCsv(): string
    {
        $csv = Writer::createFromString('');
        $csv->insertOne(DbColumn::COLUMNS);
        $csv->insertAll($this->validData);

        return $csv->getContent();
    }

    public function asSql(): string
    {
        $sqlQuery = DbTable::getStructure();
        $sqlQuery .= "\n\n";

        foreach ($this->validData as $row) {
            $sqlQuery .= 'INSERT INTO ' . DbTable::TABLE_NAME . ' (';
            $sqlQuery .= implode(', ', DbColumn::COLUMNS);
            $sqlQuery .= ')';
            $sqlQuery .= "\n";
            $sqlQuery .= 'VALUES (\'';
            $sqlQuery .= implode('\', \'', array_map('addslashes', $row));
            $sqlQuery .= '\');';
            $sqlQuery .= "\n";
        }

        return $sqlQuery;
    }

    public function asSplitSql(): array
    {
        $filename = sprintf(self::SPLIT_SQL_FILENAME_EXPORT, 0, self::NUM_QUERY_SPLIT);

        $sqlQueries[$filename] = DbTable::getStructure();
        $sqlQueries[$filename] .= "\n\n";

        foreach ($this->validData as $offset => $row) {
            if ($this->isNextSqlSplit($offset)) {
                $filename = sprintf(self::SPLIT_SQL_FILENAME_EXPORT, $offset, $offset + self::NUM_QUERY_SPLIT);
                $sqlQueries[$filename] = ''; // Reinitialize the new array
            }

            $sqlQueries[$filename] .= 'INSERT INTO ' . DbTable::TABLE_NAME . ' (';
            $sqlQueries[$filename] .= implode(', ', DbColumn::COLUMNS);
            $sqlQueries[$filename] .= ')';
            $sqlQueries[$filename] .= "\n";
            $sqlQueries[$filename] .= 'VALUES (\'';
            $sqlQueries[$filename] .= implode('\', \'', array_map('addslashes', $this->validData[$offset]));
            $sqlQueries[$filename] .= '\');';
            $sqlQueries[$filename] .= "\n";
        }

        return $sqlQueries;
    }

    private function addHealthyField(int $offset): void
    {
        $healthStatus = new HealthStatus($this->validData, $offset);
        $this->validData[$offset][DbColumn::IS_HEALTHY] = $healthStatus->isHealthy() ? '1' : '0';
    }

    private function isCsvKeyValid(string $key): bool
    {
        return in_array($key, self::WANTED_DATA, true);
    }

    private function replaceKeys(string $keyName): string
    {
        $search = [
            'code',
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
            DbColumn::BARCODE,
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

    private function isProductNameValid(array $data): bool
    {
        return !empty($data['product_name']);
    }

    private function isNextSqlSplit(int $offset): bool
    {
        // Is it a multiple of self::NUM_QUERY_SPLIT?
        return $offset % self::NUM_QUERY_SPLIT === 0;
    }
}
