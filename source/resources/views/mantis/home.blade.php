@extends('mantis.layouts.app')
@section('main')
    
			<!-- [ Main Content ] start -->
			<div class="row">
				<!-- [ sample-page ] start -->

				<div class="col-md-12 col-xl-8">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<h5 class="mb-0">Unique Visitor</h5>
						<ul class="nav nav-pills justify-content-end mb-0" id="chart-tab-tab" role="tablist">
							<li class="nav-item" role="presentation">
								<button class="nav-link" id="chart-tab-home-tab" data-bs-toggle="pill" data-bs-target="#chart-tab-home"
									type="button" role="tab" aria-controls="chart-tab-home" aria-selected="true">Month</button>
							</li>
							<li class="nav-item" role="presentation">
								<button class="nav-link active" id="chart-tab-profile-tab" data-bs-toggle="pill"
									data-bs-target="#chart-tab-profile" type="button" role="tab" aria-controls="chart-tab-profile"
									aria-selected="false">Week</button>
							</li>
						</ul>
					</div>
					<div class="card">
						<div class="card-body">
							<div class="tab-content" id="chart-tab-tabContent">
								<div class="tab-pane" id="chart-tab-home" role="tabpanel" aria-labelledby="chart-tab-home-tab"
									tabindex="0">
									<div id="visitor-chart-1"></div>
								</div>
								<div class="tab-pane show active" id="chart-tab-profile" role="tabpanel"
									aria-labelledby="chart-tab-profile-tab" tabindex="0">
									<div id="visitor-chart"></div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
	
@endsection