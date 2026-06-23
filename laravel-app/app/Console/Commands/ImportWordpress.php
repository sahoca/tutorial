<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportWordpress extends Command
{
    protected $signature = 'wp:import {file : WordPress WXR (XML) export dosyasinin yolu}';

    protected $description = 'WordPress WXR (XML) export dosyasindan sayfa ve yazilari ice aktarir';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (! is_file($file)) {
            $this->error("Dosya bulunamadi: $file");
            $this->line('İpucu: WordPress > Araçlar > Dışa Aktar ile .xml (WXR) dosyası oluştur.');

            return self::FAILURE;
        }

        $xml = simplexml_load_file($file, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (! $xml) {
            $this->error('XML okunamadi. Gecerli bir WXR dosyasi mi?');

            return self::FAILURE;
        }

        $wp = $xml->channel->children('http://wordpress.org/export/1.2/');
        $content = $xml->channel->children('http://purl.org/rss/1.0/modules/content/');
        $excerptNs = 'http://wordpress.org/export/1.2/excerpt/';

        $author = User::where('is_admin', true)->first();
        $countPost = 0;
        $countPage = 0;

        foreach ($xml->channel->item as $item) {
            $wpItem = $item->children('http://wordpress.org/export/1.2/');
            $contentItem = $item->children('http://purl.org/rss/1.0/modules/content/');
            $excerptItem = $item->children($excerptNs);

            $type = (string) $wpItem->post_type;
            $status = (string) $wpItem->status === 'publish' ? 'published' : 'draft';
            $title = trim((string) $item->title);
            $slug = (string) $wpItem->post_name ?: Str::slug($title);
            $body = (string) $contentItem->encoded;
            $excerpt = trim((string) $excerptItem->encoded);
            $date = (string) $wpItem->post_date;

            if ($title === '' && $body === '') {
                continue;
            }

            if ($type === 'page') {
                Page::updateOrCreate(['slug' => $slug], [
                    'title' => $title ?: $slug,
                    'content' => $body,
                    'excerpt' => $excerpt ?: null,
                    'status' => $status,
                ]);
                $countPage++;
                $this->line("Sayfa: $title");
            } elseif ($type === 'post') {
                $post = Post::updateOrCreate(['slug' => $slug], [
                    'title' => $title ?: $slug,
                    'content' => $body,
                    'excerpt' => $excerpt ?: null,
                    'status' => $status,
                    'user_id' => $author?->id,
                    'published_at' => $date && $date !== '0000-00-00 00:00:00' ? $date : null,
                ]);

                // Kategoriler
                foreach ($item->category as $cat) {
                    if ((string) $cat['domain'] === 'category') {
                        $catSlug = (string) $cat['nicename'] ?: Str::slug((string) $cat);
                        $category = Category::updateOrCreate(
                            ['slug' => $catSlug],
                            ['name' => trim((string) $cat)]
                        );
                        $post->categories()->syncWithoutDetaching([$category->id]);
                    }
                }

                $countPost++;
                $this->line("Yazı: $title");
            }
        }

        $this->newLine();
        $this->info("Tamamlandı: $countPost yazı, $countPage sayfa içe aktarıldı.");
        $this->line('Görseller için: WordPress uploads klasörünü laravel-app/public/storage altına kopyala,');
        $this->line('içerikteki eski URL\'leri yeni site adresinle değiştirmen gerekebilir.');

        return self::SUCCESS;
    }
}
