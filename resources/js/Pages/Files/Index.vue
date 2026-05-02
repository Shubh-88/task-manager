<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

defineProps({
  files: Object
})

const selectedFiles = ref([])
const fileInput = ref(null)
const showUploadModal = ref(false)


function handleFileChange(e) {
  selectedFiles.value = e.target.files
}


function uploadFiles() {
  const formData = new FormData()

  for (let i = 0; i < selectedFiles.value.length; i++) {
    formData.append('files[]', selectedFiles.value[i])
  }

  router.post('/upload-files', formData, {
    onSuccess: () => {
      selectedFiles.value = []
      if (fileInput.value) {
        fileInput.value.value = null
      }
      showUploadModal.value = false

      successMsg.value = 'Files uploaded successfully!'
      setTimeout(() => successMsg.value = '', 2000)
    }
  })
}

function isImage(path) {
  return path.endsWith('.jpg') || path.endsWith('.png') || path.endsWith('.jpeg')
}

function getFileName(path) {
  return path.split('/').pop()
}

const previewFile = ref(null)

function openPreview(file) {
  previewFile.value = file
}

function closePreview() {
  previewFile.value = null
}

const successMsg = ref('')

const search = ref('')

function applySearch() {
  router.get('/files', {
    search: search.value
  }, {
    preserveState: true,
    replace: true
  })
}

</script>

<template>
  <div class="min-h-screen p-6" style="background: #eef5ff;">
  <div class="max-w-6xl mx-auto">

    <h1 class="text-2xl font-bold mb-6 text-center"> File Upload & Preview</h1>

    <!-- Upload Section
//   <div class="card shadow border-0 rounded-4 mb-4 p-4">
//   <h5 class="mb-3">Upload Files</h5>

//   <input ref="fileInput" type="file" multiple @change="handleFileChange" class="form-control mb-3" />

//   <button 
//     @click="uploadFiles"
//    class="btn btn-sm text-white px-3 py-1"
//     style="background: linear-gradient(135deg,#4facfe,#00f2fe);"
//   >
//     Upload Files
//   </button>
// </div> -->


<!-- SUCCESS MESSAGE (SEPARATE LINE) -->
<div v-if="successMsg" class="alert alert-success text-center mb-3">
  {{ successMsg }}
</div>

<!-- BUTTON ROW -->
<div class="d-flex justify-content-end align-items-center gap-2 mb-4">

  <!-- Upload Button -->
  <button 
    class="btn btn-primary"
    @click="showUploadModal = true"
  >
    + Upload Files
  </button>

  <!-- Back Button -->
  <button 
    class="btn text-white px-4 py-2 back-btn"
    @click="router.visit('/dashboard')"
  >
    ← Back
  </button>

</div>

<div class="row mb-4">
  <div class="col-md-4">
    <input 
      v-model="search"
      @input="applySearch"
      class="form-control"
      placeholder="Search files..."
    />
  </div>
</div>


    <div class="row g-4">
  <div 
 v-for="file in files.data"
    :key="file.id" 
    class="col-md-3"
  >

   <div 
  class="card shadow-sm border-0 rounded-4 file-card h-100"
  @click="openPreview(file)"
  style="cursor:pointer; transition:0.3s;"
>

     
      <img 
        v-if="isImage(file.file_path)"
        :src="'/storage/' + file.file_path"
        class="card-img-top"
        style="height:150px; object-fit:cover;"
      />

  
      <div v-else class="d-flex align-items-center justify-content-center" style="height:150px;">
        📄 PDF
      </div>

      <div class="card-body p-2 text-center">
        <small class="text-truncate d-block">
          {{ getFileName(file.file_path) }}
        </small>
      </div>

    </div>

  </div>
</div>


  <div class="d-flex justify-content-center mt-4">
  <button 
    v-for="link in files.links" 
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

  <div v-if="previewFile" class="preview-overlay">

  <div class="preview-box">

    <button class="close-btn" @click="closePreview">✖</button>


    <img 
      v-if="isImage(previewFile.file_path)"
      :src="'/storage/' + previewFile.file_path"
      class="preview-content"
    />


    <iframe 
      v-else
      :src="'/storage/' + previewFile.file_path"
      class="preview-content"
    ></iframe>

  </div>


</div>




<div v-if="showUploadModal" class="modal-overlay">

  <div class="modal-box">

    <h5 class="mb-3 text-center">Upload Files</h5>

    <input 
      ref="fileInput"
      type="file"
      multiple
      @change="handleFileChange"
      class="form-control mb-3"
    />

    <div class="text-center">
      <button 
        @click="uploadFiles"
        class="btn btn-success btn-sm me-2"
      >
        Upload
      </button>

      <button 
        class="btn btn-secondary btn-sm"
        @click="showUploadModal = false"
      >
        Cancel
      </button>
    </div>

  </div>

</div>
</template>

<style>
.preview-overlay {
  position: fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background: rgba(0,0,0,0.8);
  display:flex;
  justify-content:center;
  align-items:center;
  z-index:9999;
}

.preview-box {
  position: relative;
  width: 80%;
  height: 80%;
  background: white;
  border-radius: 10px;
  overflow: hidden;
}

.preview-content {
  width:100%;
  height:100%;
  object-fit:contain;
}

.close-btn {
  position:absolute;
  top:10px;
  right:10px;
  background:red;
  color:white;
  border:none;
  padding:5px 10px;
  border-radius:5px;
}

.modal-overlay {
  position: fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background: rgba(0,0,0,0.7);
  display:flex;
  justify-content:center;
  align-items:center;
  z-index:9999;
}

.modal-box {
  background:white;
  padding:25px;
  border-radius:12px;
  width:350px;
  box-shadow:0 10px 30px rgba(0,0,0,0.3);
}

.file-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
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

</style>