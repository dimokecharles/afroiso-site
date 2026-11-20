<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyPagesTest extends TestCase
{
    use RefreshDatabase;

    private array $pages = ['privacy-policy' => 'Information we collect', 'refund-policy' => 'How to ask for a refund', 'terms' => 'Enrolment and payment'];

    public function test_live_site_keeps_the_placeholder_until_policies_are_published(): void
    {
        foreach ($this->pages as $slug => $heading) {
            $this->get('/' . $slug)
                ->assertOk()
                ->assertSee('being finalised')
                ->assertDontSee($heading)
                ->assertSee('content="noindex, follow"', false);
        }
    }

    public function test_uat_site_shows_the_drafts_for_review(): void
    {
        config(['afroiso.uat' => true, 'afroiso.uat_password' => null]);

        foreach ($this->pages as $slug => $heading) {
            $this->get('/' . $slug)->assertOk()->assertSee($heading)->assertSee('Draft for review');
        }
    }

    public function test_published_policies_show_on_the_live_site_without_the_draft_note(): void
    {
        config(['afroiso.policies_published' => true]);

        foreach ($this->pages as $slug => $heading) {
            $this->get('/' . $slug)
                ->assertOk()
                ->assertSee($heading)
                ->assertDontSee('Draft for review')
                ->assertSee('content="index, follow"', false);
        }
    }
}
