<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

namespace Lifyzer\Parser;

class DbProductTable
{
    public const NAME = 'products';

    public const STRUCTURE = <<<'TABLE'
        CREATE TABLE products (
            name varchar(255) NOT NULL,
            ingredients text NOT NULL,
            image varchar(255) NOT NULL,
            saturatedFats float NOT NULL,
            carbohydrate float NOT NULL,
            sugar float NOT NULL,
            dietaryFiber float NOT NULL,
            protein float NOT NULL,
            salt float NOT NULL,
            sodium float NOT NULL,
            alcohol float NOT NULL,
            isHealthy enum('1','0') NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
TABLE;
}
