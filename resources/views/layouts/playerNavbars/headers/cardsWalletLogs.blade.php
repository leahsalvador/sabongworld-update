<div class="header bg-gradient-info pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="header pb-4">
                <div class="container-fluid">
                    <div class="header-body">
                        <h6 class="h2 text-white d-inline-block mb-0"><i class="fas fa-wallet"></i> Wallet Management
                        </h6>
                    </div>
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-xl-12 col-lg-12">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-header"><strong>Search Transaction</strong>
                            <div class="card-header-actions float-right">
                                <div role="group" class="btn-group">
                                    <div role="group" class="btn-group"><button id="btnGroupDrop1" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                            class="btn btn-success btn-sm dropdown-toggle">
                                            Download
                                        </button>
                                        <div aria-labelledby="btnGroupDrop1" class="dropdown-menu"><a
                                                href="https://wpc15.com/admin/agentadmin/walletstationlogs?search_transaction_type=deposit&amp;search_arena=1&amp;download=1"
                                                class="dropdown-item">Download All</a> <a
                                                href="https://wpc15.com/admin/agentadmin/walletstationlogs?search_transaction_type=deposit&amp;search_arena=1&amp;downloadcurrent=1"
                                                class="dropdown-item">Download Current</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pb-2 pt-2">
                            <form method="GET" action="https://wpc15.com/admin/agentadmin/walletstationlogs"
                                accept-charset="UTF-8" id="search-transaction" role="form" class="form-inline">
                                <div class="form-group mb-0 mx-sm-1">
                                    <select id="search_transaction_type"
                                        name="search_transaction_type" class="form-control">
                                        <option value="">Filter Transaction Type</option>
                                        <option value="deposit" selected="selected">Deposit</option>
                                        <option value="withdrawal">Withdrawal</option>
                                        <option value="agent-deposit">Agent Deposit</option>
                                        <option value="agent-withdraw">Agent Withdrawal</option>
                                        <option value="system-deposit">System Deposit</option>
                                        <option value="system-withdraw">System Withdrawal</option>
                                    </select></div>
                                <div class="form-group mx-sm-1 mb-0"><select name="load_to" id="load_to"
                                        required="required" placeholder="Select a username"
                                        class="form-control username-select2 select2-hidden-accessible" tabindex="-1"
                                        aria-hidden="true">
                                        <option value="">Select a username</option>
                                        @foreach ($users as $user)
                                            <option value="{{ @$user->id }}" class="text-info">
                                                {{ @$user->username }}
                                            </option>
                                        @endforeach
                                    </select></div>
                                <a href="{{route('agent-wallet-logs')}}"
                                    class="btn btn-secondary btn-sm mb-0 ml-2"><i class="fa fa-ban"></i> Reset</a>
                                <button type="submit" class="btn btn-primary btn-sm mb-0 ml-2"><i
                                        class="fa fa-search"></i> Search</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div> --}}
        </div>
    </div>
</div>
