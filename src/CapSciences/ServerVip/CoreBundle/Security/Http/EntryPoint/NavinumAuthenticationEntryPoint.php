<?php

namespace CapSciences\ServerVip\CoreBundle\Security\Http\EntryPoint;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * NavinumAuthenticationEntryPoint starts an authentication via a login form.
 *
 */
class NavinumAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    private $httpUtils;
    private $loginPath;

    /**
     * Constructor
     *
     */
    public function __construct(HttpUtils $httpUtils, $loginPath)
    {
        $this->httpUtils = $httpUtils;
        $this->loginPath = $loginPath;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return $this->httpUtils->createRedirectResponse($request, $this->loginPath);
    }
}
