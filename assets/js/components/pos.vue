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
                    <p class="card-text">Price: {{ product.price.toFixed(2) }}</p>
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

        <div class="mt-4">
            <div>
                <label for="discount">Discount (%)</label>
                <input
                    id="discount"
                    type="number"
                    v-model.number="discount"
                    min="0"
                    max="100"
                />
            </div>
            <p>Total Amount: {{ totalAmount.toFixed(2) }}</p>
            <button
                class="btn btn-success mt-2"
                @click="submitOrder"
                :disabled="cart.length === 0"
            >
                Submit Order
            </button>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            products: [],
            cart: [], // Array to store products with quantities
            discount: 0, // Discount percentage
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
                body: JSON.stringify({
                    products: this.cart,
                    discount: this.discount
                })
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Order submitted:', data);
                    // Handle successful submission (e.g., redirect, show message)
                    this.cart = []; // Clear cart
                    this.discount = 0; // Reset discount
                })
                .catch(error => {
                    console.error('Error submitting order:', error);
                });
        }
    },
    computed: {
        totalAmount() {
            const subtotal = this.cart.reduce((sum, product) => {
                return sum + (product.price * product.quantity);
            }, 0);
            const discountAmount = (this.discount / 100) * subtotal;
            return subtotal - discountAmount;
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

.mt-4 {
    margin-top: 20px;
}

input[type="number"] {
    width: 100px;
    margin-left: 10px;
}
</style>
