<div class="container">
	<h3 class="page-title">Data for <span>"<?php echo $page; ?>"</span></h3>
	<div class="span13">
		<table id="activity-table" class="table table-striped">
			<thead>
				<tr>
					<th width="8%">#</th>
					<th width="62%">User's Name</th>
					<th width="15%">Total Comments</th>
					<th width="15%">Total Likes</th>
				</tr>
			</thead>
			<tbody>		
			</tbody>
		</table>
	</div>
	<div id="top-btns">
		<button id="get-data" class="btn">Get Data</a>
		<button id="change-page" class="btn">Change Page</button>
	</div>
</div>

<div id="activityModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
				<h4 class="modal-title" id="acmodal-title"></h4>
			</div>
			<div class="modal-body">
				<ul id="acmodal-content"></ul>
				<hr />
			</div>
			<div class="modal-footer"></div>
		</div>
	</div>
</div>

<div id="changepageModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
				<h4 class="modal-title" id="acmodal-title">Change Page</h4>
			</div>
			<div class="modal-body">
				<p>Please enter valid Facebook Page ID.</p>
				<input id="newpage" type="text" />
			</div>
			<div class="modal-footer"><button id="to-newpage" class="btn">Submit</button></div>
		</div>
	</div>
</div>

<a href="/data/exportCsv?page=<?php echo $page; ?>" class="btn">Export to CSV</a>