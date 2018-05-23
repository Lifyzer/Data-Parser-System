<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

namespace Lifyzer\Parser;

use Lifyzer\Parser\DbProductColumns as DbColumn;

class DbProductTable
{
    public const TABLE_NAME = 'product';

    private const STRUCTURE = <<<'TABLE'
        CREATE TABLE IF NOT EXISTS %tableName% (
            %id% int(10) unsigned NOT NULL AUTO_INCREMENT,
            %code% varchar(150) DEFAULT NULL,
            %name% varchar(255) NOT NULL,
            %ingredients% text NOT NULL,
            %imageUrl% varchar(255) NOT NULL,
            %fat% float NOT NULL,
            %saturatedFats% float NOT NULL,
            %carbohydrate% float NOT NULL,
            %sugar% float NOT NULL,
            %dietaryFiber% float NOT NULL,
            %protein% float NOT NULL,
            %salt% float NOT NULL,
            %sodium% float NOT NULL,
            %alcohol% float NOT NULL,
            %isOrganic% enum('1','0') NOT NULL DEFAULT '0',
            %isHealthy% enum('1','0') NOT NULL DEFAULT '0',
            PRIMARY KEY (id),
            UNIQUE KEY (barcode_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
TABLE;

    public static function getStructure(): string
    {
        $search = [
            '%tableName%',
            '%id%',
            '%code%',
            '%name%',
            '%ingredients%',
            '%imageUrl%',
            '%fat%',
            '%saturatedFats%',
            '%carbohydrate%',
            '%sugar%',
            '%dietaryFiber%',
            '%protein%',
            '%salt%',
            '%sodium%',
            '%alcohol%',
            '%isOrganic%',
            '%isHealthy%'
        ];

        $replace = [
            self::TABLE_NAME,
            DbColumn::PRODUCT_ID,
            DbColumn::BARCODE,
            DbColumn::PRODUCT_NAME,
            DbColumn::INGREDIENTS,
            DbColumn::IMAGE_URL,
            DbColumn::FAT,
            DbColumn::SATURATED_FATS,
            DbColumn::CARBOHYDRATE,
            DbColumn::SUGAR,
            DbColumn::DIETARY_FIBER,
            DbColumn::PROTEIN,
            DbColumn::SALT,
            DbColumn::SODIUM,
            DbColumn::ALCOHOL,
            DbColumn::IS_ORGANIC,
            DbColumn::IS_HEALTHY
        ];

        return str_replace($search, $replace, self::STRUCTURE);
    }
}
