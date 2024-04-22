<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card">
            <h5 class="card-header"><lang>EARNINGS</lang></h5>
            <div class="card-body">
                <canvas id="revenue" width="400" height="150"></canvas>
            </div>
            <div class="e_middle_chart_info card-body border-top">
                <div class="row">
                    <div class="offset-xl-1 col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12 p-3">
                        <h4> <lang>CURRENT_EARNINGS</lang>: <span function="day_total"></span></h4>
                        <p><lang>LAST_SAFE_CLOSED</lang></p>
                    </div>
                    <div class="offset-xl-1 col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 p-3">
                        <h2 class="font-weight-normal mb-3"><span function="week_total"></span></h2>
                        <div class="mb-0 mt-3 legend-item">
                            <span class="fa-xs text-primary mr-1 legend-title "><i class="fa fa-fw fa-square-full"></i></span>
                            <span class="legend-text"><lang>THIS_WEEK</lang></span></div>
                    </div>
                    <div class="offset-xl-1 col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 p-3">
                        <h2 class="font-weight-normal mb-3">
                            <span function="prev_total"></span>
                        </h2>
                        <div class="text-muted mb-0 mt-3 legend-item">
                            <span class="fa-xs text-secondary mr-1 legend-title">
                                <i class="fa fa-fw fa-square-full"></i>
                            </span>
                            <span class="legend-text"><lang>LAST_WEEK</lang></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>