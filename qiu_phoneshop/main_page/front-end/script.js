
document.addEventListener('DOMContentLoaded', () => {
    const profileContainer = document.querySelector('.user-btn');
    const profileOptions = document.querySelector('.profile-options');
    let hoverTimeout;
    // event listeners quando il mouse entra nel profilo
    profileContainer.addEventListener('mouseenter', () => {
        clearTimeout(hoverTimeout);
        profileOptions.classList.add('show');
    });
    // quando il mouse esce dal profilo
    profileContainer.addEventListener('mouseleave', () => {
        hoverTimeout = setTimeout(() => {
            profileOptions.classList.remove('show');
        }, 300);
    });
    // quando il mouse entra nel profile options
    profileOptions.addEventListener('mouseenter', () => {
        clearTimeout(hoverTimeout);
        profileOptions.classList.add('show');
    })
    profileOptions.addEventListener('mouseleave', () => {
        hoverTimeout = setTimeout(() => {
            profileOptions.classList.remove('show');
        }, 200);
    });
    // chiude il profile options se clicco fuori
    document.addEventListener('click', (e) => {
        if (!profileContainer.contains(e.target)) {
            profileOptions.classList.remove('show');
        }
    });
    // animazione del aggiungi al carrello
    document.addEventListener('click', (e) => {
        if (e.target.closest('.add-to-cart')) {
            const button = e.target.closest('.add-to-cart');
            const icon = button.querySelector('i');
            if (icon) {
                icon.classList.replace('fa-cart-plus', 'fa-check-circle');
                button.disabled = true;
                setTimeout(() => {
                    icon.classList.replace('fa-check-circle', 'fa-cart-plus');
                    button.disabled = false;
                }, 700);
            }
        }
    });

    // barra di ricerca
    const searchInput = document.getElementById("search-input");
    const searchBtn = document.getElementById('search-btn');

    // viene eseguita la funzione quando clicchiamo sull' icona di ricerca
    searchBtn.addEventListener('click', performSearch);

    // funzione che viene eseguita quando premiamo il tasto invio
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    // funzione che viene eseguita quando clicchiamo sul bottone di ricerca
    function performSearch() {
        // ottenere il valore della barra di ricerca
        // trim() è un metodo che rimuove gli spazi bianchi all'inizio e alla fine di una stringa
        const query = searchInput.value.trim();
        if (query !== '') {
            // AJAX chiede al server di cercare i prodotti
            // encodeURIComponent è una funzione che codifica una stringa in un formato valido per l'URL
            fetch('../back-end/search_products.php?query=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    // aggiorna la griglia dei prodotti con i risultati della ricerca
                    updateProductGrid(data, query);
                })
                .catch(error => console.error('Errore:', error));
        } else {
            // se la barra di ricerca è vuota, mostra tutti i prodotti
            fetch('../back-end/search_products.php')
                .then(response => response.json())
                .then(data => {
                    updateProductGrid(data, '');
                })
                .catch(error => console.error('Errore:', error));
        }
    }

    // funzione per aggiornare la griglia dei prodotti
    function updateProductGrid(products, query) {
        const productsGrid = document.querySelector('.products-grid');

        // cancella i contenuti della griglia dei prodotti
        productsGrid.innerHTML = '';

        // se ci sono prodotti che corrispondono alla ricerca
        if (products.length > 0) {
            // crea un header per i risultati della ricerca
            if (query) {
                const searchHeader = document.createElement('div');
                searchHeader.className = 'search-results-header';
                searchHeader.innerHTML = `<h2>Risultati per: "${query}"</h2>`;
                productsGrid.before(searchHeader);
                // rimuovi eventuali header precedenti
                const oldHeaders = document.querySelectorAll('.search-results-header');
                if (oldHeaders.length > 1) {
                    for (let i = 0; i < oldHeaders.length - 1; i++) {
                        oldHeaders[i].remove();
                    }
                }
            } else {
                // rimuovi l'header se non ci sono risultati
                const oldHeaders = document.querySelectorAll('.search-results-header');
                oldHeaders.forEach(header => header.remove());
            }
            // crea un articolo per ogni prodotto
            products.forEach(product => {
                const productCard = document.createElement('article');
                productCard.className = 'product-card';
                productCard.innerHTML = `
                    <div class="product-info">
                        <h3>${product.name}</h3>
                        <p class="product-price">€${product.price}</p>
                        <div class="product-actions">
                            <button class="quick-view" onclick="showDesc(${product.id})"><i class="fas fa-eye"></i></button>
                            <button class="add-to-cart" onclick="addItem(${product.id})">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>`;

                productsGrid.appendChild(productCard);
            });
           
        } else {
            // se non ci sono prodotti che corrispondono alla ricerca
            productsGrid.innerHTML = `
                <div class="no-results">
                    <h3>Nessun prodotto trovato per: "${query}"</h3>
                    <button class="cta-btn" onclick="document.getElementById('search-input').value='';
                    document.getElementById('search-btn').click();">
                    Mostra tutti i prodotti
                    </button>
                </div>`;
        }
    }
});
// aggiunge un prodotto al carrello
function addItem(productId) {
    fetch('../back-end/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'product_id=' + encodeURIComponent(productId)
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                // aggiorna il numero totale dei prodotti del carrello
                let cartCount = document.querySelector('.cart-count');
                cartCount.textContent = parseInt(cartCount.textContent) + 1;
            } else {
                alert("Errore: " + data.message);
            }
        })
        .catch(error => console.error('Errore:', error));
}
// mostra la descrizione del prodotto
function showDesc(productId) {
    fetch('../back-end/display_product.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'product_id=' + encodeURIComponent(productId)
    })
        .then(response => response.json())
        .then(data => {
            showDescInGrid(data);
        })
}

function showDescInGrid(productDesc) {
    const productsGrid = document.querySelector('.products-grid');

    // cancella i contenuti della griglia dei prodotti
    productsGrid.innerHTML = '';

    // creare un div per la visualizzazione dei dettagli del prodotto
    const productDetailContainer = document.createElement('div');
    productDetailContainer.className = 'product-detail-view';

    productDesc.forEach(product => {
        productDetailContainer.innerHTML = `
                    <div class="product-detail-header">
                        <h2>${product.name}</h2>
                        <button class="back-btn" onclick="document.getElementById('search-input').value='';
                            document.getElementById('search-btn').click();">
                            <i class="fas fa-arrow-left"></i> Torna ai prodotti
                        </button>
                    </div>
                    
                    <div class="product-detail-content">
                        <div class="product-detail-image">
                            <!-- You would replace this with actual product image -->
                            <div class="placeholder-image">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                        </div>
                        
                        <div class="product-detail-specs">
                            <div class="specs-grid">
                                <div class="spec-item">
                                    <i class="fas fa-memory"></i>
                                    <div>
                                        <span class="spec-label">RAM</span>
                                        <span class="spec-value">${product.ram} GB</span>
                                    </div>
                                </div>
                                
                                <div class="spec-item">
                                    <i class="fas fa-hdd"></i>
                                    <div>
                                        <span class="spec-label">Memoria</span>
                                        <span class="spec-value">${product.rom} GB</span>
                                    </div>
                                </div>
                                
                                <div class="spec-item">
                                    <i class="fas fa-battery-full"></i>
                                    <div>
                                        <span class="spec-label">Batteria</span>
                                        <span class="spec-value">${product.battery} mAh</span>
                                    </div>
                                </div>
                                
                                <div class="spec-item">
                                    <i class="fas fa-camera"></i>
                                    <div>
                                        <span class="spec-label">Fotocamera</span>
                                        <span class="spec-value">${product.camera} Mpx</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="product-detail-price">
                                <span class="price-label">Prezzo:</span>
                                <span class="price-value">€${product.price}</span>
                            </div>
                            
                            <button class="add-to-cart detail-btn" onclick="addItem(${product.id})">
                                <i class="fas fa-cart-plus"></i> Aggiungi al carrello
                            </button>
                        </div>
                    </div>
                `;
    });

    productsGrid.appendChild(productDetailContainer);
}