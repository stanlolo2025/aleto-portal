<template>
  <div>
    <h2>Register New Villager</h2>
    <div class="card mt-3">
      <div class="card-body">
        <form @submit.prevent="register">
          <!-- Personal Information -->
          <h5 class="mb-3 text-primary">Personal Information</h5>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Full Name <span class="text-danger">*</span></label>
              <input v-model="form.full_name" type="text" class="form-control" :class="{'is-invalid': errors.full_name}" required>
              <div class="invalid-feedback">{{ errors.full_name?.[0] }}</div>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
              <input v-model="form.date_of_birth" type="date" class="form-control" :class="{'is-invalid': errors.date_of_birth}" required>
              <div class="invalid-feedback">{{ errors.date_of_birth?.[0] }}</div>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Gender <span class="text-danger">*</span></label>
              <select v-model="form.gender" class="form-select" :class="{'is-invalid': errors.gender}" required>
                <option value="">Select</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
              <div class="invalid-feedback">{{ errors.gender?.[0] }}</div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Marital Status</label>
              <select v-model="form.marital_status" class="form-select">
                <option value="">Select</option>
                <option value="single">Single</option>
                <option value="married">Married</option>
                <option value="widowed">Widowed</option>
                <option value="divorced">Divorced</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Occupation</label>
              <input v-model="form.occupation" type="text" class="form-control" placeholder="Farmer, trader, student...">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Education Level</label>
              <select v-model="form.education_level" class="form-select">
                <option value="">Select</option>
                <option value="none">None</option>
                <option value="primary">Primary</option>
                <option value="secondary">Secondary</option>
                <option value="tertiary">Tertiary</option>
              </select>
            </div>
          </div>

          <!-- Spouse & Children (shown when married) -->
          <div v-if="form.marital_status === 'married' || form.marital_status === 'widowed'" class="border rounded p-3 mb-3 bg-light">
            <h6 class="text-primary mb-3">Family Members</h6>
            <div class="row mb-2">
              <div class="col-md-6 mb-2">
                <label class="form-label small">Spouse Name</label>
                <input v-model="form.spouse_name" type="text" class="form-control" placeholder="Full name of spouse">
              </div>
              <div class="col-md-6 mb-2">
                <label class="form-label small">Spouse Occupation</label>
                <input v-model="form.spouse_occupation" type="text" class="form-control" placeholder="Optional">
              </div>
            </div>
            <label class="form-label small">Children</label>
            <div v-for="(child, index) in form.children" :key="index" class="row g-2 mb-2 align-items-end">
              <div class="col-5">
                <input v-model="child.name" class="form-control form-control-sm" :placeholder="'Child ' + (index+1) + ' name'">
              </div>
              <div class="col-3">
                <select v-model="child.gender" class="form-select form-select-sm">
                  <option value="son">Son</option>
                  <option value="daughter">Daughter</option>
                </select>
              </div>
              <div class="col-3">
                <input v-model="child.dob" type="date" class="form-control form-control-sm">
              </div>
              <div class="col-1">
                <button type="button" class="btn btn-outline-danger btn-sm" @click="form.children.splice(index, 1)">x</button>
              </div>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" @click="form.children.push({name:'', gender:'son', dob:''})">+ Add Child</button>
          </div>

          <!-- Identity & Fraud Prevention -->
          <h5 class="mb-3 mt-4 text-primary">Identity & Verification</h5>
          <div class="row">
            <div class="col-md-3 mb-3">
              <label class="form-label">Household ID <span class="text-danger">*</span></label>
              <input v-model="form.household_id" type="text" class="form-control" :class="{'is-invalid': errors.household_id}" required>
              <div class="invalid-feedback">{{ errors.household_id?.[0] }}</div>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Village</label>
              <input v-model="form.village" type="text" class="form-control" placeholder="Village name">
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Ward</label>
              <input v-model="form.ward" type="text" class="form-control" placeholder="Ward">
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Zone</label>
              <input v-model="form.zone" type="text" class="form-control" placeholder="Zone">
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">National ID Number (NIN)</label>
              <input v-model="form.nin" type="text" class="form-control" :class="{'is-invalid': errors.nin}" maxlength="11" placeholder="11 digits">
              <div class="invalid-feedback">{{ errors.nin?.[0] }}</div>
              <small class="text-muted">Cross-checked with national database</small>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Phone Number</label>
              <input v-model="form.phone_number" type="text" class="form-control" :class="{'is-invalid': errors.phone_number}" placeholder="+2348012345678">
              <div class="invalid-feedback">{{ errors.phone_number?.[0] }}</div>
              <small class="text-muted">Used for duplicate detection</small>
            </div>
          </div>

          <!-- Contact & Financial -->
          <h5 class="mb-3 mt-4 text-primary">Contact & Financial</h5>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Email Address</label>
              <input v-model="form.email" type="email" class="form-control" :class="{'is-invalid': errors.email}" placeholder="Optional">
              <div class="invalid-feedback">{{ errors.email?.[0] }}</div>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Bank Name</label>
              <input v-model="form.bank_name" type="text" class="form-control" placeholder="e.g. First Bank, GTBank">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Bank Account Number</label>
              <input v-model="form.bank_account_number" type="text" class="form-control" placeholder="10-digit account number">
              <small class="text-muted">Used for duplicate detection & stipend payments</small>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Health Status</label>
              <input v-model="form.health_status" type="text" class="form-control" placeholder="Chronic illness, disability...">
              <small class="text-muted">Supports healthcare module</small>
            </div>
          </div>

          <!-- Biometric -->
          <h5 class="mb-3 mt-4 text-primary">Passport Photo & Biometric Data</h5>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Passport Photograph <span class="text-danger">*</span></label>
              <input type="file" class="form-control" accept="image/jpeg,image/png" @change="handlePassportUpload">
              <small class="text-muted">JPG/PNG, max 2MB. This will appear on their profile and ID.</small>
              <div v-if="passportPreview" class="mt-2">
                <img :src="passportPreview" alt="Preview" style="width:100px; height:120px; object-fit:cover; border:2px solid #ccc; border-radius:4px;">
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Fingerprint Scan</label>
              <div class="input-group">
                <input type="text" class="form-control" :value="form.fingerprint_data ? 'Captured ✓' : 'Not captured'" disabled>
                <button type="button" class="btn btn-outline-secondary" @click="captureFingerprint">
                  📇 Capture
                </button>
              </div>
              <small class="text-muted">Prevents duplicate registrations</small>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Facial Photo (Biometric)</label>
              <div class="input-group">
                <input type="text" class="form-control" :value="form.facial_photo ? 'Captured ✓' : 'Not captured'" disabled>
                <button type="button" class="btn btn-outline-secondary" @click="capturePhoto">
                  📷 Capture
                </button>
              </div>
            </div>
          </div>

          <!-- Alerts -->
          <div v-if="duplicateError" class="alert alert-warning mt-3">
            <strong>⚠️ Potential Duplicate Detected!</strong>
            <p class="mb-1">{{ duplicateError.message }}</p>
            <p class="mb-0"><strong>Match:</strong> {{ duplicateError.duplicate_data?.matched_villager?.full_name }} 
              (<code>{{ duplicateError.duplicate_data?.matched_villager?.unique_id }}</code>) — 
              Status: {{ duplicateError.duplicate_data?.matched_villager?.status }}</p>
            <p class="mb-0"><strong>Field(s):</strong> {{ duplicateError.duplicate_data?.matched_fields?.join(', ') }}</p>
          </div>

          <div v-if="success" class="alert alert-success mt-3">
            ✅ Villager registered successfully!<br>
            <strong>Unique ID:</strong> {{ successId }}<br>
            <strong>Registration Date:</strong> {{ registrationDate }}<br>
            <strong>Registered By:</strong> {{ registeredBy }}
          </div>

          <!-- Submit -->
          <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-lg" :disabled="loading">
              {{ loading ? 'Registering...' : '✓ Register Villager' }}
            </button>
            <router-link to="/villagers" class="btn btn-secondary btn-lg ms-2">Cancel</router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      form: {
        full_name: '', date_of_birth: '', gender: '', household_id: '',
        village: '', ward: '', zone: '',
        nin: '', phone_number: '', email: '', bank_account_number: '', bank_name: '',
        marital_status: '', occupation: '', education_level: '', health_status: '',
        fingerprint_data: '', facial_photo: '',
        spouse_name: '', spouse_occupation: '', children: [],
      },
      errors: {},
      duplicateError: null,
      success: false,
      successId: '',
      registrationDate: '',
      registeredBy: '',
      loading: false,
      passportPreview: null,
      passportFile: null,
    };
  },
  methods: {
    async register() {
      this.errors = {};
      this.duplicateError = null;
      this.success = false;
      this.loading = true;
      try {
        // Use FormData for file upload
        const formData = new FormData();
        Object.keys(this.form).forEach(key => {
          if (this.form[key]) formData.append(key, this.form[key]);
        });
        if (this.passportFile) {
          formData.append('passport_photo', this.passportFile);
        }

        const { data } = await axios.post('/villagers', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        this.success = true;
        this.successId = data.data.unique_id;
        this.registrationDate = new Date(data.data.created_at).toLocaleString();
        this.registeredBy = JSON.parse(localStorage.getItem('user'))?.name || 'Admin';

        // Save family members if married
        if (this.form.spouse_name || this.form.children.length) {
          const uid = data.data.unique_id;
          if (this.form.spouse_name) {
            await axios.post(`/villagers/${uid}/family`, { full_name: this.form.spouse_name, relationship: 'spouse', occupation: this.form.spouse_occupation || null });
          }
          for (const child of this.form.children) {
            if (child.name) {
              await axios.post(`/villagers/${uid}/family`, { full_name: child.name, relationship: child.gender === 'daughter' ? 'daughter' : 'son', date_of_birth: child.dob || null });
            }
          }
        }

        this.form = {
          full_name: '', date_of_birth: '', gender: '', household_id: '',
          village: '', ward: '', zone: '',
          nin: '', phone_number: '', email: '', bank_account_number: '', bank_name: '',
          marital_status: '', occupation: '', education_level: '', health_status: '',
          fingerprint_data: '', facial_photo: '',
          spouse_name: '', spouse_occupation: '', children: [],
        };
        this.passportPreview = null;
        this.passportFile = null;
      } catch (e) {
        if (e.response?.status === 422) {
          this.errors = e.response.data.errors || {};
        } else if (e.response?.status === 409) {
          this.duplicateError = e.response.data;
        }
      } finally {
        this.loading = false;
      }
    },
    captureFingerprint() {
      alert('Fingerprint scanner integration required. In production, this connects to the biometric device.');
      this.form.fingerprint_data = 'SIMULATED_FINGERPRINT_' + Date.now();
    },
    capturePhoto() {
      alert('Camera integration required. In production, this opens the device camera.');
      this.form.facial_photo = 'SIMULATED_PHOTO_' + Date.now();
    },
    handlePassportUpload(e) {
      const file = e.target.files[0];
      if (file) {
        this.passportFile = file;
        this.passportPreview = URL.createObjectURL(file);
      }
    },
  },
};
</script>
