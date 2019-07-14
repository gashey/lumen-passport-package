<?php

namespace Gashey\LumenPassport;


class TokenGuard extends \Laravel\Passport\Guards\TokenGuard
{
    /**
     * Authenticate the incoming request via the Bearer token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function authenticateViaBearerToken($request)
    {
        if (!$psr = $this->getPsrRequestViaBearerToken($request)) {
            return;
        }

        // If the access token is valid we will retrieve the user according to the client ID
        // associated with the token. We will use the provider implementation which may
        // be used to retrieve users from Eloquent. Next, we'll be ready to continue.
        $user = $this->provider->retrieveById(
            $psr->getAttribute('oauth_client_id') ?: null
        );
        $user->name = $psr->getAttribute('oauth_name');

        if (!$user) {
            return;
        }

        // Next, we will assign a token instance to this user which the developers may use
        // to determine if the token has a given scope, etc. This will be useful during
        // authorization such as within the developer's Laravel model policy classes.
        $token = new \Laravel\Passport\Token();
        $token->scopes = $psr->getAttribute('oauth_scopes');

        return $token ? $user->withAccessToken($token) : null;
    }
}
