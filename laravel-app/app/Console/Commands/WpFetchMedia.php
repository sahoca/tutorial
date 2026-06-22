<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Post;
use Illuminate\Console\Command;

class WpFetchMedia extends Command
{
    protected $signature = 'wp:fetch-media
        {--domain=analizdegerleme.com : Gorsellerin indirilecegi kaynak alan adi}
        {--scheme=https : http veya https}';

    protected $description = 'Icerikteki /storage/uploads/ gorsellerini kaynak siteden indirip public/storage/uploads altina kaydeder';

    public function handle(): int
    {
        $domain = $this->option('domain');
        $scheme = $this->option('scheme');

        $paths = $this->collectUploadPaths();
        if (empty($paths)) {
            $this->warn('Indirilecek gorsel bulunamadi.');

            return self::SUCCESS;
        }

        $this->info(count($paths) . ' gorsel bulundu, indiriliyor...');
        $base = public_path('storage/uploads');
        $ok = 0;
        $fail = 0;

        foreach ($paths as $rel) {
            $remote = "{$scheme}://{$domain}/wp-content/uploads/{$rel}";
            $local = $base . '/' . $rel;

            if (is_file($local) && filesize($local) > 0) {
                $ok++;
                continue;
            }

            @mkdir(dirname($local), 0775, true);
            $data = @file_get_contents($remote);
            if ($data !== false && strlen($data) > 0) {
                file_put_contents($local, $data);
                $ok++;
            } else {
                $fail++;
                $this->line("  <fg=red>x</> indirilemedi: {$rel}");
            }
        }

        $this->newLine();
        $this->info("Bitti: {$ok} indirildi/mevcut, {$fail} basarisiz.");
        if ($fail > 0) {
            $this->line('Basarisizsa: --domain ve --scheme dogru mu? Site hala ayakta mi? FTP\'den de kopyalayabilirsin.');
        }

        return self::SUCCESS;
    }

    /** Sayfa/yazi icerigi ve one cikan gorsellerden uploads yollarini toplar. */
    private function collectUploadPaths(): array
    {
        $haystacks = [];
        foreach (Page::pluck('content') as $c) {
            $haystacks[] = (string) $c;
        }
        foreach (Post::get(['content', 'featured_image']) as $p) {
            $haystacks[] = (string) $p->content;
            $haystacks[] = (string) $p->featured_image;
        }

        $blob = implode("\n", $haystacks);
        $paths = [];

        // Yerellestirilmis yollar: /storage/uploads/2022/08/x.jpg
        if (preg_match_all('#/storage/uploads/([^\s"\'\)\?]+)#', $blob, $m)) {
            foreach ($m[1] as $p) {
                $paths[$p] = true;
            }
        }
        // Hala tam URL olarak kalmis olabilecekler
        if (preg_match_all('#/wp-content/uploads/([^\s"\'\)\?]+)#', $blob, $m)) {
            foreach ($m[1] as $p) {
                $paths[$p] = true;
            }
        }

        return array_keys($paths);
    }
}
