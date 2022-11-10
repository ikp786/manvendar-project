<style>

</style>
<ul class="nav nav-tabs">
<li class="<?php echo e((Request::is('*bank-details') ? 'active' : '')); ?>">
        <a href="<?php echo e(url('aeps/bank-details')); ?>"><span>Bank Details</span></a>
    </li>

    

    <li class="<?php echo e(Request::is('*bank-details-fund') ? 'active' : ''); ?>">
        <a href="<?php echo e(url('aeps/bank-details-fund')); ?>">Settlement</a>
	</li>
		<li class="<?php echo e((Request::is('*aeps') ? 'active' : '')); ?>">
			<a href="<?php echo e(url('aeps')); ?>">Aeps Txn</a>
		</li>
</ul>    