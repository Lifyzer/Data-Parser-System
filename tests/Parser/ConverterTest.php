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

        $this->assertSame(
            [
                1 => [
                    'name' => 'Marmite yeast extract',
                    'ingredients' => 'Yeast extract, salt, vegetable extract, niacin, thiamin, spice extracts (contains _celery_), riboflavin, folic acid, vitamin B12.',
                    'image' => 'https://static.openfoodfacts.org/images/products/50184385/front_en.9.200.jpg',
                    'saturatedFats' => '0',
                    'sugar' => '1',
                    'dietaryFiber' => '3.5',
                    'protein' => '39',
                    'salt' => '9.906',
                    'alcohol' => '',
                    'isHealthy' => '1'
                ],
                2 => [
                    'name' => 'Yeast Extract',
                    'ingredients' => 'Yeast extract, salt, carrot and onion extract, spice extracts, enriched with nicotinamide (niacin), thiamin hydrochloride, riboflavin and cyanocobalamin (vitamin b12).',
                    'image' => '',
                    'saturatedFats' => '',
                    'sugar' => '',
                    'dietaryFiber' => '',
                    'protein' => '50',
                    'salt' => '12.7',
                    'alcohol' => '',
                    'isHealthy' => '1',
                ]
            ],
            $converter->asArray()
        );
    }

    public function testXmlConverted(): void
    {
        $converter = $this->initializeConversion();

        $expected = <<<'XML'
<?xml version="1.0"?>
<csv>
  <record offset="1">
    <field name="name">Marmite yeast extract</field>
    <field name="ingredients">Yeast extract, salt, vegetable extract, niacin, thiamin, spice extracts (contains _celery_), riboflavin, folic acid, vitamin B12.</field>
    <field name="image">https://static.openfoodfacts.org/images/products/50184385/front_en.9.200.jpg</field>
    <field name="saturatedFats">0</field>
    <field name="sugar">1</field>
    <field name="dietaryFiber">3.5</field>
    <field name="protein">39</field>
    <field name="salt">9.906</field>
    <field name="alcohol"></field>
    <field name="isHealthy">1</field>
  </record>
  <record offset="2">
    <field name="name">Yeast Extract</field>
    <field name="ingredients">Yeast extract, salt, carrot and onion extract, spice extracts, enriched with nicotinamide (niacin), thiamin hydrochloride, riboflavin and cyanocobalamin (vitamin b12).</field>
    <field name="image"></field>
    <field name="saturatedFats"></field>
    <field name="sugar"></field>
    <field name="dietaryFiber"></field>
    <field name="protein">50</field>
    <field name="salt">12.7</field>
    <field name="alcohol"></field>
    <field name="isHealthy">1</field>
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
