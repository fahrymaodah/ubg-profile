<?php

namespace Tests\Unit;

use App\Enums\UnitType;
use App\Services\ArticleCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleCacheServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ArticleCacheService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ArticleCacheService();
    }

    /** @test */
    public function it_returns_empty_collection_when_no_articles(): void
    {
        $articles = $this->service->getFeaturedArticles(UnitType::UNIVERSITAS, null, 4);

        $this->assertCount(0, $articles);
    }

    /** @test */
    public function it_returns_collection_for_latest_articles(): void
    {
        $articles = $this->service->getLatestArticles(UnitType::UNIVERSITAS, null, 6);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $articles);
    }

    /** @test */
    public function it_returns_collection_for_recent_articles(): void
    {
        $articles = $this->service->getRecentArticles(UnitType::UNIVERSITAS, null, 5);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $articles);
    }

    /** @test */
    public function it_returns_null_for_nonexistent_article(): void
    {
        $article = $this->service->getArticle('non-existent-slug', UnitType::UNIVERSITAS, null);

        $this->assertNull($article);
    }
}
