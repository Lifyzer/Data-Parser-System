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
                    'barcode_id' => '50184385',
                    'product_name' => 'Marmite yeast extract',
                    'ingredients' => 'Yeast extract, salt, vegetable extract, niacin, thiamin, spice extracts (contains _celery_), riboflavin, folic acid, vitamin B12.',
                    'product_image' => 'https://static.openfoodfacts.org/images/products/50184385/front_en.9.200.jpg',
                    'fat_amount' => '0.1',
                    'saturated_fats' => '0',
                    'carbohydrate' => '24',
                    'sugar' => '1',
                    'dietary_fiber' => '3.5',
                    'protein' => '39',
                    'salt' => '9.906',
                    'sodium' => '3.9',
                    'alcohol' => '',
                    'is_healthy' => '0'
                ],
                2 => [
                    'barcode_id' => '0667803001957',
                    'product_name' => 'Yeast Extract',
                    'ingredients' => 'Yeast extract, salt, carrot and onion extract, spice extracts, enriched with nicotinamide (niacin), thiamin hydrochloride, riboflavin and cyanocobalamin (vitamin b12).',
                    'product_image' => '',
                    'fat_amount' => '0',
                    'saturated_fats' => '',
                    'carbohydrate' => '0',
                    'sugar' => '',
                    'dietary_fiber' => '',
                    'protein' => '50',
                    'salt' => '0.97',
                    'sodium' => '5',
                    'alcohol' => '',
                    'is_healthy' => '1'
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
    <field name="barcode_id">50184385</field>
    <field name="product_name">Marmite yeast extract</field>
    <field name="ingredients">Yeast extract, salt, vegetable extract, niacin, thiamin, spice extracts (contains _celery_), riboflavin, folic acid, vitamin B12.</field>
    <field name="product_image">https://static.openfoodfacts.org/images/products/50184385/front_en.9.200.jpg</field>
    <field name="fat_amount">0.1</field>
    <field name="saturated_fats">0</field>
    <field name="carbohydrate">24</field>
    <field name="sugar">1</field>
    <field name="dietary_fiber">3.5</field>
    <field name="protein">39</field>
    <field name="salt">9.906</field>
    <field name="sodium">3.9</field>
    <field name="alcohol"></field>
    <field name="is_healthy">0</field>
  </row>
  <row>
    <field name="barcode_id">0667803001957</field>
    <field name="product_name">Yeast Extract</field>
    <field name="ingredients">Yeast extract, salt, carrot and onion extract, spice extracts, enriched with nicotinamide (niacin), thiamin hydrochloride, riboflavin and cyanocobalamin (vitamin b12).</field>
    <field name="product_image"></field>
    <field name="fat_amount">0</field>
    <field name="saturated_fats"></field>
    <field name="carbohydrate">0</field>
    <field name="sugar"></field>
    <field name="dietary_fiber"></field>
    <field name="protein">50</field>
    <field name="salt">0.97</field>
    <field name="sodium">5</field>
    <field name="alcohol"></field>
    <field name="is_healthy">1</field>
  </row>
</resultset>

XML;
        $this->assertSame($expected, $converter->asXml());
    }

    public function testCsvConverted(): void
    {
        $converter = $this->initializeConversion();

        $expected = <<<'CSV'
barcode_id,product_name,ingredients,product_image,fat_amount,saturated_fats,carbohydrate,sugar,dietary_fiber,protein,salt,sodium,alcohol,is_healthy
50184385,"Marmite yeast extract","Yeast extract, salt, vegetable extract, niacin, thiamin, spice extracts (contains _celery_), riboflavin, folic acid, vitamin B12.",https://static.openfoodfacts.org/images/products/50184385/front_en.9.200.jpg,0.1,0,24,1,3.5,39,9.906,3.9,,0
0667803001957,"Yeast Extract","Yeast extract, salt, carrot and onion extract, spice extracts, enriched with nicotinamide (niacin), thiamin hydrochloride, riboflavin and cyanocobalamin (vitamin b12).",,0,,0,,,50,0.97,5,,1

CSV;
        $this->assertSame($expected, $converter->asCsv());
    }

    public function testSqlConverted(): void
    {
        $converter = $this->initializeConversion();

        $expected = <<<'SQL'
        CREATE TABLE IF NOT EXISTS product (
            id int(10) unsigned NOT NULL AUTO_INCREMENT,
            barcode_id varchar(150) DEFAULT NULL,
            product_name varchar(255) NOT NULL,
            ingredients text NOT NULL,
            product_image varchar(255) NOT NULL,
            fat_amount float NOT NULL,
            saturated_fats float NOT NULL,
            carbohydrate float NOT NULL,
            sugar float NOT NULL,
            dietary_fiber float NOT NULL,
            protein float NOT NULL,
            salt float NOT NULL,
            sodium float NOT NULL,
            alcohol float NOT NULL,
            is_organic enum('1','0') NOT NULL DEFAULT '0',
            is_healthy enum('1','0') NOT NULL DEFAULT '0',
            PRIMARY KEY (id),
            UNIQUE KEY (barcode_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO product (barcode_id, product_name, ingredients, product_image, fat_amount, saturated_fats, carbohydrate, sugar, dietary_fiber, protein, salt, sodium, alcohol, is_healthy)
VALUES ('50184385', 'Marmite yeast extract', 'Yeast extract, salt, vegetable extract, niacin, thiamin, spice extracts (contains _celery_), riboflavin, folic acid, vitamin B12.', 'https://static.openfoodfacts.org/images/products/50184385/front_en.9.200.jpg', '0.1', '0', '24', '1', '3.5', '39', '9.906', '3.9', '', '0');

INSERT INTO product (barcode_id, product_name, ingredients, product_image, fat_amount, saturated_fats, carbohydrate, sugar, dietary_fiber, protein, salt, sodium, alcohol, is_healthy)
VALUES ('0667803001957', 'Yeast Extract', 'Yeast extract, salt, carrot and onion extract, spice extracts, enriched with nicotinamide (niacin), thiamin hydrochloride, riboflavin and cyanocobalamin (vitamin b12).', '', '0', '', '0', '', '', '50', '0.97', '5', '', '1');


SQL;
        $this->assertSame($expected, $converter->asSql());
    }

    public function testAsSplitSqlConverted(): void
    {
        $converter = $this->initializeConversion();

        $expected = <<<'SQL'
        CREATE TABLE IF NOT EXISTS product (
            id int(10) unsigned NOT NULL AUTO_INCREMENT,
            barcode_id varchar(150) DEFAULT NULL,
            product_name varchar(255) NOT NULL,
            ingredients text NOT NULL,
            product_image varchar(255) NOT NULL,
            fat_amount float NOT NULL,
            saturated_fats float NOT NULL,
            carbohydrate float NOT NULL,
            sugar float NOT NULL,
            dietary_fiber float NOT NULL,
            protein float NOT NULL,
            salt float NOT NULL,
            sodium float NOT NULL,
            alcohol float NOT NULL,
            is_organic enum('1','0') NOT NULL DEFAULT '0',
            is_healthy enum('1','0') NOT NULL DEFAULT '0',
            PRIMARY KEY (id),
            UNIQUE KEY (barcode_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO product (barcode_id, product_name, ingredients, product_image, fat_amount, saturated_fats, carbohydrate, sugar, dietary_fiber, protein, salt, sodium, alcohol, is_healthy)
VALUES ('50184385', 'Marmite yeast extract', 'Yeast extract, salt, vegetable extract, niacin, thiamin, spice extracts (contains _celery_), riboflavin, folic acid, vitamin B12.', 'https://static.openfoodfacts.org/images/products/50184385/front_en.9.200.jpg', '0.1', '0', '24', '1', '3.5', '39', '9.906', '3.9', '', '0');

INSERT INTO product (barcode_id, product_name, ingredients, product_image, fat_amount, saturated_fats, carbohydrate, sugar, dietary_fiber, protein, salt, sodium, alcohol, is_healthy)
VALUES ('0667803001957', 'Yeast Extract', 'Yeast extract, salt, carrot and onion extract, spice extracts, enriched with nicotinamide (niacin), thiamin hydrochloride, riboflavin and cyanocobalamin (vitamin b12).', '', '0', '', '0', '', '', '50', '0.97', '5', '', '1');


SQL;
        $split2 = $converter->asSplitSql()['food-database-0-1000'];
        $this->assertSame($expected, $split2);
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
