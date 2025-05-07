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
//an empty div to temporaly store the content of the selected section
const tempBox = document.getElementById('tempBox');

sidebarSection.forEach((link) => {
    link.addEventListener('click', function () {
        sidebarSection.forEach((link) => {
            link.classList.remove('active');
        });
        this.classList.add('active');

        switch (this.id) {
            case 'dashboard':
                const main = document.querySelector('main');

                main.style.display = 'block';
                tempBox.style.display = 'none';
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


function hiddenMain(){
    const main = document.querySelector('main');
    main.style.display = 'none';
}

function showManagement(){
    hiddenMain();
   
}

function showUsers(){
    hiddenMain();
    tempBox.style.display = 'block';
    tempBox.innerHTML = `<table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Shipping address</th>
                                    <th>Registration date</th>
                                </tr>
                            </thead>
                            <tbody id="tempTbody">
                            </tbody>
                        </table>
                    `
                    ;
        const tempTbody = document.getElementById('tempTbody');
        tempTbody.innerHTML = '';
        fetch('front-end/load.php?action=users')
        .then(response => response.json())
        .then(data => {
            console.log(data);
        data.forEach(users => {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td>${users.id}</td>
            <td>${users.username}</td>
            <td>${users.email}</td>
            <td>${users.shipping_address}</td>
            <td>€${users.registration_date}</td>
            `;
            tempTbody.appendChild(row);
        });
    });
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

function showSales(){


}


    
