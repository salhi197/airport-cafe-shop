<template>
  <div class="sells">
    <h2>All Sales</h2>
    <div v-if="loading" class="loading">Loading sales...</div>
    <div v-else-if="error" class="error">Error loading sales: {{ error }}</div>
    <div v-else>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Products</th>
            <th>Discount (%)</th>
            <th>Total Amount</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sell in sells" :key="sell.id">
            <td>{{ sell.id }}</td>
            <td>
              <ul>
                <li v-for="product in sell.products" :key="product.id">
                  {{ product.name }} (x{{ product.quantity }})
                </li>
              </ul>
            </td>
            <td>{{ sell.discount }}</td>
            <td>{{ calculateTotalAmount(sell).toFixed(2) }}</td>
            <td>{{ new Date(sell.date).toLocaleString() }}</td>
            <td>
              <button class="btn btn-danger" @click="removeSell(sell.id)">
                Remove
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      sells: [],
      loading: true,
      error: null,
    };
  },
  created() {
    this.fetchSells();
  },
  methods: {
    fetchSells() {
      fetch('/api/sells')
        .then((response) => response.json())
        .then((data) => {
          this.sells = data;
          this.loading = false;
        })
        .catch((error) => {
          console.error('Error fetching sells:', error);
          this.error = 'Unable to load sells.';
          this.loading = false;
        });
    },
    calculateTotalAmount(sell) {
      const subtotal = sell.products.reduce((sum, product) => {
        return sum + product.price * product.quantity;
      }, 0);
      const discountAmount = (sell.discount / 100) * subtotal;
      return subtotal - discountAmount;
    },
    removeSell(sellId) {
      fetch(`/api/sell/remove/${sellId}`, {
        method: 'DELETE',
      })
        .then((response) => {
          if (response.ok) {
            this.sells = this.sells.filter((sell) => sell.id !== sellId);
          } else {
            console.error('Failed to remove sell');
          }
        })
        .catch((error) => {
          console.error('Error removing sell:', error);
        });
    },
  },
};
</script>

<style scoped>
.loading,
.error {
  text-align: center;
  font-size: 1.2rem;
  color: #333;
}

.table {
  width: 100%;
  margin-top: 20px;
}

.btn-danger {
  color: #fff;
  background-color: #dc3545;
}
</style>
