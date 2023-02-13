<div wire:poll.5000ms>
    <div class="container">
        <h4 class="text-white">Round History</h4>
        <div>
            <div style="display: inline-block">
                <div class="rounded-circle rounded-lg text-center"
                     style="vertical-align:middle; display: inline-block;padding-top: 10px; width: 40px; height: 40px;background-color: red">
                                            <span class="text-center"
                                                  style="align-items: center;">{{ $meronCounts }}</span>
                </div>
                <span class="badge"
                      style="vertical-align:middle; background-color: red; color: white !important;margin-left: -12px;padding-left: 10px"> Meron </span>
            </div>
            <div style="display: inline-block">
                <div class="rounded-circle rounded-lg text-center"
                     style="vertical-align:middle; display: inline-block;padding-top: 10px; width: 40px; height: 40px;background-color: blue">
                                            <span class="text-center"
                                                  style="align-items: center;">{{ $walaCounts }}</span>
                </div>
                <span class="badge"
                      style="vertical-align:middle; background-color: blue; color: white !important;margin-left: -12px;padding-left: 10px"> Wala </span>
            </div>
            <div style="display: inline-block">
                <div class="rounded-circle rounded-lg text-center"
                     style="vertical-align:middle; display: inline-block;padding-top: 10px; width: 40px; height: 40px;background-color: green">
                                            <span class="text-center"
                                                  style="align-items: center;">{{ $drawCounts }}</span>
                </div>
                <span class="badge"
                      style="vertical-align:middle; background-color: green; color: white !important;margin-left: -12px;padding-left: 10px"> Draw </span>
            </div>
            <div style="display: inline-block">
                <div class="rounded-circle rounded-lg text-center"
                     style="vertical-align:middle; display: inline-block;padding-top: 10px; width: 40px; height: 40px;background-color: gray">
                                            <span class="text-center"
                                                  style="align-items: center;">{{ $cancelCounts }}</span>
                </div>
                <span class="badge"
                      style="vertical-align:middle; background-color: gray; color: white !important;margin-left: -12px;padding-left: 10px"> Cancelled </span>
            </div>
        </div>
    </div>
    @php
        $gameTemp = '';
        $sameCounter = 0;
        $counter = 0;
    @endphp
    <div class="mt-3" id="style-1"
         style="width: 100%;position: relative; overflow-x: scroll; padding-bottom: 24px;
         ">
        <div style="width: 4280px;">
            <div style="display: inline-block;vertical-align: top;padding-left: 5px;">
                @foreach ($logs as $log)
                    @php
                        $gameState = $log->winner . $log->status;
                    @endphp
                    @if($gameState == $gameTemp && $sameCounter < 5)
                        <div class="rounded-circle rounded-lg text-center"
                             style="margin-top: 2px; padding-top: 10px; width: 40px; height: 40px; @if($log->winner == 'heads') background-color: red @elseif($log->winner == 'tails') background-color: blue @elseif($log->winner == 'draw') background-color: green @else background-color: gray @endif">
                                                    <span class="text-center"
                                                          style="display: inline-flex; align-items: center;">{{ $log->round }}</span>
                        </div>
                        @php
                            $sameCounter++;
                        @endphp
                    @else
                        @if($sameCounter != 5 && $counter != 0)
                            @for($i=0;$i+$sameCounter<5;$i++)
                                <div class="rounded-circle rounded-lg text-center"
                                     style="padding-top: 10px; width: 40px; height: 40px; background-color: #485fe0;margin: 1px">
                                </div>
                            @endfor
                        @endif
                        @php
                            $sameCounter = 0;
                        @endphp
            </div>
            <div style="display: inline-block;vertical-align: top;padding-left: 5px">
                <div class="rounded-circle rounded-lg text-center"
                     style="margin-top: 2px; padding-top: 10px; width: 40px; height: 40px; @if($log->winner == 'heads') background-color: red @elseif($log->winner == 'tails') background-color: blue @elseif($log->winner == 'draw') background-color: green @else background-color: gray @endif">
                                            <span class="text-center"
                                                  style="display: inline-flex; align-items: center;">{{ $log->round }}</span>
                </div>
                @endif
                @php
                    $gameTemp = $gameState;
                    $counter++;
                @endphp
                @if($counter == count($logs) && $sameCounter < 5)
                    @for($i=0;$i+$sameCounter<5;$i++)
                        <div class="rounded-circle rounded-lg text-center"
                             style="padding-top: 10px; width: 40px; height: 40px; background-color: #485fe0;margin: 1px">
                        </div>
                    @endfor
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
