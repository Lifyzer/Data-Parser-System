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
    public function testArrayConverted(): void
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
            'saturated-fat_100g' => '',
            'fiber_100g' => '',
            'proteins_100g' => '',
            'casein_100g' => '',
            'salt' => '',
            'sodium_100g' => '',
            'alcohol' => '',
            'vitamin-a_100g' => '',
            'vitamin-c_100g' => '',
            'vitamin-d_100g' => '',
            'vitamin-e_100g' => '',
            'vitamin-k_100g' => '',
            'calcium_100g' => '',
            'magnesium_100g' => '',
            'iron_100g' => '',
            'zinc_100g' => '',
            'cacao_100g' => '',
            'fruits-vegetables-nuts_100g' => '',
            'caffeine_100g' => '',
            'isHealthy' => '0'
        ],
        $converter->asArray()[1]);
    }

    public function testXmlConverted(): void
    {
        $converter = $this->initializeConversion();

        $expected = <<<'XML'
<?xml version="1.0"?>
<csv>
  <record offset="0">
    <field name="name">product_name</field>
    <field name="countries_en">ingredients_text</field>
    <field name="ingredients_text">proteins_100g</field>
    <field name="allergens_en">sugars_100g</field>
    <field name="additives_en">cacao_100g</field>
    <field name="sugar"></field>
    <field name="cholesterol_100g"></field>
    <field name="saturated-fat_100g"></field>
    <field name="fiber_100g"></field>
    <field name="proteins_100g"></field>
    <field name="casein_100g"></field>
    <field name="salt"></field>
    <field name="sodium_100g"></field>
    <field name="alcohol"></field>
    <field name="vitamin-a_100g"></field>
    <field name="vitamin-c_100g"></field>
    <field name="vitamin-d_100g"></field>
    <field name="vitamin-e_100g"></field>
    <field name="vitamin-k_100g"></field>
    <field name="calcium_100g"></field>
    <field name="magnesium_100g"></field>
    <field name="iron_100g"></field>
    <field name="zinc_100g"></field>
    <field name="cacao_100g"></field>
    <field name="fruits-vegetables-nuts_100g"></field>
    <field name="caffeine_100g"></field>
    <field name="isHealthy">0</field>
  </record>
  <record offset="1">
    <field name="name">NuutellaaPH</field>
    <field name="countries_en">sugar</field>
    <field name="ingredients_text"> chocolate butter</field>
    <field name="allergens_en"> hazelnut</field>
    <field name="additives_en">30</field>
    <field name="sugar">300</field>
    <field name="cholesterol_100g">200</field>
    <field name="saturated-fat_100g"></field>
    <field name="fiber_100g"></field>
    <field name="proteins_100g"></field>
    <field name="casein_100g"></field>
    <field name="salt"></field>
    <field name="sodium_100g"></field>
    <field name="alcohol"></field>
    <field name="vitamin-a_100g"></field>
    <field name="vitamin-c_100g"></field>
    <field name="vitamin-d_100g"></field>
    <field name="vitamin-e_100g"></field>
    <field name="vitamin-k_100g"></field>
    <field name="calcium_100g"></field>
    <field name="magnesium_100g"></field>
    <field name="iron_100g"></field>
    <field name="zinc_100g"></field>
    <field name="cacao_100g"></field>
    <field name="fruits-vegetables-nuts_100g"></field>
    <field name="caffeine_100g"></field>
    <field name="isHealthy">0</field>
  </record>
  <record offset="2">
    <field name="name">Bonne MAman</field>
    <field name="countries_en"> Strawberry</field>
    <field name="ingredients_text"> sugar</field>
    <field name="allergens_en"> jam</field>
    <field name="additives_en"> strawberry</field>
    <field name="sugar">0</field>
    <field name="cholesterol_100g">200</field>
    <field name="saturated-fat_100g">0</field>
    <field name="fiber_100g"></field>
    <field name="proteins_100g"></field>
    <field name="casein_100g"></field>
    <field name="salt"></field>
    <field name="sodium_100g"></field>
    <field name="alcohol"></field>
    <field name="vitamin-a_100g"></field>
    <field name="vitamin-c_100g"></field>
    <field name="vitamin-d_100g"></field>
    <field name="vitamin-e_100g"></field>
    <field name="vitamin-k_100g"></field>
    <field name="calcium_100g"></field>
    <field name="magnesium_100g"></field>
    <field name="iron_100g"></field>
    <field name="zinc_100g"></field>
    <field name="cacao_100g"></field>
    <field name="fruits-vegetables-nuts_100g"></field>
    <field name="caffeine_100g"></field>
    <field name="isHealthy">0</field>
  </record>
</csv>

XML;
        $this->assertSame($expected, $converter->asXml());
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

    private function getFixtureCsvData(): string
    {
        return dirname(__DIR__) . '/fixtures/test-data.csv';
    }
}
