<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace Lifyzer\Parser;

use Lifyzer\Parser\Exception\InvalidFileException;

class CsvFile
{
    private const VALID_EXTENSION_FILE = 'csv';
    private const MINIMUM_FILE_LENGTH = 3;

    /** @var string */
    private $filename;

    /**
     * CsvFile constructor.
     *
     * @param string $filename
     *
     * @throws InvalidFileException
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;

        if ($this->isInvalidExtension() || $this->isTooShortFilename()) {
            $exceptionCode = $this->isInvalidExtension() ? InvalidFileException::WRONG_EXTENSION : InvalidFileException::TOO_SHORT;

            throw new InvalidFileException(
                'File type or file length is invalid',
                $exceptionCode
            );
        }
    }

    public function getValue(): string
    {
        return $this->filename;
    }

    private function isInvalidExtension(): bool
    {
        return strpos($this->getFileExtension(), self::VALID_EXTENSION_FILE) !== 0;
    }

    private function isTooShortFilename(): bool
    {
        $filename = basename($this->filename, '.' . $this->getFileExtension());

        return strlen($filename) < self::MINIMUM_FILE_LENGTH;
    }

    private function getFileExtension(): string
    {
        return pathinfo($this->filename, PATHINFO_EXTENSION);
    }
}
