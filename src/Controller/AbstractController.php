<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use Smarty;

abstract class AbstractController
{
    public function __construct(protected readonly Smarty $view)
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function render(string $template, array $data = [], int $statusCode = 200): Response
    {
        foreach ($data as $key => $value) {
            $this->view->assign($key, $value);
        }

        return new Response($this->view->fetch($template), $statusCode);
    }
}
