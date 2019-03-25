<?php
/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney
 * @license   https://github.com/phly/keep-a-changelog/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\Provider;

interface ProviderInterface
{
    public function createRelease(
        string $package,
        string $releaseName,
        string $tagName,
        string $changelog,
        string $token,
        ?string $enterpriseUrl
    ) : ?string;

    public function getRepositoryUrlRegex() : string;

    public function generatePullRequestLink(string $package, int $pr, ?string $enterpriseUrl) : string;
}
