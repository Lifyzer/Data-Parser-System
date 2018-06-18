<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

declare(strict_types=1);

namespace Lifyzer\Tests\Interpreter\Health;

use Lifyzer\Interpreter\Health\HealthStatus;
use Lifyzer\Parser\DbProductColumns as DbColumn;
use PHPUnit\Framework\TestCase;

class HealthStatusTest extends TestCase
{
    private const OFFSET = 1;

    /**
     * @dataProvider healthyDataProvider
     */
    public function testIsHealthy(array $data): void
    {
        $healthStatus = new HealthStatus($data, self::OFFSET);

        $this->assertTrue($healthStatus->isHealthy());
    }

    /**
     * @dataProvider unhealthyDataProvider
     */
    public function testIsNotHealthy(array $data): void
    {
        $healthStatus = new HealthStatus($data, self::OFFSET);

        $this->assertFalse($healthStatus->isHealthy());
    }

    public function healthyDataProvider(): array
    {
        return [
            [
                [
                    self::OFFSET => [
                        // Data are set in string, so we keep the same in our tests
                        DbColumn::FAT => '0',
                        DbColumn::SATURATED_FATS => '1',
                        DbColumn::ALCOHOL => '0',
                        DbColumn::SUGAR => '3',
                        DbColumn::SALT => '0',
                        DbColumn::INGREDIENTS => 'Carrot and onion extract, spice extracts, thiamin hydrochloride, riboflavin and cyanocobalamin (vitamin b12).',
                    ]
                ]
            ]
        ];
    }

    public function unhealthyDataProvider(): array
    {
        return [
            [
                [
                    self::OFFSET => [
                        // Data are set in string, so we keep the same in our tests
                        DbColumn::FAT => '3',
                        DbColumn::SATURATED_FATS => '30',
                        DbColumn::ALCOHOL => '1',
                        DbColumn::SUGAR => '3',
                        DbColumn::SALT => '0',
                        DbColumn::INGREDIENTS => 'Carrot and onion extract, spice extracts, thiamin hydrochloride, riboflavin and cyanocobalamin (vitamin b12).',
                    ]
                ]
            ]
        ];
    }
}
