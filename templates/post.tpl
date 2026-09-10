{extends file="layout.tpl"}

{block name="content"}
    <article class="post">
        <h1 class="post__title">{$postPage->post->title}</h1>
        <div class="post__meta">
            <span>{$postPage->post->publishedAt->format('d.m.Y')}</span>
            <span>{$postPage->post->views} просмотров</span>
        </div>
        <img class="post__image" src="{$postPage->post->image}" alt="{$postPage->post->title}">
        <p class="post__description">{$postPage->post->description}</p>
        <div class="post__content">{$postPage->post->content}</div>
        <div class="post__categories">
            {foreach $postPage->categories as $category}
                <a class="tag" href="/category/{$category->slug}">{$category->name}</a>
            {/foreach}
        </div>
    </article>

    {if $postPage->similarPosts|@count > 0}
        <section class="similar-posts">
            <h2>Похожие статьи</h2>
            <div class="post-grid">
                {foreach $postPage->similarPosts as $similar}
                    <article class="post-card">
                        <a class="post-card__image" href="/post/{$similar->slug}">
                            <img src="{$similar->image}" alt="{$similar->title}" loading="lazy">
                        </a>
                        <div class="post-card__body">
                            <h3 class="post-card__title"><a href="/post/{$similar->slug}">{$similar->title}</a></h3>
                        </div>
                    </article>
                {/foreach}
            </div>
        </section>
    {/if}
{/block}
