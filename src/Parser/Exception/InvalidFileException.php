<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

namespace Lifyzer\Parser\Exception;

use InvalidArgumentException;

class InvalidFileException extends InvalidArgumentException
{
    public const WRONG_EXTENSION = 1;
    public const TOO_SHORT = 2;
}
