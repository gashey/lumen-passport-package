<?php

namespace Gashey\LumenPassport;


class TokenRepository extends \Laravel\Passport\TokenRepository
{
    public function isAccessTokenRevoked($id)
    {
        //TODO: implement a check for if access token is revoked
        return false;
    }
}
