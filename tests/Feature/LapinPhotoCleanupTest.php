<?php

namespace Tests\Feature;

use App\Models\Lapin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LapinPhotoCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_remplacer_la_photo_dun_lapin_supprime_lancienne(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        Storage::disk('public')->put('lapins/ancienne.jpg', 'contenu');
        $lapin = Lapin::factory()->create(['photo_path' => 'lapins/ancienne.jpg']);

        Storage::disk('public')->put('lapins/nouvelle.jpg', 'contenu');
        $lapin->update(['photo_path' => 'lapins/nouvelle.jpg']);

        Storage::disk('public')->assertMissing('lapins/ancienne.jpg');
        Storage::disk('public')->assertExists('lapins/nouvelle.jpg');
    }

    public function test_la_suppression_douce_preserve_la_photo_mais_la_suppression_definitive_lefface(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        Storage::disk('public')->put('lapins/photo.jpg', 'contenu');
        $lapin = Lapin::factory()->create(['photo_path' => 'lapins/photo.jpg']);

        $lapin->delete();
        Storage::disk('public')->assertExists('lapins/photo.jpg');

        $lapin->forceDelete();
        Storage::disk('public')->assertMissing('lapins/photo.jpg');
    }
}
