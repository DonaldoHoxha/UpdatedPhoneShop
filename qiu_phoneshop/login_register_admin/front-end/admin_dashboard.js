const sidebarSection = document.querySelectorAll(".sidebar a");
const dashboardSection = document.getElementById("dashboard");

sidebarSection.forEach((link) => {
    link.addEventListener("click", function () {
        sidebarSection.forEach((link) => {
            link.classList.remove("active");
        });
        this.classList.add("active");

    });

    switch (this.id) {
        case "users":
            showUsers();
            break;
    }
});



function showUsers(){

}

function showHistory(){

}
    
