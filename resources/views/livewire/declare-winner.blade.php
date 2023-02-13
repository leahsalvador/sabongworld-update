<div wire:poll.150000ms>
    @if($winner->count())
        <div><h1>Hello World</h1></div>
        <script>
            alert('Test');
            /*Swal.fire({
                title: 'Game #',
                html: '<p></p>',
                allowOutsideClick: true
            })*/
        </script>
    @endif
</div>
