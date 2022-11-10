<style>

</style>
<ul class="nav nav-tabs">
<li class="{{(Request::is('*bank-details') ? 'active' : '')}}">
        <a href="{{url('aeps/bank-details')}}"><span>Bank Details</span></a>
    </li>

    {{--<li class="{{Request::is('aeps/bank-details-list') ? 'active' : ''}}">
         <a href="{{url('aeps/bank-details-list')}}">Bank Details</a>
    </li>--}}

    <li class="{{Request::is('*bank-details-fund') ? 'active' : ''}}">
        <a href="{{url('aeps/bank-details-fund')}}">Settlement</a>
	</li>
		<li class="{{(Request::is('*aeps') ? 'active' : '')}}">
			<a href="{{url('aeps')}}">Aeps Txn</a>
		</li>
</ul>    