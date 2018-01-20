<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace Lifyzer\Tests\Parser;

use Lifyzer\Parser\Converter;
use PHPUnit\Framework\TestCase;
use League\Csv\AbstractCsv;
use Phake;

class ConverterTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testConverter(array $data)
    {
        $abstractCsv = Phake::mock(AbstractCsv::class);
        Phake::when($abstractCsv)->getRecords(Phake::anyParameters())->thenReturn($data);

        $converter = new Converter($abstractCsv);

        $this->assertSame([], $converter->asArray());
    }

    public function dataProvider(): array
    {
        return [
            [
                [
                    'product_name' => 'NuutellaaPH',
                    'ingredients_text' => 'sugar, chocolate butter, hazelnut',
                    'proteins_100g' => 30,
                    'sugars_100g' => 300,
                    'cacao_100g' => 200
                ]
            ]
        ];
    }
}
