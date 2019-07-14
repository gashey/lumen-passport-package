<?php

namespace Gashey\LumenPassport;


class TokenRepository extends \Laravel\Passport\TokenRepository
{
    /**
     * Check if the access token has been revoked.
     *
     * @param  string  $id
     *
     * @return bool Return true if this token has been revoked
     */
    public function isAccessTokenRevoked($id)
    {
        //TODO: implement a check for if access token is revoked
        return false;
    }
}
