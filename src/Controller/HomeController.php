<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Service\CategoryService;
use Smarty;

final class HomeController extends AbstractController
{
    public function __construct(Smarty $view, private readonly CategoryService $categories)
    {
        parent::__construct($view);
    }

    public function index(): Response
    {
        return $this->render('home.tpl', [
            'categoryPreviews' => $this->categories->getHomePagePreviews(),
        ]);
    }
}
