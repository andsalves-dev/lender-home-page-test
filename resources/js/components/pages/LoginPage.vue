<template>
    <b-container class="login-container">
        <h3 class="mt-4">Login</h3>
        <b-form class="mt-4" @submit="onSubmit">
            <b-form-row>
                <b-col>
                    <b-form-group label="Email:">
                        <b-form-input v-model="form.email" required type="email"></b-form-input>
                    </b-form-group>
                    <b-form-group label="Password:">
                        <b-form-input v-model="form.password" required type="password"></b-form-input>
                    </b-form-group>
                </b-col>
            </b-form-row>

            <b-button type="submit" variant="primary">Submit</b-button>
        </b-form>

        <loading-overlay :active="isLoading" :is-full-page="true"></loading-overlay>
    </b-container>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        data() {
            return {
                form: {
                    email: '',
                    password: '',
                },
            }
        },
        computed: {
            ...mapState({
                isLoading: state => state.auth.isLoading,
                hasError: state => state.auth.hasError,
                user: state => state.auth.user,
                userLoaded: state => state.auth.userLoaded,
            }),
        },
        methods: {
            onSubmit(event) {
                event.preventDefault();
                const {email, password} = this.form;

                return this.$store.dispatch('auth/performLogin', {
                    email, password,
                    onFinished: (success, error) => {
                        if (success) {
                            window.toastr.success('Logged in successfully', 'Success');
                            this.$router.push({path: '/'});
                        } else {
                            if (error.response.status === 401) {
                                window.toastr.error('Invalid Credentials', 'Error');
                            } else {
                                window.toastr.error('Error while performing login', 'Error');
                            }
                        }
                    }
                });
            },
        }
    }
</script>
