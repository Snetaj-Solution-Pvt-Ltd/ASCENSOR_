<template>
  <div class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-3/5 max-w-4xl max-h-screen flex flex-col">
      
      <!-- Modal Header -->
      <div class="sticky top-0 bg-white z-10 p-4 border-b flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-blue-900">Edit Task</h2>
      </div>

      <!-- Modal Body -->
      <div class="overflow-y-auto max-h-[70vh] p-6 space-y-6 scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100 max-h-[400px] overflow-y-auto
        [&::-webkit-scrollbar]:w-2
        [&::-webkit-scrollbar-track]:bg-gray-100
        [&::-webkit-scrollbar-thumb]:bg-gray-300
        dark:[&::-webkit-scrollbar-track]:bg-neutral-700
        dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
        
        <form @submit.prevent="saveTask">
          <!-- Task Name -->
          <div class="mb-2">
            <label class="block text-gray-700 font-medium mb-1">Task Name <span class="text-red-500">*</span></label>
            <input type="text" v-model="localtask.title" class="w-full border rounded-lg px-4 py-3 focus:ring focus:border-blue-400" required />
          </div>

          <!-- Task Assigned -->
          <div class="mb-2">
            <label class="block text-gray-700 font-medium mb-1">Task Assigned</label>
            <select v-model="localtask.user_id" class="w-full border rounded-lg px-4 py-3 focus:ring focus:border-blue-400">
              <option v-for="user in userList" :key="user.id" :value="user.id">{{ user.name }}</option>
            </select>
          </div>

          <!-- Task Status -->
          <div class="mb-2">
            <label class="block text-gray-700 font-medium mb-1">Task Status</label>
            <select v-model="localtask.status" class="w-full border rounded-lg px-4 py-3 focus:ring focus:border-blue-400">
              <option v-for="(status, index) in user?.task_status || []" :key="index" :value="status">{{ status }}</option>
            </select>
          </div>

          <!-- Due Date -->
          <div class="mb-2">
            <label class="block text-gray-700 font-medium mb-1">Due Date</label>
            <input type="date" v-model="formattedDeadline" :min="minDate" class="w-full border rounded-lg px-4 py-3 focus:ring focus:border-blue-400" />
          </div>

          <!-- Task Category -->
          <div class="mb-2">
            <label class="block text-gray-700 font-medium mb-1">Category</label>
            <select v-model="localtask.category_id" class="w-full border rounded-lg px-4 py-3 focus:ring focus:border-blue-400">
              <option v-for="category in user?.categories || []" :key="category.id" :value="category.id">{{ category.category_name }}</option>
            </select>
          </div>

          <!-- Description -->
          <div class="mb-2">
            <label class="block text-gray-700 font-medium mb-1">Description</label>
            <textarea v-model="localtask.description" class="w-full border rounded-lg px-4 py-3 h-28 focus:ring focus:border-blue-400"></textarea>
          </div>
        </form>
      </div>

      <!-- Modal Footer -->
      <div class="sticky bottom-0 bg-white z-10 p-4 border-t flex justify-end gap-4">
        <button type="button" @click="closeModal" class="px-6 py-3 border rounded-lg">Cancel</button>
        <button type="submit" @click="saveTask" class="bg-blue-900 text-white px-6 py-3 rounded-lg">Submit</button>
      </div>

    </div>
  </div>
  <loader ref="refLoading"/>
</template>

<script>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useAuthStore } from "@/store/AuthStore";
import Loader from "@/components/Loading.vue";

export default {
  props: {
    task: {
      type: Object,
      required: true,
    },
  },
  components: {
    Loader
  },
  setup(props, { emit }) {
    const authStore = useAuthStore();
    const user = authStore.getUser;
    const localtask = ref({ ...props.task });
    const userList = ref([]);
    const refLoading = ref('');
    // Fetch users
    const fetchUsers = async () => {
      refLoading.value.loader(true);
      try {
        const response = await axios.get("users/list");
        if (response.data.status) {
          userList.value = response.data.users;
          refLoading.value.loader(false);
        }
      } catch (error) {
        console.error("Error fetching:", error);
        refLoading.value.loader(false);
      }
    };

    // Lifecycle Hook
    onMounted(() => {
      fetchUsers();
    });

    const formattedDeadline = computed({
      get() {
        return localtask.value.deadline
          ? new Date(localtask.value.deadline).toISOString().split("T")[0]
          : "";
      },
      set(value) {
        localtask.value.deadline = value;
      },
    })
    const minDate = computed(() => new Date().toISOString().split("T")[0]);
    // Methods
    const closeModal = () => emit("close");
    const saveTask = () => emit("save", localtask.value);
    return {
      localtask,
      userList,
      user,
      formattedDeadline,
      minDate,
      closeModal,
      saveTask,
      refLoading
    };
  },
   
};
</script>
