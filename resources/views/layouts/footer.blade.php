</body>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
    integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.sidenav').sidenav();
    });

    $(document).ready(function() {
    $('input#input_text, textarea#textarea2').characterCounter();
  });
  
  $(document).ready(function(){
    $('select').formSelect();
  });
  $(document).ready(function(){
    $('.modal').modal();
  });
</script>
