<div wire:poll.3000ms>
<div class="row">
    <div class="col text-white ml-2 h3">
        {{ date('h:i:s A', $now) }}
    </div>
  </div>
</div>
<script>
    window.livewire.on('play-audio', async (message) => {
        if (message.winner != 'none') {
            await test(message)
            // var coin = new Audio("http://bentebet.com/image/coinss.m4a");
            // // var audio = new Audio("http://bentebet.com/image/bgm.mp3");
            // // var coin = new Audio("http://localhost:8000/image/coinss.m4a");
            //     coin.play();
            //     setTimeout(() => {
            //         coin.pause();
            //     }, 10000);
        }
    })

</script>
