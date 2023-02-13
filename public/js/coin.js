// jQuery(document).ready(function($) {
//
//     function flip1() {
//         var flipResult = Math.floor(Math.random() * 2);
//         $("#coin-1").removeClass().addClass("col s3 m3");
//         setTimeout(function() {
//             if (flipResult === 0) {
//                 $("#coin-1").addClass("heads col s3 m3");
//                 console.log("it is head");
//             } else {
//                 $("#coin-1").addClass("tails col s3 m3");
//                 console.log("it is tails");
//             }
//         }, 100);
//         return flipResult;
//     }
//
//     function flip2() {
//         var flipResult = Math.floor(Math.random() * 2);
//         $("#coin-2").removeClass().addClass("col s3 m3");
//         setTimeout(function() {
//             if (flipResult === 0) {
//                 $("#coin-2").addClass("heads col s3 m3");
//                 console.log("it is head");
//             } else {
//                 $("#coin-2").addClass("tails col s3 m3");
//                 console.log("it is tails");
//             }
//         }, 100);
//         return flipResult;
//     }
//
//         document.getElementById("heads").addEventListener("click", (e) => {
//             let points = document.querySelector('#amount').value;
//             if (points == '') {
//                 Swal.fire(
//                     'Bet Amount not set!',
//                     'Please set an amount to bet!',
//                     'warning'
//                 )
//                 return;
//             }
//             console.log((points == ''));
//             points.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
//             Swal.fire({
//                 title: 'Are you sure?',
//                 text: `You want to bet ${points} points to heads?`,
//                 icon: 'warning',
//                 showCancelButton: true,
//                 confirmButtonColor: '#343a40',
//                 cancelButtonColor: '#d33',
//                 confirmButtonText: 'Yes, place bet!'
//             }).then((result) => {
//                 if (result.isConfirmed) {
//                     Swal.fire(
//                         'Placed!',
//                         'Your bet has been placed.',
//                         'success'
//                     ).then((result) => {
//                         if (result.isConfirmed) {
//                             document.getElementById("betSide").value = 'heads';
//                             let myForm = document.getElementById("betForm");
//                             let formData = new FormData(myForm);
//                             fetch("/player/bet", {
//                                 body: formData,
//                                 method: "post"
//                             });
//                             document.querySelector('#amount').value = '';
//                             // let curr_wallet = document.getElementById('current_wallet').textContent
//                             // let curr_bet = document.getElementById('current_bet_heads').textContent
//                             // console.log(parseInt(curr_wallet), parseInt(curr_bet))
//                             // document.getElementById('current_wallet').textContent = parseInt(curr_wallet) - parseInt(points);
//                             // document.getElementById('current_bet_heads').textContent = parseInt(curr_bet) + parseInt(points);
//                         }
//                     });
//                 }
//             })
//         });
//         document.getElementById("tails").addEventListener("click", (e) => {
//             let points = document.querySelector('#amount').value;
//             if (points == '') {
//                 Swal.fire(
//                     'Bet Amount not set!',
//                     'Please set an amount to bet!',
//                     'warning'
//                 )
//                 return;
//             }
//             points.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
//             console.log(points);
//             Swal.fire({
//                 title: 'Are you sure?',
//                 text: `You want to bet ${points} points to tails?`,
//                 icon: 'warning',
//                 showCancelButton: true,
//                 confirmButtonColor: '#343a40',
//                 cancelButtonColor: '#d33',
//                 confirmButtonText: 'Yes, place bet!'
//             }).then((result) => {
//                 if (result.isConfirmed) {
//                     Swal.fire(
//                         'Placed!',
//                         'Your bet has been placed.',
//                         'success'
//                     ).then((result) => {
//                         if (result.isConfirmed) {
//                             document.getElementById("betSide").value = 'tails';
//                             let myForm = document.getElementById("betForm");
//                             let formData = new FormData(myForm);
//                             fetch("/player/bet", {
//                                 body: formData,
//                                 method: "post"
//                             });
//                             document.querySelector('#amount').value = '';
//                             // let curr_wallet = document.getElementById('current_wallet').textContent
//                             // let curr_bet = document.getElementById('current_bet_tails').textContent
//
//                             // document.getElementById('current_wallet').textContent = parseInt(curr_wallet) - parseInt(points);
//                             // document.getElementById('current_bet_tails').textContent = parseInt(curr_bet) + parseInt(points);
//                         }
//                     });
//                 }
//             })
//         });
// });
//
// function setPrice(price) {
//
//     document.querySelector('#amount').value = price;
// }