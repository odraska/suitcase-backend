<div class="page__content <% if $Menu($PageLevel).count > 1 && $PageLevel > 1 %>page__content--with-sidebar<% end_if %>">
    <h1 class="page__title">$Title</h1>
    $Content
    $ElementalArea
    $Form
</div>
<% if $Menu($PageLevel).count > 1 && $PageLevel > 1 %>
    <% include Sidebar %>
<% end_if %>
