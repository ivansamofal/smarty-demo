{extends file="layout.tpl"}

{block name="content"}
    <article>
        <h1>{$postPage->post->title}</h1>
        <img src="{$postPage->post->image}" alt="{$postPage->post->title}">
        <p>{$postPage->post->description}</p>
        <div>{$postPage->post->content}</div>
        <p>{$postPage->post->publishedAt->format('d.m.Y')} &middot; {$postPage->post->views} просмотров</p>
        <p>
            Категории:
            {foreach $postPage->categories as $category}
                <a href="/category/{$category->slug}">{$category->name}</a>{if !$category@last}, {/if}
            {/foreach}
        </p>
    </article>

    {if $postPage->similarPosts|@count > 0}
        <section>
            <h2>Похожие статьи</h2>
            <ul>
                {foreach $postPage->similarPosts as $similar}
                    <li><a href="/post/{$similar->slug}">{$similar->title}</a></li>
                {/foreach}
            </ul>
        </section>
    {/if}
{/block}
