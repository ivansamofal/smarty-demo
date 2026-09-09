{extends file="layout.tpl"}

{block name="content"}
    <h1>{$categoryPage->category->name}</h1>
    <p>{$categoryPage->category->description}</p>

    <nav>
        <a href="/category/{$categoryPage->category->slug}?sort=date">По дате</a>
        <a href="/category/{$categoryPage->category->slug}?sort=views">По просмотрам</a>
    </nav>

    <ul>
        {foreach $categoryPage->posts->items as $post}
            <li>
                <a href="/post/{$post->slug}">{$post->title}</a>
                <span>{$post->publishedAt->format('d.m.Y')}</span>
                <span>{$post->views} просмотров</span>
            </li>
        {foreachelse}
            <li>В этой категории пока нет статей.</li>
        {/foreach}
    </ul>

    {if $categoryPage->posts->totalPages() > 1}
        <nav>
            {if $categoryPage->posts->hasPrevious()}
                <a href="/category/{$categoryPage->category->slug}?sort={$categoryPage->sort->value}&page={$categoryPage->posts->previousPage()}">Назад</a>
            {/if}
            <span>Страница {$categoryPage->posts->page} из {$categoryPage->posts->totalPages()}</span>
            {if $categoryPage->posts->hasNext()}
                <a href="/category/{$categoryPage->category->slug}?sort={$categoryPage->sort->value}&page={$categoryPage->posts->nextPage()}">Вперёд</a>
            {/if}
        </nav>
    {/if}
{/block}
