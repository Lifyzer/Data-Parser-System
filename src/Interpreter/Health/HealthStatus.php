<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace Lifyzer\Interpreter\Health;

use Lifyzer\Parser\DbProductColumns as DbColumn;

class HealthStatus
{
    private const MINIMUM_INGREDIENT_LENGTH = 5;
    private const MAXIMUM_HEALTHY_SUGAR = 30;
    private const MAXIMUM_HEALTHY_SALT = 12;
    private const MAXIMUM_HEALTHY_FAT = 20;
    private const DANGER_LEVEL = 5;

    private const BAD_INGREDIENTS = [
        'emulsifier' => 3,
        'additive' => 3,
        'stabiliser' => 2,
        'aspartame' => 5,
        'dextrose' => 2,
        'palm oil' => 2.5, // palm oil is carcinogenic (EFSA source)
        'firming agent' => 0.7,
        'calcium chloride' => 0.7, // https://www.livestrong.com/article/457871-how-does-calcium-chloride-work/
        'aspartame' => 0.4,
        'acesulfame' => 0.4
    ];

    private const GOOD_INGREDIENTS = [
        'apples' => 1, // should be more than one
        'broccoli' => 2.5,
        'lentil' => 2,
        'spinach' => 1,
        'walnuts' => 1, // should be more than one
        'chestnuts' => 1, // should be more than one
        'avocados' => 1, // should be more than one
        'lemon' => 1,
        'antioxidant' => 2,
        'sesame' => 1.5,
        'curcuma' => 1
    ];

    /** @var array */
    private $data;

    /** @var int */
    private $offset;

    public function __construct(array $data, int $offset)
    {
        $this->data = $data;
        $this->offset = $offset;
    }

    public function isHealthy(): bool
    {
        if ($this->areBadIngredients() || $this->isTooMuchSugar() || $this->isTooFat() ||
            $this->isTooMuchSalt() || $this->isAlcohol()) {
            return false;
        }

        return true;
    }

    private function areBadIngredients(): bool
    {
        if ($this->areIngredientsInvalid()) {
            return false;
        }

        $dangerLevel = 0; // neutral level

        // Increase the danger lever if "dangerous" ingredients are found
        foreach (self::BAD_INGREDIENTS as $name => $level) {
            if (stripos($this->data[$this->offset][DbColumn::INGREDIENTS], $name) !== false) {
                $dangerLevel += $level;
            }
        }

        // Decrease the danger lever if "healthy" ingredients are found
        foreach (self::GOOD_INGREDIENTS as $name => $level) {
            if (stripos($this->data[$this->offset][DbColumn::INGREDIENTS], $name) !== false) {
                if ($dangerLevel === 0) {
                    break; // Cannot go under zero
                }

                $dangerLevel -= $level;
            }
        }

        return $dangerLevel >= self::DANGER_LEVEL;
    }

    private function areIngredientsInvalid(): bool
    {
        return empty($this->data[$this->offset][DbColumn::INGREDIENTS]) ||
            strlen($this->data[$this->offset][DbColumn::INGREDIENTS]) < self::MINIMUM_INGREDIENT_LENGTH;
    }

    private function isTooMuchSugar(): bool
    {
        return (int)$this->data[$this->offset][DbColumn::SUGAR] > self::MAXIMUM_HEALTHY_SUGAR;
    }

    private function isTooFat(): bool
    {
        return (int)$this->data[$this->offset][DbColumn::SATURATED_FATS] > self::MAXIMUM_HEALTHY_FAT;
    }

    private function isTooMuchSalt(): bool
    {
        return (int)$this->data[$this->offset][DbColumn::SALT] > self::MAXIMUM_HEALTHY_SALT;
    }

    private function isAlcohol(): bool
    {
        return (int)$this->data[$this->offset][DbColumn::ALCOHOL] !== 0;
    }
}
