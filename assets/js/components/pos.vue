<!-- Pos.vue -->

<template>
    <div class="pos">
        <h2>Point of Sale</h2>
        <div class="card-deck">
            <div v-for="product in products" :key="product.id" class="card">
                <img :src="product.image_url" class="card-img-top" alt="Product Image">
                <div class="card-body">
                    <h5 class="card-title">{{ product.name }}</h5>
                    <p class="card-text">{{ product.description }}</p>
                    <p class="card-text">Price: {{ product.price }}</p>
                    <button class="btn btn-primary">Add to Cart</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            products: []
        };
    },
    created() {
        this.fetchProducts();
    },
    methods: {
        fetchProducts() {
            // Replace with actual API endpoint to fetch products from Symfony backend
            fetch('/api/products')
                .then(response => response.json())
                .then(data => {
                    this.products = data;
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
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
</style>
