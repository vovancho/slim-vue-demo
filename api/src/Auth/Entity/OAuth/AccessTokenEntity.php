<?php

declare(strict_types=1);

namespace App\Auth\Entity\OAuth;

use Doctrine\ORM\Mapping as ORM;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="oauth_access_tokens")
 */
class AccessTokenEntity implements AccessTokenEntityInterface
{
    use AccessTokenTrait;
    use TokenEntityTrait;
    use EntityTrait;

    /**
     * @ORM\Column(type="string", length=80)
     * @ORM\Id
     */
    protected $identifier;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="expiry_date_time")
     */
    protected $expiryDateTime;

    /**
     * @ORM\Column(type="guid", name="user_identifier")
     */
    protected $userIdentifier;

    /**
     * @var ClientEntityInterface
     * @ORM\Column(type="oauth_client")
     */
    protected $client;

    /**
     * @var ScopeEntityInterface[]
     * @ORM\Column(type="oauth_scopes")
     */
    protected $scopes = [];

    protected ?string $email = null;

    public function getUserEmail(): ?string
    {
        return $this->email;
    }

    public function setUserEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * Generate a JWT from the access token
     *
     * @param CryptKey $privateKey
     *
     * @return Token
     */
    private function convertToJWT(CryptKey $privateKey)
    {
        $builder = (new Builder())
            ->setAudience($this->getClient()->getIdentifier())
            ->setId($this->getIdentifier())
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration($this->getExpiryDateTime()->getTimestamp())
            ->setSubject((string)$this->getUserIdentifier())
            ->set('scopes', $this->getScopes())
            ->sign(new Sha256(), new Key($privateKey->getKeyPath(), $privateKey->getPassPhrase()));

        $builder->withClaim('email', $this->getUserEmail());

        return $builder->getToken();
    }
}
