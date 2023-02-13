<div>
    <div class="card card-stats">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    Round <i class="fa fa-hashtag"></i>
                </div>
                <div class="col-md-2">
                    <button id="reset-game" type="button" class="btn btn-danger">
                        Reset Game
                    </button>
                </div>
                <div class="col-md-2">
                    <button data-toggle="modal" data-target="#nextGame" class="btn btn-primary">
                        Next Game
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body" style="padding: 1rem 0rem;">
            <div class="row">
                <div class="col">
                    <form action="{{ route('modify-game')}}" id="race-form" method="POST">
                        @csrf
                        <input type="hidden" value="" name="action_type" id="action_type">
                        <input type="hidden" value="" name="winner" id="winner">
                        <input type="hidden" value="" name="gr_id" id="gr_id">
                        <table class="table align-items-center table-bordered table-condensed table-striped" id="games"
                               style="width: 100%;">
                            <thead>
                            <tr>
                                <th>Round</th>
                                <th>Winner</th>
                                <th>Status</th>
                                <th>MERON</th>
                                <th>WALA</th>
                                <th>Date</th>
                                <th>Button Color</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($game_rounds as $gr)
                                <tr>
                                    <td>Game #{{ @$gr->round }}</td>
                                    <td>
                                            <?php
                                            if ($gr->winner == 'heads') {
                                                echo '<strong style="color:red;">' . strtoupper($gr->meron) . '</strong>';
                                            } else if ($gr->winner == 'tails') {
                                                echo '<strong style="color:blue;">' . strtoupper($gr->wala) . '</strong>';
                                            }
                                            ?>
                                    </td>
                                    <td>
                                        <strong>{{ $gr->winner=='draw' ? strtoupper(@$gr->winner) : strtoupper(@$gr->status) }}</strong>
                                    </td>
                                    <td><strong>{{ strtoupper(@$gr->meron) }}</strong>
                                            <?php if (in_array($gr->status, ['closed', 'undo']) && !in_array($gr->winner, ['heads', 'tails'])): ?>
                                        <button type="button"
                                                class="btn btn-sm btn-block border-light update-winner-race"
                                                data-id="{{@$gr->id}}" data-round="{{@$gr->round}}" data-winner="heads"
                                                style="display:block; background-color:red; color:#fff; width:auto;"
                                                data-name="MERON">Set Winner
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong>{{ strtoupper(@$gr->wala) }}</strong>
                                            <?php if (in_array($gr->status, ['closed', 'undo']) && !in_array($gr->winner, ['heads', 'tails'])): ?>
                                        <button type="button"
                                                class="btn btn-sm btn-block border-light update-winner-race"
                                                data-id="{{@$gr->id}}" data-round="{{@$gr->round}}" data-winner="tails"
                                                style="display:block; background-color:blue; color:#fff; width:auto;"
                                                data-name="WALA">Set Winner
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                    <td>{{ date('Y-m-d', strtotime($gr->created_at)) }}</td>
                                    <td>{{ strtoupper(@$gr->Game_color) }}</td>
                                    <td>
                                        @if($gr->status == 'upcoming')
                                            <button type="button"
                                                    class="btn btn-primary btn-block border-light d-inline start-race"
                                                    data-id="{{@$gr->id}}" data-round="{{@$gr->round}}"
                                                    style="display:inline-block; width: auto; margin-top: 0; margin-bottom: 2px;">
                                                Start
                                            </button>
                                        @elseif($gr->status == 'open')
                                            <button type="button"
                                                    class="btn btn-danger btn-block border-light d-inline close-race"
                                                    data-id="{{@$gr->id}}" data-round="{{@$gr->round}}"
                                                    style="display:inline-block; width: auto; margin-top: 0; margin-bottom: 2px;">
                                                Close
                                            </button>
                                            <button type="button"
                                                    class="btn btn-secondary btn-block border-light d-inline cancel-race"
                                                    data-id="{{@$gr->id}}" data-round="{{@$gr->round}}"
                                                    style="display:inline-block; width: auto; margin-top: 0; margin-bottom: 2px;">
                                                Cancel
                                            </button>
                                        @elseif(in_array($gr->winner, ['heads', 'tails']) && $gr->status !== 'done')
                                            <button type="button"
                                                    class="btn btn-secondary btn-block border-light d-inline hide-race"
                                                    data-id="{{@$gr->id}}" data-round="{{@$gr->round}}"
                                                    style="display:inline-block; width: auto; margin-top: 0; margin-bottom: 2px;">
                                                Hide
                                            </button>
                                            @if($gr->status !== 'undo')
                                                <button type="button"
                                                        class="btn btn-warning btn-block border-light d-inline undo-race"
                                                        data-id="{{@$gr->id}}" data-round="{{@$gr->round}}"
                                                        style="display:inline-block; width: auto; margin-top: 0; margin-bottom: 2px;">
                                                    Undo
                                                </button>
                                            @endif
                                        @endif

                                        @if(in_array($gr->status, ['closed','undo']) && !in_array($gr->winner, ['heads', 'tails']))
                                            <button type="button"
                                                    class="btn btn-info btn-block border-light d-inline draw-race"
                                                    data-id="{{@$gr->id}}" data-round="{{@$gr->round}}"
                                                    style="display:inline-block; width: auto; margin-top: 0; margin-bottom: 2px;">
                                                Draw
                                            </button>
                                            <button type="button"
                                                    class="btn btn-secondary btn-block border-light d-inline cancel-race"
                                                    data-id="{{@$gr->id}}" data-round="{{@$gr->round}}"
                                                    style="display:inline-block; width: auto; margin-top: 0; margin-bottom: 2px;">
                                                Cancel
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="nextGame" tabindex="-1" role="dialog" aria-labelledby="nextGame"
     aria-hidden="true">
    <div class="modal-dialog modal-danger modal-dialog-centered" role="document">
        <div class="modal-content bg-gradient-danger">
            <form action="{{ route('add-game') }}" id="add-race-form" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title" id="modal-title-notification">
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div id="loading_form_body" class="card-body">
                        <div class="form-group"><label
                                for="company"><strong>{{__('Game Color')}}</strong></label>
                            <select id="race_color" required="required" name="Game_color"
                                    class="form-control">
                                <option value="red" style="color:red;">{{__('RED')}}</option>
                                <option value="blue" selected="selected"
                                        style="color:blue;">{{__('BLUE')}}</option>
                            </select>
                            @if ($errors->has('Game_color'))
                                <span class="invalid-feedback red-text" style="display: block;"
                                      role="alert">
                                            <strong>{{ $errors->first('game_color') }}</strong>
                                        </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="game-round-number"><strong>Please reset game number</strong></label>
                            <div class="input-group">
                                <input id="game-round-number" placeholder="Game 1"
                                       required="required" name="game_round_number" type="number" step="1"
                                       min="1"
                                       class="form-control"/>
                            </div>
                            @if ($errors->has('round'))
                                <span class="invalid-feedback red-text" style="display: block;"
                                      role="alert">
                                            <strong>{{ $errors->first('game_round_number') }}</strong>
                                        </span>
                            @endif
                        </div>

                        <div class="row">
                            <input
                                placeholder="MERON Team Name" id="red_team_name"
                                name="red_team_name" type="text" class="form-control " hidden>
                        </div>
                        <div class="row">
                            <input
                                placeholder="WALA Team Name" id="blue_team_name"
                                name="blue_team_name" type="text" class="form-control " hidden>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-white ml-auto"
                            data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                    <button type="submit" class="btn btn-white"><i class="fa fa-save"></i>
                        <?php echo e(__('Submit')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

