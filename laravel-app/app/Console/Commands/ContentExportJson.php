<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Console\Command;

class ContentExportJson extends Command
{
    protected $signature = 'content:export-json {--path= : Cikti klasoru}';

    protected $description = 'CMS icerigini (sayfa, yazi, kategori, menu, ayar) JSON dosyalarina aktarir';

    public function handle(): int
    {
        $dir = $this->option('path') ?: base_path('../site-backup/data');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $data = [
            'settings.json' => Setting::orderBy('key')->get(['key', 'value']),
            'categories.json' => Category::orderBy('name')->get(['name', 'slug', 'description']),
            'pages.json' => Page::orderBy('sort_order')->get([
                'title', 'slug', 'content', 'excerpt', 'meta_title', 'meta_description', 'status', 'sort_order',
            ]),
            'posts.json' => Post::with('categories:slug')->orderByDesc('published_at')->get()->map(fn ($p) => [
                'title' => $p->title,
                'slug' => $p->slug,
                'excerpt' => $p->excerpt,
                'content' => $p->content,
                'featured_image' => $p->featured_image,
                'status' => $p->status,
                'published_at' => optional($p->published_at)->toDateTimeString(),
                'categories' => $p->categories->pluck('slug')->toArray(),
            ]),
            'menu.json' => MenuItem::orderBy('location')->orderBy('sort_order')->get([
                'label', 'url', 'location', 'sort_order', 'is_active',
            ]),
        ];

        foreach ($data as $file => $rows) {
            file_put_contents(
                rtrim($dir, '/') . '/' . $file,
                json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
            $this->line("Yazildi: $file (" . count($rows) . " kayit)");
        }

        $this->info('JSON disa aktarim tamamlandi: ' . realpath($dir));

        return self::SUCCESS;
    }
}
