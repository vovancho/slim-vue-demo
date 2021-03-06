<?php

declare(strict_types=1);

namespace App\Auth\Entity\OAuth;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    private array $clients;

    public function __construct(array $clients)
    {
        $this->clients = $clients;
    }

    /**
     * Get a client.
     *
     * @param string $clientIdentifier The client's identifier
     *
     * @return ClientEntityInterface|null
     */
    public function getClientEntity($clientIdentifier)
    {
        $client = new ClientEntity($clientIdentifier);
        $client->setName($this->clients[$clientIdentifier]['name']);
        $client->setRedirectUri($this->clients[$clientIdentifier]['redirect_uri']);

        return $client;
    }

    /**
     * Validate a client's secret.
     *
     * @param string $clientIdentifier The client's identifier
     * @param null|string $clientSecret The client's secret (if sent)
     * @param null|string $grantType The type of grant the client is using (if sent)
     *
     * @return bool
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        if (array_key_exists($clientIdentifier, $this->clients) === false) {
            return false;
        }

        if (
            $this->clients[$clientIdentifier]['is_confidential'] === true
            && password_verify($clientSecret, $this->clients[$clientIdentifier]['secret']) === false
        ) {
            return false;
        }

        return true;
    }
}
