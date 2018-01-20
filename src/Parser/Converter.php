<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace Lifyzer\Parser;

use League\Csv\Reader;

class Converter
{
    private const EXPORT_FILENAME = 'food-database.sql';

    public function __construct(CsvFile $file)
    {
        $path = $file->getValue();

        $csv = Reader::createFromPath($path, 'r');

        if () {
            $csv->insertOne($header);
        }
    }

    public function get()
    {

    }

    private function doesItHealthy(): bool
    {
        return true;
    }
}
