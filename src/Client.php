<?php

namespace Cloudflare;

use Cloudflare\Endpoints\AbstractEndpoint;
use Cloudflare\Exceptions\BadMethodCallException;
use Cloudflare\Exceptions\InvalidArgumentException;
use Cloudflare\HttpClient\HttpClient;

/**
 * Simple PHP Cloudflare client.
 *
 * @method \Cloudflare\Endpoints\Accounts accounts()
 * @method \Cloudflare\Endpoints\Zones zones()
 * @method \Cloudflare\Endpoints\IP ips()
 * @method \Cloudflare\Endpoints\Workers workers()
 * @method \Cloudflare\Endpoints\Tunnel tunnel()
 * @method \Cloudflare\Endpoints\D1 d1()
 *
 * @author Sergkei Melingk <sergio11of@gmail.com>
 *
 * Website: https://github.com/SergkeiM/php-cloudflare-api
 */
class Client
{
    /**
     * HTTP Client wrapper for Guzzle.
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @param string $token Cloudflare Token https://developers.cloudflare.com/fundamentals/api/get-started/create-token
     * @param array $middlewares Guzzle middlewares. https://docs.guzzlephp.org/en/stable/handlers-and-middleware.html#middleware
     * @return void
     */
    public function __construct(
        string $use,
        string $token,
        string $email = '',
        string $key = '',
        array $middlewares = []
    ) {

        $this->httpClient = new HttpClient($use, $token, $email, $key, $middlewares);
    }

    /**
     * @return HttpClient HTTP Client wrapper for Guzzle.
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    /**
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return AbstractEndpoint
     */
    public function api(string $name): AbstractEndpoint
    {

        $api = match ($name) {
            'accounts' => new Endpoints\Accounts($this),
            'zones' => new Endpoints\Zones($this),
            'ips' => new Endpoints\IP($this),
            'workers' => new Endpoints\Workers($this),
            'tunnel' => new Endpoints\Tunnel($this),
            'd1' => new Endpoints\D1($this),
            default => throw new InvalidArgumentException(sprintf('Undefined api instance called: "%s"', $name))
        };

        return $api;
    }

    /**
     * @param string $name
     * @param array  $args
     *
     * @return AbstractEndpoint
     */
    public function __call(string $name, array $args): AbstractEndpoint
    {
        try {
            return $this->api($name);
        } catch (InvalidArgumentException $e) {
            throw new BadMethodCallException(sprintf('Undefined method called: "%s"', $name));
        }
    }
}
