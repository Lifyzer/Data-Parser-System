<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace Lifyzer\Tests\Parser;

use League\Csv\Reader;
use Lifyzer\Parser\Converter;
use Lifyzer\Parser\CsvFile;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    public function testArrayConverted()
    {
        $converter = $this->initializeConversion();

        $this->assertSame([
            'name' => 'NuutellaaPH',
            'countries_en' => 'sugar',
            'ingredients_text' => ' chocolate butter',
            'allergens_en' => ' hazelnut',
            'additives_en' => '30',
            'sugar' => '300',
            'cholesterol_100g' => '200',
            'saturated-fat_100g' => null,
            'fiber_100g' => null,
            'proteins_100g' => null,
            'casein_100g' => null,
            'salt' => null,
            'sodium_100g' => null,
            'alcohol' => null,
            'vitamin-a_100g' => null,
            'vitamin-c_100g' => null,
            'vitamin-d_100g' => null,
            'vitamin-e_100g' => null,
            'vitamin-k_100g' => null,
            'calcium_100g' => null,
            'magnesium_100g' => null,
            'iron_100g' => null,
            'zinc_100g' => null,
            'cacao_100g' => null,
            'fruits-vegetables-nuts_100g' => null,
            'caffeine_100g' => null,
            'isHealthy' => 0
        ],
            $converter->asArray()[1]
        );
    }

    public function testXmlConverted()
    {
        $converter = $this->initializeConversion();

        $this->assertSame([
            'name' => 'NuutellaaPH',
            'countries_en' => 'sugar',
            'ingredients_text' => ' chocolate butter',
            'allergens_en' => ' hazelnut',
            'additives_en' => '30',
            'sugar' => '300',
            'cholesterol_100g' => '200',
            'saturated-fat_100g' => null,
            'fiber_100g' => null,
            'proteins_100g' => null,
            'casein_100g' => null,
            'salt' => null,
            'sodium_100g' => null,
            'alcohol' => null,
            'vitamin-a_100g' => null,
            'vitamin-c_100g' => null,
            'vitamin-d_100g' => null,
            'vitamin-e_100g' => null,
            'vitamin-k_100g' => null,
            'calcium_100g' => null,
            'magnesium_100g' => null,
            'iron_100g' => null,
            'zinc_100g' => null,
            'cacao_100g' => null,
            'fruits-vegetables-nuts_100g' => null,
            'caffeine_100g' => null,
            'isHealthy' => 0
        ],
            $converter->asXml()
        );
    }

    private function initializeConversion(): Converter
    {
        /**
         * @internal I decided not to mock the class and use fixture, in order to test
         * CSV League behaviours and also because the CSV class is singleton
         */
        $fullPathFile = $this->getFixtureCsvData();
        $file = (new CsvFile($fullPathFile))->getValue();
        $csvReader = Reader::createFromPath($file, 'r+');

        return new Converter($csvReader);
    }

    private function getFixtureCsvData()
    {
        return dirname(__DIR__) . '/fixtures/test-data.csv';
    }
}
