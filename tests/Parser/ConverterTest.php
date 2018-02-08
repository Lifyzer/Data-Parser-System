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
                    'carbohydrate' => '24',
                    'sugar' => '1',
                    'dietaryFiber' => '3.5',
                    'protein' => '39',
                    'salt' => '9.906',
                    'sodium' => '3.9',
                    'alcohol' => '',
                    'isHealthy' => '1'
                ],
                2 => [
                    'name' => 'Yeast Extract',
                    'ingredients' => 'Yeast extract, salt, carrot and onion extract, spice extracts, enriched with nicotinamide (niacin), thiamin hydrochloride, riboflavin and cyanocobalamin (vitamin b12).',
                    'image' => '',
                    'saturatedFats' => '',
                    'carbohydrate' => '0',
                    'sugar' => '',
                    'dietaryFiber' => '',
                    'protein' => '50',
                    'salt' => '12.7',
                    'sodium' => '5',
                    'alcohol' => '',
                    'isHealthy' => '1'
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
<resultset>
  <row>
    <field name="name">Marmite yeast extract</field>
    <field name="ingredients">Yeast extract, salt, vegetable extract, niacin, thiamin, spice extracts (contains _celery_), riboflavin, folic acid, vitamin B12.</field>
    <field name="image">https://static.openfoodfacts.org/images/products/50184385/front_en.9.200.jpg</field>
    <field name="saturatedFats">0</field>
    <field name="carbohydrate">24</field>
    <field name="sugar">1</field>
    <field name="dietaryFiber">3.5</field>
    <field name="protein">39</field>
    <field name="salt">9.906</field>
    <field name="sodium">3.9</field>
    <field name="alcohol"></field>
    <field name="isHealthy">1</field>
  </row>
  <row>
    <field name="name">Yeast Extract</field>
    <field name="ingredients">Yeast extract, salt, carrot and onion extract, spice extracts, enriched with nicotinamide (niacin), thiamin hydrochloride, riboflavin and cyanocobalamin (vitamin b12).</field>
    <field name="image"></field>
    <field name="saturatedFats"></field>
    <field name="carbohydrate">0</field>
    <field name="sugar"></field>
    <field name="dietaryFiber"></field>
    <field name="protein">50</field>
    <field name="salt">12.7</field>
    <field name="sodium">5</field>
    <field name="alcohol"></field>
    <field name="isHealthy">1</field>
  </row>
</resultset>

XML;
        $this->assertSame($expected, $converter->asXml());
    }

    public function testCsvConverted(): void
    {
        $converter = $this->initializeConversion();

        $expected = <<<'CSV'
name,ingredients,image,saturatedFats,carbohydrate,sugar,dietaryFiber,protein,salt,sodium,alcohol,isHealthy
"Marmite yeast extract","Yeast extract, salt, vegetable extract, niacin, thiamin, spice extracts (contains _celery_), riboflavin, folic acid, vitamin B12.",https://static.openfoodfacts.org/images/products/50184385/front_en.9.200.jpg,0,24,1,3.5,39,9.906,3.9,,1
"Yeast Extract","Yeast extract, salt, carrot and onion extract, spice extracts, enriched with nicotinamide (niacin), thiamin hydrochloride, riboflavin and cyanocobalamin (vitamin b12).",,,0,,,50,12.7,5,,1

CSV;
        $this->assertSame($expected, $converter->asCsv());
    }

    public function testSqlConverted(): void
    {
        $converter = $this->initializeConversion();

        $expected = <<<'SQL'
        CREATE TABLE products (
            name varchar(255) NOT NULL,
            ingredients text NOT NULL,
            image varchar(255) NOT NULL,
            saturatedFats float NOT NULL,
            carbohydrate float NOT NULL,
            sugar float NOT NULL,
            dietaryFiber float NOT NULL,
            protein float NOT NULL,
            salt float NOT NULL,
            sodium float NOT NULL,
            alcohol float NOT NULL,
            isHealthy enum('1','0') NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO products (name, ingredients, image, saturatedFats, carbohydrate, sugar, dietaryFiber, protein, salt, sodium, alcohol, isHealthy)
VALUES ('Marmite yeast extract', 'Yeast extract, salt, vegetable extract, niacin, thiamin, spice extracts (contains _celery_), riboflavin, folic acid, vitamin B12.', 'https://static.openfoodfacts.org/images/products/50184385/front_en.9.200.jpg', '0', '24', '1', '3.5', '39', '9.906', '3.9', '', '1');
INSERT INTO products (name, ingredients, image, saturatedFats, carbohydrate, sugar, dietaryFiber, protein, salt, sodium, alcohol, isHealthy)
VALUES ('Yeast Extract', 'Yeast extract, salt, carrot and onion extract, spice extracts, enriched with nicotinamide (niacin), thiamin hydrochloride, riboflavin and cyanocobalamin (vitamin b12).', '', '', '0', '', '', '50', '12.7', '5', '', '1');

SQL;
        $this->assertSame($expected, $converter->asSql());
    }

    public function testProductDataWithoutName(): void
    {
        $fullPathFile = $this->getFixtureCsvDataWithoutProductName();
        $file = (new CsvFile($fullPathFile))->getValue();
        $csvReader = Reader::createFromPath($file, 'r+');

        $converter = new Converter($csvReader);

        $this->assertSame([], $converter->asArray());
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

    private function getFixtureCsvDataWithoutProductName(): string
    {
        return dirname(__DIR__) . '/fixtures/test-empty-product-name-data.csv';
    }
}
