<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace Lifyzer\Tests\Parser;

use Lifyzer\Parser\CsvFile;
use Lifyzer\Parser\Exception\InvalidFileException;
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
     */
    public function testWrongExtension(string $filename): void
    {
        $this->expectException(InvalidFileException::class);
        $this->expectExceptionCode(InvalidFileException::WRONG_EXTENSION);

        new CsvFile($filename);
    }

    public function testTooShortFilename(): void
    {
        $this->expectException(InvalidFileException::class);
        $this->expectExceptionCode(InvalidFileException::TOO_SHORT);

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
