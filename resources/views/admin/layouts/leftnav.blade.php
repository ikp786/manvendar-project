<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <!--- Divider -->
        <div id="sidebar-menu">
            <ul>

                <li class="text-muted menu-title">Navigation</li>

                <li class="has_sub">
                    <a href="{{ url('/dashboard') }}" class="waves-effect active"><i class="ti-home"></i> <span> Dashboard </span> </a>
                </li>

                <li class="has_sub">
                    <a href="#" class="waves-effect"><i class="ti-layout-column3"></i> <span> Master </span> </a>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('company') }}">Company Master</a></li>
                        <li><a href="{{ url('provider') }}">Provider</a></li>
                        <li><a href="{{ url('api-manage') }}">API Manager</a></li>
                        <li><a href="{{ url('news-update') }}">News Update</a></li>
                        <li><a href="{{ url('scheme-manage') }}">Scheme Manager</a></li>
                    </ul>
                </li>


                <li class="has_sub">
                    <a href="#" class="waves-effect"><i class="ti-pencil-alt"></i><span> Payments </span></a>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('payment-request-view')}}">Payment Request</a></li>
                        <li><a href="{{ url('fund-transfer') }}">Payment </a></li>
                    </ul>
                </li>

                <li class="has_sub">
                    <a href="#" class="waves-effect"><i class="ti-menu-alt"></i><span>Reports </span></a>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('all-recharge-report') }}">Recharge Report</a></li>
                        <li><a href="{{ url('payment-report') }}">Payment Report</a></li>
                        <li><a href="{{ url('long-code-report') }}">LongCode Report</a></li>
                        <li><a href="{{ url('sms-report') }}">SMS Report</a></li>
                        <li><a href="{{ url('money-transfer-report-view') }}">Money Transfer Report</a></li>
                        <li><a href="{{ url('operator-wise-report') }}">Operator wise Report</a></li>
                    </ul>
                </li>

                <li class="has_sub">
                    <a href="#" class="waves-effect"><i class="ti-bar-chart"></i><span class="label label-pink pull-right">10</span><span> Members </span></a>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('member') }}">Members</a></li>
                    </ul>
                </li>


                <li class="text-muted menu-title">Extra</li>

                <li class="has_sub">
                    <a href="#" class="waves-effect"><i class="ti-user"></i><span> Application </span></a>
                    <ul class="list-unstyled">
                        <li><a href="android-recharge-application"> Android Application </a></li>
                        <li><a href="java-recharge-application"> Java Application </a></li>
                        <li><a href="ipohne-recharge-application"> iPhone Application </a></li>
                        <li><a href="windows-recharge-application"> Windows Application </a></li>
                        <li><a href="sms-recharge-format"> SMS base Recharge </a></li>
                    </ul>
                </li>

            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>