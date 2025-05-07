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
const container = document.querySelector('.container');
const tempBox = document.querySelector('.tempBox_inactive');
//an empty div to temporaly store the content of the selected section

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
                tempBox.classList.remove('tempBox');
                tempBox.classList.add('tempBox_inactive');
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


function hiddenMain() {
    const main = document.querySelector('main');
    main.style.display = 'none';


}

function showManagement() {
    hiddenMain();

}

function showUsers() {
    hiddenMain();
    tempBox.classList.remove('tempBox_inactive');
    tempBox.classList.add('tempBox');
    tempBox.innerHTML = `   
                        <h2>User list</h2>
                            <table class="userTable">
                            <thead>
                                <tr class="userData">
                                    <th class="headUser">ID</th>
                                    <th class="headUser">Username</th>
                                    <th class="headUser">Email</th>
                                    <th class="headUser">Shipping address</th>
                                    <th class="headUser">Registration date</th>
                                </tr>
                            </thead>
                            <tbody id="tempTbody">
                            </tbody>
                        </table>`;

    const tempTbody = document.getElementById('tempTbody');
    if (!tempTbody) {
        console.error('tempTbody element not found');
        return;
    }

    tempTbody.innerHTML = '';

    fetch('load.php?action=users')
        .then(response => response.json())
        .then(data => {
            console.log(data);
            data.forEach(users => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="columnUser">${users.id}</td>
                    <td class="columnUser">${users.username}</td>
                    <td class="columnUser">${users.email}</td>
                    <td class="columnUser">${users.shipping_address}</td>
                    <td class="columnUser">${users.registration_date}</td>`;
                tempTbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error:', error));
}

function showHistory() {


}
function showProducts() {


}

function showOrders() {


}

function showAnalytics() {


}

function showSettings() {


}

function showSales() {


}



