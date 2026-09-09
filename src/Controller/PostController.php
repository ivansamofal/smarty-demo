<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Service\PostService;
use Smarty;

final class PostController extends AbstractController
{
    public function __construct(Smarty $view, private readonly PostService $posts)
    {
        parent::__construct($view);
    }

    public function show(string $slug): Response
    {
        return $this->render('post.tpl', [
            'postPage' => $this->posts->getPostPage($slug),
        ]);
    }
}
