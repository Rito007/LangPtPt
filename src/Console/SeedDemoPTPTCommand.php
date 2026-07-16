<?php

namespace Rito007\LangPtPt\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SeedDemoPTPTCommand extends Command
{
    protected $signature = 'bagisto:seed-demo-ptpt';

    protected $description = 'Seed pt_PT demo data translations into the database';

    private string $locale = 'pt_PT';

    private array $localeSpecificAttributes = [
        'name'              => 2,
        'short_description' => 9,
        'description'       => 10,
        'meta_title'        => 16,
        'meta_keywords'     => 17,
        'meta_description'  => 18,
    ];

    private int $errors = 0;

    public function handle(): int
    {
        $file = base_path('packages/Rito007/LangPtPt/src/Resources/demo-pt_PT.json');

        if (! file_exists($file)) {
            $this->error("File not found: $file");
            return Command::FAILURE;
        }

        if (! $this->localeExists()) {
            $this->error("Locale '{$this->locale}' not found in the locales table.");
            $this->warn('Add it via Admin > Settings > Locales or run the LocalesTableSeeder.');
            return Command::FAILURE;
        }

        $demoProducts = DB::table('products')->count();
        if ($demoProducts === 0) {
            $this->warn('No products found in the database.');
            $this->warn('Run php artisan bagisto:install --demo-samples first, then re-run this command.');
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($file), true);
        if (! $data) {
            $this->error('Failed to parse JSON.');
            return Command::FAILURE;
        }

        $this->info("Seeding pt_PT demo data... ($demoProducts products found)");
        $bar = $this->output->createProgressBar(array_sum(array_map('count', $data)));
        $bar->start();

        $stats = [];

        foreach ($data as $section => $items) {
            $method = 'seed' . str_replace('_', '', ucwords($section, '_'));

            if (method_exists($this, $method)) {
                $stats[$section] = $this->$method($items, $bar);
            } else {
                $bar->advance(count($items));
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Done!');

        $this->table(
            ['Section', 'Rows'],
            collect($stats)->filter(fn($v) => $v > 0)->map(fn($v, $k) => [$k, $v])->values()->toArray()
        );
        $this->line("Total: <info>" . collect($stats)->sum() . "</info> rows");

        if ($this->errors > 0) {
            $this->newLine();
            $this->warn("$this->errors FK errors caught and skipped (demo products may be incomplete).");
        }

        $this->newLine();
        $this->info('Rebuilding product flat index...');
        Artisan::call('indexer:index', ['--type' => ['flat'], '--mode' => ['full']]);
        $this->line(Artisan::output());

        return Command::SUCCESS;
    }

    private function localeExists(): bool
    {
        return DB::table('locales')->where('code', $this->locale)->exists();
    }

    private function seedProducts(array $items, $bar): int
    {
        $count = 0;

        foreach ($items as $item) {
            $productId = $item['id'];

            foreach ($this->localeSpecificAttributes as $code => $attributeId) {
                $value = $item['translations'][$code] ?? null;

                if ($value === null || $value === '') {
                    continue;
                }

                try {
                    DB::table('product_attribute_values')->updateOrInsert(
                        [
                            'product_id'   => $productId,
                            'attribute_id' => $attributeId,
                            'locale'       => $this->locale,
                            'channel'      => null,
                        ],
                        ['text_value' => $value, 'unique_id' => "null|{$this->locale}|{$productId}|{$attributeId}"]
                    );
                    $count++;
                } catch (\Exception $e) {
                    $this->errors++;
                }
            }

            $bar->advance();
        }

        return $count;
    }

    private function seedCustomAttributes(array $items, $bar): int
    {
        $count = 0;

        foreach ($items as $item) {
            $attributeId = $item['id'];
            $name = $item['translations']['admin_name'] ?? null;

            if ($name === null || $name === '') {
                $bar->advance();
                continue;
            }

            try {
                DB::table('attribute_translations')->updateOrInsert(
                    ['attribute_id' => $attributeId, 'locale' => $this->locale],
                    ['name' => $name]
                );
                $count++;
            } catch (\Exception $e) {
                $this->errors++;
            }

            $bar->advance();
        }

        return $count;
    }

    private function seedCustomAttributeTranslations(array $items, $bar): int
    {
        $count = 0;

        foreach ($items as $item) {
            $attributeId = $item['id'];
            $name = $item['translations']['name'] ?? $item['translations']['admin_name'] ?? null;

            if ($name === null || $name === '') {
                $bar->advance();
                continue;
            }

            try {
                DB::table('attribute_translations')->updateOrInsert(
                    ['attribute_id' => $attributeId, 'locale' => $this->locale],
                    ['name' => $name]
                );
                $count++;
            } catch (\Exception $e) {
                $this->errors++;
            }

            $bar->advance();
        }

        return $count;
    }

    private function seedCustomAttributeOptions(array $items, $bar): int
    {
        $count = 0;

        foreach ($items as $item) {
            $optionId = $item['id'];
            $label = $item['translations']['admin_name'] ?? null;

            if ($label === null || $label === '') {
                $bar->advance();
                continue;
            }

            try {
                DB::table('attribute_option_translations')->updateOrInsert(
                    ['attribute_option_id' => $optionId, 'locale' => $this->locale],
                    ['label' => $label]
                );
                $count++;
            } catch (\Exception $e) {
                $this->errors++;
            }

            $bar->advance();
        }

        return $count;
    }

    private function seedCustomAttributeOptionTranslations(array $items, $bar): int
    {
        $count = 0;

        foreach ($items as $item) {
            $optionId = $item['id'];
            $label = $item['translations']['label'] ?? $item['translations']['admin_name'] ?? null;

            if ($label === null || $label === '') {
                $bar->advance();
                continue;
            }

            try {
                DB::table('attribute_option_translations')->updateOrInsert(
                    ['attribute_option_id' => $optionId, 'locale' => $this->locale],
                    ['label' => $label]
                );
                $count++;
            } catch (\Exception $e) {
                $this->errors++;
            }

            $bar->advance();
        }

        return $count;
    }

    private function seedBundleOptions(array $items, $bar): int
    {
        $count = 0;

        foreach ($items as $item) {
            $optionId = $item['id'];
            $label = $item['translations']['label'] ?? null;

            if ($label === null || $label === '') {
                $bar->advance();
                continue;
            }

            try {
                DB::table('product_bundle_option_translations')->updateOrInsert(
                    ['product_bundle_option_id' => $optionId, 'locale' => $this->locale],
                    ['label' => $label]
                );
                $count++;
            } catch (\Exception $e) {
                $this->errors++;
            }

            $bar->advance();
        }

        return $count;
    }

    private function seedDownloadableLinks(array $items, $bar): int
    {
        $count = 0;

        foreach ($items as $item) {
            $linkId = $item['id'];
            $title = $item['translations']['title'] ?? null;

            if ($title === null || $title === '') {
                $bar->advance();
                continue;
            }

            try {
                DB::table('product_downloadable_link_translations')->updateOrInsert(
                    ['product_downloadable_link_id' => $linkId, 'locale' => $this->locale],
                    ['title' => $title]
                );
                $count++;
            } catch (\Exception $e) {
                $this->errors++;
            }

            $bar->advance();
        }

        return $count;
    }

    private function seedDownloadableSamples(array $items, $bar): int
    {
        $count = 0;

        foreach ($items as $item) {
            $sampleId = $item['id'];
            $title = $item['translations']['title'] ?? null;

            if ($title === null || $title === '') {
                $bar->advance();
                continue;
            }

            try {
                DB::table('product_downloadable_sample_translations')->updateOrInsert(
                    ['product_downloadable_sample_id' => $sampleId, 'locale' => $this->locale],
                    ['title' => $title]
                );
                $count++;
            } catch (\Exception $e) {
                $this->errors++;
            }

            $bar->advance();
        }

        return $count;
    }

    private function seedBookingProductEventTickets(array $items, $bar): int
    {
        $count = 0;

        foreach ($items as $item) {
            $ticketId = $item['id'];
            $name = $item['translations']['name'] ?? null;
            $description = $item['translations']['description'] ?? null;

            if ($name === null && $description === null) {
                $bar->advance();
                continue;
            }

            try {
                $data = [];
                if ($name !== null) $data['name'] = $name;
                if ($description !== null) $data['description'] = $description;

                DB::table('booking_product_event_ticket_translations')->updateOrInsert(
                    ['booking_product_event_ticket_id' => $ticketId, 'locale' => $this->locale],
                    $data
                );
                $count++;
            } catch (\Exception $e) {
                $this->errors++;
            }

            $bar->advance();
        }

        return $count;
    }
}
