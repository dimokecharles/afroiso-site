<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Order;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultingProjectsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Contact $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['name' => 'Office Admin']);
        $this->client = Contact::create(['name' => 'Tendai Client', 'email' => 'client@example.com', 'organisation' => 'Example Mining', 'source' => 'manual']);
    }

    private function project(array $extra = []): Project
    {
        return Project::create($extra + ['contact_id' => $this->client->id, 'title' => 'ISO 9001 implementation', 'standard' => 'ISO 9001:2026', 'stage' => 'gap-analysis', 'value' => 6000]);
    }

    public function test_project_is_created_and_moved_through_its_stages(): void
    {
        $this->actingAs($this->admin)->get('/admin/projects')->assertOk()->assertSee('No consulting projects yet.');
        $this->actingAs($this->admin)->get('/admin/projects/create?contact=' . $this->client->id)->assertOk()->assertSee('Example Mining');

        $this->actingAs($this->admin)->post('/admin/projects', [
            'contact_id' => $this->client->id, 'title' => 'ISO 9001 implementation', 'standard' => 'ISO 9001:2026', 'stage' => 'proposal',
            'value' => '6000', 'start_date' => '2027-01-11', 'target_date' => '2027-06-30', 'assigned_to' => $this->admin->id,
        ])->assertRedirect();

        $project = Project::firstOrFail();
        $this->actingAs($this->admin)->get('/admin/projects/' . $project->id)->assertOk()->assertSee('ISO 9001 implementation')->assertSee('US$6,000.00')->assertSee('Office Admin');

        $this->actingAs($this->admin)->put('/admin/projects/' . $project->id, ['contact_id' => $this->client->id, 'title' => 'ISO 9001 implementation', 'stage' => 'building'])->assertRedirect();
        $this->assertSame('building', $project->fresh()->stage);
        $this->actingAs($this->admin)->get('/admin/projects?stage=building')->assertOk()->assertSee('ISO 9001 implementation');
        $this->actingAs($this->admin)->get('/admin/projects?stage=completed')->assertOk()->assertDontSee('ISO 9001 implementation');

        $this->actingAs($this->admin)->post('/admin/projects', ['contact_id' => $this->client->id, 'title' => 'Bad dates', 'stage' => 'proposal', 'start_date' => '2027-06-01', 'target_date' => '2027-01-01'])
            ->assertSessionHasErrors('target_date');
    }

    public function test_invoice_raised_from_a_project_is_linked_and_totalled(): void
    {
        $project = $this->project();

        $this->actingAs($this->admin)->get('/admin/orders/create?project=' . $project->id)->assertOk()->assertSee('client@example.com')->assertSee('ISO 9001 implementation');
        $this->actingAs($this->admin)->post('/admin/orders', [
            'project_id' => $project->id, 'customer_name' => 'Tendai Client', 'customer_email' => 'client@example.com',
            'organisation' => 'Example Mining', 'description' => 'Gap analysis, first invoice', 'amount' => '2000',
        ])->assertRedirect();

        $invoice = Order::firstOrFail();
        $this->assertSame($project->id, $invoice->project_id);
        $this->assertSame($this->client->id, $invoice->contact_id);

        $this->actingAs($this->admin)->get('/admin/projects/' . $project->id)->assertOk()->assertSee('Gap analysis, first invoice')->assertSee('US$2,000.00 outstanding');
        $invoice->markPaid('manual');
        $this->actingAs($this->admin)->get('/admin/projects/' . $project->id)->assertOk()->assertSee('US$0.00 outstanding');
        $this->actingAs($this->admin)->get('/admin/projects')->assertOk()->assertSee('US$2,000.00');
    }

    public function test_project_shows_on_the_contact_and_the_dashboard_and_takes_notes(): void
    {
        $project = $this->project(['target_date' => '2026-01-01']);

        $this->actingAs($this->admin)->get('/admin/contacts/' . $this->client->id)->assertOk()->assertSee('Consulting project: ISO 9001 implementation');
        $this->actingAs($this->admin)->get('/admin')->assertOk()->assertSee('Active consulting projects')->assertSee('ISO 9001 implementation')->assertSee('Overdue');

        $this->actingAs($this->admin)->post('/admin/notes', ['on' => 'project', 'id' => $project->id, 'body' => 'Kick-off meeting held.'])->assertRedirect();
        $this->actingAs($this->admin)->get('/admin/projects/' . $project->id)->assertOk()->assertSee('Kick-off meeting held.');
    }

    public function test_roles_limit_consulting_and_invoicing(): void
    {
        $project = $this->project();
        $sales = User::factory()->create(['role' => 'sales']);
        $content = User::factory()->create(['role' => 'content']);

        $this->actingAs($content)->get('/admin/projects')->assertForbidden();
        $this->actingAs($content)->post('/admin/notes', ['on' => 'project', 'id' => $project->id, 'body' => 'Should not save.'])->assertForbidden();

        $this->actingAs($sales)->get('/admin/projects/' . $project->id)->assertOk()->assertDontSee('New invoice for this project');
        $this->actingAs($sales)->get('/admin/orders/create?project=' . $project->id)->assertForbidden();
        $this->actingAs($this->admin)->get('/admin/projects/' . $project->id)->assertOk()->assertSee('New invoice for this project');
    }
}
