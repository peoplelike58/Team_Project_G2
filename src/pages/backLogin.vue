<template>
    <div class="login-page">
        <div class="login-card">
            <header class="login-header">
                <img src="../assets/icons/Logo.png" alt="山上見 Logo" class="logo" />
                <h2>山上見後台管理系統</h2>
            </header>

            <el-form
                class="login-form"
                :model="form"
                @submit.prevent="handleLogin"
                label-width="0">
                <el-form-item prop="username">
                    <el-input v-model="form.username" placeholder="帳號" prefix-icon="el-icon-user"/>
                </el-form-item>

                <el-form-item prop="password">
                    <el-input v-model="form.password" type="password" placeholder="密碼" prefix-icon="el-icon-lock"/>
                </el-form-item>

                <el-form-item>
                    <el-button class="login-button" type="primary" @click="handleLogin">登入</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script setup>
    import {reactive} from 'vue'
    import {useRouter} from 'vue-router'
    import {ElMessage} from 'element-plus'

    const router = useRouter()
    const form = reactive({username: '', password: ''})

    function handleLogin() {
        if (form.username === 'admin' && form.password === 'admin') {
            localStorage.setItem('auth', 'true')
            router.push('/admin/home')
        } else {
            ElMessage.error('帳號或密碼錯誤')
        }
    }
</script>

<style lang="scss" scoped="scoped">
    .login-page {
        width: 100vw;
        height: 100vh;
        background: url("@/assets/mountain-bg.jpg") no-repeat center/cover;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        width: 360px;
        padding: 32px 24px;
        background: rgba(255, 255, 255, 0.85);
        border-radius: 12px;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        text-align: center;
    }

    .login-header {
        margin-bottom: 24px;

        .logo {
            width: 64px;
            margin-bottom: 12px;
        }

        h2 {
            font-size: 22px;
            color: #2e3a59;
            font-weight: 600;
        }
    }

    .login-form {
        .el-input {
            margin-bottom: 16px;
            .el-input__inner {
                padding: 10px 12px;
                border-radius: 6px;
            }
        }

        .login-button {
            width: 70%;
            margin: 0 auto;
            padding: 10px 0;
            background: #516E41;
            border-color: #516E41;
            border-radius: 10px;
            font-weight: 500;
        }
    }
</style>