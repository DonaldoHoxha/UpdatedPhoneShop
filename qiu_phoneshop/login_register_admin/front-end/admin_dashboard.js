document.addEventListener("DOMContentLoaded", () => {
  dashboardDate();
});




const sideMenu = document.querySelector("aside");
const menuBtn = document.getElementById("menu-btn");
const closeBtn = document.getElementById("close-btn");

const darkMode = document.querySelector(".dark-mode");

menuBtn.addEventListener("click", () => {
  sideMenu.style.display = "block";
});

closeBtn.addEventListener("click", () => {
  sideMenu.style.display = "none";
});

darkMode.addEventListener("click", () => {
  document.body.classList.toggle("dark-mode-variables");
  darkMode.querySelector("span:nth-child(1)").classList.toggle("active");
  darkMode.querySelector("span:nth-child(2)").classList.toggle("active");
});

const sidebarSection = document.querySelectorAll(".sidebar a");
const dashboardSection = document.getElementById("dashboard");
const container = document.querySelector(".container");
const tempBox = document.querySelector(".tempBox_inactive");
//an empty div to temporaly store the content of the selected section
const totalSales = document.querySelector(".totalSales");
sidebarSection.forEach((link) => {
  link.addEventListener("click", function () {
    sidebarSection.forEach((link) => {
      link.classList.remove("active");
    });
    this.classList.add("active");

    switch (this.id) {
      case "dashboard":
        const main = document.querySelector("main");

        main.style.display = "block";
        tempBox.classList.remove("tempBox");
        tempBox.classList.add("tempBox_inactive");
        break;
      case "users":
        showUsers();
        break;
      case "products":
        showProducts();
        break;
      case "orders":
        showOrders();
        break;
      case "analytics":
        showAnalytics();
        break;
      case "settings":
        showSettings();
        break;
      case "sales":
        showSales();
        break;
      default:
        break;
    }
  });
});
// Load the dashboard data when the page loads
function dashboardDate() {
  fetch('../back-end/load.php?action=analytics')
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        document.querySelector('.totalSales').innerText = "$" + data.totalSales.toLocaleString();
        document.querySelector('.totalSearches').innerText = data.totalSearches.toLocaleString();
      } else if (data.status === 'error') {
        console.error('Error fetching analytics data:', data.message);
        document.querySelector('.totalSales').innerText = 'Error';
        document.querySelector('.totalSearches').innerText = 'Error';
      }
      else {
        console.error('Error fetching analytics data:', error);
      }
    })
    .catch(error => console.error('Error fetching analytics data:', error));
}
function hiddenMain() {
  const main = document.querySelector("main");
  main.style.display = "none";
}

function showTotalSales() {
  fetch("../back-end/load.php?action=totalSales")
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      data.forEach((users) => {
        totalSales.innerHTML = `
                  `;
      });
    })
    .catch((error) => console.error("Error:", error));
}
function showUsers() {
  hiddenMain();
  tempBox.classList.remove("tempBox_inactive");
  tempBox.classList.add("tempBox");
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

  const tempTbody = document.getElementById("tempTbody");
  if (!tempTbody) {
    console.error("tempTbody element not found");
    return;
  }

  tempTbody.innerHTML = "";

  fetch("../back-end/load.php?action=users")
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      data.forEach((users) => {
        const row = document.createElement("tr");
        row.innerHTML = `
                    <td class="columnUser">${users.id}</td>
                    <td class="columnUser">${users.username}</td>
                    <td class="columnUser">${users.email}</td>
                    <td class="columnUser">${users.shipping_address}</td>
                    <td class="columnUser">${users.registration_date}</td>`;
        tempTbody.appendChild(row);
      });
    })
    .catch((error) => console.error("Error:", error));
}

function showProducts() {
  hiddenMain();
  tempBox.classList.remove("tempBox_inactive");
  tempBox.classList.add("tempBox");

  tempBox.innerHTML = `   
        <h1>Product list</h1>
        <div class="table-container">
            <table class="tempTable">
                <thead>
                    <tr class="tempData">
                        <th>ProductID</th>
                        <th>Product</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>RAM</th>
                        <th>Storage</th>
                        <th>Camera</th>
                        <th>Battery</th>
                    </tr>
                </thead>
                <tbody id="tempTbody"></tbody>
            </table>
            <div id="pagination" class="pagination"></div>
        </div>

        <div id="productModal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <h2></h2>
                <form id="addProductForm" method="POST">
                    <div class="form-group" id="productID"></div>
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
                        <label for="rom">Storage (GB):</label>
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
                    <button type="submit" class="submit-btn"></button>
                </form>
            </div>
        </div>

        <div class="product-actions">
            <button id="addProductBtn" class="btn">Add Product</button>
            <button id="updateProductBtn" class="btn">Update Product</button>
        </div>
    `;

  // Carica la prima pagina
  loadProducts(1);

  // Inizializza la modal
  initProductModal();
}

function loadProducts(page) {
  const tempTbody = document.getElementById("tempTbody");
  const pagination = document.getElementById("pagination");

  // Mostra loader
  tempTbody.innerHTML =
    '<tr><td colspan="9" class="loading"><i class="fas fa-spinner fa-spin"></i> Loading products...</td></tr>';
  //l'API moderna di js per effettuare richieste HTTP (AJAX= Asynchronous JavaScript and XML)
  //XML(eXtensible markup language) è un linguaggio di markup, e con il suo formato di dati
  // viene ampiamente usato per lo scambio di dati tra client e server
  //AJAX è una tecnica js usato da lato client per comunicare con il server senza ricaricare la pagina

  fetch(`../back-end/load.php?action=products&page=${page}`, {
    method: "GET",
    credentials: "include",
  })
    //gestione della risposta
    .then((response) => {
      if (!response.ok)
        throw new Error(`Errore nella risposta: ${response.status}`);
      return response.json();
    })
    .then((data) => {
      if (!data.products || !Array.isArray(data.products)) {
        throw new Error("Dati non validi ricevuti");
      }

      // Popola la tabella
      tempTbody.innerHTML = "";
      data.products.forEach((product) => {
        const row = document.createElement("tr");
        row.innerHTML = `
                <td>${product.id}</td>
                <td>${product.name}</td>
                <td>${product.brand}</td>
                <td>$${product.price}</td>
                <td>${product.quantity}</td>
                <td>${product.ram} GB</td>
                <td>${product.rom} GB</td>
                <td>${product.camera} MP</td>
                <td>${product.battery} mAh</td>
            `;
        tempTbody.appendChild(row);

        // Aggiungi click handler per l'update
        row.addEventListener("click", () => {
          openUpdateModal(product);
        });
      });

      // Genera la paginazione
      generatePagination(
        data.pagination.current_page,
        data.pagination.total_pages,
        "products"
      );
    })
    .catch((error) => {
      console.error("Errore durante il caricamento dei prodotti:", error);
      tempTbody.innerHTML = `<tr><td colspan="9" class="error">Error loading products: ${error.message}</td></tr>`;
    });
}

function generatePagination(currentPage, totalPages, contentType) {
  const pagination = document.getElementById("pagination");
  if (!pagination || totalPages <= 1) {
    pagination.innerHTML = "";
    return;
  }

  let html = "";
  const maxVisiblePages = 5;
  let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
  let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

  if (endPage - startPage + 1 < maxVisiblePages) {
    startPage = Math.max(1, endPage - maxVisiblePages + 1);
  }

  // Link "Previous"
  if (currentPage > 1) {
    html += `<a href="#" class="page-link page-nav" data-page="${currentPage - 1
      }" data-type="${contentType}">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>`;
  } else {
    html += `<span class="page-link page-nav disabled">
                    <i class="fas fa-chevron-left"></i> Previous
                </span>`;
  }

  // Numeri pagina
  if (startPage > 1) {
    html += `<a href="#" class="page-link" data-page="1" data-type="${contentType}">1</a>`;
    if (startPage > 2) html += `<span class="page-dots">...</span>`;
  }

  for (let i = startPage; i <= endPage; i++) {
    if (i === currentPage) {
      html += `<span class="page-link active">${i}</span>`;
    } else {
      html += `<a href="#" class="page-link" data-page="${i}" data-type="${contentType}">${i}</a>`;
    }
  }

  if (endPage < totalPages) {
    if (endPage < totalPages - 1) html += `<span class="page-dots">...</span>`;
    html += `<a href="#" class="page-link" data-page="${totalPages}" data-type="${contentType}">${totalPages}</a>`;
  }

  // Link "Next"
  if (currentPage < totalPages) {
    html += `<a href="#" class="page-link page-nav" data-page="${currentPage + 1
      }" data-type="${contentType}">
                    Next <i class="fas fa-chevron-right"></i>
                </a>`;
  } else {
    html += `<span class="page-link page-nav disabled">
                    Next <i class="fas fa-chevron-right"></i>
                </span>`;
  }

  pagination.innerHTML = html;

  // Aggiungi event listener
  document.querySelectorAll(".page-link[data-page]").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const page = parseInt(this.dataset.page);
      const type = this.dataset.type;

      if (type === "products") {
        loadProducts(page);
      } else if (type === "orders") {
        loadOrders(page);
      }

      document.querySelector(".table-container").scrollIntoView({
        behavior: "smooth",
      });
    });
  });
}

function initProductModal() {
  const modal = document.getElementById("productModal");
  const form = document.getElementById("addProductForm");
  const modalTitle = modal.querySelector("h2");
  const span = document.querySelector(".close-modal");
  const submitBtn = document.querySelector(".submit-btn");
  const productID = document.getElementById("productID");

  const addBtn = document.getElementById("addProductBtn");
  const updateBtn = document.getElementById("updateProductBtn");

  addBtn.onclick = function () {
    modalTitle.textContent = "Add New Product";
    form.action = "../back-end/add_product.php";
    submitBtn.textContent = "Add Product";
    productID.style.display = "none";
    productID.innerHTML = "";
    form.reset();
    modal.style.display = "block";
  };

  updateBtn.onclick = function () {
    modalTitle.textContent = "Update Product";
    form.action = "../back-end/update_product.php";
    submitBtn.textContent = "Update Product";
    productID.style.display = "block";
    productID.innerHTML = `
            <label for="id">ID:</label>
            <input type="number" id="id" name="id" required>
        `;
    form.reset();
    modal.style.display = "block";
  };

  span.onclick = function () {
    modal.style.display = "none";
  };

  window.onclick = function (event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  };

  // Gestione submit form
  form.addEventListener("submit", function (e) {
    e.preventDefault(); //Impedisce al browser di eseguire l'azione standard associata a un evento.
    // In questo caso, impedisce l'invio del form e gestisco manualmente l'invio tramite JavaScript.
    const formData = new FormData(this);

    fetch(this.action, {
      method: "POST",
      body: formData,
      credentials: "include",
    })
      .then((response) => {
        if (!response.ok) {
          return response.json().then((err) => Promise.reject(err));
        }
        return response.json();
      })
      .then((data) => {
        if (data.status === "success") {
          // Mostra messaggio di successo
          showNotification("success", data.message);

          // Ricarica i prodotti
          const currentPage =
            document.querySelector(".page-link.active")?.textContent || 1;
          loadProducts(currentPage);

          // Chiudi modal
          modal.style.display = "none";
        } else {
          showNotification("error", data.message || "Errore sconosciuto");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showNotification("error", error.message || "Errore di connessione");
      });
  });

  function showNotification(type, message) {
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.remove();
    }, 5000);
  }
}

function openUpdateModal(product) {
  const modal = document.getElementById("productModal");
  const form = document.getElementById("addProductForm");
  const modalTitle = modal.querySelector("h2");

  modalTitle.textContent = "Update Product";
  form.action = "../back-end/update_product.php";
  form.querySelector(".submit-btn").textContent = "Update Product";

  const productID = document.getElementById("productID");
  productID.style.display = "block";
  productID.innerHTML = `
        <div class="form-group">
            <label for="id">ID:</label>
            <input type="number" id="id" name="id" 
                   value="${product.id}" 
                   readonly 
                   style="background-color: #f0f0f0;">
        </div>
    `;

  // Precompila il form
  document.getElementById("name").value = product.name || "";
  document.getElementById("brand").value = product.brand || "";
  document.getElementById("ram").value = product.ram || "";
  document.getElementById("rom").value = product.rom || "";
  document.getElementById("camera").value = product.camera || "";
  document.getElementById("battery").value = product.battery || "";
  document.getElementById("price").value = product.price || "";
  document.getElementById("quantity").value = product.quantity || "";

  modal.style.display = "block";
}

function showOrders() {
  hiddenMain();
  tempBox.classList.remove("tempBox_inactive");
  tempBox.classList.add("tempBox");
  tempBox.innerHTML = `   
        <h1>Order list</h1>
        <div class="table-container">
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
                <tbody id="tempTbody"></tbody>
            </table>
            <div id="pagination" class="pagination"></div>
        </div>`;

  loadOrders(1); // Carica la prima pagina degli ordini
}

function loadOrders(page) {
  const tempTbody = document.getElementById("tempTbody");
  const pagination = document.getElementById("pagination");

  tempTbody.innerHTML =
    '<tr><td colspan="9" class="loading"><i class="fas fa-spinner fa-spin"></i> Loading orders...</td></tr>';

  fetch(`../back-end/load.php?action=orders&page=${page}`)
    .then((response) => response.json())
    .then((data) => {
      tempTbody.innerHTML = "";
      data.orders.forEach((order) => {
        const row = document.createElement("tr");
        row.innerHTML = `
                    <td>${order.orderID}</td>
                    <td>${order.userID}</td>
                    <td>${order.username}</td>
                    <td>${order.productID}</td>
                    <td>${order.name}</td>
                    <td>${order.quantity}</td>
                    <td>$${order.total_price}</td>
                    <td>${new Date(order.order_date).toLocaleDateString()}</td>
                    <!--Metodo degli oggetti Date in js che formatta la data secondo le impostazioni locali del browser/ambiente-->
                    <td>${order.shipping_address}</td>`;
        tempTbody.appendChild(row);
      });

      generatePagination(
        data.pagination.current_page,
        data.pagination.total_pages,
        "orders"
      );
    })
    .catch((error) => {
      console.error("Error:", error);
      tempTbody.innerHTML = `<tr><td colspan="9" class="error">Error loading orders: ${error.message}</td></tr>`;
    });
}
function showAnalytics() {
  hiddenMain();
  tempBox.classList.remove("tempBox_inactive");
  tempBox.classList.add("tempBox");

  fetch("../back-end/load.php?action=analytics")
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      if (data.status === "success") {
        tempBox.innerHTML = `   
        <h1>Analytics</h1>
            <div class="analyse">
                <div class="sales">
                    <div class="status">
                        <div class="info">
                            <h3>Total Sales</h3>
                            <h1 class="totalSales">$${data.totalSales}</h1>
                        </div>
                        <div class="progress">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                +81%
                            </div>
                        </div>
                    </div>
                </div>

                <!--Searches-->
                <div class="searches">
                    <div class="status">
                        <div class="info">
                            <h3>Searches</h3>
                            <h1 class="totalSearches">${data.totalSearches}</h1>
                        </div>
                        <div class="progress">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                -2%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
    `;
      } else {
        console.error("Error loading analytics:", data.message);
      }
    });

}

function showSettings() { }
