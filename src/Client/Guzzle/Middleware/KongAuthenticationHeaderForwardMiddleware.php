<?php

namespace WakeOnWeb\Bundle\KongOAuth2FirewallBundle\Client\Guzzle\Middleware;

use Closure;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Quentin Schuler <qschuler@neosyne.com>
 */
class KongAuthenticationHeaderForwardMiddleware
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param callable $handler
     *
     * @return Closure
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $sfRequest = $this->requestStack->getMasterRequest();

            if ($sfRequest !== null) {
                if ($header = $sfRequest->headers->get('X-Consumer-ID')) {
                    $request = $request->withHeader('X-Consumer-ID', $header);
                }

                if ($header = $sfRequest->headers->get('X-Anonymous-Consumer')) {
                    $request = $request->withHeader('X-Anonymous-Consumer', $header);
                }

                if ($header = $sfRequest->headers->get('X-Authenticated-UserID')) {
                    $request = $request->withHeader('X-Authenticated-UserID', $header);
                }
            }

            return $handler($request, $options);
        };
    }
}
