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
                                </tr>
                            </thead>
                            <tbody id="tempTbody">
                            </tbody>
                        </table>
                        <div id="productModal" class="modal">
                            <div class="modal-content">
                                <span class="close-modal">&times;</span>
                                <h2>Add New Product</h2>
                                <form id="addProductForm" action="../back-end/add_product.php" method="POST">
                                    <div class="form-group">
                                        <label for="name">Name:</label>
                                        <input type="text" id="name" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="brand">Brand:</label>
                                        <input type="text" id="brand" name="brand" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="ram">RAM (GB):</label>
                                        <input type="number" id="ram" name="ram" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="rom">ROM (GB):</label>
                                        <input type="number" id="rom" name="rom" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="camera">Camera (MP):</label>
                                        <input type="number" step="0.1" id="camera" name="camera" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="battery">Battery (mAh):</label>
                                        <input type="number" id="battery" name="battery" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Price ($):</label>
                                        <input type="number" step="0.01" id="price" name="price" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="quantity">Quantity:</label>
                                        <input type="number" id="quantity" name="quantity" required>
                                    </div>
                                    <button type="submit" class="submit-btn">Add Product</button>
                                </form>
                            </div>
                        </div>
                        <button id="addProductBtn" class="btn">Add Product</button>`;

    // Carica i prodotti esistenti
    const tempTbody = document.getElementById('tempTbody');
    tempTbody.innerHTML = '';

    fetch('load.php?action=products')
        .then(response => response.json())
        .then(data => {
            data.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.brand}</td>
                    <td>$${product.price}</td>
                    <td>${product.quantity}</td>`;
                tempTbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error:', error));

    // Gestione della modale
    const modal = document.getElementById("productModal");
    const btn = document.getElementById("addProductBtn");
    const span = document.getElementsByClassName("close-modal")[0];

    // Quando viene cliccato il pulsante add product allora il form diventa visibile
    btn.onclick = function () {
        modal.style.display = "block";
    }

    // Quando viene cliccato il pulsante di chiusura della modale, la modale viene nascosta
    span.onclick = function () {
        modal.style.display = "none";
    }

    // Quando viene cliccato qualunque parte della pagina, la modale viene nascosta
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
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





}

function showAnalytics() {


}

function showSettings() {


}

function showSales() {


}



