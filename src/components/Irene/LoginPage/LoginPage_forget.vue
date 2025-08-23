<script setup>
import { ref, onMounted, watchEffect } from 'vue'
import { useRouter } from 'vue-router'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  redirectToLoginPath: { type: String, default: '' },
  showHumanCheck: { type: Boolean, default: true },
  autoCloseOnSuccess: { type: Boolean, default: true }
})
const emit = defineEmits(['update:modelValue', 'success', 'close'])

const isOpen = ref(props.modelValue)
const email = ref('')
const pending = ref(false)
const msg = ref('')
const msgType = ref('info')
const isHuman = ref(false)
const emailRef = ref(null)

const router = useRouter()

onMounted(() => emailRef.value?.focus())
watchEffect(() => { isOpen.value = props.modelValue })

const closeModal = () => {
  emit('close')
  emit('update:modelValue', false)
}
const backToLogin = () => {
  closeModal()
  if (props.redirectToLoginPath) router.push(props.redirectToLoginPath)
}
const onSubmit = async () => {
  msg.value = ''
  msgType.value = 'info'
  if (!email.value) {
    msg.value = '請輸入 Email'
    msgType.value = 'error'
    emailRef.value?.focus()
    return
  }
  if (props.showHumanCheck && !isHuman.value) {
    msg.value = '請完成驗證'
    msgType.value = 'error'
    return
  }
  try {
    pending.value = true
    await new Promise(r => setTimeout(r, 700))
    msg.value = '已寄出重設密碼連結，請至信箱收信。'
    msgType.value = 'success'
    emit('success', { email: email.value })
    if (props.autoCloseOnSuccess) setTimeout(closeModal, 600)
  } catch (e) {
    msg.value = '寄送失敗，請稍後再試。'
    msgType.value = 'error'
  } finally {
    pending.value = false
  }
}
</script>

<template>
  <div v-if="isOpen" class="modal-overlay" @click="closeModal">
    <div class="modal-container" @click.stop>
      <button @click="closeModal" class="close-btn" aria-label="關閉">×</button>

      <div class="modal-content" role="dialog" aria-labelledby="fp-title" aria-modal="true">
        <h1 id="fp-title" class="title">忘記密碼</h1>

        <form class="form-container" @submit.prevent="onSubmit" novalidate>
          <div class="input-group">
            <label for="fp-email" class="input-label">Email</label>
            <div class="input-wrapper">
              <span class="input-icon">@</span>
              <input
                id="fp-email"
                ref="emailRef"
                v-model.trim="email"
                type="email"
                class="input-field"
                inputmode="email"
                autocomplete="email"
                placeholder="you@example.com"
                required
                aria-invalid="false"
              />
            </div>
          </div>

          <div class="input-group" v-if="showHumanCheck">
            <label class="input-label">驗證</label>
            <div class="input-wrapper" style="align-items:center">
              <input id="isHuman" type="checkbox" v-model="isHuman" style="width:18px;height:18px;margin:0 10px 0 4px" />
              <label for="isHuman" style="cursor:pointer">我不是機器人</label>
            </div>
          </div>

          <div class="action-row">
            <button type="submit" class="primary-btn" :disabled="pending">
              {{ pending ? '寄送中…' : '寄送重設連結' }}
            </button>
            <button type="button" class="secondary-btn" @click="backToLogin">
              返回登入
            </button>
          </div>

          <p v-if="msg" :class="['helper-text', msgType]" role="status" style="margin-top:8px">
            {{ msg }}
          </p>
        </form>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@import '@/assets/styles/othermixins.scss';
</style>

