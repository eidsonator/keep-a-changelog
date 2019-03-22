<?php
/**
 * User: teidson@bapm.com
 * Date: 3/21/19
 * Time: 6:44 PM
 */

namespace Phly\KeepAChangelog;

class Prune
{
    public const TYPE_ADDED = 'added';
    public const TYPE_CHANGED = 'changed';
    public const TYPE_DEPRECATED = 'deprecated';
    public const TYPE_REMOVED = 'removed';
    public const TYPE_FIXED = 'fixed';

    public const TYPES = [
        self::TYPE_ADDED,
        self::TYPE_CHANGED,
        self::TYPE_DEPRECATED,
        self::TYPE_REMOVED,
        self::TYPE_FIXED,
    ];

    public function __invoke(string $changelogFile){
        $contents = file($changelogFile);
        if (false === $contents) {
            throw Exception\ChangelogFileNotFoundException::at($changelogFile);
        }

        file_put_contents(
            $changelogFile,
            $this->prune(implode($contents))
        );
    }

    private function prune(string $contents)
    {
        foreach (self::TYPES as $type) {
            $contents = preg_replace('/### ' . $type . '\n\n- Nothing.\n[\n]?/i', '', $contents);
        }
        return substr($contents, 0, -1);
    }
}