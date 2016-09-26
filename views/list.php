<section page>
	<?php if(Auth::user()) {?>
	<p>
		<a d-ib switch="all" href="#all">All</a>
		<a d-ib switch="done" href="#done">Done</a>
		<a d-ib switch="active" href="#active">Active</a>
	</p>
	<p>
		<input root-task type="text" placeholder="Enter new name for the task">
	</p>
	<div list>
		<p>Loading tasks...</p>
	</div>
	<?php } else { view("403"); }?>
</section>