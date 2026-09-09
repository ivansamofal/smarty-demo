<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Service\CategoryService;
use App\Support\PostSort;
use Smarty;

final class CategoryController extends AbstractController
{
    private const PER_PAGE = 10;

    public function __construct(Smarty $view, private readonly CategoryService $categories)
    {
        parent::__construct($view);
    }

    public function show(string $slug, Request $request): Response
    {
        $sort = PostSort::tryFrom((string) $request->query('sort', PostSort::DATE->value)) ?? PostSort::DATE;
        $page = max(1, (int) $request->query('page', '1'));

        return $this->render('category.tpl', [
            'categoryPage' => $this->categories->getCategoryPage($slug, $sort, $page, self::PER_PAGE),
        ]);
    }
}
