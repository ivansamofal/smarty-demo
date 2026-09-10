{extends file="layout.tpl"}

{block name="content"}
    <div class="page-header">
        <h1>{$categoryPage->category->name}</h1>
        <p class="page-header__description">{$categoryPage->category->description}</p>
    </div>

    <div class="sort-tabs">
        <a class="sort-tabs__link{if $categoryPage->sort->value == 'date'} sort-tabs__link--active{/if}" href="/category/{$categoryPage->category->slug}?sort=date">По дате</a>
        <a class="sort-tabs__link{if $categoryPage->sort->value == 'views'} sort-tabs__link--active{/if}" href="/category/{$categoryPage->category->slug}?sort=views">По просмотрам</a>
    </div>

    <div class="post-list">
        {foreach $categoryPage->posts->items as $post}
            <article class="post-row">
                <a class="post-row__image" href="/post/{$post->slug}">
                    <img src="{$post->image}" alt="{$post->title}" loading="lazy">
                </a>
                <div class="post-row__body">
                    <h3 class="post-row__title"><a href="/post/{$post->slug}">{$post->title}</a></h3>
                    <p class="post-row__excerpt">{$post->description}</p>
                    <div class="post-row__meta">
                        <span>{$post->publishedAt->format('d.m.Y')}</span>
                        <span>{$post->views} просмотров</span>
                    </div>
                </div>
            </article>
        {foreachelse}
            <p class="empty-state">В этой категории пока нет статей.</p>
        {/foreach}
    </div>

    {if $categoryPage->posts->totalPages() > 1}
        <nav class="pagination">
            {if $categoryPage->posts->hasPrevious()}
                <a class="pagination__link" href="/category/{$categoryPage->category->slug}?sort={$categoryPage->sort->value}&page={$categoryPage->posts->previousPage()}">&larr; Назад</a>
            {/if}
            <span class="pagination__status">Страница {$categoryPage->posts->page} из {$categoryPage->posts->totalPages()}</span>
            {if $categoryPage->posts->hasNext()}
                <a class="pagination__link" href="/category/{$categoryPage->category->slug}?sort={$categoryPage->sort->value}&page={$categoryPage->posts->nextPage()}">Вперёд &rarr;</a>
            {/if}
        </nav>
    {/if}
{/block}
