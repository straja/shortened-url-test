<template>
    <div class="main">
        <h1>TEST</h1>
        <form @submit.prevent="submit">
            <div class="form-group mb-2">
                <label for="url" class="mb-2">URL</label>
                <input type="text" class="form-control mb-2" id="url" placeholder="Insert url" v-model="form.url">
                <p class="text-danger" v-for="error of v.$errors" :key="error.$uid">
                    {{ error.$message }}
                </p>
                <small id="emailHelp" class="form-text text-muted mb-2">We'll convert and go to url you set.</small>
            </div>
            <button type="submit" class="btn btn-primary" @click="submit">Submit</button>
        </form>
        <hr>
        <hr class="danger" v-if="message">
        <h2 v-if="message" class="text-danger">{{ message }}</h2>
        <hr class="danger" v-if="message">
        <h2 v-if="urls.length">URLs</h2>
        <ul v-if="urls.length">
            <li v-for="url of urls">
                <a :href="url.url" target="_blank">example.com/{{ url.hash }}</a>
            </li>
        </ul>
    </div>
</template>

<script > 
    import { useVuelidate } from '@vuelidate/core';
    import { required, url } from '@vuelidate/validators';
    import axios from 'axios'

    export default {
        name: 'Test',
        setup: () => ({ v: useVuelidate() }),
        data: () => ({
            form: {
                url: null,
            },
            urls: [],
            message: null,
        }),
        mounted() {
            axios.get('get-urls')
                .then(response => {
                    this.urls = response.data;
                })
                .catch(error => {
                    console.log(error);
                });
        },
        validations: {
            form: {
                url: {
                    required,
                    url,
                }
            }
        },
        methods: {
            submit() {
                if (this.v.form.$invalid) {
                    this.v.form.$touch();
                    return;
                }

                axios.post('set-url', this.form)
                    .then(response => {
                        this.goto(response.data.url);
                    })
                    .catch(error => {
                        this.error = error.data['error'];
                    });
            },
            goto(url) {
                window.location.href = url;
            }
        }
        
    }
</script>

<style scoped>
    .main {
        margin: 20px auto;
        width: 50%;
    }
</style>