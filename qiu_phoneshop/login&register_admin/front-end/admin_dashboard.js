const sidebarSection = document.querySelectorAll(".sidebar a");
const dashboardSection = document.getElementById("dashboard");

sidebarSection.addEventListener("click", function () {
    sidebarSection.forEach((section) => {
        section.classList.remove("active");
    });
    this.classList.add("active");

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
    
