<?php
if (!isset($_GET['AJAX']))
{
	?>
			</div>
			<script type="text/javascript">
$(function() {
	if (location.hash)
	{
		$.get(location.hash.substring(1), {AJAX:'1'}, function(data) {$("#content-area").html(data);})
	}
	$("#navigation a").click (function () {
		$.get ($(this).attr("href"), {AJAX:'1'}, function(data) {$("#content-area").html(data);});
		location.hash=$(this).attr("href");
		return false;
	});
});
			</script>
		</body>
	</html>
	<?php
}