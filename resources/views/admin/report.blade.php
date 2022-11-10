@extends('admin.layouts.template')

@section('content')
<style>
h2,h3
{
font-size: 18px;
color: #98a6ad !important;
}
</style>
<link href="new_report/service.css" rel="stylesheet" type="text/css" media="all"/>
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all"/>

                <div class="clearfix">
                    <h2 class="small-heading">REPORTS</h2>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1 col-sm-12">
                            <div class="row">

                                <div class="col-md-4 col-sm-12">
                                    <div class="faq-desc-item">
                                        <div class="flip-container text-center" ontouchstart="this.classList.toggle(&#39;hover&#39;);">
                                            <div class="flipper">
                                                <div class="front">
                                                    <h3>TODAY SALE</h3>
                                                    <h4>{{ number_format($data['today_sales'],2) }}
													
													
													
													</h4><hr class="hr">
													
													<h3>THIS MONTH TILL DATE</h3>
                                                    <h4>{{ number_format($data['this_m_t_date'],2) }}
													
													
													</h4><hr class="hr">
													<h3>LAST MONTH SALE</h3>
                                                    <h4>{{ number_format($data['last_month'],2) }}
													</h4>
													
                                                </div>
                                                <div class="back">

                                                <h3>SARAL LM</h3>
												<h4>{{ number_format(@$data['lm_saral_sale'],2) }}</h4><hr>
												<h3>SMART LM</h3>
												<h4>{{ number_format(@$data['lm_smart_sale'],2) }}</h4><hr>
												<h3>SHARP LM</h3>
												<h4>{{ number_format(@$data['lm_sharp_sale'],2) }}</h4><hr>
												<h3>SHINE LM</h3>
												<h4>{{ number_format(@$data['lm_shine_sale'],2) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <div class="faq-desc-item">
                                        <div class="flip-container text-center" ontouchstart="this.classList.toggle(&#39;hover&#39;);">
                                            <div class="flipper">
                                                <div class="front">
												@if(Auth::user()->id == 1018)
                                                    <h3>Coming soon...</h3>
													
													<h4>{{ number_format($data['today_saral_sale'],2) }}
													
													<span>{{number_format((float)$data['t_saral_per'],1)}}%</span>
													</h4><hr class="hr">
													
													@else
													<h3>SARAL (Today)</h3>
													<h4>{{ number_format($data['today_saral_sale'],2) }}
													
													
													</h4><hr class="hr">
													@endif
													
													<h3>SMART (Today)</h3>
													<h4>{{ number_format($data['today_smart_sale'],2) }}
													
													
													
													</h4><hr class="hr">
													
													<h3>SHARP (Today)</h3>
												    <h4>{{ number_format($data['today_sharp_sale'],2) }}
													
													</h4><hr class="hr">

													<h3>SHINE (Today)</h3>
												    <h4>{{ number_format(@$data['today_shine_sale'],2) }}								
													</h4><hr class="hr">

													
													
                                                </div>

                                               
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <div class="faq-desc-item">
                                        <div class="flip-container text-center" ontouchstart="this.classList.toggle(&#39;hover&#39;);">
                                            <div class="flipper">
                                                <div class="front">
                                                    
                                                <h3>SARAL CMTD</h3>
												<h4>{{ number_format($data['cmtd_saral_sale'],2) }}
												
												</h4><hr class="hr">
												<h3>SMART CMTD</h3>
												<h4>{{ number_format($data['cmtd_smart_sale'],2) }}
												
												</h4><hr class="hr">
												<h3>SHARP CMTD</h3>
												<h4>{{ number_format($data['cmtd_sharp_sale'],2) }}
													
												</h4><hr class="hr">
												<h3>SHINE CMTD</h3>
												<h4>{{ number_format($data['cmtd_shine_sale'],2) }}	
                                                </div>
                                                 
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>

								
								 <div class="col-md-4 col-sm-12">
                                    <div class="faq-desc-item">
                                        <div class="flip-container text-center" ontouchstart="this.classList.toggle(&#39;hover&#39;);">
                                            <div class="flipper" style="margin-top:75px">
                                                <div class="front">
												
                                                    <h3>Active Agents</h3>
													
													<h4>
													<span style=""></span>
													<span>{{ @$data['active_agents'] }}</span>
													</h4><hr class="hr">
													
													
													
													<h3>Total Agents</h3>
													<h4>
													<span style=""></span>
													<span>{{ @$data['total_agents'] }}</span>
													
													</h4><hr class="hr">
													
													<h3>Active %</h3>
												    <h4>
													<span style=""></span>
													<span>{{ @$data['agent_percentage'] }} %</span>
													</h4><hr class="hr">
													
                                                </div>
                                                <div class="back">
                                                  
													<h3>Active Agents</h3>
													<h4>Active Agents</h4><hr>
													<h3>Active Agents</h3>
													<h4>
													Active Agents
													</h4><hr class="hr">
													
													<h3>Active Agents</h3>
													<h4>Active Agents</h4><hr class="hr">
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								
								  
								
								    <div class="col-md-4 col-sm-12">
                                    <div class="faq-desc-item">
                                        <div class="flip-container text-center" ontouchstart="this.classList.toggle(&#39;hover&#39;);">
                                            <div class="flipper" style="margin-top:75px">
                                                <div class="front">
                                                    <h3>Transfered Amount </h3>
												    <h4>{{ number_format(@$data['evalue_create'],2) }}</h4> <hr class="hr">
													
													<h3>Requested Amount </h3>
												    <h4>{{ number_format(@$data['a_evalue_create'],2) }}</h4> <hr class="hr">
													
													@if(Auth::user()->role_id == 1)
													 <h3>Profit Amount </h3>
												    <h4>{{ number_format(@$data['e_profit'],2) }}</h4> <hr class="hr">
													
													@endif
                                                </div>
                                                <div class="back">

                                                <h3>SARAL LMT</h3>
												<h4>{{ number_format(@$data['lm_saral_sale'],2) }}</h4><hr>
												<h3>SMART LMT</h3>
												<h4>{{ number_format(@$data['lm_smart_sale'],2) }}</h4><hr>
												<h3>SHARP LMT</h3>
												<h4>{{ number_format(@$data['lm_sharp_sale'],2) }}</h4><hr>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

								    <!--<div class="col-md-4 col-sm-12">
                                    <div class="faq-desc-item">
                                        <div class="flip-container text-center" ontouchstart="this.classList.toggle(&#39;hover&#39;);">
                                            <div class="flipper">
                                                <div class="front">
                                                    <i class="fa fa-paint-brush"></i>
                                                    <h5>Web Designing</h5>
                                                </div>
                                                <div class="back">
                                                    <!-- <i class="fa fa-paint-brush fa-fw"></i> -->
                                               <!--     <h5></h5>
                                                    <p>Mozilla Web Developer, MooTools &amp; jQuery Consultant, MooTools Core Developer, Javascript Fanatic, CSS Tinkerer, PHP Hacker, and web lover.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>-->
								
								
                            </div>
                        </div>
                    </div>
                </div>
    
@endsection