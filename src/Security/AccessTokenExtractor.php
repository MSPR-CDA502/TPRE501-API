<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\AccessToken\AccessTokenExtractorInterface;

class AccessTokenExtractor implements AccessTokenExtractorInterface
{
    public function extractAccessToken(Request $request): ?string
    {
        if (!$request->headers->has('X-Auth-Token') || !\is_string($header = $request->headers->get('X-Auth-Token'))) {
            return null;
        }

        $token = $request->headers->get('X-Auth-Token');

        return $token;
    }
}
