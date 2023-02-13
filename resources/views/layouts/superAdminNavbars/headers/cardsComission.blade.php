<div class="header bg-gradient-info pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="header pb-4">
                <div class="container-fluid">
                    <div class="header-body">
                        <h6 class="h2 text-white d-inline-block mb-0"><i class="fas fa-percent"></i> COMMISSION MANAGEMENT</h6>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6 col-lg-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0" id="user-wallet-name"> {{@$user->name}} Comission</h5>
                                    <span class="h2 font-weight-bold mb-0 " id="user-wallet-points">{{number_format(@$user->wallet->comission, 2, '.', ',')}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
