<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;

class ContentImportJson extends Command
{
    protected $signature = 'content:import-json {--path= : JSON klasoru}';

    protected $description = 'site-backup/data altindaki JSON dosyalarindan icerigi CMS tablolarina yukler';

    public function handle(): int
    {
        $dir = $this->option('path') ?: base_path('../site-backup/data');
        if (! is_dir($dir)) {
            $this->error("Klasor bulunamadi: $dir");

            return self::FAILURE;
        }

        $read = fn (string $f) => is_file("$dir/$f") ? json_decode(file_get_contents("$dir/$f"), true) : [];

        foreach ($read('settings.json') as $s) {
            Setting::updateOrCreate(['key' => $s['key']], ['value' => $s['value']]);
        }
        foreach ($read('categories.json') as $c) {
            Category::updateOrCreate(['slug' => $c['slug']], $c);
        }
        foreach ($read('pages.json') as $p) {
            Page::updateOrCreate(['slug' => $p['slug']], $p);
        }

        $author = User::where('is_admin', true)->first();
        foreach ($read('posts.json') as $p) {
            $cats = $p['categories'] ?? [];
            unset($p['categories']);
            $post = Post::updateOrCreate(['slug' => $p['slug']], $p + ['user_id' => $author?->id]);
            $ids = Category::whereIn('slug', $cats)->pluck('id')->toArray();
            $post->categories()->sync($ids);
        }
        foreach ($read('menu.json') as $m) {
            MenuItem::updateOrCreate(['label' => $m['label'], 'location' => $m['location']], $m);
        }

        $this->info('JSON iceriden yukleme tamamlandi.');

        return self::SUCCESS;
    }
}
