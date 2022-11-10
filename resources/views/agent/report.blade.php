@extends('layouts.app')

@section('content')
 <style type="text/css">
	.nav-tabs>li>a
{
color:white;
}
</style>
<div class="super_container">

	
	<div class="home">
		
	
	</div>

	<!-- Search -->

	<div class="search">
		

		<!-- Search Contents -->
		
		<div class="">
			<div class="">
				<div class="">

					<!-- Search Tabs -->

					@include('partials.tab')
					@include('agent.report.report-type')
				  
				   
				   
					<br>
			
					
					
				</div>
			</div>
		</div>		
	</div>
	@include('layouts.footer')

</div>

 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
