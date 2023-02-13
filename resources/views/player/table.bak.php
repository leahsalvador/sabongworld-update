@foreach ($logs as $log)
@switch(substr($log->round, -1))
@case('1')
one.innerHTML +=
`<td class="border bg-white border-light text-center"><span
        class="badge text-dark {{ $log->winner == 'heads' ? 'text-white' : ($log->winner == 'none' ? 'bg-light text-black' : 'text-white') }}"
        style="background-color: {{ $log->winner == 'heads' ? 'red' : ($log->winner == 'tails' ? 'blue' : '#ced4da') }}">{{ $log->round }}</span>
</td>`
@break
@case('2')
two.innerHTML +=
`<td class="border bg-white border-light text-center"><span
        class="badge text-dark {{ $log->winner == 'heads' ? 'text-white' : ($log->winner == 'none' ? 'bg-light text-black' : 'text-white') }}"
        style="background-color: {{ $log->winner == 'heads' ? 'red' : ($log->winner == 'tails' ? 'blue' : '#ced4da') }}">{{ $log->round }}</span>
</td>`
@break
@case('3')
three.innerHTML +=
`<td class="border bg-white border-light text-center"><span
        class="badge text-dark {{ $log->winner == 'heads' ? 'text-white' : ($log->winner == 'none' ? 'bg-light text-black' : 'text-white') }}"
        style="background-color: {{ $log->winner == 'heads' ? 'red' : ($log->winner == 'tails' ? 'blue' : '#ced4da') }}">{{ $log->round }}</span>
</td>`
@break
@case('4')
four.innerHTML +=
`<td class="border bg-white border-light text-center"><span
        class="badge text-dark {{ $log->winner == 'heads' ? 'text-white' : ($log->winner == 'none' ? 'bg-light text-black' : 'text-white') }}"
        style="background-color: {{ $log->winner == 'heads' ? 'red' : ($log->winner == 'tails' ? 'blue' : '#ced4da') }}">{{ $log->round }}</span>
</td>`
@break
@case('5')
five.innerHTML +=
`<td class="border bg-white border-light text-center"><span
        class="badge text-dark {{ $log->winner == 'heads' ? 'text-white' : ($log->winner == 'none' ? 'bg-light text-black' : 'text-white') }}"
        style="background-color: {{ $log->winner == 'heads' ? 'red' : ($log->winner == 'tails' ? 'blue' : '#ced4da') }}">{{ $log->round }}</span>
</td>`
@break
@case('6')
one.innerHTML +=
`<td class="border bg-white border-light text-center"><span
        class="badge text-dark {{ $log->winner == 'heads' ? 'text-white' : ($log->winner == 'none' ? 'bg-light text-black' : 'text-white') }}"
        style="background-color: {{ $log->winner == 'heads' ? 'red' : ($log->winner == 'tails' ? 'blue' : '#ced4da') }}">{{ $log->round }}</span>
</td>`
@break
@case('7')
two.innerHTML +=
`<td class="border bg-white border-light text-center"><span
        class="badge text-dark {{ $log->winner == 'heads' ? 'text-white' : ($log->winner == 'none' ? 'bg-light text-black' : 'text-white') }}"
        style="background-color: {{ $log->winner == 'heads' ? 'red' : ($log->winner == 'tails' ? 'blue' : '#ced4da') }}">{{ $log->round }}</span>
</td>`
@break
@case('8')
three.innerHTML +=
`<td class="border bg-white border-light text-center"><span
        class="badge text-dark {{ $log->winner == 'heads' ? 'text-white' : ($log->winner == 'none' ? 'bg-light text-black' : 'text-white') }}"
        style="background-color: {{ $log->winner == 'heads' ? 'red' : ($log->winner == 'tails' ? 'blue' : '#ced4da') }}">{{ $log->round }}</span>
</td>`
@break
@case('9')
four.innerHTML +=
`<td class="border bg-white border-light text-center"><span
        class="badge text-dark {{ $log->winner == 'heads' ? 'text-white' : ($log->winner == 'none' ? 'bg-light text-black' : 'text-white') }}"
        style="background-color: {{ $log->winner == 'heads' ? 'red' : ($log->winner == 'tails' ? 'blue' : '#ced4da') }}">{{ $log->round }}</span>
</td>`
@break
@case('0')
five.innerHTML +=
`<td class="border bg-white border-light text-center"><span
        class="badge text-dark {{ $log->winner == 'heads' ? 'text-white' : ($log->winner == 'none' ? 'bg-light text-black' : 'text-white') }}"
        style="background-color: {{ $log->winner == 'heads' ? 'red' : ($log->winner == 'tails' ? 'blue' : '#ced4da') }}">{{ $log->round }}</span>
</td>`
@break

@endswitch
@endforeach