<div class="navbar-custom" style="background:black; color:white;">
    <div class="container">
        <div id="navigation">
            <ul class="navigation-menu">
                <li class="has-submenu active">
                    <a href="{{ url('dashboard') }}" style="color:white;">Dashboard</a>
                </li>
                @if(Auth::user()->role_id <= 3)
                <li class="has-submenu">
                    <a href="#" style="color:white;">Master</a>
                    <ul class="submenu megamenu">
                        <li>
                            <ul>
                                <li>
                                    <span>DMR & Recharge</span>
                                </li>
                                <li><a href="{{ url('company') }}">Company Master</a></li>
                                @if(Auth::user()->role_id == 1)
                                <li><a href="{{ url('provider') }}">Product Master</a></li>
                                <li><a href="{{ url('api-manage') }}">Api Manager</a></li>
                                <li><a href="{{ url('news-update') }}">News Update</a></li>
                                <li><a href="{{ url('scheme-manage') }}">Scheme Manager</a></li>
                                <li><a href="{{ url('up-front-commission') }}">DMR Scheme Manager</a></li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </li>
                @endif
                <li class="has-submenu">
                    <a href="#" style="color:white;">Member</a>
                    <ul class="submenu">
                        @if(Auth::user()->role_id <= 3)
                        @if(Auth::user()->role_id == 1)
                        <li><a href="{{ url('admin/member') }}">Admin </a></li>
                        <li><a href="{{ url('admin/master-distributor') }}">Master Distributor</a></li>
                        @endif

                        <li><a href="{{ url('admin/distributor') }}">Distributor</a></li>

                        @endif
                        @if(Auth::user()->role_id <= 4 || Auth::user()->role_id == 1)
                        <li><a href="{{ url('admin/retailer') }}">Retailer</a></li>
                        @endif
                        @if(Auth::user()->role_id == 1)
                        <li><a href="{{ url('member/export') }}">Export Members</a></li>
                        @endif
                    </ul>
                </li>
                
				@if(Auth::user()->id != 121)
                <li class="has-submenu">
                    <a href="#">Money Report</a>
                    <ul class="submenu">
                        @if(Auth::user()->role_id <= 4)
                        <li class="has-submenu">
                            <a href="#">Money</a>
                            <ul class="submenu">
                                @if(Auth::user()->role_id == 1)

                                <li><a href="{{ url('all-recharge-report') }}">All Transaction Report</a></li>
                                <li><a href="{{ url('operator-wise-report') }}">Product Report</a></li>
                                <li><a href="{{ url('all-recharge-report-ca') }}">All Transaction Report All (CA)</a></li>

                                @endif
                                <li><a href="{{ url('payment-report') }}">Payment Report</a></li>
                                <li><a href="{{ url('admin-all-recharge-report') }}">Money Transaction Report</a>
                                
                            </ul>
                        </li>
                        @endif
                        <li><a href="{{ url('all-recharge-report') }}">All Transaction Report</a></li>
                        <li><a href="{{ url('agent-report') }}">Agent Report</a></li>
                        <li><a href="{{ url('account-statement') }}">Account statement</a></li>
                        

                        @if(Auth::user()->role_id == 1)


                        @endif
                    </ul>
                </li>
@endif
                <li class="has-submenu">
                    <a href="#"style="color:white;">Recharge Report</a>
                    <ul class="submenu">
                        @if(Auth::user()->role_id <= 4)
                        <li><a href="{{ url('all-rechrge-transaction') }}">All Recharge Report</a></li>
                        <li><a href="{{ url('all-recharge-recharge-report') }}">Recharge Transaction Report</a></li>
                        <li><a href="{{ url('account-statement-recharge') }}">Account statement Recharge</a></li>
                        @endif
                    </ul>
                </li>
                
                @if(Auth::user()->role_id < 5)
                <li class="has-submenu">
                    <a href="#"style="color:white;">Payments</a>
                    <ul class="submenu megamenu">
                        <li>
                            <ul>
							@if(Auth::user()->id != 121)
                                <li><a href="{{ url('fund-transfer') }}">Payment Transfer</a></li>
                                @if(Auth::user()->role_id <= 3)
                                <li><a href="{{ url('fund-return') }}">Payment Return</a></li>
                                @endif
								 @endif
                                <li><a href="{{ url('payment-request-view') }}">Agent Request View</a></li>
                                <li><a href="{{ url('fund-request-report') }}">Agent Request Report</a></li>
                                @if(Auth::user()->role_id > 1)
                                <li><a href="{{ url('fund-request') }}">Fund Request</a></li>
                                @endif
                                <li><a href="{{ url('load-cash-admin') }}">Fund Request Report</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @endif
                @if(Auth::user()->role_id == 1)

                @endif

                <li class="has-submenu">
                    <a href="#" style="color:white;">Analytic</a>
                    <ul class="submenu megamenu">
                        <li>
                            <ul>
                                @if(Auth::user()->role_id == 1)
                                <li><a href="{{ url('reports') }}">Money Reports</a></li>
								<li><a href="{{ url('recharge-utility') }}">Recharge Reports</a></li>
								<li><a href="{{ url('white-label-reports') }}">White Label Reports</a></li>
                                @endif
                                @if(Auth::user()->role_id == 3)
                                <li><a href="{{ url('reportmd') }}">Money Reports</a></li>
								 <li><a href="{{ url('md-recharge-utility') }}">Recharge Reports</a></li>
                                @endif
                                @if(Auth::user()->role_id == 4)
                                <li><a href="{{ url('reportdist') }}">Money Reports</a></li>
								<li><a href="{{ url('dist-recharge-utility') }}">Recharge Reports</a></li>
                                @endif
                                @if(Auth::user()->role_id == 5)
                                <li><a href="{{ url('reportagent') }}">Reports</a></li>
                                @endif
                                <li><a href="{{ url('agent-wise') }}">Agent wise </a></li>
                                @if(Auth::user()->role_id == 1)
                                <li><a href="{{ url('complain-request-view') }}">Complain Request View </a></li>
                                <li><a href="{{ url('report-distributor') }}">Distributor month wise </a></li>
                                <li><a href="{{ url('agent-sharp') }}">Sharp Scheme(LMT) </a></li>

                                @endif
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- End navigation menu        -->
        </div>
    </div>
</div>