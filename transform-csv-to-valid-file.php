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

$fullPathProviderDb = __DIR__ . '/data/providers/openfoodfacts_search.csv';
$fullPathOutputDb = __DIR__ . '/data/output/' . Converter::FILENAME_EXPORT;

$file = (new CsvFile($fullPathProviderDb))->getValue();
$csvReader = Reader::createFromPath($file, 'r+');
$converter = new Converter($csvReader);
file_put_contents($fullPathOutputDb, $converter->asSql());

echo '<p>The DB has been generated in: <a href="file://' . $fullPathOutputDb . '">' . $fullPathOutputDb . '</a> (very big file! Your computer might freeze)</p>';
