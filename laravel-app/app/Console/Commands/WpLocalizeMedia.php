<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Console\Command;

class WpLocalizeMedia extends Command
{
    protected $signature = 'wp:localize-media {--domain=analizdegerleme.com : Eski alan adi}';

    protected $description = 'Icerikteki eski alan adina ait gorsel URL\'lerini yerel /storage/uploads/ yoluna cevirir';

    public function handle(): int
    {
        $domain = preg_quote($this->option('domain'), '/');

        // http(s)://(www.)domain/wp-content/uploads/  veya  //domain/wp-content/uploads/  ->  /storage/uploads/
        $pattern = '/(https?:)?\/\/(www\.)?' . $domain . '\/wp-content\/uploads\//i';
        $replace = '/storage/uploads/';

        $pages = 0;
        foreach (Page::all() as $page) {
            $new = preg_replace($pattern, $replace, $page->content);
            if ($new !== $page->content) {
                $page->content = $new;
                $page->saveQuietly();
                $pages++;
            }
        }

        $posts = 0;
        foreach (Post::all() as $post) {
            $changed = false;
            $newContent = preg_replace($pattern, $replace, (string) $post->content);
            if ($newContent !== $post->content) {
                $post->content = $newContent;
                $changed = true;
            }
            if ($post->featured_image) {
                $newImg = preg_replace($pattern, $replace, $post->featured_image);
                if ($newImg !== $post->featured_image) {
                    $post->featured_image = $newImg;
                    $changed = true;
                }
            }
            if ($changed) {
                $post->saveQuietly();
                $posts++;
            }
        }

        // Ayarlardaki olasi gorseller (logo vb.)
        foreach (Setting::all() as $s) {
            $new = preg_replace($pattern, $replace, (string) $s->value);
            if ($new !== $s->value) {
                Setting::set($s->key, $new);
            }
        }

        $this->info("URL yerellestirme tamam: {$pages} sayfa, {$posts} yazi guncellendi.");
        $this->line('Gorselleri indirmek icin: php artisan wp:fetch-media');

        return self::SUCCESS;
    }
}
