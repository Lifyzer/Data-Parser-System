<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

use League\Csv\Reader;
use Lifyzer\Parser\Converter;
use Lifyzer\Parser\CsvFile;

ini_set('display_errors', 'On');

/** Reset the time limit and increase the memory **/
@set_time_limit(0);
@ini_set('memory_limit', '-1');

if (!ini_get('auto_detect_line_endings')) {
    ini_set('auto_detect_line_endings', '1');
}


require 'vendor/autoload.php';

$fullProviderDbPath = __DIR__ . '/data/providers/' . Converter::FILENAME_PROVIDER;
$outputDbPath = __DIR__ . '/data/output/';

$file = (new CsvFile($fullProviderDbPath))->getValue();
$csvReader = Reader::createFromPath($file, 'r+');
$converter = new Converter($csvReader);
$sqlSlices = $converter->asSplitSql();

foreach ($sqlSlices as $filename => $sqlInsert) {
    $fullPath = $outputDbPath . $filename . Converter::FILENAME_EXPORT_EXT;

    if (is_file($fullPath)) { // Don't re-update existing files
        continue;
    }
    file_put_contents($fullPath, $sqlInsert);
}

echo '<p>The output DB file has been generated in: ' . $outputDbPath . ' folder.</p>';
