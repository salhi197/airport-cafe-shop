<template>
    <div class="pos">
        <h2>Point of Sale</h2>
        <div v-if="loading" class="loading">Loading products...</div>
        <div v-else-if="error" class="error">Error loading products: {{ error }}</div>
        <div class="card-deck">
            <div v-for="product in products" :key="product.id" class="card">
                <img :src="product.image_url" class="card-img-top" alt="Product Image">
                <div class="card-body">
                    <h5 class="card-title">{{ product.name }}</h5>
                    <p class="card-text">{{ product.description }}</p>
                    <p class="card-text">Price: {{ product.price }}</p>
                    <button
                        class="btn btn-primary"
                        @click="addToCart(product)"
                        :disabled="isInCart(product)"
                    >
                        Add to Cart
                    </button>
                    <button
                        class="btn btn-danger"
                        @click="removeFromCart(product)"
                        :disabled="!isInCart(product)"
                    >
                        Remove from Cart
                    </button>
                    <p v-if="isInCart(product)">Quantity: {{ getQuantity(product) }}</p>
                </div>
            </div>
        </div>
        <button
            class="btn btn-success mt-4"
            @click="submitOrder"
            :disabled="cart.length === 0"
        >
            Submit Order
        </button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            products: [],
            cart: [], // Array to store products with quantities
            loading: true,
            error: null
        };
    },
    created() {
        this.fetchProducts();
    },
    methods: {
        fetchProducts() {
            fetch('/api/products')
                .then(response => response.json())
                .then(data => {
                    this.products = data;
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
                    this.error = 'Unable to load products.';
                    this.loading = false;
                });
        },
        addToCart(product) {
            const existingProduct = this.cart.find(p => p.id === product.id);
            if (existingProduct) {
                existingProduct.quantity += 1; // Increment quantity
            } else {
                this.cart.push({ ...product, quantity: 1 }); // Add new product with quantity
            }
        },
        removeFromCart(product) {
            const index = this.cart.findIndex(p => p.id === product.id);
            if (index !== -1) {
                if (this.cart[index].quantity > 1) {
                    this.cart[index].quantity -= 1; // Decrement quantity
                } else {
                    this.cart.splice(index, 1); // Remove product if quantity is 1
                }
            }
        },
        isInCart(product) {
            return this.cart.some(p => p.id === product.id);
        },
        getQuantity(product) {
            const item = this.cart.find(p => p.id === product.id);
            return item ? item.quantity : 0;
        },
        submitOrder() {
            fetch('/api/sell/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ products: this.cart })
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Order submitted:', data);
                    // Handle successful submission (e.g., redirect, show message)
                    this.cart = []; // Clear cart
                })
                .catch(error => {
                    console.error('Error submitting order:', error);
                });
        }
    }
};
</script>

<style scoped>
/* Scoped styles for the component */
.card-deck {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
}

.card {
    width: 18rem;
    margin-bottom: 20px;
}

.loading, .error {
    text-align: center;
    font-size: 1.2rem;
    color: #333;
}

.btn-primary:disabled, .btn-danger:disabled {
    opacity: 0.6;
}

.btn-success {
    margin-top: 20px;
}
</style>
