<?php
/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney
 * @license   https://github.com/phly/keep-a-changelog/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace PhlyTest\KeepAChangelog;

use PHPUnit\Framework\TestCase;
use Phly\KeepAChangelog\Prune;

class PruneTest extends TestCase
{
    private $tempFile;

    public function tearDown()
    {
        if ($this->tempFile && file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testPrunesChangelogAsExpected() {
        $initialChangelogFile = sprintf('%s/_files/%s', realpath(__DIR__), 'CHANGELOG-PRUNED-INITIAL.md');
        $expectedChangelogFile = sprintf('%s/_files/%s', realpath(__DIR__), 'CHANGELOG-PRUNED-EXPECTED.md');

        $this->tempFile = tempnam(sys_get_temp_dir(), 'KAC');
        file_put_contents($this->tempFile, file_get_contents($initialChangelogFile));
        (new Prune())($this->tempFile);
        $this->assertFileEquals($expectedChangelogFile, $this->tempFile);
    }
}
