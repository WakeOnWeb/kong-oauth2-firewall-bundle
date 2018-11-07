# WakeOnWebKongOAuth2FirewallBundle

**Note:** The bundle handle the authentication of user when used behind a Kong API Gateway. Be sure to isolate your 
application from the outside world. Kong transform user credentials into a set of headers that are used to authenticate 
the user. An attacker can fake them with a direct access to your app.

## Configuration

_/config/packages/security.yaml_

    security:
        providers:
            trusted:
                id: WakeOnWeb\Bundle\KongOAuth2FirewallBundle\Security\TrustedUserProvider
            # OR USE YOUR OWN PROVIDER
            # in_memory:
            #     memory:
            #         users:
            #             66ce7c7d-ef0a-47f9-b952-a7dd44ebc7cc: ~
    
        firewalls:
            dev:
                pattern: ^/(_(profiler|wdt)|css|images|js)/
                security: false
            main:
                kong:
                    consumer_ids:
                        - 8ca2548f-3b97-4a03-847e-9e42c150e644
                    anonymous_consumer_ids:
                        - 836b6e33-fd83-43b6-ab77-74390873d7b6
    
        access_control:
            - { path: ^/me, roles: IS_AUTHENTICATED_FULLY }
            - { path: ^/ping, roles: IS_AUTHENTICATED_ANONYMOUSLY }

The authentication mechanism use a combination of headers provided by kong. An authenticated user will have the 
following two headers `X-Consumer-ID` and `X-Authenticated-UserID`. An anonymous user will only have the header 
`X-Anonymous-Consumer`.

It appears that the `X-Authenticated-UserID` header is not reliable at all. The firewall could only trust the 
`X-Consumer-ID` and the `X-Anonymous-Consumer`. That's why both the IDs should appears in the firewall configuration.

## Todo

- Add an HTTP Client / Guzzle Middleware that forwards the kong authentication headers when communicating with other 
  local microservices.
