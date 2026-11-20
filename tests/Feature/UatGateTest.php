<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UatGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_live_site_is_open_and_indexable(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertHeaderMissing('X-Robots-Tag')
            ->assertSee('content="index, follow"', false)
            ->assertDontSee('UAT test site');
    }

    public function test_uat_site_asks_for_its_login(): void
    {
        config(['afroiso.uat' => true, 'afroiso.uat_user' => 'tester', 'afroiso.uat_password' => 'secret-for-tests']);

        $this->get('/about')->assertStatus(401)->assertHeader('WWW-Authenticate');
        $this->withBasicAuth('tester', 'wrong')->get('/about')->assertStatus(401);
    }

    public function test_uat_site_opens_with_its_login_and_is_hidden_from_search(): void
    {
        config(['afroiso.uat' => true, 'afroiso.uat_user' => 'tester', 'afroiso.uat_password' => 'secret-for-tests']);

        $this->withBasicAuth('tester', 'secret-for-tests')->get('/about')
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertSee('content="noindex, nofollow"', false)
            ->assertSee('UAT test site');
    }
}
