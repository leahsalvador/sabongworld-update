<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:image" content="{{config('settings.img.logo')}}">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1024">
    <meta property="og:image:height" content="1024">
    <title>{{env('APP_NAME')}}</title>
    <!-- Favicon -->
    <link href="{{config('settings.img.logo')}}" rel="icon" type="image/x-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css"
          integrity="sha512-kq3FES+RuuGoBW3a9R2ELYKRywUEQv0wvPTItv3DSGqjpbNtGWVdvT8qwdKkqvPzT93jp8tSF4+oN4IeTEIlQA=="
          crossorigin="anonymous"/>
    <!-- Extra details for Live View on GitHub Pages -->
    <link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-wordpress-admin@4/wordpress-admin.css" rel="stylesheet">

    <!-- Icons -->
    <link href="{{ asset('argon') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
    <link href="{{ asset('argon') }}/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <!-- Argon CSS -->
    <link type="text/css" href="{{ asset('argon') }}/css/argon.css?v=1.0.0" rel="stylesheet">
</head>

<body class="{{ $class ?? 'bg-default' }}">
@auth()
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    @include('layouts.agentNavbars.sidebar')
@endauth

<div class="main-content">
    @include('layouts.agentNavbars.navbar')
    @yield('content')
</div>

@guest()
    @include('layouts.footers.guest')
@endguest

<script src="{{ asset('argon') }}/vendor/jquery/dist/jquery.min.js"></script>
<script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
@stack('js')

<!-- Argon JS -->
<script src="{{ asset('argon') }}/js/argon.js?v=1.0.0"></script>
<script>
    $(document).ready(function () {
        var table = $('#example').DataTable({
            dom: 'Bfrtip',
            order: [],
            buttons: [
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
        });

        var table = $('#example2').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
        });

    });

    $(function () {
        console.log('test');
        $(document).on('click', '.close-race', function () {
            console.log('close race');
            const gr_id = $(this).attr('data-id');
            $('#action_type').val('closed');
            $('#gr_id').val(gr_id);
            Swal.fire({
                title: "Close Round #" + $(this).attr('data-round'),
                html: `Are you sure?`,
                icon: "warning",
                allowOutsideClick: false,
                showCancelButton: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    $("#race-form").submit();

                    let timerInterval
                    Swal.fire({
                        title: 'Success!',
                        timer: 2000,
                        icon: 'success',
                        timerProgressBar: false,
                        didOpen: () => {
                        },
                        willClose: () => {
                            clearInterval(timerInterval)
                        }
                    }).then((result) => {

                    })
                }
            });
        });
        $(document).on('click', '.start-race', function () {
            console.log('start race');
            const gr_id = $(this).attr('data-id');
            $('#action_type').val('start');
            $('#gr_id').val(gr_id);
            Swal.fire({
                title: "Start Game #" + $(this).attr('data-round'),
                html: `Are you sure?`,
                icon: "warning",
                allowOutsideClick: false,
                showCancelButton: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    $("#race-form").submit();

                    let timerInterval
                    Swal.fire({
                        title: 'Success!',
                        timer: 2000,
                        icon: 'success',
                        timerProgressBar: false,
                        didOpen: () => {
                        },
                        willClose: () => {
                            clearInterval(timerInterval)
                        }
                    }).then((result) => {

                    })
                }
            });
        });
        $(document).on('click', '.draw-race', function () {
            console.log('close Game');
            const gr_id = $(this).attr('data-id');
            $('#action_type').val('draw');
            $('#gr_id').val(gr_id);
            Swal.fire({
                title: "Draw Round #" + $(this).attr('data-round'),
                html: `Are you sure?`,
                icon: "warning",
                allowOutsideClick: false,
                showCancelButton: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    $("#race-form").submit();
                    let timerInterval
                    Swal.fire({
                        title: 'Success!',
                        timer: 2000,
                        icon: 'success',
                        timerProgressBar: false,
                        didOpen: () => {
                        },
                        willClose: () => {
                            clearInterval(timerInterval)
                        }
                    }).then((result) => {

                    })
                }
            });
        });
        $(document).on('click', '.cancel-race', function () {
            console.log('close race');
            const gr_id = $(this).attr('data-id');
            $('#action_type').val('cancel');
            $('#gr_id').val(gr_id);
            Swal.fire({
                title: "Cancel Round #" + $(this).attr('data-round'),
                html: `Are you sure?`,
                icon: "warning",
                allowOutsideClick: false,
                showCancelButton: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    $("#race-form").submit();
                    let timerInterval
                    Swal.fire({
                        title: 'Success!',
                        timer: 2000,
                        icon: 'success',
                        timerProgressBar: false,
                        didOpen: () => {
                        },
                        willClose: () => {
                            clearInterval(timerInterval)
                        }
                    }).then((result) => {

                    })
                }
            });
        });
        $(document).on('click', '.hide-race', function () {
            console.log('close race');
            const gr_id = $(this).attr('data-id');
            $('#action_type').val('hide');
            $('#gr_id').val(gr_id);
            Swal.fire({
                title: "Hide Round #" + $(this).attr('data-round'),
                html: `Are you sure?`,
                icon: "warning",
                allowOutsideClick: false,
                showCancelButton: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    $("#race-form").submit();

                    let timerInterval
                    Swal.fire({
                        title: 'Success!',
                        timer: 2000,
                        icon: 'success',
                        timerProgressBar: false,
                        didOpen: () => {
                        },
                        willClose: () => {
                            clearInterval(timerInterval)
                        }
                    }).then((result) => {

                    })
                }
            });
        });
        $(document).on('click', '.update-winner-race', function () {
            console.log('close race');
            const gr_id = $(this).attr('data-id');
            const winner = $(this).attr('data-winner');
            $('#action_type').val('update-winner');
            $('#winner').val(winner);
            $('#gr_id').val(gr_id);
            Swal.fire({
                title: "Update Winner Round #" + $(this).attr('data-round'),
                html: `Are you sure ` + $(this).attr('data-name') + ` is your winner?`,
                icon: "warning",
                allowOutsideClick: false,
                showCancelButton: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    $("#race-form").submit();
                    Swal.fire({
                        title: 'Please wait',
                        html: '<p>Saving...</p>',
                        allowOutsideClick: false
                    })
                    Swal.showLoading();
                    return;
                    let timerInterval
                    Swal.fire({
                        title: 'Success!',
                        timer: 2000,
                        icon: 'success',
                        timerProgressBar: false,
                        didOpen: () => {
                        },
                        willClose: () => {
                            clearInterval(timerInterval)
                        }
                    }).then((result) => {

                    })
                }
            });
        });
        $(document).on('click', '#add-game-btn', function () {

            if ($('#round').val().trim().length <= 0) {
                Swal.fire({
                    title: "Add Race",
                    html: `Please add race number`,
                    icon: "warning",
                    allowOutsideClick: false
                });

                return;
            } else if ($('#round').val() <= 0) {
                Swal.fire({
                    title: "Add Race",
                    html: `Please add valid race number.`,
                    icon: "warning",
                    allowOutsideClick: false
                });

                return;
            }
            if ($('#red_team_name').val().trim().length <= 0) {
                Swal.fire({
                    title: "Add Race",
                    html: `Please add blue team`,
                    icon: "warning",
                    allowOutsideClick: false
                });

                return;
            }
            if ($('#blue_team_name').val().trim().length <= 0) {
                Swal.fire({
                    title: "Add Race",
                    html: `Please add blue team`,
                    icon: "warning",
                    allowOutsideClick: false
                });
                return;
            }
            Swal.fire({
                title: "Add Race",
                html: `Are you sure?`,
                icon: "warning",
                allowOutsideClick: false,
                showCancelButton: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    $("#add-race-form").submit();
                    Swal.fire({
                        title: 'Please wait',
                        html: '<p></p>',
                        allowOutsideClick: false
                    })
                    Swal.showLoading();
                    /*let timerInterval
                    Swal.fire({
                        title: 'Success!',
                        timer: 2000,
                        icon: 'success',
                        timerProgressBar: false,
                        didOpen: () => {},
                        willClose: () => {
                            clearInterval(timerInterval)
                        }
                    }).then((result) => {

                    })//*/
                }
            });
        });
        $(document).on('click', '.undo-race', function () {
            console.log('undo race');
            const gr_id = $(this).attr('data-id');
            $('#action_type').val('undo');
            $('#gr_id').val(gr_id);
            Swal.fire({
                title: "Undo Round #" + $(this).attr('data-round'),
                html: `Are you sure?`,
                icon: "warning",
                allowOutsideClick: false,
                showCancelButton: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    $("#race-form").submit();
                    let timerInterval
                    Swal.fire({
                        title: 'Success!',
                        timer: 2000,
                        icon: 'success',
                        timerProgressBar: false,
                        didOpen: () => {
                        },
                        willClose: () => {
                            clearInterval(timerInterval)
                        }
                    }).then((result) => {
                    })
                }
            });
        });
    });
</script>
</body>

</html>
