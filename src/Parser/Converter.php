<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace Lifyzer\Parser;

use League\Csv\Writer;

class Converter
{
    private const EXPORT_FILENAME = 'food-database.sql';
    private const MAXIMUM_HEALTHY_SUGAR = 20;

    /** @var Writer */
    private $csv;

    /**
     * @param CsvFile $file
     *
     * @throws \League\Csv\CannotInsertRecord
     */
    public function __construct(CsvFile $file)
    {
        $path = $file->getValue();

        $this->csv = Writer::createFromPath($path, 'r+');

        foreach ($this->csv->fetch() as $result) {

            $result['sugars_100g'];

            $isHealthyValue = $this->isItHealthy($result) ? 1 : 0;
            $this->csv->insertOne(['isHealthy']);
            $this->csv->insertOne($isHealthyValue);
        }
    }

    public function getResults(): string
    {
        return $this->csv;
    }

    public function export(): void
    {

    }

    private function isItHealthy(array $data): bool
    {
        return (int)$data['sugars_100g'] > self::MAXIMUM_HEALTHY_SUGAR;
    }
}
