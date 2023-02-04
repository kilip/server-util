<?php

namespace Olympus\Service\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\User;
use Cloudflare\API\Endpoints\DNS;
use GuzzleHttp\Client;

class CloudflareCommand extends Command
{
    private $cfApiKey;
    private $cfEmail;
    private $cfDomain;
    private $icanHazipUrl;

    public function __construct(
        #[Autowire('%env(CLOUDFLARE_API_KEY)%')]
        $cfApiKey,

        #[Autowire('%env(CLOUDFLARE_EMAIL)%')]
        $cfEmail,

        #[Autowire('%env(CLOUDFLARE_DOMAIN)%')]
        $cfDomain,

        #[Autowire('%env(IPV4_ICANHAZIP_URL)%')]
        $icanHazipUrl
        
    )
    {
        parent::__construct('cf:start');
        $this->cfApiKey = $cfApiKey;
        $this->cfEmail = $cfEmail;
        $this->cfDomain = $cfDomain;
        $this->icanHazipUrl = $icanHazipUrl;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = new APIKey($this->cfEmail, $this->cfApiKey);
        $adapter = new Guzzle($key);
        $user = new User($adapter);
        $records = new DNS($adapter);
        
        $client = new Client([
            'base_uri' => $this->icanHazipUrl,
            'timeout' => 2
        ]);
        $response = $client->request('GET', '/');
        $output->writeln($response->getBody());

        return 0;
    }
}