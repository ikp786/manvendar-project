<ul class="nav nav-tabs" >
	     <li class="<?php echo e((Request::is('admin/offline/record') ? 'active' : '')); ?>">
			<a href="<?php echo e(url('admin/offline/record')); ?>"><span>OffLine</span></a></li>
	      <li class="<?php echo e((Request::is('admin/offline/updated-record') ? 'active' : '')); ?>"><a href="<?php echo e(route('offline-updated')); ?>"><span>Update Offline</span></a></li>
     
</ul>