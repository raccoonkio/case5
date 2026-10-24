<?php

namespace Tests\Feature;

use App\Trip;
use App\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TripSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ReportSourcesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_reports_preserve_original_authors_sources_and_historical_budgets(): void
    {
        $this->seed(DatabaseSeeder::class);
        $trip = Trip::where('source_url', 'https://t-j.ru/must-see-teriberka/')->firstOrFail();

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk()->assertSee('Александра Жихарева')->assertSee('Герман Жихарев')->assertSee('https://t-j.ru/must-see-teriberka/')->assertSee('Читать оригинал')->assertSee('Краткий пересказ отчёта')->assertSee('86 500,00 ₽');
    }

    public function test_reseeding_replaces_old_demo_stories_without_duplicates_and_preserves_personal_entries(): void
    {
        $this->freezeTime();
        $owner = User::factory()->create(['email' => 'anna@example.com']);
        $personal = Trip::factory()->for($owner)->create(['title' => 'Моя настоящая личная история']);
        Trip::factory()->for($owner)->create(['title' => 'Там, где горы встречают небо', 'photo_path' => 'assets/altai.jpg']);
        $this->seed(DatabaseSeeder::class);

        $this->seed(TripSeeder::class);

        $this->assertDatabaseCount('trips', 8);
        $this->assertModelExists($personal);
        $this->assertDatabaseMissing('trips', ['title' => 'Там, где горы встречают небо']);
        $this->assertDatabaseHas('trips', ['location' => 'Крым', 'source_author' => 'Александр Кот', 'budget' => 77390]);
        $this->assertDatabaseHas('trips', ['location' => 'Карелия, Россия', 'source_author' => 'Ромчик', 'budget' => 85000]);
    }

    public function test_kaliningrad_report_credits_its_actual_publisher(): void
    {
        $this->seed(DatabaseSeeder::class);
        $trip = Trip::where('source_author', 'Ольга Решилова')->firstOrFail();

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk()->assertSee('Т—Ж')->assertSee('Ольга Решилова')->assertSee('65 166,00 ₽')->assertSee($trip->source_url)->assertDontSee('Людмил');
        $this->assertStringNotContainsString('подруг', $trip->body);
        $this->assertStringNotContainsString('муж', $trip->body);
        $this->assertStringNotContainsString('ребён', $trip->body);
        $this->assertStringContainsString('Я выбрала', $trip->body);
    }

    public function test_feed_links_to_original_reports_and_displays_source_authors(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get(route('trips.index'));

        $response->assertOk()->assertSee('Александр Кот')->assertSee('Александра Жихарева')->assertSee('Ромчик')->assertSee('Т—Ж · источник')->assertSee('https://t-j.ru/by-car-with-dog-to-karelia-trip/')->assertDontSee('Стамбул')->assertDontSee('Италия');
    }
}
