<?php
/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney
 * @license   https://github.com/phly/keep-a-changelog/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\Provider;

use Gitlab\Client as GitLabClient;

class GitLab implements ProviderInterface
{
    private $url;
    /**
     * @inheritDoc
     */
    public function createRelease(
        string $package,
        string $releaseName,
        string $tagName,
        string $changelog,
        string $token,
        string $enterpriseUrl
    ) : ?string {
        $this->url = $enterpriseUrl ?? 'https://gitlab.com';
        $client = GitLabClient::create($this->url);
        $client->authenticate($token, GitLabClient::AUTH_HTTP_TOKEN);
        $release = $client->api('repositories')->createRelease($package, $tagName, $changelog);

        return $release['tag_name'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getRepositoryUrlRegex() : string
    {
        $enterpriseUrl = preg_replace('/http[s]?:\/\//', '', $this->url);
        return sprintf('(%s[:/](.*?)\.git)', $enterpriseUrl);
    }

    /**
     * @inheritDoc
     */
    public function generatePullRequestLink(string $package, int $pr, ?string $enterpriseUrl) : string
    {
        return sprintf('%s/%s/merge_requests/%d', $enterpriseUrl ?: 'https://gitlab.com', $package, $pr);
    }
}
