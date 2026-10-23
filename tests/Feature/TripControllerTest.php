<?php

namespace Tests\Feature;

use App\Trip;
use App\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TripControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @return array<string, mixed> */
    private function tripData(): array
    {
        return ['title' => 'Моё путешествие в горы', 'location' => 'Алтай, Россия', 'budget' => '42000.50', 'body' => 'Пешие прогулки, горные озёра и незабываемый вид с перевала.', 'places' => "Чуйский тракт\nГейзерное озеро", 'photo' => UploadedFile::fake()->image('trip.jpg', 600, 400)];
    }

    public function test_guests_can_read_other_users_travel_stories(): void
    {
        $trip = Trip::factory()->create(['title' => 'Уникальная история']);
        $this->get(route('trips.index'))->assertOk()->assertSee('Уникальная история');
        $this->get(route('trips.show', $trip))->assertOk()->assertSee($trip->body)->assertSee('42 000,00 ₽')->assertSee('Чуйский тракт');
    }

    public function test_guest_cannot_create_trip(): void
    {
        $this->post(route('trips.store'), [])->assertRedirect(route('login'));
        $this->assertDatabaseCount('trips', 0);
    }

    public function test_personal_diary_only_lists_current_users_trips(): void
    {
        $user = User::factory()->create();
        Trip::factory()->for($user)->create(['title' => 'Мой личный маршрут']);
        Trip::factory()->create(['title' => 'Чужой маршрут']);
        $this->actingAs($user)->get(route('diary'))->assertOk()->assertSee('Мой личный маршрут')->assertDontSee('Чужой маршрут');
    }

    public function test_publishing_saves_photo_budget_and_places_under_authenticated_owner(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $other = User::factory()->create();
        $response = $this->actingAs($user)->post(route('trips.store'), $this->tripData() + ['user_id' => $other->id, 'photo_path' => 'malicious.php']);
        $trip = Trip::firstOrFail();
        $response->assertRedirect(route('trips.show', $trip));
        $this->assertSame($user->id, $trip->user_id);
        $this->assertSame('42000.50', $trip->budget);
        $this->assertSame(['Чуйский тракт', 'Гейзерное озеро'], $trip->places);
        Storage::disk('public')->assertExists($trip->photo_path);
    }

    public function test_rejects_non_image_upload(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $data = $this->tripData();
        $data['photo'] = UploadedFile::fake()->create('attack.svg', 10, 'image/svg+xml');
        $response = $this->actingAs($user)->post(route('trips.store'), $data);
        $response->assertSessionHasErrors('photo');
        $this->assertDatabaseCount('trips', 0);
        Storage::disk('public')->assertEmpty();
    }

    public function test_rejects_photo_larger_than_four_megabytes(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $data = $this->tripData();
        $data['photo'] = UploadedFile::fake()->image('large.jpg')->size(4097);
        $response = $this->actingAs($user)->post(route('trips.store'), $data);
        $response->assertSessionHasErrors(['photo' => 'Фотография должна быть размером до 4 МБ.']);
        $this->assertDatabaseCount('trips', 0);
        Storage::disk('public')->assertEmpty();
    }

    /** @return array<string, array{string, mixed}> */
    public static function invalidFields(): array
    {
        return ['short title' => ['title', 'аб'], 'negative budget' => ['budget', -1], 'fractional kopecks' => ['budget', '42.001'], 'empty body' => ['body', ''], 'empty places' => ['places', '   '], 'missing photo' => ['photo', null]];
    }

    #[DataProvider('invalidFields')]
    public function test_rejects_invalid_trip_data_without_creating_record(string $field, mixed $value): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $data = $this->tripData();
        $data[$field] = $value;
        $response = $this->actingAs($user)->post(route('trips.store'), $data);
        $response->assertSessionHasErrors($field);
        $this->assertDatabaseCount('trips', 0);
        Storage::disk('public')->assertEmpty();
    }

    public function test_editing_and_deleting_are_unavailable_even_to_the_owner(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->for($user)->create();

        $this->actingAs($user)->get('/trips/'.$trip->id.'/edit')->assertNotFound();
        $this->put('/trips/'.$trip->id, ['title' => 'Изменённая история'])->assertMethodNotAllowed();
        $this->delete('/trips/'.$trip->id)->assertMethodNotAllowed();

        $this->assertModelExists($trip);
        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'title' => $trip->title]);
    }

    public function test_feed_and_creation_form_only_expose_required_features(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->for($user)->create();

        $this->actingAs($user)->get(route('trips.index'))->assertOk()
            ->assertDontSee('Поиск по месту или названию')->assertDontSee('Сначала дешевле')
            ->assertDontSee('Категории путешествий')->assertDontSee('историй путешествий');
        $this->get(route('trips.create'))->assertOk()->assertSee('Стоимость поездки')
            ->assertSee('Места для посещения')->assertSee('Добавьте фотографию')
            ->assertDontSee('Начало путешествия')->assertDontSee('Возвращение')->assertDontSee('Настроение поездки');
        $this->get(route('trips.show', $trip))->assertOk()
            ->assertDontSee('Продолжительность')->assertDontSee('Редактировать историю')->assertDontSee('Удалить путешествие');
    }

    public function test_user_content_is_escaped_in_story(): void
    {
        $trip = Trip::factory()->create(['title' => '<script>alert(1)</script>', 'body' => '<img src=x onerror=alert(1)>', 'places' => ['<svg onload=alert(1)>']]);
        $this->get(route('trips.show', $trip))->assertOk()->assertSee($trip->title)->assertSee($trip->body)->assertDontSee($trip->title, false)->assertDontSee($trip->body, false);
    }

    public function test_unknown_story_returns_not_found(): void
    {
        $this->get('/trips/99999')->assertNotFound();
    }
}
