<template>
  <!-- Filter Modal -->
  <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-lg w-96">
      <!-- Modal Header -->
      <div class="flex justify-between items-center mb-3">
        <h3 class="text-lg font-semibold text-blue-900">Filter</h3>
        <button @click="$emit('close')" class="text-gray-500">✖</button>
      </div>

      <!-- Category Dropdown -->
      <div class="mb-2">
        <label class="block text-gray-700 font-medium mb-1">Category</label>
        <select v-model="filters.category" class="w-full border rounded-lg px-4 py-3 focus:ring focus:border-blue-400">
          <option v-for="category in user?.categories || []" :key="category.id" :value="category.id">{{
            category.category_name }}</option>
        </select>
      </div>

      <!-- Staus Dropdown -->

      <div class="mb-2">
        <label class="block text-gray-700 font-medium mb-1">Status</label>
        <select v-model="filters.status" class="w-full border rounded-lg px-4 py-3 focus:ring focus:border-blue-400">
          <option v-for="(status, index) in user?.task_status || []" :key="index" :value="status">{{ status }}</option>
        </select>
      </div>

      <!-- Buttons -->
      <div class="flex justify-end gap-2 mt-4">
        <button @click="resetFilters" class="px-4 py-2 border rounded">Reset</button>
        <button @click="applyFilters" class="px-4 py-2 bg-blue-900 text-white rounded">Apply</button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, watch } from "vue";
import { useAuthStore } from "@/store/AuthStore";
export default {
  props: {
    showModal: Boolean,
    filter: {
      type: Object,
      default: () => ({ category: "", status: "" })
    }
  },
  setup(props, { emit }) {
    const authStore = useAuthStore();
    const user = authStore.getUser;

    // Reactive filters
    const filters = ref({
      category: "",
      status: "",
    });

    // Watch prop filter changes and update local filters
    watch(() => props.filter,(newFilter) => {
        filters.value = newFilter ? { ...newFilter } : { category: "", status: "" };
      },
      { immediate: true }
    );

    const applyFilters = () => {
      emit("applyFilters", filters.value);
    };

    const resetFilters = () => {
      filters.value = { category: "", status: "" };
    };

    return {
      user,
      filters,
      applyFilters,
      resetFilters,
    };
  },
};
</script>
