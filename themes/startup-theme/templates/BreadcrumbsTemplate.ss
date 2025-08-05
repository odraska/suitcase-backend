<% if $Pages %>
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <ol class="breadcrumbs__list">
            <% loop $Pages %>
                <% if not $IsLast %>
                    <li class="breadcrumbs__list-item">
                        <% if not $Up.Unlinked %><a href="$Link"><% end_if %>$MenuTitle.XML<% if not Up.Unlinked %></a><% end_if %>
                        <span class="breadcrumbs__list-delimiter" aria-hidden="true">/</span>
                    </li>
                <% end_if %>
            <% end_loop %>
        </ol>
    </nav>
<% end_if %>
