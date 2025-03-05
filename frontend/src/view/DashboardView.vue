<template>
  <div class="p-4 bg-gray-100 min-h-screen">
    <!-- Tabs  start-->
    <div class="flex border-b text-lg font-medium">
      <div v-for="(tab, index) in tabs" :key="index" @click="activeTab = index" class="px-4 py-2 cursor-pointer" :class="activeTab === index
        ? 'border-b-2 border-blue-900 text-blue-900'
        : 'text-gray-500'
        ">
        {{ tab }}
      </div>
    </div>

    <!-- Filters -->
    <div class="flex gap-4 mt-4 p-4 bg-white rounded shadow">
      <button v-for="filter in filters" :key="filter.name" class="px-4 py-2 border rounded flex items-center" :class="filter.active
        ? 'border-blue-900 text-blue-900'
        : 'border-gray-300 text-gray-600'
        " @click="setActiveFilter(filter.name)">
        {{ filter.name }}
        <span class="ml-2 bg-gray-200 px-2 rounded">{{ filter.count }}</span>
      </button>
      <button @click="openAddTaskModal" class="ml-auto bg-blue-900 text-white px-4 py-2 rounded">
        Add Task
      </button>
      <button @click="openFilterModal" class="p-2 border rounded">
        <i class="fas fa-filter"></i>
      </button>
    </div>

    <!-- Import AddTask Modal Component -->
    <AddTaskModal v-if="showModal" @close="closeAddModal" />

    <!-- Import Filter Modal Component -->
    <FilterModal v-if="showFilterModal" :showModal="showFilterModal" :filter="filter" @close="showFilterModal = false"
      @applyFilters="handleFilters" />

    <!-- end call add modal -->
    <!-- Task List -->
    <div class="bg-white rounded shadow mt-4">
      <div class="px-4 py-2 border-b text-gray-600 font-medium">
        Task Details
      </div>
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-100 text-left text-gray-600">
            <th class="p-2  text-center flex justify-self-center">
              <!-- Hidden default checkbox -->
              <input type="checkbox" v-model="allChecked" @change="toggleAllTasks" id="allCheckbox"
                class="peer hidden" />
              <!-- Custom circular label -->
              <label for="allCheckbox"
                class="w-6 h-6 rounded border-2 border-gray-400 bg-white peer-checked:bg-green-700 peer-checked:border-transparent cursor-pointer flex items-center justify-center">
                <!-- Checkmark icon (using SVG) when checked -->
                <svg v-if="allChecked" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </label>
            </th>
            <th class="p-2 border">Task Name</th>
            <th class="p-2 border">Description</th>
            <th class="p-2 border">Assignee</th>
            <th class="p-2 border">Due Date</th>
            <th class="p-2 border">Category</th>
            <th class="p-2 border">Status</th>
            <th class="p-2 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="tasks.length === 0" class="border-b">
            <td class="p-2 border text-center" colspan="8">{{ noRecord }}</td>
          </tr>
          <tr v-for="(task) in filteredTasks" :key="task.id" class="border-b">
            <td class="p-2 text-center flex justify-self-center">
              <!-- Hidden default checkbox -->

              <input type="checkbox" v-model="task.isCompleted" @change="toggleTaskStatus(task)"
                :id="'checkbox' + task.id" :name="'checkbox' + task.id" :value="task.id" class="peer hidden" />

              <!-- Custom Square Label -->
              <label :for="'checkbox' + task.id"
                class="w-6 h-6 rounded border-2 border-gray-400 bg-white peer-checked:bg-green-700 peer-checked:border-transparent cursor-pointer flex items-center justify-center">
                <!-- Checkmark Icon (using SVG) when checked -->
                <svg v-if="task.isCompleted" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </label>
            </td>
            <td class="p-2 border">{{ task.title }}</td>
            <td class="p-2 border">{{ task.description }}</td>
            <td class="p-2 border">{{ task.user.name }}</td>
            <td class="p-2 border" :class="task.isOverdue ? 'text-red-500' : 'text-gray-500'">
              {{ task.deadline }}
            </td>
            <td class="p-2 border">{{ task.category.category_name }}</td>
            <td class="p-2 border"
              :class="task.status === 'Completed' ? 'text-green-500' : task.status === 'In progress' ? 'text-orange-500' : 'text-red-500'">
              {{ task.status }}
            </td>
            <td class="p-2 border">
              <div class="flex space-x-2">
                <button @click="openEditModal(task)" class="text-blue-900 hover:text-blue-900">
                  <i class="fas fa-edit"></i>
                </button>
                <button @click="openDeleteModal(task.id)" class="text-red-500 hover:text-red-700">
                  <i class="fas fa-trash"></i>
                </button>
                <!-- Delete Confirmation Modal -->
                <DeleteConfirmationModal v-if="showDeleteModal" :show="showDeleteModal" :taskId="taskToDeleteId"
                  @close="closeDeleteModal" @confirm="deleteTask" />
                <!-- Edit Task Modal -->
                <EditTaskModal v-if="showEditModal" :show="showEditModal" :task="taskToEdit" @close="closeEditModal"
                  @save="updateTask" />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <flash v-if="this.regiMessage" :message="this.regiMessage" :type="this.messageType" />
  <loader v-if="this.isLoading" :loading="true" />
</template>

<script>
import axios from "axios";
import AddTaskModal from "../modals/AddTaskModal.vue";
import FilterModal from "../modals/FilterModal.vue";
import EditTaskModal from "../modals/EditTaskModal.vue";
import DeleteConfirmationModal from '../modals/DeleteConfirmationModal.vue';
import Flash from "@/components/Flash.vue";
import Loader from "@/components/Loading.vue";

export default {
  components: {
    AddTaskModal, FilterModal, EditTaskModal, DeleteConfirmationModal, Flash, Loader
  },

  data() {
    return {
      showModal: false,
      showFilterModal: false,
      showEditModal: false,
      showDeleteModal: false,
      taskToEdit: null,
      taskToDeleteId: null,
      allChecked: false,
      activeTab: 0,
      noRecord: "Task not added",
      tabs: ["My Tasks"],
      filters: [
        { name: "Total", count: 0, active: true },
        { name: "Not Started", count: 0, active: false },
        { name: "In Progress", count: 0, active: false },
        { name: "Completed", count: 0, active: false },
      ],
      tasks: [],
      filter: { category: "", status: "" },
      isLoading: false,
      regiMessage: '',
      messageType: '',
    };
  },

  computed: {
    filteredTasks() {
      return this.tasks.filter((task) => {
        return (
          this.filters.find(
            (f) => f.name === "Open" && f.active && !task.isCompleted
          ) ||
          this.filters.find(
            (f) => f.name === "Completed" && f.active && task.isCompleted
          ) ||
          this.filters.find((f) => f.name === "Total" && f.active)
        );
      });
    },
  },
  mounted() {
    this.fetchTasks();
  },
  methods: {
    setActiveFilter(filterName) {
      this.filters.forEach((f) => (f.active = f.name === filterName));
    },
    toggleTaskStatus(task) {
      task.status = task.isCompleted ? 'Completed' : 'Not Started';
      this.saveUpdatedTask(task);
      this.allChecked = this.filteredTasks.every((t) => t.isCompleted);
    },
    toggleAllTasks() {
      this.filteredTasks.forEach((task) => {
        task.isCompleted = this.allChecked
        task.status = task.isCompleted ? 'Completed' : 'Not Started';
        this.saveUpdatedTask(task);
      }
      );
    },
    openAddTaskModal() {
      this.showModal = true;
    },
    closeAddModal() {
      this.showModal = false;
      if (this.filter && (this.filter.status || this.filter.category)) {
        this.filterTasks(this.filter);
        return;
      }
      setTimeout(() => {
        this.fetchTasks();
      }, 1000)

    },
    // Edit Modal Open
    openEditModal(task) {
      this.taskToEdit = { ...task };
      this.showEditModal = true;

    },
    // Edit Modal Close
    closeEditModal() {
      this.showEditModal = false;
      this.taskToEdit = null;
      if (this.filter && (this.filter.status || this.filter.category)) {
        this.filterTasks(this.filter);
        return;
      }
      setTimeout(() => {
        this.fetchTasks();
      }, 1000)
    },
    // Update edit task
    updateTask(updatedTask) {
      this.saveUpdatedTask(updatedTask)
    },
    // Delete Modal Open
    openDeleteModal(taskId) {
      this.showDeleteModal = true;
      this.taskToDeleteId = taskId;
    },
    // Delete Modal Close
    closeDeleteModal() {
      this.showDeleteModal = false;
      this.taskToDeleteId = null;
    },
    // Delete Task
    deleteTask(taskId) {
      this.deleteTasks(taskId);
      this.closeDeleteModal();
    },
    // Filter Modal Open
    openFilterModal() {
      this.filter = { ...this.filter };
      this.showFilterModal = true;
    },
    //Handle filter
    handleFilters(filters) {
      this.filterTasks(filters);
    },
    // Fetch Tasks
    async fetchTasks() {
      this.isLoading = true;
      this.regiMessage = '';
      try {
        const response = await axios.get('task/list');
        const data = response.data;
        if (data.status) {
          // this.tasks = data.task;
          this.checkTaskComplete(data.task);
          this.isLoading = false;
          this.regiMessage = data.message;
          this.messageType = 'success';
        }
      } catch (error) {
        this.tasks = [];
        // this.regiMessage = error.response?.data?.message;
        // this.messageType = 'error';
        this.isLoading = false;
      }
    },
    // Save task after edit
    async saveUpdatedTask(updatedTask) {
      this.isLoading = true;
      this.regiMessage = '';
      const formData = new FormData();
      formData.append("id", updatedTask.id);
      formData.append("user_id", updatedTask.user_id);
      formData.append("title", updatedTask.title);
      formData.append("description", updatedTask.description);
      formData.append("deadline", updatedTask.deadline);
      formData.append("category_id", updatedTask.category_id);
      formData.append("status", updatedTask.status);
      try {
        const response = await axios.post('task/update', formData);
        const data = await response.data;
        if (data.status) {
          this.regiMessage = data.message;
          this.messageType = 'success';
          setTimeout(() => {
            this.closeEditModal();
          }, 2000)
        }
      } catch (error) {
        this.regiMessage = error.response?.data?.message;
        this.messageType = 'error';
        this.isLoading = false;
      }
    },
    checkTaskComplete(task) {
      this.tasks = [];
      task.forEach((t) => {
        t['isCompleted'] = t.status === 'Completed' ? true : false;
        this.tasks.push(t);
        this.allChecked = this.filteredTasks.every((t) => t.isCompleted);
      });
    },
    // delete Tasks
    async deleteTasks(id) {
      this.isLoading = true;
      this.regiMessage = '';
      const formData = new FormData();
      formData.append("id", id);
      try {
        const response = await axios.post('task/delete', formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });
        if (response.data) {
          this.regiMessage = response.data.message;
          this.messageType = 'success';
          setTimeout(() => {
            this.fetchTasks();
          }, 2000);
        }
      } catch (error) {
        console.error('Error fetching:', error);
        this.fetchTasks();
      }
    },
    // Filter Tasks
    async filterTasks(filter) {
      this.isLoading = true;
      this.regiMessage = '';
      this.filter = filter;
      try {
        const response = await axios.get(`task/list?status=${filter.status}&category=${filter.category}`);
        if (response.data) {
          this.tasks = response.data.task;
          this.regiMessage = response.data.message;
          this.messageType = 'success';
          setTimeout(() => {
            this.showFilterModal = false;
            this.isLoading = false;
          }, 2000);
        }
      } catch (error) {
        console.error('Error fetching:', error.response.data.message);
        this.noRecord = error.response.data.message
        this.showFilterModal = false;
        this.tasks = [];
        this.regiMessage = error.response?.data?.message;
        this.messageType = 'error';
        this.isLoading = false;
      }
    }
  },
};

// const logout = async () => {
//   authStore.setUser([])
//   localStorage.removeItem("token", null);
//   window.location.href = '/'
// }
</script>
