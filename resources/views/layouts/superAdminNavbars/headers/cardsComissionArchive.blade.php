<div class="header bg-gradient-info pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="header pb-4">
                <div class="container-fluid">
                    <div class="header-body">
                        <h6 class="h2 text-white d-inline-block mb-0"><i class="fa fa-archive"></i> COMMISSION
                            ARCHIVE</h6>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6 col-lg-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Current Comission</h5>
                                    <span
                                        class="h2 font-weight-bold mb-0">{{ isset(auth()->user()->wallet->comission) ? number_format(auth()->user()->wallet->comission, 2, '.', ',') : 0 }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                        <i class="fas fa-percent"></i>
                                    </div>
                                </div>
                            </div>
                            @if (auth()->user()->user_level != 'sub-agent')
                                
                            <a href="{{ auth()->user()->user_level == 'admin' ? route('admin-agent-comission') : route('agent-comission') }}" type="button"
                                
                                class="btn btn-outline-secondary mt-3 mb-0 "><i class="fas fa-cogs"></i> Manage
                                Comission</a>
                                @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
