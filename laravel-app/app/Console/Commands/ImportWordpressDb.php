<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportWordpressDb extends Command
{
    protected $signature = 'wp:import-db {--prefix=wp_ : WordPress tablo on eki}';

    protected $description = 'Ayni veritabanindaki WordPress tablolarindan (wp_posts vb.) icerigi CMS tablolarina aktarir';

    private string $p;

    public function handle(): int
    {
        $this->p = $this->option('prefix');

        $this->importSettings();
        $this->importCategories();
        $pages = $this->importPages();
        $posts = $this->importPosts();
        $this->importMenu();

        $this->newLine();
        $this->info("Aktarim tamamlandi: {$pages} sayfa, {$posts} yazi.");
        $this->line('Not: Icerikteki gorseller eski alan adina (http://analizdegerleme.com/wp-content/uploads/...) isaret ediyor.');
        $this->line('Gorselleri kalici yapmak icin uploads klasorunu public/storage altina kopyalayip URL\'leri guncelleyebilirsiniz.');

        return self::SUCCESS;
    }

    private function t(string $name): string
    {
        return $this->p . $name;
    }

    private function importSettings(): void
    {
        $opts = DB::table($this->t('options'))
            ->whereIn('option_name', ['blogname', 'blogdescription', 'admin_email'])
            ->pluck('option_value', 'option_name');

        $map = [
            'site_name' => $opts['blogname'] ?? null,
            'site_tagline' => $opts['blogdescription'] ?? null,
            'site_description' => $opts['blogdescription'] ?? null,
            'contact_email' => $opts['admin_email'] ?? null,
        ];
        foreach ($map as $k => $v) {
            if ($v !== null) {
                Setting::set($k, $v);
            }
        }
        $this->line('Ayarlar aktarildi.');
    }

    private function importCategories(): void
    {
        $rows = DB::table($this->t('term_taxonomy') . ' as tt')
            ->join($this->t('terms') . ' as t', 't.term_id', '=', 'tt.term_id')
            ->where('tt.taxonomy', 'category')
            ->select('t.term_id', 't.name', 't.slug', 'tt.description')
            ->get();

        foreach ($rows as $r) {
            if ($r->slug === 'uncategorized' || $r->slug === 'genel') {
                continue;
            }
            Category::updateOrCreate(['slug' => $r->slug], [
                'name' => $r->name,
                'description' => $r->description ?: null,
            ]);
        }
        $this->line("Kategoriler aktarildi ({$rows->count()}).");
    }

    private function importPages(): int
    {
        // Tema/builder demo sayfalarini atla
        $skip = ['2-columns-sidebar', 'gallery', 'portfolio-masonry', 'home-2'];

        $rows = DB::table($this->t('posts'))
            ->where('post_type', 'page')
            ->where('post_status', 'publish')
            ->orderBy('menu_order')->orderBy('ID')
            ->get();

        $count = 0;
        foreach ($rows as $r) {
            if (in_array($r->post_name, $skip, true)) {
                continue;
            }
            $content = $this->cleanContent($r->post_content);
            if (trim(strip_tags($content)) === '') {
                continue;
            }

            Page::updateOrCreate(['slug' => $r->post_name ?: Str::slug($r->post_title)], [
                'title' => $r->post_title ?: $r->post_name,
                'content' => $content,
                'excerpt' => $this->excerpt($r->post_excerpt, $content),
                'status' => 'published',
                'sort_order' => (int) $r->menu_order,
            ]);
            $count++;
        }
        $this->line("Sayfalar aktarildi ({$count}).");

        return $count;
    }

    private function importPosts(): int
    {
        $author = User::where('is_admin', true)->first();

        $rows = DB::table($this->t('posts'))
            ->where('post_type', 'post')
            ->where('post_status', 'publish')
            ->orderByDesc('post_date')
            ->get();

        $count = 0;
        foreach ($rows as $r) {
            $content = $this->cleanContent($r->post_content);

            $post = Post::updateOrCreate(['slug' => $r->post_name ?: Str::slug($r->post_title)], [
                'title' => $r->post_title ?: $r->post_name,
                'content' => $content,
                'excerpt' => $this->excerpt($r->post_excerpt, $content),
                'featured_image' => $this->featuredImage($r->ID),
                'status' => 'published',
                'user_id' => $author?->id,
                'published_at' => $this->date($r->post_date),
            ]);

            // Kategoriler
            $catSlugs = DB::table($this->t('term_relationships') . ' as tr')
                ->join($this->t('term_taxonomy') . ' as tt', 'tt.term_taxonomy_id', '=', 'tr.term_taxonomy_id')
                ->join($this->t('terms') . ' as t', 't.term_id', '=', 'tt.term_id')
                ->where('tr.object_id', $r->ID)
                ->where('tt.taxonomy', 'category')
                ->pluck('t.slug');

            $ids = Category::whereIn('slug', $catSlugs)->pluck('id')->toArray();
            if ($ids) {
                $post->categories()->sync($ids);
            }

            $count++;
        }
        $this->line("Yazilar aktarildi ({$count}).");

        return $count;
    }

    private function importMenu(): void
    {
        // Birincil menuyu bul (en cok ogesi olan nav_menu)
        $menu = DB::table($this->t('term_taxonomy') . ' as tt')
            ->join($this->t('terms') . ' as t', 't.term_id', '=', 'tt.term_id')
            ->where('tt.taxonomy', 'nav_menu')
            ->orderByDesc('tt.count')
            ->select('tt.term_taxonomy_id', 't.name')
            ->first();

        if (! $menu) {
            return;
        }

        $itemIds = DB::table($this->t('term_relationships'))
            ->where('term_taxonomy_id', $menu->term_taxonomy_id)
            ->pluck('object_id');

        $items = DB::table($this->t('posts'))
            ->whereIn('ID', $itemIds)
            ->where('post_type', 'nav_menu_item')
            ->orderBy('menu_order')
            ->get();

        $created = 0;
        foreach ($items as $item) {
            $meta = DB::table($this->t('postmeta'))
                ->where('post_id', $item->ID)
                ->pluck('meta_value', 'meta_key');

            // Sadece ust seviye ogeler (sadelik icin)
            if (! empty($meta['_menu_item_menu_item_parent']) && $meta['_menu_item_menu_item_parent'] !== '0') {
                continue;
            }

            $type = $meta['_menu_item_type'] ?? 'custom';
            $url = '#';
            if ($type === 'custom') {
                $url = $meta['_menu_item_url'] ?? '#';
            } elseif ($type === 'post_type') {
                $targetId = $meta['_menu_item_object_id'] ?? null;
                $target = $targetId ? DB::table($this->t('posts'))->where('ID', $targetId)->first() : null;
                if ($target) {
                    $url = $target->post_type === 'page' ? '/' . $target->post_name : '/blog/' . $target->post_name;
                }
            }

            $label = trim($item->post_title) ?: 'Menu';
            // Eski alan adini goreceli yola cevir
            $url = str_replace(['http://analizdegerleme.com', 'https://analizdegerleme.com'], '', $url) ?: '/';

            MenuItem::updateOrCreate(
                ['label' => $label, 'location' => 'header'],
                ['url' => $url, 'location' => 'header', 'sort_order' => (int) $item->menu_order, 'is_active' => true]
            );
            $created++;
        }
        $this->line("Menu ogeleri aktarildi ({$created}).");
    }

    private function featuredImage(int $postId): ?string
    {
        $thumbId = DB::table($this->t('postmeta'))
            ->where('post_id', $postId)
            ->where('meta_key', '_thumbnail_id')
            ->value('meta_value');

        if (! $thumbId) {
            return null;
        }

        $url = DB::table($this->t('posts'))->where('ID', $thumbId)->value('guid');

        return $url ?: null;
    }

    private function cleanContent(?string $html): string
    {
        $html = (string) $html;
        if (trim($html) === '') {
            return '';
        }

        // Gutenberg blok yorumlarini kaldir
        $html = preg_replace('/<!--\s*\/?wp:.*?-->/s', '', $html);
        // WPBakery / diger kisa kodlari kaldir (icerigi koru)
        $html = preg_replace('/\[\/?[a-zA-Z0-9_\-]+[^\]]*\]/', '', $html);
        // Fazla bos satirlari sadelestir
        $html = preg_replace("/\n{3,}/", "\n\n", $html);

        return $this->autop(trim($html));
    }

    private function autop(string $text): string
    {
        if ($text === '') {
            return '';
        }
        $blocks = preg_split('/\n\s*\n/', $text);
        $out = '';
        foreach ($blocks as $b) {
            $b = trim($b);
            if ($b === '') {
                continue;
            }
            if (preg_match('/^<(p|div|h[1-6]|ul|ol|li|table|img|figure|blockquote|section|iframe)/i', $b)) {
                $out .= $b . "\n";
            } else {
                $out .= '<p>' . nl2br($b) . "</p>\n";
            }
        }

        return $out;
    }

    private function excerpt(?string $excerpt, string $content): ?string
    {
        $excerpt = trim((string) $excerpt);
        if ($excerpt !== '') {
            return Str::limit(strip_tags($excerpt), 280);
        }
        $plain = trim(strip_tags($content));

        return $plain !== '' ? Str::limit($plain, 200) : null;
    }

    private function date(?string $date): ?string
    {
        if (! $date || $date === '0000-00-00 00:00:00') {
            return null;
        }

        return $date;
    }
}
