<?php

namespace App\Controllers\Blog;

class PostsController extends Controller
{
    public function index()
    {
        response()->render('pages.blog.index', [
            'posts' => $this->posts(),
        ]);
    }

    public function show($slug)
    {
        $file = $this->blogDir() . '/' . basename($slug) . '.md';

        if (!file_exists($file)) {
            response()->exit('Post not found', 404);
        }

        $post = $this->parse($file);
        $post['html'] = (new \Parsedown())->setSafeMode(true)->text($post['body']);

        response()->render('pages.blog.post', ['post' => $post]);
    }

    /**
     * All posts, newest first
     */
    protected function posts(): array
    {
        $posts = array_map(fn ($file) => $this->parse($file), glob($this->blogDir() . '/*.md'));

        usort($posts, fn ($a, $b) => strcmp($b['date'], $a['date']));

        return $posts;
    }

    /**
     * Split a post file into frontmatter + body
     */
    protected function parse(string $file): array
    {
        $raw = file_get_contents($file);
        $meta = ['title' => basename($file, '.md'), 'date' => date('Y-m-d', filemtime($file)), 'description' => ''];
        $body = $raw;

        if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)$/s', $raw, $matches)) {
            foreach (explode("\n", $matches[1]) as $line) {
                if (strpos($line, ':') !== false) {
                    [$key, $value] = explode(':', $line, 2);
                    $meta[trim($key)] = trim(trim($value), '"\'');
                }
            }

            $body = $matches[2];
        }

        return array_merge($meta, [
            'slug' => basename($file, '.md'),
            'body' => $body,
        ]);
    }

    protected function blogDir(): string
    {
        // posts live next to the other app directories: app/blog
        return dirname(AppPaths('routes')) . '/blog';
    }
}
