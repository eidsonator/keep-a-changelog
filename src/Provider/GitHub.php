<?php
/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 * @copyright Copyright (c) 2018 Matthew Weier O'Phinney
 * @license   https://github.com/phly/keep-a-changelog/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\Provider;

use Github\Client as GitHubClient;

class GitHub implements ProviderInterface
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
        $this->url = $enterpriseUrl ?: 'https://github.com';
        [$org, $repo] = explode('/', $package);
        $client = new GitHubClient(null, null, $this->url);
        $client->authenticate($token, GitHubClient::AUTH_HTTP_TOKEN);
        $release = $client->api('repo')->releases()->create(
            $org,
            $repo,
            [
                'tag_name'   => $tagName,
                'name'       => $releaseName,
                'body'       => $changelog,
                'draft'      => false,
                'prerelease' => false,
            ]
        );

        return $release['html_url'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getRepositoryUrlRegex() : string
    {
        $enterpriseUrl = preg_replace('/http[s]?:\/\//', '', $this->url);

        return sprintf ('(%s[:/](.*?)\.git)', $enterpriseUrl);
    }

    /**
     * @inheritDoc
     */
    public function generatePullRequestLink(string $package, int $pr, ?string $enterpriseUrl) : string
    {
        return sprintf('%s/%s/pull/%d', $enterpriseUrl ?: 'https://github.com', $package, $pr);
    }
}
