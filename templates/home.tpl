{extends file="layout.tpl"}

{block name="content"}
    {foreach $categoryPreviews as $preview}
        <section>
            <h2><a href="/category/{$preview->category->slug}">{$preview->category->name}</a></h2>
            <p>{$preview->category->description}</p>
            <ul>
                {foreach $preview->posts as $post}
                    <li>
                        <a href="/post/{$post->slug}">{$post->title}</a>
                        <span>{$post->publishedAt->format('d.m.Y')}</span>
                    </li>
                {/foreach}
            </ul>
            <a href="/category/{$preview->category->slug}">Все статьи</a>
        </section>
    {foreachelse}
        <p>Пока нет категорий со статьями.</p>
    {/foreach}
{/block}
