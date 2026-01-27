<?php

namespace App\Http\Controllers;

use App\Enums\ArticleStatus;
use App\Enums\UnitType;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles.
     * Shows content from ALL units with labels (cascade BOTH)
     * 
     * Prodi: shows prodi's + fakultas's + universitas's articles
     * Fakultas: shows fakultas's + universitas's + prodi's (under fakultas) articles
     * Universitas: shows universitas's + all fakultas + all prodi articles
     */
    public function index(Request $request): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');
        $perPage = $request->attributes->get('settings', [])['articles_per_page'] ?? 12;

        // Use cascade BOTH for articles (berita) - shows all directions with labels
        $articles = Article::forUnitCascadeBoth($unitType, $unitId)
            ->published()
            ->latest('published_at')
            ->orderByDesc('id')
            ->with(['category', 'author'])
            ->paginate($perPage);

        // Categories cascade both directions but deduplicate by name
        $allCategories = ArticleCategory::forUnitCascadeBoth($unitType, $unitId)
            ->active()
            ->ordered()
            ->get();
        
        // Get all category names for cascade
        $categoryNames = $allCategories->pluck('name')->unique();
        
        // Get all category IDs grouped by name
        $categoryIdsByName = $allCategories->groupBy('name')->map(fn($cats) => $cats->pluck('id')->toArray());
        
        // Count articles by category name (sum all categories with same name)
        $articleCountsByName = [];
        foreach ($categoryIdsByName as $name => $ids) {
            $articleCountsByName[$name] = Article::forUnitCascadeBoth($unitType, $unitId)
                ->published()
                ->whereIn('category_id', $ids)
                ->count();
        }
        
        // Deduplicate categories by name and add computed count
        $categories = $allCategories->unique('name')->values()->map(function ($category) use ($articleCountsByName) {
            $category->articles_count = $articleCountsByName[$category->name] ?? 0;
            return $category;
        });

        $featuredArticle = Article::forUnitCascadeBoth($unitType, $unitId)
            ->published()
            ->featured()
            ->latest('published_at')
            ->orderByDesc('id')
            ->first();

        // Get recent articles for sidebar
        $sidebarCount = $request->attributes->get('settings', [])['sidebar_articles_count'] ?? 5;
        $recentArticles = Article::forUnitCascadeBoth($unitType, $unitId)
            ->published()
            ->latest('published_at')
            ->orderByDesc('id')
            ->take((int) $sidebarCount)
            ->get();

        // Get fakultas for label display
        $fakultas = null;
        if ($unitType === UnitType::PRODI && $unitId) {
            $prodi = Prodi::find($unitId);
            $fakultas = $prodi?->fakultas;
        } elseif ($unitType === UnitType::FAKULTAS && $unitId) {
            $fakultas = \App\Models\Fakultas::find($unitId);
        }

        return view('articles.index', compact('articles', 'categories', 'featuredArticle', 'recentArticles', 'unitType', 'unitId', 'fakultas'));
    }

    /**
     * Display articles by category.
     * Filters by category NAME to support cascade BOTH (multiple categories with same name from different units)
     */
    public function category(Request $request, ArticleCategory $category): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');
        $perPage = $request->attributes->get('settings', [])['articles_per_page'] ?? 12;

        // Check if category is accessible (cascade BOTH - always accessible)
        $isAccessible = ArticleCategory::forUnitCascadeBoth($unitType, $unitId)
            ->where('id', $category->id)
            ->exists();

        if (!$isAccessible) {
            abort(404);
        }

        // Get all category IDs with the same NAME from cascade BOTH units
        $categoryIds = ArticleCategory::forUnitCascadeBoth($unitType, $unitId)
            ->where('name', $category->name)
            ->pluck('id')
            ->toArray();

        // Filter articles by all matching category IDs (not just one)
        $articles = Article::forUnitCascadeBoth($unitType, $unitId)
            ->whereIn('category_id', $categoryIds)
            ->published()
            ->latest('published_at')
            ->orderByDesc('id')
            ->with(['author', 'category'])
            ->paginate($perPage);

        // Categories for sidebar - same logic as index
        $allCategories = ArticleCategory::forUnitCascadeBoth($unitType, $unitId)
            ->active()
            ->ordered()
            ->get();
        
        $categoryIdsByName = $allCategories->groupBy('name')->map(fn($cats) => $cats->pluck('id')->toArray());
        
        $articleCountsByName = [];
        foreach ($categoryIdsByName as $name => $ids) {
            $articleCountsByName[$name] = Article::forUnitCascadeBoth($unitType, $unitId)
                ->published()
                ->whereIn('category_id', $ids)
                ->count();
        }
        
        $categories = $allCategories->unique('name')->values()->map(function ($cat) use ($articleCountsByName) {
            $cat->articles_count = $articleCountsByName[$cat->name] ?? 0;
            return $cat;
        });

        $recentArticles = Article::forUnitCascadeBoth($unitType, $unitId)
            ->published()
            ->latest('published_at')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // Get fakultas for label display
        $fakultas = null;
        if ($unitType === UnitType::PRODI && $unitId) {
            $prodi = Prodi::find($unitId);
            $fakultas = $prodi?->fakultas;
        } elseif ($unitType === UnitType::FAKULTAS && $unitId) {
            $fakultas = \App\Models\Fakultas::find($unitId);
        }

        return view('articles.category', compact('articles', 'category', 'categories', 'recentArticles', 'unitType', 'unitId', 'fakultas'));
    }

    /**
     * Display the specified article.
     */
    public function show(Request $request, Article $article): View
    {
        $unitType = $request->attributes->get('unit_type', UnitType::UNIVERSITAS);
        $unitId = $request->attributes->get('unit_id');

        // Check if article is accessible (cascade BOTH - use query to check)
        $isAccessible = $article->isPublished() && Article::forUnitCascadeBoth($unitType, $unitId)
            ->where('id', $article->id)
            ->exists();

        if (!$isAccessible) {
            abort(404);
        }

        // Increment view count
        $article->incrementViewCount();

        // Get related articles (cascade BOTH)
        $relatedArticles = Article::forUnitCascadeBoth($unitType, $unitId)
            ->published()
            ->where('id', '!=', $article->id)
            ->when($article->category_id, function ($query) use ($article, $unitType, $unitId) {
                // Get all category IDs with same name
                $categoryIds = ArticleCategory::forUnitCascadeBoth($unitType, $unitId)
                    ->where('name', $article->category?->name)
                    ->pluck('id');
                return $query->whereIn('category_id', $categoryIds);
            })
            ->latest('published_at')
            ->orderByDesc('id')
            ->take(4)
            ->get();

        // Get categories for sidebar (cascade BOTH, deduplicated by name)
        $allCategories = ArticleCategory::forUnitCascadeBoth($unitType, $unitId)
            ->active()
            ->ordered()
            ->get();
        
        $categoryIdsByName = $allCategories->groupBy('name')->map(fn($cats) => $cats->pluck('id')->toArray());
        
        $articleCountsByName = [];
        foreach ($categoryIdsByName as $name => $ids) {
            $articleCountsByName[$name] = Article::forUnitCascadeBoth($unitType, $unitId)
                ->published()
                ->whereIn('category_id', $ids)
                ->count();
        }
        
        $categories = $allCategories->unique('name')->values()->map(function ($cat) use ($articleCountsByName) {
            $cat->articles_count = $articleCountsByName[$cat->name] ?? 0;
            return $cat;
        });

        // Get recent articles for sidebar (cascade BOTH)
        $recentArticles = Article::forUnitCascadeBoth($unitType, $unitId)
            ->published()
            ->latest('published_at')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // Get fakultas for label display
        $fakultas = null;
        if ($unitType === UnitType::PRODI && $unitId) {
            $prodi = Prodi::find($unitId);
            $fakultas = $prodi?->fakultas;
        } elseif ($unitType === UnitType::FAKULTAS && $unitId) {
            $fakultas = \App\Models\Fakultas::find($unitId);
        }

        return view('articles.show', compact('article', 'relatedArticles', 'categories', 'recentArticles', 'unitType', 'unitId', 'fakultas'));
    }
}
