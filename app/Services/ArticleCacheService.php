<?php

namespace App\Services;

use App\Enums\UnitType;
use App\Models\Article;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ArticleCacheService
{
    /**
     * Cache TTL in seconds (30 minutes)
     */
    protected int $cacheTtl = 1800;

    /**
     * Get featured articles with caching
     */
    public function getFeaturedArticles(UnitType $unitType, ?int $unitId, int $limit = 4): Collection
    {
        $cacheKey = $this->getCacheKey('featured', $unitType, $unitId, $limit);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($unitType, $unitId, $limit) {
            return Article::forUnit($unitType, $unitId)
                ->published()
                ->featured()
                ->with(['category', 'author'])
                ->latest('published_at')
                ->take($limit)
                ->get();
        });
    }

    /**
     * Get latest articles with caching
     */
    public function getLatestArticles(UnitType $unitType, ?int $unitId, int $limit = 6): Collection
    {
        $cacheKey = $this->getCacheKey('latest', $unitType, $unitId, $limit);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($unitType, $unitId, $limit) {
            return Article::forUnit($unitType, $unitId)
                ->published()
                ->with(['category', 'author'])
                ->latest('published_at')
                ->take($limit)
                ->get();
        });
    }

    /**
     * Get recent articles for sidebar with caching
     */
    public function getRecentArticles(UnitType $unitType, ?int $unitId, int $limit = 5): Collection
    {
        $cacheKey = $this->getCacheKey('recent', $unitType, $unitId, $limit);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($unitType, $unitId, $limit) {
            return Article::forUnit($unitType, $unitId)
                ->published()
                ->select(['id', 'title', 'slug', 'featured_image', 'published_at'])
                ->latest('published_at')
                ->take($limit)
                ->get();
        });
    }

    /**
     * Get single article with related data
     */
    public function getArticle(string $slug, UnitType $unitType, ?int $unitId): ?Article
    {
        $cacheKey = "article.detail.{$unitType->value}.{$unitId}.{$slug}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($slug, $unitType, $unitId) {
            return Article::forUnit($unitType, $unitId)
                ->where('slug', $slug)
                ->published()
                ->with(['category', 'author'])
                ->first();
        });
    }

    /**
     * Get related articles
     */
    public function getRelatedArticles(Article $article, UnitType $unitType, ?int $unitId, int $limit = 4): Collection
    {
        $cacheKey = "article.related.{$unitType->value}.{$unitId}.{$article->id}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($article, $unitType, $unitId, $limit) {
            return Article::forUnit($unitType, $unitId)
                ->published()
                ->where('id', '!=', $article->id)
                ->when($article->category_id, function ($query) use ($article) {
                    return $query->where('category_id', $article->category_id);
                })
                ->with(['category'])
                ->latest('published_at')
                ->take($limit)
                ->get();
        });
    }

    /**
     * Generate cache key
     */
    protected function getCacheKey(string $type, UnitType $unitType, ?int $unitId, int $limit = 0): string
    {
        $parts = ['articles', $type, $unitType->value, $unitId ?? 'null'];
        
        if ($limit > 0) {
            $parts[] = $limit;
        }

        return implode('.', $parts);
    }

    /**
     * Clear article cache for a specific unit
     */
    public function clearCache(UnitType $unitType, ?int $unitId = null): void
    {
        $patterns = [
            "articles.featured.{$unitType->value}",
            "articles.latest.{$unitType->value}",
            "articles.recent.{$unitType->value}",
            "article.detail.{$unitType->value}",
            "article.related.{$unitType->value}",
        ];

        // Note: This is a simplified approach. In production with Redis,
        // you'd use Cache::tags() or pattern-based key deletion
        if ($unitId) {
            foreach ($patterns as $pattern) {
                Cache::forget("{$pattern}.{$unitId}");
            }
        }
    }

    /**
     * Clear all article caches
     */
    public function clearAllCache(): void
    {
        // Clear common cache keys
        Cache::flush();
    }
}
