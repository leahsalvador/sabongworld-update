document.getElementById("depositForm").addEventListener("submit", (e) => {
    e.preventDefault();
    console.log(e);
    Swal.fire({
        title: "Add points?",
        text: "Please double check if the details are correct!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#343a40",
        cancelButtonColor: "#d33",
        confirmButtonText: "Add points!",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire(
                "Success!",
                "Your cash in request has been sent.",
                "success"
            ).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("depositForm").submit();
                }
            });
        } else {
            return;
        }
    });
});

document.getElementById("withdrawForm").addEventListener("submit", (e) => {
    e.preventDefault();
    console.log(e);

    Swal.fire({
        title: "Withdraw?",
        text: "Please double check if the details are correct!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#343a40",
        cancelButtonColor: "#d33",
        confirmButtonText: "Add points!",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire(
                "Success!",
                "Your withdraw request has been sent.",
                "success"
            ).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("withdrawForm").submit();
                }
            });

        } else {
            return;
        }
    });
    document.querySelector("#amount").value = price;
});


// function payment(methodId) {
//     let methodSelect = document.getElementById(methodId);
//     let account = JSON.parse(methodSelect.options[methodSelect.selectedIndex].id);
//     console.log(account);
//     let x = document.getElementById('accountUserIdDeposit');
//     console.log(x);
//     let y = document.getElementById('accountUserIdWithdraw');
//     console.log(y);
//     document.getElementById('accountUserIdDeposit').value = account.user_id;
//     document.getElementById('accountUserIdWithdraw').value = account.user_id;
// }