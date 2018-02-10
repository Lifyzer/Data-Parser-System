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
    public const TABLE_NAME = 'products';

    private const STRUCTURE = <<<'TABLE'
        CREATE TABLE %tableName% (
            %id% int(10) unsigned NOT NULL AUTO_INCREMENT,
            %name% varchar(255) NOT NULL,
            %ingredients% text NOT NULL,
            %imageUrl% varchar(255) NOT NULL,
            %saturatedFats% varchar(20) NOT NULL,
            %carbohydrate% varchar(20) NOT NULL,
            %sugar% varchar(20) NOT NULL,
            %dietaryFiber% varchar(20) NOT NULL,
            %protein% varchar(20) NOT NULL,
            %salt% varchar(20) NOT NULL,
            %sodium% varchar(20) NOT NULL,
            %alcohol% varchar(20) NOT NULL,
            %isHealthy% enum('1','0') NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
TABLE;

    /**
     * @return string
     */
    public static function getStructure(): string
    {
        $search = [
            '%tableName%',
            '%id%',
            '%name%',
            '%ingredients%',
            '%imageUrl%',
            '%saturatedFats%',
            '%carbohydrate%',
            '%sugar%',
            '%dietaryFiber%',
            '%protein%',
            '%salt%',
            '%sodium%',
            '%alcohol%',
            '%isHealthy%'
        ];

        $replace = [
            self::TABLE_NAME,
            DbColumn::PRODUCT_ID,
            DbColumn::PRODUCT_NAME,
            DbColumn::INGREDIENTS,
            DbColumn::IMAGE_URL,
            DbColumn::SATURATED_FATS,
            DbColumn::CARBOHYDRATE,
            DbColumn::SUGAR,
            DbColumn::DIETARY_FIBER,
            DbColumn::PROTEIN,
            DbColumn::SALT,
            DbColumn::SODIUM,
            DbColumn::ALCOHOL,
            DbColumn::IS_HEALTHY
        ];

        return str_replace($search, $replace, self::STRUCTURE);
    }
}
