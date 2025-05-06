<?php

namespace Botble\Translation\Services;

use Botble\Translation\Exceptions\LargeTextException;
use Botble\Translation\Exceptions\RateLimitException;
use Botble\Translation\Exceptions\TranslationDecodingException;
use Botble\Translation\Exceptions\TranslationRequestException;
use Botble\Translation\Tokens\GoogleTokenGenerator;
use Botble\Translation\Tokens\TokenProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Throwable;

class GoogleTranslate
{
    protected Client $client;

    protected ?string $source;

    protected ?string $target;

    protected ?string $lastDetectedSource;

    protected string $url = 'https://translate.google.com/translate_a/single';

    protected array $options = [];

    protected array $urlParams = [
        'client' => 'gtx',
        'hl' => 'en',
        'dt' => [
            't',
            // Translate
            'bd',
            // Full translate with synonym ($bodyArray[1])
            'at',
            // Other translate ($bodyArray[5] - in google translate page this shows when click on translated word)
            'ex',
            // Example part ($bodyArray[13])
            'ld',
            // I don't know ($bodyArray[8])
            'md',
            // Definition part with example ($bodyArray[12])
            'qca',
            // I don't know ($bodyArray[8])
            'rw',
            // Read also part ($bodyArray[14])
            'rm',
            // I don't know
            'ss',
            // Full synonym ($bodyArray[11])
        ],
        'sl' => null, // Source language
        'tl' => null, // Target language
        'q' => null, // String to translate
        'ie' => 'UTF-8', // Input encoding
        'oe' => 'UTF-8', // Output encoding
        'multires' => 1,
        'otf' => 0,
        'pc' => 1,
        'trs' => 1,
        'ssel' => 0,
        'tsel' => 0,
        'kc' => 1,
        'tk' => null,
    ];

    /**
     * @var array Regex key-value patterns to replace on response data
     */
    protected array $resultRegexes = [
        '/,+/' => ',',
        '/\[,/' => '[',
    ];

    protected TokenProviderInterface $tokenProvider;

    public function __construct(
        string $target = 'en',
        string $source = null,
        array $options = [],
        TokenProviderInterface $tokenProvider = null
    ) {
        $this->client = new Client(['verify' => false]);
        $this->setTokenProvider($tokenProvider ?? new GoogleTokenGenerator())
            ->setOptions($options) // Options are already set in client constructor tho.
            ->setSource($source)
            ->setTarget($target);
    }

    public function setTarget(string $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function setSource(string $source = null): self
    {
        $this->source = $source ?? 'auto';

        return $this;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function setClient(string $client): self
    {
        $this->urlParams['client'] = $client;

        return $this;
    }

    public function setOptions(array $options = []): self
    {
        $this->options = $options;

        return $this;
    }

    public function setTokenProvider(TokenProviderInterface $tokenProvider): self
    {
        $this->tokenProvider = $tokenProvider;

        return $this;
    }

    public function getLastDetectedSource(): ?string
    {
        return $this->lastDetectedSource;
    }

    public static function trans(
        string $string,
        string $target = 'en',
        string $source = null,
        array $options = [],
        TokenProviderInterface $tokenProvider = null
    ): ?string {
        return (new self())
            ->setTokenProvider($tokenProvider ?? new GoogleTokenGenerator())
            ->setOptions($options) // Options are already set in client constructor tho.
            ->setSource($source)
            ->setTarget($target)
            ->translate($string);
    }

    public function translate(string $string): ?string
    {
        // If the source and target languages are the same, just return the string without any request to Google.
        if ($this->source === $this->target) {
            return $string;
        }

        $responseArray = $this->getResponse($string);

        // Check if translation exists
        if (empty($responseArray[0])) {
            return null;
        }

        // Detect languages
        $detectedLanguages = [];

        // One way of detecting language
        foreach ($responseArray as $item) {
            if (is_string($item)) {
                $detectedLanguages[] = $item;
            }
        }

        // Another way of detecting language
        if (isset($responseArray[count($responseArray) - 2][0][0])) {
            $detectedLanguages[] = $responseArray[count($responseArray) - 2][0][0];
        }

        // Set initial detected language to null
        $this->lastDetectedSource = null;

        // Iterate and set last detected language
        foreach ($detectedLanguages as $lang) {
            if ($this->isValidLocale($lang)) {
                $this->lastDetectedSource = $lang;

                break;
            }
        }

        // The response sometime can be a translated string.
        if (is_string($responseArray)) {
            return $responseArray;
        }

        if (is_array($responseArray[0])) {
            return (string) array_reduce($responseArray[0], static function ($carry, $item) {
                $carry .= $item[0];

                return $carry;
            });
        }

        return (string) $responseArray[0];
    }

    public function getResponse(string $string): array|string
    {
        $queryArray = array_merge($this->urlParams, [
            'sl' => $this->source,
            'tl' => $this->target,
            'tk' => $this->tokenProvider->generateToken($this->source, $this->target, $string),
            'q' => $string,
        ]);

        // Remove array indexes from URL so that "&dt[2]=" turns into "&dt=" and so on.
        $queryUrl = preg_replace('/%5B\d+%5D=/', '=', http_build_query($queryArray));

        try {
            $response = $this->client->get(
                $this->url,
                [
                    'query' => $queryUrl,
                ] + $this->options
            );
        } catch (GuzzleException $e) {
            match ($e->getCode()) {
                429, 503 => throw new RateLimitException($e->getMessage(), $e->getCode()),
                413 => throw new LargeTextException($e->getMessage(), $e->getCode()),
                default => throw new TranslationRequestException($e->getMessage(), $e->getCode()),
            };
        } catch (Throwable $e) {
            throw new TranslationRequestException($e->getMessage(), $e->getCode());
        }

        $body = $response->getBody(); // Get response body

        // Modify body to avoid json errors
        $bodyJson = preg_replace(array_keys($this->resultRegexes), array_values($this->resultRegexes), $body);

        // Decode JSON data
        try {
            $bodyArray = json_decode($bodyJson, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new TranslationDecodingException('Data cannot be decoded or it is deeper than the recursion limit');
        }

        return $bodyArray;
    }

    protected function isValidLocale(string $lang): bool
    {
        return (bool) preg_match('/^([a-z]{2,3})(-[A-Za-z]{2,4})?$/', $lang);
    }
}
