<?php

/*
 * This file is part of fof/passport.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GmFire\Passport\Controllers;

use Exception;
use Flarum\Forum\Auth\Registration;
use Flarum\Forum\Auth\ResponseFactory;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use GmFire\Passport\Events\SendingResponse;
use GmFire\Passport\Providers\PassportProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PassportController implements RequestHandlerInterface
{
    protected $settings;
    protected $response;
    protected $events;
    protected $url;

    public function __construct(ResponseFactory $response, SettingsRepositoryInterface $settings, Dispatcher $events, UrlGenerator $url)
    {
        $this->response = $response;
        $this->settings = $settings;
        $this->events = $events;
        $this->url = $url;
    }

    protected function getProvider($redirectUri)
    {
        return new PassportProvider([
            'clientId'     => $this->settings->get('fof-passport.app_id'),
            'clientSecret' => $this->settings->get('fof-passport.app_secret'),
            'redirectUri'  => $redirectUri,
            'settings'     => $this->settings,
        ]);
    }

    /**
     * @return array
     */
    protected function getAuthorizationUrlOptions()
    {
        $scopes = $this->settings->get('fof-passport.app_oauth_scopes', '');

        return ['scope' => $scopes];
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $redirectUri = $this->url->to('forum')->route('auth.passport');

        $provider = $this->getProvider($redirectUri);

        $session = $request->getAttribute('session');
        $queryParams = $request->getQueryParams();

        if ($error = Arr::get($queryParams, 'error')) {
            $hint = Arr::get($queryParams, 'hint');

            throw new Exception("$error: $hint");
        }

        $code = Arr::get($queryParams, 'code');

        if (!$code) {
            $authUrl = $provider->getAuthorizationUrl($this->getAuthorizationUrlOptions());
            $session->put('oauth2state', $provider->getState());

            return new RedirectResponse($authUrl);
        }

        $state = Arr::get($queryParams, 'state');

        if (!$state || $state !== $session->get('oauth2state')) {
            $session->remove('oauth2state');

            throw new Exception('Invalid state');
        }

        $token = $provider->getAccessToken('authorization_code', compact('code'));
        $user = $provider->getResourceOwner($token);

        // 判断头像是否能读取
        if ($user->toArray()['avatar']) {
            $avatarData = @file_get_contents($user->toArray()['avatar']);
        }

        $response = $this->response->make(
            'passport',
            $user->getId(),
            function (Registration $registration) use ($user, $avatarData) {
                $registration
                    ->provideTrustedEmail($user->getEmail())
                    ->provide('nickname', $user->toArray()['username'])
                    ->provide('username', $user->getId().'');
                if ($avatarData) {
                    $registration->provideAvatar($user->toArray()['avatar']);
                }
                $registration->setPayload($user->toArray());
            }
        );

        $this->events->dispatch(new SendingResponse(
            $response,
            $user,
            $token
        ));

        return $response;
    }
}
