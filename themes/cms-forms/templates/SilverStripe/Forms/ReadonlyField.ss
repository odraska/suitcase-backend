<span id="$ID" class="form-control-static<% if $extraClass %> $extraClass<% end_if %>" <% include SilverStripe/Forms/AriaAttributes %>>
	$FormattedValue
</span>
<% if $IncludeHiddenField %>
	<input $getAttributesHTML("id", "type") id="hidden-{$ID}" type="hidden" />
<% end_if %>
