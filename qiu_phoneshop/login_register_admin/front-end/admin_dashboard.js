document.addEventListener("DOMContentLoaded", () => {
  // inizializzare dashboard
  loadAnalyticsData();
  initializeSidebar();
  initializeDarkMode();
  check();


});

function check() {
  // Check if a section is specified in the URL hash, otherwise default to dashboard
  const hash = window.location.hash.substring(1) || 'dashboard';
  // Find the matching sidebar link and set it as active
  const activeLink = document.querySelector(`.sidebar a#${hash}`);
  if (activeLink) {
    document.querySelectorAll(".sidebar a").forEach(l => l.classList.remove("active"));
    activeLink.classList.add("active");
  }
  // Load the appropriate section
  handleSectionChange(hash);
}
// Admin Account Deletion Functions
function showAdminDeleteModal(e) {
  e.preventDefault();
  document.getElementById('adminDeleteModal').style.display = 'flex';
}

function closeAdminDeleteModal() {
  document.getElementById('adminDeleteModal').style.display = 'none';
  document.querySelector('.delete').classList.remove("active");
  const dashboard = document.getElementById('dashboard');
  dashboard.classList.add("active");

}

function deleteAdminAccount() {
  fetch('../back-end/delete_account.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    }
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Account eliminato con successo');
        window.location.href = '../../login_register_user/logout.php';
      } else {
        alert('Errore durante l\'eliminazione dell\'account');
      }
    })
}

// Close modal when clicking outside
document.getElementById('adminDeleteModal').addEventListener('click', function (e) {
  if (e.target === this) {
    closeAdminDeleteModal();
  }
});

// ===== UTILITY FUNCTIONS =====
// Generic fetch wrapper with error handling
const fetchData = async (url, options = {}) => {
  try {
    const response = await fetch(url, { credentials: 'include', ...options });
    if (!response.ok) {
      throw new Error(`Network error: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error(`Error fetching data from ${url}:`, error);
    showNotification('error', `Failed to load data: ${error.message}`);
    return { status: 'error', message: error.message };
  }
};

// Show notification messages
const showNotification = (type, message) => {
  const notification = document.createElement('div');
  notification.className = `notification ${type}`;
  notification.textContent = message;
  document.body.appendChild(notification);

  setTimeout(() => notification.remove(), 5000);
};

// ===== UI SETUP =====
const initializeSidebar = () => {
  const sideMenu = document.querySelector("aside");
  const menuBtn = document.getElementById("menu-btn");
  const closeBtn = document.getElementById("close-btn");

  menuBtn.addEventListener("click", () => sideMenu.style.display = "block");
  closeBtn.addEventListener("click", () => sideMenu.style.display = "none");

  // Set up sidebar navigation
  const sidebarLinks = document.querySelectorAll(".sidebar a");
  sidebarLinks.forEach(link => {
    link.addEventListener("click", function (e) {
      // Don't apply to logout link
      if (this.id === "logout") return;

      e.preventDefault();

      // Update URL hash to reflect current section
      window.location.hash = this.id;

      // Remove active class from all links
      sidebarLinks.forEach(l => l.classList.remove("active"));
      // Add active class to clicked link
      this.classList.add("active");

      // Handle section display based on ID
      const sectionId = this.id;
      handleSectionChange(sectionId);
    });
  });
};

const initializeDarkMode = () => {
  const darkMode = document.querySelector(".dark-mode");
  darkMode.addEventListener("click", () => {
    document.body.classList.toggle("dark-mode-variables");
    darkMode.querySelector("span:nth-child(1)").classList.toggle("active");
    darkMode.querySelector("span:nth-child(2)").classList.toggle("active");
  });
};

// ===== SECTION MANAGEMENT =====
const handleSectionChange = (sectionId) => {
  const main = document.querySelector("main");
  const tempBox = document.getElementById("tempBox");

  // Default to showing main and hiding tempBox
  main.style.display = "none";
  tempBox.classList.remove("tempBox_inactive");
  tempBox.classList.add("tempBox");

  // Handle different sections
  switch (sectionId) {
    case "dashboard":
      main.style.display = "block";
      tempBox.classList.remove("tempBox");
      tempBox.classList.add("tempBox_inactive");
      loadAnalyticsData();
      break;
    case "users":
      renderUsersSection(tempBox);
      loadPagedData('users', 1);
      break;
    case "products":
      renderProductsSection(tempBox);
      loadPagedData('products', 1);
      break;
    case "orders":
      renderOrdersSection(tempBox);
      loadPagedData('orders', 1);
      break;
    case "analytics":
      renderAnalyticsSection(tempBox);
      break;
    case "settings":
      renderSettingsSection(tempBox);
      break;
    case "tickets":
      renderTicketsSection(tempBox);
      break;
    case "reports":
      renderReportsSection(tempBox);
      break;
    default:
      main.style.display = "block";
      tempBox.classList.remove("tempBox");
      tempBox.classList.add("tempBox_inactive");
      break;
  }
};

// ===== DATA LOADING =====
const loadAnalyticsData = async () => {
  const data = await fetchData('../back-end/load.php?action=analytics');

  if (data.status === 'success') {
    document.querySelector('.totalSales').innerText = "$" + data.totalSales.toLocaleString();
    document.querySelector('.totalSearches').innerText = data.totalSearches.toLocaleString();
  }
};

// Generic function to load paginated data
const loadPagedData = async (dataType, page = 1) => {
  const tempTbody = document.getElementById("tempTbody");

  if (!tempTbody) {
    console.error(`Cannot find tbody element for ${dataType}`);
    return;
  }

  // Show loading state
  tempTbody.innerHTML = `<tr><td colspan="10" class="loading">
    <i class="material-icons-sharp">hourglass_empty</i> Loading ${dataType}...</td></tr>`;

  const data = await fetchData(`../back-end/load.php?action=${dataType}&page=${page}`);

  // Log data response for debugging
  console.log(`Loaded ${dataType} data:`, data);

  // Handle error state
  if (data.status === 'error') {
    tempTbody.innerHTML = `<tr><td colspan="10" class="error">
      Error loading ${dataType}: ${data.message}</td></tr>`;
    return;
  }

  // Clear table body
  tempTbody.innerHTML = '';

  // Use appropriate renderer based on data type
  switch (dataType) {
    case 'users':
      if (data.users && data.users.length > 0) {
        renderUserRows(data.users, tempTbody);
      } else {
        tempTbody.innerHTML = `<tr><td colspan="10">No users found</td></tr>`;
      }
      break;
    case 'products':
      if (data.products && data.products.length > 0) {
        renderProductRows(data.products, tempTbody);
      } else {
        tempTbody.innerHTML = `<tr><td colspan="10">No products found</td></tr>`;
      }
      break;
    case 'orders':
      if (data.orders && data.orders.length > 0) {
        renderOrderRows(data.orders, tempTbody);
      } else {
        tempTbody.innerHTML = `<tr><td colspan="10">No orders found</td></tr>`;
      }
      break;
  }

  // Generate pagination controls
  if (data.pagination) {
    console.log(`Generating pagination for ${dataType}:`, data.pagination);
    generatePagination(
      data.pagination.current_page,
      data.pagination.total_pages,
      dataType
    );
  } else {
    console.error(`No pagination data for ${dataType}`);
    document.getElementById("pagination").innerHTML = "";
  }
};

// ===== SECTION RENDERERS =====
const renderUsersSection = (container) => {
  container.innerHTML = `   
    <h1>User list</h1>
    <div class="table-container">
      <table class="tempTable">
        <thead>
          <tr class="tempData">
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Shipping address</th>
            <th>Registration date</th>
          </tr>
        </thead>
        <tbody id="tempTbody"></tbody>
      </table>
      <div id="pagination" class="pagination"></div>
    </div>`;
};

const renderProductsSection = (container) => {
  container.innerHTML = `
    <div class="title-btn">
      <h1>Product list</h1>
      <div class="product-actions">
        <button id="addProductBtn" class="btn">Add Product</button>
        <button id="updateProductBtn" class="btn">Update Product</button>
      </div>
    </div>  
    
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
    </div>`;

  // Initialize the product modal functionality
  initProductModal();
};

const renderOrdersSection = (container) => {
  container.innerHTML = `
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
};

const renderAnalyticsSection = async (container) => {
  const data = await fetchData('../back-end/load.php?action=analytics');

  if (data.status === 'success') {
    container.innerHTML = `
      <h1>Analytics</h1>
      <div class="analyse">
        <div class="sales">
          <div class="status">
            <div class="info">
              <h3>Total Sales</h3>
              <h1 class="totalSales">$${data.totalSales.toLocaleString()}</h1>
            </div>
            <div class="progress">
              <svg>
                <circle cx="38" cy="38" r="36"></circle>
              </svg>
              <div class="percentage">+81%</div>
            </div>
          </div>
        </div>

        <div class="visits">
          <div class="status">
            <div class="info">
              <h3>Site visits</h3>
              <h1>12,345</h1>
            </div>
            <div class="progress">
              <svg>
                <circle cx="38" cy="38" r="36"></circle>
              </svg>
              <div class="percentage">+12%</div>
            </div>
          </div>
        </div>

        <div class="searches">
          <div class="status">
            <div class="info">
              <h3>Searches</h3>
              <h1 class="totalSearches">${data.totalSearches.toLocaleString()}</h1>
            </div>
            <div class="progress">
              <svg>
                <circle cx="38" cy="38" r="36"></circle>
              </svg>
              <div class="percentage">-2%</div>
            </div>
          </div>
        </div>
      </div>`;
  } else {
    container.innerHTML = `<h1>Analytics</h1><p>Error loading analytics data: ${data.message}</p>`;
  }
};

const renderSettingsSection = (container) => {
  container.innerHTML = `<h1>Settings</h1><p>Settings functionality coming soon</p>`;
};

const renderTicketsSection = (container) => {
  container.innerHTML = `<h1>Tickets</h1><p>Ticket management functionality coming soon</p>`;
};

const renderReportsSection = (container) => {
  container.innerHTML = `<h1>Reports</h1><p>Reports functionality coming soon</p>`;
};

// ===== DATA ROW RENDERERS =====
const renderUserRows = (users, tbody) => {
  if (!users || users.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5">No users found</td></tr>';
    return;
  }

  users.forEach(user => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td class="columnUser">${user.id}</td>
      <td class="columnUser">${user.username}</td>
      <td class="columnUser">${user.email}</td>
      <td class="columnUser">${user.shipping_address}</td>
      <td class="columnUser">${user.registration_date}</td>`;
    tbody.appendChild(row);
  });
};

const renderProductRows = (products, tbody) => {
  if (!products || products.length === 0) {
    tbody.innerHTML = '<tr><td colspan="9">No products found</td></tr>';
    return;
  }

  products.forEach(product => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${product.id}</td>
      <td>${product.name}</td>
      <td>${product.brand}</td>
      <td>$${product.price}</td>
      <td>${product.quantity}</td>
      <td>${product.ram} GB</td>
      <td>${product.rom} GB</td>
      <td>${product.camera} MP</td>
      <td>${product.battery} mAh</td>`;
    tbody.appendChild(row);

    // Add click handler for product update
    row.addEventListener('click', () => openUpdateModal(product));
  });
};

const renderOrderRows = (orders, tbody) => {
  if (!orders || orders.length === 0) {
    tbody.innerHTML = '<tr><td colspan="9">No orders found</td></tr>';
    return;
  }

  orders.forEach(order => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${order.orderID || order.id}</td>
      <td>${order.userID || order.user_id}</td>
      <td>${order.username}</td>
      <td>${order.productID || order.product_id}</td>
      <td>${order.name || order.product_name}</td>
      <td>${order.quantity}</td>
      <td>$${order.total_price}</td>
      <td>${formatDate(order.order_date)}</td>
      <td>${order.shipping_address}</td>`;
    tbody.appendChild(row);
  });
};

// Format date helper
const formatDate = (dateString) => {
  const date = new Date(dateString);
  if (isNaN(date.getTime())) return dateString; // Return original if invalid
  return date.toLocaleDateString();
};

// ===== PAGINATION =====
const generatePagination = (currentPage, totalPages, contentType) => {
  const pagination = document.getElementById("pagination");
  if (!pagination) {
    console.error("Pagination container not found");
    return;
  }

  if (totalPages <= 1) {
    pagination.innerHTML = "";
    return;
  }

  console.log(`Generating pagination for ${contentType}. Current page: ${currentPage}, Total pages: ${totalPages}`);

  let html = "";
  const maxVisiblePages = 5;
  let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
  let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

  if (endPage - startPage + 1 < maxVisiblePages) {
    startPage = Math.max(1, endPage - maxVisiblePages + 1);
  }

  // Previous link
  if (currentPage > 1) {
    html += `<a href="#" class="page-link page-nav" data-page="${currentPage - 1}" data-type="${contentType}">
              <span class="material-icons-sharp">chevron_left</span> Previous
            </a>`;
  } else {
    html += `<span class="page-link page-nav disabled">
              <span class="material-icons-sharp">chevron_left</span> Previous
            </span>`;
  }

  // Page numbers
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

  // Next link
  if (currentPage < totalPages) {
    html += `<a href="#" class="page-link page-nav" data-page="${currentPage + 1}" data-type="${contentType}">
              Next <span class="material-icons-sharp">chevron_right</span>
            </a>`;
  } else {
    html += `<span class="page-link page-nav disabled">
              Next <span class="material-icons-sharp">chevron_right</span>
            </span>`;
  }

  pagination.innerHTML = html;

  // Add event listeners to page links
  document.querySelectorAll(".page-link[data-page]").forEach(link => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const page = parseInt(this.dataset.page);
      const type = this.dataset.type;
      console.log(`Clicked on page ${page} for ${type}`);

      loadPagedData(type, page);

      document.querySelector(".table-container").scrollIntoView({
        behavior: "smooth"
      });
    });
  });
};

// ===== PRODUCT MODALS =====
const initProductModal = () => {
  const modal = document.getElementById("productModal");
  if (!modal) return;

  const form = document.getElementById("addProductForm");
  const modalTitle = modal.querySelector("h2");
  const closeBtn = document.querySelector(".close-modal");
  const submitBtn = document.querySelector(".submit-btn");
  const productID = document.getElementById("productID");

  const addBtn = document.getElementById("addProductBtn");
  const updateBtn = document.getElementById("updateProductBtn");

  // Add Product button functionality
  if (addBtn) {
    addBtn.onclick = function () {
      modalTitle.textContent = "Add New Product";
      form.action = "../back-end/add_product.php";
      submitBtn.textContent = "Add Product";
      productID.style.display = "none";
      productID.innerHTML = "";
      form.reset();
      modal.style.display = "block";
    };
  }

  // Update Product button functionality
  if (updateBtn) {
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
  }

  // Close modal functionality
  if (closeBtn) {
    closeBtn.onclick = () => modal.style.display = "none";
    window.onclick = event => {
      if (event.target == modal) modal.style.display = "none";
    };
  }

  // Form submission handling
  if (form) {
    form.addEventListener("submit", async function (e) {
      e.preventDefault();
      const formData = new FormData(this);

      try {
        const response = await fetch(this.action, {
          method: "POST",
          body: formData,
          credentials: "include"
        });

        const data = await response.json();

        if (data.status === "success") {
          showNotification("success", data.message);

          // Reload products list with current page
          const currentPage = document.querySelector(".page-link.active")?.textContent || 1;
          loadPagedData("products", parseInt(currentPage));

          // Close modal
          modal.style.display = "none";
        } else {
          showNotification("error", data.message || "Unknown error");
        }
      } catch (error) {
        console.error("Error:", error);
        showNotification("error", error.message || "Connection error");
      }
    });
  }
};

const openUpdateModal = (product) => {
  const modal = document.getElementById("productModal");
  if (!modal) return;

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
             readonly ">
    </div>
  `;

  // Populate form with product data
  document.getElementById("name").value = product.name || "";
  document.getElementById("brand").value = product.brand || "";
  document.getElementById("ram").value = product.ram || "";
  document.getElementById("rom").value = product.rom || "";
  document.getElementById("camera").value = product.camera || "";
  document.getElementById("battery").value = product.battery || "";
  document.getElementById("price").value = product.price || "";
  document.getElementById("quantity").value = product.quantity || "";

  modal.style.display = "block";
};
