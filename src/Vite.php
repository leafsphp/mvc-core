<?php

namespace Leaf;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Vite
{
    /**
     * The Content Security Policy nonce to apply to all generated tags.
     * @var string|null
     */
    protected static $nonce;

    /**
     * The key to check for integrity hashes within the manifest.
     * @var string|false
     */
    protected static $integrityKey = 'integrity';

    /**
     * The configured entry points.
     * @var array
     */
    protected static $entryPoints = [];

    /**
     * The path to the "hot" file.
     *
     * @var string|null
     */
    protected static $hotFile;

    /**
     * The path to the build directory.
     *
     * @var string
     */
    protected static $buildDirectory = 'build';

    /**
     * The name of the manifest file.
     *
     * @var string
     */
    protected static $manifestFilename = 'manifest.json';

    /**
     * The script tag attributes resolvers.
     *
     * @var array
     */
    protected static $scriptTagAttributesResolvers = [];

    /**
     * The style tag attributes resolvers.
     *
     * @var array
     */
    protected static $styleTagAttributesResolvers = [];

    /**
     * The preload tag attributes resolvers.
     *
     * @var array
     */
    protected static $preloadTagAttributesResolvers = [];

    /**
     * The preloaded assets.
     *
     * @var array
     */
    protected static $preloadedAssets = [];

    /**
     * The cached manifest files.
     *
     * @var array
     */
    protected static $manifests = [];

    /**
     * Get the preloaded assets.
     *
     * @return array
     */
    public static function preloadedAssets()
    {
        return static::$preloadedAssets;
    }

    /**
     * Get the Content Security Policy nonce applied to all generated tags.
     *
     * @return string|null
     */
    public static function cspNonce()
    {
        return static::$nonce;
    }

    /**
     * Generate or set a Content Security Policy nonce to apply to all generated tags.
     *
     * @param  string|null  $nonce
     * @return string
     */
    public static function useCspNonce($nonce = null)
    {
        return static::$nonce = $nonce ?? Str::random(40);
    }

    /**
     * Use the given key to detect integrity hashes in the manifest.
     *
     * @param  string|false  $key
     */
    public static function useIntegrityKey($key)
    {
        static::$integrityKey = $key;
    }

    /**
     * Set the Vite entry points.
     *
     * @param  array  $entryPoints
     */
    public static function withEntryPoints($entryPoints)
    {
        static::$entryPoints = $entryPoints;
    }

    /**
     * Set the filename for the manifest file.
     *
     * @param  string  $filename
     */
    public static function useManifestFilename($filename)
    {
        static::$manifestFilename = $filename;
    }

    /**
     * Get the Vite "hot" file path.
     *
     * @return string
     */
    public static function hotFile()
    {
        return static::$hotFile ?? PublicPath('hot', false);
    }

    /**
     * Set the Vite "hot" file path.
     *
     * @param  string  $path
     */
    public static function useHotFile($path)
    {
        static::$hotFile = $path;
    }

    /**
     * Set the Vite build directory.
     *
     * @param  string  $path
     */
    public static function useBuildDirectory($path)
    {
        static::$buildDirectory = $path;
    }

    /**
     * Use the given callback to resolve attributes for script tags.
     *
     * @param  (callable(string, string, ?array, ?array): array)|array  $attributes
     */
    public static function useScriptTagAttributes($attributes)
    {
        if (!is_callable($attributes)) {
            $attributes = fn () => $attributes;
        }

        static::$scriptTagAttributesResolvers[] = $attributes;
    }

    /**
     * Use the given callback to resolve attributes for style tags.
     *
     * @param  (callable(string, string, ?array, ?array): array)|array  $attributes
     */
    public static function useStyleTagAttributes($attributes)
    {
        if (!is_callable($attributes)) {
            $attributes = fn () => $attributes;
        }

        static::$styleTagAttributesResolvers[] = $attributes;
    }

    /**
     * Use the given callback to resolve attributes for preload tags.
     *
     * @param  (callable(string, string, ?array, ?array): (array|false))|array|false  $attributes
     */
    public static function usePreloadTagAttributes($attributes)
    {
        if (!is_callable($attributes)) {
            $attributes = fn () => $attributes;
        }

        static::$preloadTagAttributesResolvers[] = $attributes;
    }

    /**
     * Generate Vite tags for an entrypoint.
     *
     * @param  string|string[]  $entrypoints
     * @param  string|null  $buildDirectory
     * @return \Illuminate\Support\HtmlString
     *
     * @throws \Exception
     */
    public static function build($entrypoints, $buildDirectory = null)
    {
        $entrypoints = collect($entrypoints);
        $buildDirectory ??= static::$buildDirectory;

        if (static::isRunningHot()) {
            return new HtmlString(
                $entrypoints
                    ->prepend('@vite/client')
                    ->map(fn ($entrypoint) => static::makeTagForChunk($entrypoint, static::hotAsset($entrypoint), null, null))
                    ->join('')
            );
        }

        $manifest = static::manifest($buildDirectory);

        $tags = collect();
        $preloads = collect();

        foreach ($entrypoints as $entrypoint) {
            $chunk = static::chunk($manifest, $entrypoint);

            $preloads->push([
                $chunk['src'],
                static::assetPath("{$buildDirectory}/{$chunk['file']}"),
                $chunk,
                $manifest,
            ]);

            foreach ($chunk['imports'] ?? [] as $import) {
                $preloads->push([
                    $import,
                    static::assetPath("{$buildDirectory}/{$manifest[$import]['file']}"),
                    $manifest[$import],
                    $manifest,
                ]);

                foreach ($manifest[$import]['css'] ?? [] as $css) {
                    $partialManifest = Collection::make($manifest)->where('file', $css);

                    $preloads->push([
                        $partialManifest->keys()->first(),
                        static::assetPath("{$buildDirectory}/{$css}"),
                        $partialManifest->first(),
                        $manifest,
                    ]);

                    $tags->push(static::makeTagForChunk(
                        $partialManifest->keys()->first(),
                        static::assetPath("{$buildDirectory}/{$css}"),
                        $partialManifest->first(),
                        $manifest
                    ));
                }
            }

            $tags->push(static::makeTagForChunk(
                $entrypoint,
                static::assetPath("{$buildDirectory}/{$chunk['file']}"),
                $chunk,
                $manifest
            ));

            foreach ($chunk['css'] ?? [] as $css) {
                $partialManifest = Collection::make($manifest)->where('file', $css);

                $preloads->push([
                    $partialManifest->keys()->first(),
                    static::assetPath("{$buildDirectory}/{$css}"),
                    $partialManifest->first(),
                    $manifest,
                ]);

                $tags->push(static::makeTagForChunk(
                    $partialManifest->keys()->first(),
                    static::assetPath("{$buildDirectory}/{$css}"),
                    $partialManifest->first(),
                    $manifest
                ));
            }
        }

        [$stylesheets, $scripts] = $tags->unique()->partition(fn ($tag) => str_starts_with($tag, '<link'));

        $preloads = $preloads->unique()
            ->sortByDesc(fn ($args) => static::isCssPath($args[1]))
            ->map(fn ($args) => static::makePreloadTagForChunk(...$args));

        return new HtmlString($preloads->join('') . $stylesheets->join('') . $scripts->join(''));
    }

    /**
     * Make tag for the given chunk.
     *
     * @param  string  $src
     * @param  string  $url
     * @param  array|null  $chunk
     * @param  array|null  $manifest
     * @return string
     */
    protected static function makeTagForChunk($src, $url, $chunk, $manifest)
    {
        if (
            static::$nonce === null
            && static::$integrityKey !== false
            && !array_key_exists(static::$integrityKey, $chunk ?? [])
            && static::$scriptTagAttributesResolvers === []
            && static::$styleTagAttributesResolvers === []
        ) {
            return static::makeTag($url);
        }

        if (static::isCssPath($url)) {
            return static::makeStylesheetTagWithAttributes(
                $url,
                static::resolveStylesheetTagAttributes($src, $url, $chunk, $manifest)
            );
        }

        return static::makeScriptTagWithAttributes(
            $url,
            static::resolveScriptTagAttributes($src, $url, $chunk, $manifest)
        );
    }

    /**
     * Make a preload tag for the given chunk.
     *
     * @param  string  $src
     * @param  string  $url
     * @param  array  $chunk
     * @param  array  $manifest
     * @return string
     */
    protected static function makePreloadTagForChunk($src, $url, $chunk, $manifest)
    {
        $attributes = static::resolvePreloadTagAttributes($src, $url, $chunk, $manifest);

        if ($attributes === false) {
            return '';
        }

        static::$preloadedAssets[$url] = static::parseAttributes(
            Collection::make($attributes)->forget('href')->all()
        );

        return '<link ' . implode(' ', static::parseAttributes($attributes)) . ' />';
    }

    /**
     * Resolve the attributes for the chunks generated script tag.
     *
     * @param  string  $src
     * @param  string  $url
     * @param  array|null  $chunk
     * @param  array|null  $manifest
     * @return array
     */
    protected static function resolveScriptTagAttributes($src, $url, $chunk, $manifest)
    {
        $attributes = static::$integrityKey !== false
            ? ['integrity' => $chunk[static::$integrityKey] ?? false]
            : [];

        foreach (static::$scriptTagAttributesResolvers as $resolver) {
            $attributes = array_merge($attributes, $resolver($src, $url, $chunk, $manifest));
        }

        return $attributes;
    }

    /**
     * Resolve the attributes for the chunks generated stylesheet tag.
     *
     * @param  string  $src
     * @param  string  $url
     * @param  array|null  $chunk
     * @param  array|null  $manifest
     * @return array
     */
    protected static function resolveStylesheetTagAttributes($src, $url, $chunk, $manifest)
    {
        $attributes = static::$integrityKey !== false
            ? ['integrity' => $chunk[static::$integrityKey] ?? false]
            : [];

        foreach (static::$styleTagAttributesResolvers as $resolver) {
            $attributes = array_merge($attributes, $resolver($src, $url, $chunk, $manifest));
        }

        return $attributes;
    }

    /**
     * Resolve the attributes for the chunks generated preload tag.
     *
     * @param  string  $src
     * @param  string  $url
     * @param  array  $chunk
     * @param  array  $manifest
     * @return array|false
     */
    protected static function resolvePreloadTagAttributes($src, $url, $chunk, $manifest)
    {
        $attributes = static::isCssPath($url) ? [
            'rel' => 'preload',
            'as' => 'style',
            'href' => $url,
            'nonce' => static::$nonce ?? false,
            'crossorigin' => static::resolveStylesheetTagAttributes($src, $url, $chunk, $manifest)['crossorigin'] ?? false,
        ] : [
            'rel' => 'modulepreload',
            'href' => $url,
            'nonce' => static::$nonce ?? false,
            'crossorigin' => static::resolveScriptTagAttributes($src, $url, $chunk, $manifest)['crossorigin'] ?? false,
        ];

        $attributes = static::$integrityKey !== false
            ? array_merge($attributes, ['integrity' => $chunk[static::$integrityKey] ?? false])
            : $attributes;

        foreach (static::$preloadTagAttributesResolvers as $resolver) {
            if (false === ($resolvedAttributes = $resolver($src, $url, $chunk, $manifest))) {
                return false;
            }

            $attributes = array_merge($attributes, $resolvedAttributes);
        }

        return $attributes;
    }

    /**
     * Generate an appropriate tag for the given URL in HMR mode.
     *
     * @deprecated Will be removed in a future Laravel version.
     *
     * @param  string  $url
     * @return string
     */
    protected static function makeTag($url)
    {
        if (static::isCssPath($url)) {
            return static::makeStylesheetTagWithAttributes($url, []);
        }

        return static::makeScriptTagWithAttributes($url, []);
    }

    /**
     * Generate a script tag with attributes for the given URL.
     *
     * @param  string  $url
     * @param  array  $attributes
     * @return string
     */
    protected static function makeScriptTagWithAttributes($url, $attributes)
    {
        $attributes = static::parseAttributes(array_merge([
            'type' => 'module',
            'src' => $url,
            'nonce' => static::$nonce ?? false,
        ], $attributes));

        return '<script ' . implode(' ', $attributes) . '></script>';
    }

    /**
     * Generate a link tag with attributes for the given URL.
     *
     * @param  string  $url
     * @param  array  $attributes
     * @return string
     */
    protected static function makeStylesheetTagWithAttributes($url, $attributes)
    {
        $attributes = static::parseAttributes(array_merge([
            'rel' => 'stylesheet',
            'href' => $url,
            'nonce' => static::$nonce ?? false,
        ], $attributes));

        return '<link ' . implode(' ', $attributes) . ' />';
    }

    /**
     * Determine whether the given path is a CSS file.
     *
     * @param  string  $path
     * @return bool
     */
    protected static function isCssPath($path)
    {
        return preg_match('/\.(css|less|sass|scss|styl|stylus|pcss|postcss)$/', $path) === 1;
    }

    /**
     * Parse the attributes into key="value" strings.
     *
     * @param  array  $attributes
     * @return array
     */
    protected static function parseAttributes($attributes)
    {
        return Collection::make($attributes)
            ->reject(fn ($value, $key) => in_array($value, [false, null], true))
            ->flatMap(fn ($value, $key) => $value === true ? [$key] : [$key => $value])
            ->map(fn ($value, $key) => is_int($key) ? $value : $key . '="' . $value . '"')
            ->values()
            ->all();
    }

    /**
     * Generate React refresh runtime script.
     *
     * @return \Illuminate\Support\HtmlString|void
     */
    public static function reactRefresh()
    {
        if (!static::isRunningHot()) {
            return;
        }

        $attributes = static::parseAttributes([
            'nonce' => static::cspNonce(),
        ]);

        return new HtmlString(
            sprintf(
                <<<'HTML'
                <script type="module" %s>
                    import RefreshRuntime from '%s'
                    RefreshRuntime.injectIntoGlobalHook(window)
                    window.$RefreshReg$ = () => {}
                    window.$RefreshSig$ = () => (type) => type
                    window.__vite_plugin_react_preamble_installed__ = true
                </script>
                HTML,
                implode(' ', $attributes),
                static::hotAsset('@react-refresh')
            )
        );
    }

    /**
     * Get the path to a given asset when running in HMR mode.
     *
     * @return string
     */
    protected static function hotAsset($asset)
    {
        return rtrim(file_get_contents(static::hotFile())) . '/' . $asset;
    }

    /**
     * Get the URL for an asset.
     *
     * @param  string  $asset
     * @param  string|null  $buildDirectory
     * @return string
     */
    public static function asset($asset, $buildDirectory = null)
    {
        $buildDirectory ??= static::$buildDirectory;

        if (static::isRunningHot()) {
            return static::hotAsset($asset);
        }

        $chunk = static::chunk(static::manifest($buildDirectory), $asset);

        return static::assetPath($buildDirectory . '/' . $chunk['file']);
    }

    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    protected static function assetPath($path, $secure = null)
    {
        return PublicPath($path);
    }

    /**
     * Get the the manifest file for the given build directory.
     *
     * @param  string  $buildDirectory
     * @return array
     *
     * @throws \Exception
     */
    protected static function manifest($buildDirectory)
    {
        $path = static::manifestPath($buildDirectory);

        if (!isset(static::$manifests[$path])) {
            if (!is_file($path)) {
                throw new Exception("Vite manifest not found at: {$path}");
            }

            static::$manifests[$path] = json_decode(file_get_contents($path), true);
        }

        return static::$manifests[$path];
    }

    /**
     * Get the path to the manifest file for the given build directory.
     *
     * @param  string  $buildDirectory
     * @return string
     */
    protected static function manifestPath($buildDirectory)
    {
        return PublicPath($buildDirectory . '/' . static::$manifestFilename, false);
    }

    /**
     * Get a unique hash representing the current manifest, or null if there is no manifest.
     *
     * @param  string|null  $buildDirectory
     * @return string|null
     */
    public static function manifestHash($buildDirectory = null)
    {
        $buildDirectory ??= static::$buildDirectory;

        if (static::isRunningHot()) {
            return null;
        }

        if (!is_file($path = static::manifestPath($buildDirectory))) {
            return null;
        }

        return md5_file($path) ?: null;
    }

    /**
     * Get the chunk for the given entry point / asset.
     *
     * @param  array  $manifest
     * @param  string  $file
     * @return array
     *
     * @throws \Exception
     */
    protected static function chunk($manifest, $file)
    {
        if (!isset($manifest[$file])) {
            throw new Exception("Unable to locate file in Vite manifest: {$file}.");
        }

        return $manifest[$file];
    }

    /**
     * Determine if the HMR server is running.
     *
     * @return bool
     */
    public static function isRunningHot()
    {
        return is_file(static::hotFile());
    }
}
