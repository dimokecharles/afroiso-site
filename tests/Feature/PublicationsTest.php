<?php

namespace Tests\Feature;

use App\Models\Publication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_publications_page_shows_titles_and_covers(): void
    {
        $this->seed();

        $this->get('/publications')
            ->assertOk()
            ->assertSee('Systems Over Hustle: ISO Standards for African SMEs')
            ->assertSee('StandardIQ, 1–15 November 2026: The Quality Leadership Test')
            ->assertSee('images/publications/standardiq-october-2026.webp')
            ->assertDontSee('Cover to follow')
            ->assertDontSee('[BOOK TITLE]');
    }

    public function test_every_cover_file_exists(): void
    {
        $this->seed();

        foreach (Publication::pluck('cover_path') as $coverPath) {
            $this->assertFileExists(public_path($coverPath));
        }
    }

    public function test_migration_replaces_placeholders_on_an_existing_site(): void
    {
        Publication::create(['type' => 'book', 'title' => '[BOOK TITLE]', 'price' => 40, 'sort_order' => 10]);
        Publication::create(['type' => 'magazine', 'title' => '[MAGAZINE TITLE]', 'price' => 4.99, 'sort_order' => 20]);
        $editedInAdmin = Publication::create(['type' => 'book', 'title' => 'A book the client added', 'sort_order' => 40]);

        $migration = require database_path('migrations/2026_11_19_011817_fill_in_publications.php');
        $migration->up();
        $migration->up();

        $this->assertSame(4, Publication::count());
        $this->assertDatabaseMissing('publications', ['title' => '[BOOK TITLE]']);
        $this->assertDatabaseMissing('publications', ['title' => '[MAGAZINE TITLE]']);
        $this->assertDatabaseHas('publications', [
            'title' => 'Systems Over Hustle: ISO Standards for African SMEs',
            'cover_path' => 'images/publications/systems-over-hustle.jpg',
        ]);
        $this->assertSame('A book the client added', $editedInAdmin->fresh()->title);
    }
}
