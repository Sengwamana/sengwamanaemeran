<?php

namespace Botble\Base\Commands;

use Botble\Base\Commands\Traits\ValidateCommandInput;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\text;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'cms:google-fonts:update', description: 'Update Google Fonts')]
class GoogleFontsUpdateCommand extends Command
{
    use ValidateCommandInput;

    public function handle(): int
    {
        // Get the API key from https://developers.google.com/fonts/docs/developer_api#APIKey
        $apiKey = config('core.base.general.google_fonts_key');

        if (empty($apiKey)) {
            $apiKey = text(
                label: 'Google Fonts API Key',
                required: true,
                validate: $this->validate('string')
            );
        }

        $fontsPath = core_path('base/resources/data/google-fonts.json');

        $currentFonts = File::exists($fontsPath) ? json_decode(File::get($fontsPath), true) : [];

        $this->components->info('Fetching Google Fonts...');

        $response = Http::withoutVerifying()
            ->get('https://www.googleapis.com/webfonts/v1/webfonts', [
                'key' => $apiKey,
            ]);

        if ($response->failed()) {
            $this->components->error($response->reason() ?: 'Failed to fetch Google Fonts.');

            return self::FAILURE;
        }

        $fonts = $response->json('items');

        $fonts = array_map(fn ($font) => $font['family'], $fonts);

        File::put($fontsPath, json_encode($fonts, JSON_PRETTY_PRINT));

        $newFonts = array_diff($fonts, $currentFonts);

        if (empty($newFonts)) {
            $this->components->info('No new fonts found.');

            return self::SUCCESS;
        }

        $this->components->info(sprintf('Added new %s fonts: %s', count($newFonts), implode(', ', $newFonts)));

        return self::SUCCESS;
    }
}
