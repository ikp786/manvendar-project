<ul class="nav nav-tabs" >
				<?php if(Auth::user()->role_id == 1): ?>
					<li  class="<?php echo e((Request::is('md-payment') ? 'active' : '')); ?>"><a href="<?php echo e(url('md-payment')); ?>" ><span>ADM To MD</span></a></li>
					<li  class="<?php echo e((Request::is('dist-payment') ? 'active' : '')); ?>"><a href="<?php echo e(url('dist-payment')); ?>" ><span>ADM To Dist</span></a></li>
					<li  class="<?php echo e((Request::is('retailer-payment') ? 'active' : '')); ?>"><a href="<?php echo e(url('retailer-payment')); ?>"><span>ADM To Retailer</span></a></li>
				<?php elseif(Auth::user()->role_id == 3): ?>
					<li  class="<?php echo e((Request::is('dist-payment') ? 'active' : '')); ?>"><a href="<?php echo e(url('dist-payment')); ?>" ><span>ADM To Dist</span></a></li>
					<li class="<?php echo e((Request::is('retailer-payment') ? 'active' : '')); ?>"><a href="<?php echo e(url('retailer-payment')); ?>"><span>Retailer Fund Transfer</span></a></li>
				<?php elseif(Auth::user()->role_id == 4): ?>
					<li class="<?php echo e((Request::is('fund-return') ? 'active' : '')); ?>"><a href="<?php echo e(url('retailer-payment')); ?>"><span>Retailer Fund Transfer</span></a></li>
				<?php endif; ?>
				<!--<li class="<?php echo e((Request::is('payment-statement') ? 'active' : '')); ?>"><a href="<?php echo e(url('payment-statement')); ?>"><span>Payment Statement</span></a></li>-->
</ul>