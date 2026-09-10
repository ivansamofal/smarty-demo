{extends file="layout.tpl"}

{block name="content"}
    {foreach $categoryPreviews as $preview}
        <section class="category-section">
            <div class="category-section__header">
                <h2><a href="/category/{$preview->category->slug}">{$preview->category->name}</a></h2>
                <a class="btn btn--outline" href="/category/{$preview->category->slug}">Все статьи</a>
            </div>
            <p class="category-section__description">{$preview->category->description}</p>
            <div class="post-grid">
                {foreach $preview->posts as $post}
                    <article class="post-card">
                        <a class="post-card__image" href="/post/{$post->slug}">
                            <img src="{$post->image}" alt="{$post->title}" loading="lazy">
                        </a>
                        <div class="post-card__body">
                            <h3 class="post-card__title"><a href="/post/{$post->slug}">{$post->title}</a></h3>
                            <p class="post-card__excerpt">{$post->description}</p>
                            <div class="post-card__meta">
                                <span>{$post->publishedAt->format('d.m.Y')}</span>
                                <span>{$post->views} просмотров</span>
                            </div>
                        </div>
                    </article>
                {/foreach}
            </div>
        </section>
    {foreachelse}
        <p class="empty-state">Пока нет категорий со статьями.</p>
    {/foreach}
{/block}
