<?php

/*
 * This file is part of fof/passport.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GmFire\Passport\Events;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Psr\Http\Message\ResponseInterface;

class SendingResponse
{
    /**
     * @var ResponseInterface
     */
    public $response;
    /**
     * @var ResourceOwnerInterface
     */
    public $user;
    /**
     * @var string
     */
    public $token;

    public function __construct(ResponseInterface &$response, ResourceOwnerInterface $user, string $token)
    {
        $this->response = &$response;
        $this->user = $user;
        $this->token = $token;
    }
}
