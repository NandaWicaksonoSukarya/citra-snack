    // =====================================================
    // DATA PRODUK
    // =====================================================

    let products = [];


    // =====================================================
    // VARIABEL
    // =====================================================

    let selectedCategory = "Semua";

    let cart =
        JSON.parse(localStorage.getItem("cart")) || [];


    // =====================================================
    // AMBIL PRODUK DARI FIRESTORE
    // =====================================================

    async function loadProducts() {
        try {
            const response = await fetch("./api/products.php");

            if (!response.ok) {
                throw new Error("Gagal menghubungi server.");
            }

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message);
            }

            products = result.data.map(item => ({
                id: Number(item.ID_Produk),
                name: item.Nama_Produk,
                category: item.Kategori || "",
                price: Number(item.Harga),
                image: item.Foto_Produk || "",
                description: item.Deskripsi || "",
                stock: Number(item.Stok)
            }));

            console.log("Produk dari MySQL:", products);

            displayProducts();
            displayCart();

        } catch (error) {
            console.error("Gagal mengambil produk:", error);
        }
    }


    // =====================================================
    // FORMAT RUPIAH
    // =====================================================

    function formatRupiah(number) {

        return new Intl.NumberFormat("id-ID", {

            style: "currency",

            currency: "IDR",

            minimumFractionDigits: 0

        }).format(number);

    }


    // =====================================================
    // TAMPILKAN PRODUK
    // =====================================================

    function displayProducts() {

        const productContainer =
            document.getElementById("productContainer");

        const emptyProduct =
            document.getElementById("emptyProduct");

        const searchInput =
            document.getElementById("searchInput");


        const searchKeyword =
            searchInput.value
                .toLowerCase()
                .trim();


        const filteredProducts =
            products.filter(product => {

                const matchCategory =
                    selectedCategory === "Semua" ||
                    product.category === selectedCategory;


                const matchSearch =
                    product.name
                        .toLowerCase()
                        .includes(searchKeyword) ||

                    product.category
                        .toLowerCase()
                        .includes(searchKeyword);


                return matchCategory && matchSearch;

            });


        productContainer.innerHTML = "";


        // Jika tidak ada produk

        if (filteredProducts.length === 0) {

            emptyProduct.style.display = "block";

            return;

        }


        emptyProduct.style.display = "none";


        // Tampilkan produk

        filteredProducts.forEach(product => {

            const productCard =
                document.createElement("div");


            productCard.className =
                "col-md-6";


            productCard.innerHTML = `

                <div class="product-card">

                    <div class="product-image">

                        <img
                            src="${product.image}"
                            alt="${product.name}"
                            onerror="this.style.display='none';">

                    </div>


                    <div class="product-content">

                        <span class="product-category">
                            ${product.category}
                        </span>


                        <h3>
                            ${product.name}
                        </h3>


                        <p>
                            ${product.description}
                        </p>


                        <div class="product-bottom">

                            <strong>
                                ${formatRupiah(product.price)}
                            </strong>


                            <button
                                type="button"
                                class="add-cart-btn"
                                data-product-id="${product.id}">

                                <i class="bi bi-plus-lg"></i>

                                Tambah

                            </button>

                        </div>

                    </div>

                </div>

            `;


            // Tombol tambah ke cart

            const addButton =
                productCard.querySelector(
                    ".add-cart-btn"
                );


            addButton.addEventListener(
                "click",
                function () {

                    addToCart(product.id);

                }
            );


            productContainer.appendChild(
                productCard
            );

        });

    }


    // =====================================================
    // TAMBAH KE KERANJANG
    // =====================================================

    function addToCart(productId) {

        const product =
            products.find(
                item => item.id === productId
            );


        if (!product) {

            console.error(
                "Produk tidak ditemukan:",
                productId
            );

            return;

        }


        const existingItem =
            cart.find(
                item => item.id === productId
            );


        if (existingItem) {

            existingItem.quantity += 1;

        } else {

            cart.push({

                id: product.id,

                name: product.name,

                category: product.category,

                price: product.price,

                image: product.image,

                quantity: 1

            });

        }


        saveCart();

        displayCart();

    }


    // =====================================================
    // SIMPAN CART
    // =====================================================

    function saveCart() {

        localStorage.setItem(
            "cart",
            JSON.stringify(cart)
        );

    }


    // =====================================================
    // TAMPILKAN KERANJANG
    // =====================================================

    function displayCart() {

        const cartItems =
            document.getElementById("cartItems");

        const emptyCart =
            document.getElementById("emptyCart");

        const cartTotal =
            document.getElementById("cartTotal");


        cartItems.innerHTML = "";


        // Keranjang kosong

        if (cart.length === 0) {

            emptyCart.style.display = "block";

            cartTotal.textContent = "Rp0";

            return;

        }


        emptyCart.style.display = "none";


        let total = 0;


        cart.forEach(item => {

            const subtotal =
                item.price * item.quantity;


            total += subtotal;


            const cartItem =
                document.createElement("div");


            cartItem.className =
                "cart-item";


            cartItem.innerHTML = `

                <div class="cart-item-info">

                    <strong>
                        ${item.name}
                    </strong>

                    <span>
                        ${formatRupiah(item.price)}
                    </span>

                </div>


                <div class="cart-item-bottom">

                    <div class="quantity-control">

                        <button
                            type="button"
                            class="quantity-minus"
                            data-id="${item.id}">

                            −

                        </button>


                        <span>
                            ${item.quantity}
                        </span>


                        <button
                            type="button"
                            class="quantity-plus"
                            data-id="${item.id}">

                            +

                        </button>

                    </div>


                    <strong class="cart-subtotal">

                        ${formatRupiah(subtotal)}

                    </strong>


                    <button
                        type="button"
                        class="delete-cart"
                        data-id="${item.id}">

                        <i class="bi bi-trash"></i>

                    </button>

                </div>

            `;


            // Tombol -

            cartItem
                .querySelector(".quantity-minus")
                .addEventListener(
                    "click",
                    function () {

                        changeQuantity(
                            item.id,
                            -1
                        );

                    }
                );


            // Tombol +

            cartItem
                .querySelector(".quantity-plus")
                .addEventListener(
                    "click",
                    function () {

                        changeQuantity(
                            item.id,
                            1
                        );

                    }
                );


            // Tombol hapus

            cartItem
                .querySelector(".delete-cart")
                .addEventListener(
                    "click",
                    function () {

                        removeFromCart(
                            item.id
                        );

                    }
                );


            cartItems.appendChild(
                cartItem
            );

        });


        cartTotal.textContent =
            formatRupiah(total);

    }


    // =====================================================
    // UBAH JUMLAH
    // =====================================================

    function changeQuantity(
        productId,
        change
    ) {

        const item =
            cart.find(
                item => item.id === productId
            );


        if (!item) return;


        item.quantity += change;


        if (item.quantity <= 0) {

            cart =
                cart.filter(
                    item => item.id !== productId
                );

        }


        saveCart();

        displayCart();

    }


    // =====================================================
    // HAPUS PRODUK
    // =====================================================

    function removeFromCart(productId) {

        cart =
            cart.filter(
                item => item.id !== productId
            );


        saveCart();

        displayCart();

    }


    // =====================================================
    // KOSONGKAN KERANJANG
    // =====================================================

    document
        .getElementById("clearCart")
        .addEventListener(
            "click",
            function () {

                if (cart.length === 0) {

                    return;

                }


                const confirmClear =
                    confirm(
                        "Kosongkan semua isi keranjang?"
                    );


                if (!confirmClear) {

                    return;

                }


                cart = [];


                saveCart();

                displayCart();

            }
        );


    // =====================================================
    // FILTER KATEGORI
    // =====================================================

    document
        .querySelectorAll(".category-btn")
        .forEach(button => {

            button.addEventListener(
                "click",
                function () {

                    document
                        .querySelectorAll(
                            ".category-btn"
                        )
                        .forEach(btn => {

                            btn.classList.remove(
                                "active"
                            );

                        });


                    this.classList.add(
                        "active"
                    );


                    selectedCategory =
                        this.dataset.category;


                    displayProducts();

                }
            );

        });


    // =====================================================
    // SEARCH
    // =====================================================

    document
        .getElementById("searchInput")
        .addEventListener(
            "input",
            function () {

                displayProducts();

            }
        );


    // =====================================================
    // CHECKOUT
    // =====================================================

    document
        .getElementById("checkoutButton")
        .addEventListener(
            "click",
            function () {

                if (cart.length === 0) {

                    alert(
                        "Keranjang masih kosong. Silakan pilih menu terlebih dahulu."
                    );

                    return;

                }


                window.location.href =
                    "checkout.php";

            }
        );


    // =====================================================
    // JALANKAN SAAT HALAMAN DIBUKA
    // =====================================================

    loadProducts();