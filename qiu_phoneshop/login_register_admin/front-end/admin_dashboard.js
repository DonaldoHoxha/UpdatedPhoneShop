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
    link.addEventListener('click', function () {
        sidebarSection.forEach((link) => {
            link.classList.remove('active');
        });
        this.classList.add('active');

        switch (this.id) {
            case 'dashboard':
                const main = document.querySelector('main');
                const userProfile = document.querySelector('.user-profile');
                const reminders = document.querySelector('.reminders');
            
                main.style.display = 'block';
                userProfile.style.display = 'block';
                reminders.style.display = 'block';
                break;
            case 'management':
                showManagement();
                break;
            case 'users':
                showUsers();
                break;
            case 'history':
                showHistory();
                break;
            case 'products':
                showProducts();
                break;
            case 'orders':
                showOrders();
                break;
            case 'analytics':
                showAnalytics();
                break;
            case 'settings':
                showSettings();
                break;
            case 'sales':
                showSales();
                break;
            default:
                break;
        }

    });

  
});

function hiddenMainContent(){
    const main = document.querySelector('main');
    const userProfile = document.querySelector('.user-profile');
    const reminders = document.querySelector('.reminders');

    main.style.display = 'none';
    userProfile.style.display = 'none';
    reminders.style.display = 'none';
}

function showManagement(){
    hiddenMainContent();
}

function showUsers(){
    hiddenMainContent();

}

function showHistory(){
    hiddenMainContent();

}
function showProducts(){
    hiddenMainContent();

}

function showOrders(){
    hiddenMainContent();

}

function showAnalytics(){
    hiddenMainContent();

}

function showSettings(){
    hiddenMainContent();

}

function showSales(){
    hiddenMainContent();

}


    
