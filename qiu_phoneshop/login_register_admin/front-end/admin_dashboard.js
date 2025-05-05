const sideMenu = document.querySelector('aside');
const menuBtn = document.getElementById('menu-btn');
const closeBtn = document.getElementById('close-btn');

const darkMode = document.querySelector('.dark-mode');

menuBtn.addEventListener('click', () => {
    sideMenu.style.display = 'block';
});

closeBtn.addEventListener('click', () => {
    sideMenu.style.display = 'none';
});

darkMode.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode-variables');
    darkMode.querySelector('span:nth-child(1)').classList.toggle('active');
    darkMode.querySelector('span:nth-child(2)').classList.toggle('active');
})






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

function showProducts(){
}

function showOrders(){
}

function showAnalytics(){
}

function showSettings(){
}



    
