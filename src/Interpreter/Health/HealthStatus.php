<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace Lifyzer\Interpreter\Health;

use Lifyzer\Interpreter\Health\Data\Ingredients;
use Lifyzer\Parser\DbProductColumns as DbColumn;

class HealthStatus
{
    private const MINIMUM_INGREDIENT_LENGTH = 5;
    private const MAXIMUM_HEALTHY_SUGAR = 25; // gram
    private const MAXIMUM_HEALTHY_SALT = 1; // gram
    private const MAXIMUM_HEALTHY_FAT = 35; // gram
    private const DANGER_LEVEL = 5;
    private const NEUTRAL_LEVEL = 0;

    /** @var array */
    private $data;

    /** @var int */
    private $offset;

    /** @var int */
    private $dangerLevel;

    public function __construct(array $data, int $offset)
    {
        $this->data = $data;
        $this->offset = $offset;
        $this->dangerLevel = self::NEUTRAL_LEVEL;
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

        // Increase self::$dangerLevel if "dangerous" ingredients are found
        $this->calculateBadIngredients();

        // Decrease self::$dangerLevel if "healthy" ingredients are found
        $this->calculateGoodIngredients();

        return $this->dangerLevel >= self::DANGER_LEVEL;
    }

    private function calculateBadIngredients(): void
    {
        foreach (Ingredients::BAD_INGREDIENTS as $name => $level) {
            if ($this->findInIngredients($name)) {
                $this->dangerLevel += $level;
            }
        }
    }

    private function calculateGoodIngredients(): void
    {
        foreach (Ingredients::GOOD_INGREDIENTS as $name => $level) {
            if ($this->findInIngredients($name)) {
                if ($this->dangerLevel === 0) {
                    break; // Cannot go under zero
                }

                $this->dangerLevel -= $level;
            }
        }
    }

    private function areIngredientsInvalid(): bool
    {
        return empty($this->data[$this->offset][DbColumn::INGREDIENTS]) ||
            strlen($this->data[$this->offset][DbColumn::INGREDIENTS]) < self::MINIMUM_INGREDIENT_LENGTH;
    }

    private function isTooMuchSugar(): bool
    {
        return (float)$this->data[$this->offset][DbColumn::SUGAR] > self::MAXIMUM_HEALTHY_SUGAR;
    }

    private function isTooFat(): bool
    {
        return (float)$this->data[$this->offset][DbColumn::SATURATED_FATS] > self::MAXIMUM_HEALTHY_FAT ||
            (float)$this->data[$this->offset][DbColumn::FAT] >= self::MAXIMUM_HEALTHY_FAT;
    }

    private function isTooMuchSalt(): bool
    {
        return (float)$this->data[$this->offset][DbColumn::SALT] > self::MAXIMUM_HEALTHY_SALT;
    }

    private function isAlcohol(): bool
    {
        return !empty($this->data[$this->offset][DbColumn::ALCOHOL]);
    }

    /**
     * Find words (components) in ingredients using RegEx pattern.
     *
     * @param string $component
     *
     * @return bool
     */
    private function findInIngredients(string $component): bool
    {
        return preg_match(
                '/' . $component . '/i',
                $this->data[$this->offset][DbColumn::INGREDIENTS]
            ) === 1;
    }
}
