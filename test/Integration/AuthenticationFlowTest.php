<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Integration\Support\IntegrationTestCase;

final class AuthenticationFlowTest extends IntegrationTestCase
{
    public function test_guest_is_redirected_when_trying_to_access_the_dashboard(): void
    {
        $response = $this->request('GET', '/dashboard');

        self::assertSame(302, $response->statusCode());
        self::assertSame('/test/login', $response->headers()['Location']);
    }

    public function test_user_can_login_and_logout(): void
    {
        $loginResponse = $this->login('user@gymtrack.test');

        self::assertSame(302, $loginResponse->statusCode());
        self::assertSame('/test/dashboard', $loginResponse->headers()['Location']);
        self::assertSame(3, $_SESSION['auth_user_id']);

        $logoutResponse = $this->request('POST', '/logout', [], [
            '_token' => $this->app->csrf()->token('logout'),
        ]);

        self::assertSame(302, $logoutResponse->statusCode());
        self::assertSame('/test/login', $logoutResponse->headers()['Location']);
        self::assertArrayNotHasKey('auth_user_id', $_SESSION);
    }
}
