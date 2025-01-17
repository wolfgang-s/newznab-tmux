<div class="card card-body">
	<h1>{$title}</h1>
	<a class="btn btn-success" href="{{url("/admin/release-list")}}"><i class="fa fa-arrow-left"></i> Go
		back</a>
	<form action="release-edit?action=submit" method="POST">
		{{csrf_field()}}
		<table class="input data table table-striped responsive-utilities jambo-table">
			<tr>
				<td><label for="name">Original Name:</label></td>
				<td>
					<input type="hidden" name="id" value="{$release.id}"/>
					<input type="hidden" name="guid" value="{$release.guid}"/>
					<input id="name" class="long" name="name" value="{$release.name|escape:'htmlall'}"/>
				</td>
			</tr>
			<tr>
				<td><label for="searchname">Search Name:</label></td>
				<td>
					<input id="searchname" class="long" name="searchname"
						   value="{$release.searchname|escape:'htmlall'}"/>
				</td>
			</tr>
			<tr>
				<td><label for="fromname">From Name:</label></td>
				<td>
					<input id="fromname" class="long" name="fromname" value="{$release.fromname|escape:'htmlall'}"/>
				</td>
			</tr>
			<tr>
				<td><label for="category">Category:</label></td>
				<td>
					{html_options id="category" name=category options=$catlist selected=$release.categories_id}
				</td>
			</tr>
			<tr>
				<td><label for="tagnames">Category Tags:</label></td>
				<td>
					<input id="tagnames" class="long" name="tagnames" value="{implode(',', $release->tagNames())}"/>
				</td>
			</tr>
			<tr>
				<td><label for="totalpart">Parts:</label></td>
				<td>
					<input id="totalpart" class="short" name="totalpart" value="{$release.totalpart}"/>
				</td>
			</tr>
			<tr>
				<td><label for="grabs">Grabs:</label></td>
				<td>
					<input id="grabs" class="short" name="grabs" value="{$release.grabs}"/>
				</td>
			</tr>
			<tr>
				<td><label for="videos_id">Video Id:</label></td>
				<td>
					<input id="videos_id" class="short" name="videos_id" value="{$release.videos_id}"/>
				</td>
			</tr>
			<tr>
				<td><label for="tv_episodes_id">TV Episode Id:</label></td>
				<td>
					<input id="tv_episodes_id" class="short" name="tv_episodes_id" value="{$release.tv_episodes_id}"/>
				</td>
			</tr>
			<tr>
				<td><label for="imdbid">IMDB Id:</label></td>
				<td>
					<input id="imdbid" class="short" name="imdbid" value="{$release.imdbid}"/>
				</td>
			</tr>
			<tr>
				<td><label for="anidbid">AniDB Id:</label></td>
				<td>
					<input id="anidbid" class="short" name="anidbid" value="{$release.anidbid}"/>
				</td>
			</tr>
			<tr>
				<td>Group:</td>
				<td>
					{$release.group_name}
				</td>
			</tr>
			<tr>
				<td><label for="size">Size:</label></td>
				<td>
					<input id="size" class="long" name="size" value="{$release.size}"/>
				</td>
			</tr>
			<tr>
				<td><label for="postdate">Posted Date:</label></td>
				<td>
					<input id="postdate" class="long" name="postdate" value="{$release.postdate}"/>
				</td>
			</tr>
			<tr>
				<td><label for="adddate">Added Date:</label></td>
				<td>
					<input id="adddate" class="long" name="adddate" value="{$release.adddate}"/>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input class="btn btn-success" type="submit" value="Save"/>
				</td>
			</tr>
		</table>
	</form>
</div>
