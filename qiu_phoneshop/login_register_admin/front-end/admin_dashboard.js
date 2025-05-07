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
            case 'users':
                showUsers();
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
function showUsers() {
    hiddenMain();
    tempBox.classList.remove('tempBox_inactive');
    tempBox.classList.add('tempBox');
    tempBox.innerHTML = `   
                        <h1>User list</h1>
                            <table class="tempTable">
                            <thead>
                                <tr class="tempData">
                                    <th >ID</th>
                                    <th >Username</th>
                                    <th >Email</th>
                                    <th >Shipping address</th>
                                    <th >Registration date</th>
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

function showProducts() {
    hiddenMain();
    tempBox.classList.remove('tempBox_inactive');
    tempBox.classList.add('tempBox');
    tempBox.innerHTML = `   
                        <h1>Product list</h1>
                            <table class="tempTable">
                            <thead>
                                <tr class="tempData">
                                    <th>ProductID</th>
                                    <th>Product</th>
                                    <th>Brand</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Sales</th>
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

    fetch('load.php?action=products')
        .then(response => response.json())
        .then(data => {
            console.log(data);
            data.forEach(products => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td >${products.id}</td>
                    <td >${products.name}</td>
                    <td >${products.brand}</td>
                    <td >${products.price}</td>
                    <td >${products.quantity}</td>
                    <td ></td>
                    `;
                tempTbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error:', error));

}

function showOrders() {
    hiddenMain();
    tempBox.classList.remove('tempBox_inactive');
    tempBox.classList.add('tempBox');
    tempBox.innerHTML = `   
                        <h1>Order list</h1>
                            <table class="tempTable">
                            <thead>
                                <tr class="tempData">
                                    <th>ID</th>
                                    <th>CustomerID</th>
                                    <th>Customer</th>
                                    <th>ProductID</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>OrderDate</th>
                                    <th>ShippingAddress</th>
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

    fetch('load.php?action=orders')
        .then(response => response.json())
        .then(data => {
            console.log(data);
            data.forEach(orders => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td >${orders.orderID}</td>
                    <td >${orders.userID}</td>
                    <td >${orders.username}</td>
                    <td >${orders.productID}</td>
                    <td >${orders.name}</td>
                    <td >${orders.quantity}</td>
                    <td >${orders.total_price}</td>  
                    <td >${orders.order_date}</td>
                    <td >${orders.shipping_address}</td>
                    
                    `;
                tempTbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error:', error));

        const crudDiv = document.createElement('div');
        crudDiv.classList.add('crudDiv');
        crudDiv.innerHTML = `
        <button id="addOrderBtn">Add Product</button>
        <button id="updateOrderBtn">Update Product</button>
        <form method="POST" action="load.php?crud=add" id="addProduct">
        </form>
        <form method="POST" action="load.php?crud=update" id="updateProduct">
        </form>
        `;



}

function showAnalytics() {


}

function showSettings() {


}

function showSales() {


}



