<template>
  <div class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-3/5 max-w-4xl max-h-screen flex flex-col">
      
      <!-- Modal Header -->
      <div class="sticky top-0 bg-white z-10 p-4 border-b flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-blue-900">Add Task</h2>
      </div>
       <!-- Related Forms Dropdown -->
      

      <!-- Modal Body -->
    <div class="overflow-y-auto max-h-[70vh] p-6 space-y-6 scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[400px] overflow-y-auto
  [&::-webkit-scrollbar]:w-2
  [&::-webkit-scrollbar-track]:bg-gray-100
  [&::-webkit-scrollbar-thumb]:bg-gray-300
  dark:[&::-webkit-scrollbar-track]:bg-neutral-700
  dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">

        <form @submit.prevent="submitForm">
          <!-- Task Name -->
          <div class="mb-2">
            <label class="block text-gray-700 font-medium mb-1">Task Name <span class="text-red-500">*</span></label>
            <input type="text" v-model="task.name" class="w-full border rounded-lg px-4 py-3 focus:ring focus:border-blue-400" required />
          </div>

         <!-- Task Assigned -->
          <div class="mb-2">
            <label class="block text-gray-700 font-medium mb-1">Task Assigned</label>
            <select v-model="task.assigned" class="w-full border rounded-lg px-4 py-3 focus:ring focus:border-blue-400">
              <option :value="user.id" v-for="(user) in getUserList" :key="user.id">{{user.name}}</option>
            </select>
          </div>
<!-- Task Status -->
<div class="mb-2">
            <label class="block text-gray-700 font-medium mb-1">Task Status</label>
            <select v-model="task.status" class="w-full border rounded-lg px-4 py-3 focus:ring focus:border-blue-400">
              <option :value="status"  v-for="(status, index) in user.task_status" :key="index">{{status}}</option>
            </select>
          </div>

          <!-- Start Date & Due Date -->
          <div class="mb-2">
              <label class="block text-gray-700 font-medium mb-1">Due Date</label>
              <input type="date" v-model="task.deadline" :min="minDate" class="w-full border rounded-lg px-4 py-3 focus:ring focus:border-blue-400" />
            </div>

           <!-- Task Category -->
          <div class="mb-2">
            <label class="block text-gray-700 font-medium mb-1">Category</label>
            <select v-model="task.category_id" class="w-full border rounded-lg px-4 py-3 focus:ring focus:border-blue-400">
              <option :value="category.id"  v-for="(category) in user.categories" :key="category.id">{{category.category_name}}</option>
            </select>
          </div>
          <!-- Description -->
          <div class="mb-2">
            <label class="block text-gray-700 font-medium mb-1">Description</label>
            <textarea v-model="task.description" class="w-full border rounded-lg px-4 py-3 h-28 focus:ring focus:border-blue-400"></textarea>
          </div>
        </form>
      </div>

      <!-- Modal Footer (Fixed) -->
      <div class="sticky bottom-0 bg-white z-10 p-4 border-t flex justify-end gap-4">
        <button type="button" @click="closeModal" class="px-6 py-3 border rounded-lg">Cancel</button>
        <button type="submit" @click="saveTask" class="bg-blue-900 text-white px-6 py-3 rounded-lg">Submit</button>
      </div>

    </div>
  </div>
  <flash ref="refFlash"/>
  <loader ref="refLoading"/>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { defineEmits } from 'vue';
import axios from "axios";
import { useAuthStore } from "@/store/AuthStore";
import Flash from "@/components/Flash.vue";
import Loader from "@/components/Loading.vue";
// Define the custom events that your component will emit
const emit = defineEmits(['close', 'save']); // Define events like 'close' and 'save'
const getUserList = ref([]);
const authStore = useAuthStore();
const user = authStore.getUser;
const refFlash = ref('');
const refLoading = ref('');
const minDate = computed(() => new Date().toISOString().split("T")[0]);
// Fetch users
const fetchUsers = async () => {
    refLoading.value.loader(true);
  try {
    const response = await axios.get('users/list');
    const data = response.data;
    if (data.status) {
      getUserList.value = data.users;
      refLoading.value.loader(false);
    }
  } catch (error) {
    console.error('Error fetching:', error);
    refLoading.value.loader(false);
  }
};

// Fetch users when component is mounted
onMounted(fetchUsers);

// Function to emit the 'close' event
function closeModal() {
  emit('close'); // Emit the 'close' event
}

// Function to emit the 'save' event
const saveTask = async() =>{
  refLoading.value.loader(true);
  const formData = new FormData();
  formData.append("user_id", task.value.assigned);
  formData.append("title", task.value.name);
  formData.append("description", task.value.description);
  formData.append("deadline", task.value.deadline);
  formData.append("category_id", task.value.category_id);
  formData.append("status", task.value.status);
  try {
        const response = await axios.post('task/create',formData);
        const data = await response.data;
        if (data.status) {
          refFlash.value.flash(response.data.message, 'success');
         setTimeout(()=>{
          emit('close');
         }, 2000)
          
        }
      } catch (error) {
        refFlash.value.flash(error.response?.data?.message, 'error');
    refLoading.value.loader(false);
      }
}


// Task form data
const task = ref({
  name: "",
  description: "",
  assigned: 0,
  category_id: 0,
  deadline: "",
  status: ""
});




</script>
