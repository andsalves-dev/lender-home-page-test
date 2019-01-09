<template>
    <b-container class="mt-4">
        <b-form @submit="onSubmit" v-if="!teamNotFound">
            <b-form-row>
                <b-col sm="6">
                    <b-form-group label="Team Name:">
                        <b-form-input v-model="form.name" required placeholder="Enter name"></b-form-input>
                    </b-form-group>
                </b-col>
                <b-col sm="6">
                    <b-form-group label="Team Type:">
                        <b-form-select :options="teamTypes" required v-model="form.type">
                        </b-form-select>
                    </b-form-group>
                </b-col>
            </b-form-row>
            <b-button type="submit" variant="primary">Save</b-button>
            <b-button @click="() => $router.push('/teams')">Cancel</b-button>
        </b-form>

        <h3 v-if="teamNotFound">Team not found</h3>

        <loading-overlay :active="applyingChanges || isLoading" :is-full-page="true"></loading-overlay>
    </b-container>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        data() {
            return {
                form: {
                    name: '',
                    type: 'national-team',
                },
                teamTypes: [
                    {text: 'National Team', value: 'national-team'},
                    {text: 'Club', value: 'club'},
                ],
                teamNotFound: false,
            }
        },
        created() {
            if (this.$route.params['id']) {
                return this.$store.dispatch('teams/findItem', {
                    id: this.$route.params['id'],
                    onFinished: (success, data) => {
                        if (success) {
                            this.form.name = data.name;
                            this.form.type = data.type;
                        } else {
                            if (data.response && data.response.status === 404) {
                                this.teamNotFound = true;
                            } else {
                                window.toastr.error('Error while loading team', 'Error');
                            }
                        }
                    }
                });
            }
        },
        computed: {
            ...mapState({
                teams: state => state.teams.items,
                isLoading: state => state.teams.isLoading,
                hasError: state => state.teams.hasError,
                loaded: state => state.teams.loaded,
                applyingChanges: state => state.teams.applyingChanges,
            }),
        },
        methods: {
            onSubmit(evt) {
                evt.preventDefault();
                return this.$store.dispatch('teams/updateOrCreate', {
                    data: this.form,
                    id: this.$route.params['id'] || null,
                    onFinished: (success) => {
                        if (success) {
                            window.toastr.success('Team saved successfully', 'Success');
                            this.$router.push({path: '/teams'});
                        } else {
                            window.toastr.error('Error while saving team', 'Error');
                        }
                    }
                });
            },
        }
    }
</script>
