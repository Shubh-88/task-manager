<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

defineProps({
  tasks: Object
})

const showForm = ref(false)
const isEdit = ref(false)
const editId = ref(null)
const errors = ref({})
const successMsg = ref('')
const search = ref('')
const statusFilter = ref('')
const errors = ref({})

router.post(url, data, {
  onSuccess: () => {
    errors.value = {}
  },
  onError: (err) => {
    errors.value = err
  }
})

const form = ref({
  title: '',
  description: '',
  due_date: '',
   status: 'pending' 
})

// CREATE
function createTask() {
  router.post('/tasks', form.value, {
    onSuccess: () => {
      successMsg.value = 'Task created successfully!'
      setTimeout(() => successMsg.value = '', 2000)
      resetForm()
    },
    onError: (err) => {
      errors.value = err
    }
  })
}



// OPEN EDIT
function openEdit(task) {
  isEdit.value = true
  showForm.value = true
  editId.value = task.id

  form.value = {
    title: task.title,
    description: task.description,
    due_date: task.due_date,
     status: task.status  
  }
}

// UPDATE
function updateTask() {
   router.put(`/tasks/${editId.value}`, form.value, {
    onSuccess: () => {
      successMsg.value = 'Task updated successfully!'
     setTimeout(() => successMsg.value = '', 2000)
      resetForm()
    },
    onError: (err) => {
      errors.value = err
    }
  })
}

// DELETE
const confirmDeleteId = ref(null)

function deleteTask(id) {
  confirmDeleteId.value = id
}

function confirmDelete() {
  router.delete(`/tasks/${confirmDeleteId.value}`, {
    onSuccess: () => {
      successMsg.value = 'Task deleted!'
     setTimeout(() => successMsg.value = '', 2000)
      confirmDeleteId.value = null
    }
  })
}

// RESET
function resetForm() {
  form.value = { title: '', description: '', due_date: '' ,  status: 'pending'   }
  showForm.value = false
  isEdit.value = false
  editId.value = null
}

//FILTER
function applyFilters() {
  router.get('/tasks', {
    search: search.value,
    status: statusFilter.value
  }, {
    preserveState: true,
    replace: true
  })
}
</script>

<template>
<div class="min-h-screen py-4" style="background: #eef5ff;">
  <div class="container">

<div class="d-flex justify-content-between align-items-center mb-4 py-3">
  <div class="w-100 text-center position-relative">
    <h3 class="fw-bold mb-0">Task Manager</h3>
    
 <button 
  class="btn text-white fw-semibold position-absolute end-0 top-50 px-4 py-2 shadow back-btn"
  @click="router.visit('/dashboard')"
>
  ← Back
</button>
  </div>

</div>


<div class="card border-0 rounded-4 shadow-lg mb-5 premium-card">
    <div class="card-body">

    <div v-if="successMsg" class="alert alert-success">
    {{ successMsg }}
  </div>

   
   <div class="d-flex justify-content-end mb-3">
<button class="btn add-btn" @click="showForm = true">
    + Add Task
  </button>
</div>

      <div class="row mb-3">
  <div class="col-md-4">
    <input v-model="search" @input="applyFilters" class="form-control" placeholder="Search...">
  </div>

  <div class="col-md-3">
    <select v-model="statusFilter" @change="applyFilters" class="form-control">
      <option value="">All</option>
      <option value="pending">Pending</option>
      <option value="completed">Completed</option>
    </select>
  </div>
</div>

    
      <div class="table-responsive">
       <table class="table align-middle text-center premium-table">
         <thead class="table-header">
            <tr>
              <th>Title</th>
              <th>Description</th>
              <th>Status</th>
              <th>Due Date</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-if="tasks.data.length === 0">
              <td colspan="5">No tasks found!!</td>
            </tr>

            <tr v-for="task in tasks.data" :key="task.id">
              <td>{{ task.title }}</td>
              <td>{{ task.description }}</td>
              <td>
                <span class="badge status-pending text-dark" v-if="task.status==='pending'">
                  Pending
                </span>
                <span class="badge status-completed" v-else>
                  Completed
                </span>
              </td>
              <td>{{ task.due_date }}</td>

              <td>
                <button class="btn btn-sm btn-primary me-2" @click="openEdit(task)">
                  Edit
                </button>

                <button class="btn btn-sm btn-danger" @click="deleteTask(task.id)">
                  Delete
                </button>
              </td>
            </tr>

          </tbody>
        </table>
        <div class="d-flex justify-content-center mt-3">
  <button 
    v-for="link in tasks.links" 
    :key="link.label"
    class="btn btn-sm mx-1"
    :class="[
      link.active ? 'btn-primary' : 'btn-outline-secondary',
      !link.url ? 'disabled' : ''
    ]"
    v-html="link.label"
    @click="link.url && router.visit(link.url)"
  ></button>
</div>
      </div>

    </div>
  </div>

  <div v-if="showForm" class="modal-backdrop-custom">

    <div class="modal-box">

      <h5 class="text-center mb-3">
        {{ isEdit ? 'Edit Task' : 'Add Task' }}
      </h5>

      <input v-model="form.title" class="form-control mb-2" placeholder="Title">
      <div v-if="errors.title" class="text-danger mb-2">
  {{ errors.title }}
</div>
      <input v-model="form.description" class="form-control mb-2" placeholder="Description">
      <div v-if="errors.description" class="text-danger">{{ errors.description }}</div>
      <input type="date" v-model="form.due_date" class="form-control mb-3">
      <div v-if="errors.due_date" class="text-danger">{{ errors.due_date }}</div>
      <select v-model="form.status" class="form-control mb-3">
  <option value="pending">Pending</option>
  <option value="completed">Completed</option>
</select>

      <div class="text-center">

        <button v-if="!isEdit" class="btn btn-primary me-2" @click="createTask">
          Save
        </button>

        <button v-else class="btn btn-success me-2" @click="updateTask">
          Update
        </button>

        <button class="btn btn-secondary" @click="resetForm">
          Cancel
        </button>

      </div>

    </div>
  </div>

</div>


<div v-if="confirmDeleteId" class="modal-backdrop-custom">
  <div class="modal-box text-center">

    <h5 class="mb-3">Are you sure?</h5>

    <button class="btn btn-danger me-2" @click="confirmDelete">
      Yes Delete
    </button>

    <button class="btn btn-secondary" @click="confirmDeleteId = null">
      Cancel
    </button>

  </div>
</div>
</div>
</template>

<style>
.modal-backdrop-custom {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.modal-box {
  background: white;
  padding: 20px;
  border-radius: 10px;
  width: 350px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.back-btn {
  background: linear-gradient(135deg, #667eea, #764ba2);
  border: none;
  transition: all 0.3s ease;
}

.back-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
  background: linear-gradient(135deg, #5a6be0, #6a3ea1);
}

.premium-card {
  background: #ffffff;
  border-radius: 16px;
}

.premium-table {
  border-radius: 12px;
  overflow: hidden;
}

.table-header {
  background: linear-gradient(135deg, #4facfe, #00c6ff);
  color: white;
}

.premium-table tbody tr {
  transition: 0.2s;
}

.premium-table tbody tr:hover {
  background: #f1f7ff;
  transform: scale(1.01);
}

.add-btn {
  background: linear-gradient(135deg, #4facfe, #00c6ff);
  color: white;
  border: none;
  padding: 6px 14px;
  border-radius: 8px;
}

.add-btn:hover {
  background: linear-gradient(135deg, #3a8bfd, #00b4db);
}

.status-pending {
  background: #fff3cd;
  color: #856404;
  padding: 6px 10px;
  border-radius: 8px;
}

.status-completed {
  background: #d4edda;
  color: #155724;
  padding: 6px 10px;
  border-radius: 8px;
}
</style>