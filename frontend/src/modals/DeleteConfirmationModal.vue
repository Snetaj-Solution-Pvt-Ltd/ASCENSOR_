<template>
  <div v-if="show" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Are you sure you want to delete this task?</h2>
      <div class="flex justify-end space-x-4">
        <button @click="cancelDelete" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
        <button @click="confirmDelete" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    show: Boolean,
    taskId: Number,
  },
  methods: {
    cancelDelete() {
      this.$emit('close');
    },
    confirmDelete() {
      this.$emit('confirm', this.taskId);
    }
  }
}
</script>
