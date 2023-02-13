<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{url('/')}}/image/head.png" class="logo" alt="Online Cara Cruz">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
