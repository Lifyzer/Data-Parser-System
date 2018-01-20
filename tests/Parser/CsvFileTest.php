<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace Lifyzer\Tests\Parser;

use Lifyzer\Parser\CsvFile;
use PHPUnit\Framework\TestCase;

class CsvFileTest extends TestCase
{
    public function testValidValue(): void
    {
        $file = new CsvFile('my-great-file.csv');

        $this->assertSame('my-great-file.csv', $file->getValue());
    }

    /**
     * @dataProvider invalidFilenamesProvider
     * @expectedException \Lifyzer\Parser\Exception\InvalidFileException
     * @expectedExceptionCode \Lifyzer\Parser\Exception\InvalidFileException::WRONG_EXTENSION
     */
    public function testWrongExtension(string $filename): void
    {
        new CsvFile($filename);
    }

    /**
     * @expectedException \Lifyzer\Parser\Exception\InvalidFileException
     * @expectedExceptionCode \Lifyzer\Parser\Exception\InvalidFileException::TOO_SHORT
     */
    public function testTooShortFilename(): void
    {
        new CsvFile('/var/www/html/a.csv');
    }

    public function invalidFilenamesProvider(): array
    {
        return [
            ['blabla.doc'],
            ['myfile.CSV'],
            ['myfile']
        ];
    }
}
