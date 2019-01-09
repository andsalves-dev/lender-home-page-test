<template>
    <b-navbar toggleable="md" type="dark" variant="info" v-if="user">
        <b-navbar-toggle target="nav_collapse"></b-navbar-toggle>

        <b-collapse is-nav id="nav_collapse">
            <b-navbar-nav>
                <router-link to="/teams" class="nav-link">Teams</router-link>
                <router-link to="/players" class="nav-link">Players</router-link>
            </b-navbar-nav>

            <b-navbar-nav class="ml-auto">
                <b-nav-item-dropdown right>
                    <!-- Using button-content slot -->
                    <template slot="button-content">
                        <em>Hello {{user && user.name}}</em>
                    </template>
                    <b-button variant="link" class="dropdown-item" role="menuitem" @click="logout">
                        Sign out
                    </b-button>
                </b-nav-item-dropdown>
            </b-navbar-nav>
        </b-collapse>

        <loading-overlay :active="isLoading && !userLoaded" :is-full-page="true"></loading-overlay>
    </b-navbar>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        computed: {
            ...mapState({
                isLoading: state => state.auth.isLoading,
                hasError: state => state.auth.hasError,
                user: state => state.auth.user,
                userLoaded: state => state.auth.userLoaded,
            }),
        },
        created() {
            this.loadUser();
        },
        methods: {
            loadUser() {
                if (this.$router.history['current'].name !== 'login') {
                    return this.$store.dispatch('auth/getCurrentUser', {});
                }
            },
            logout() {
                return this.$store.dispatch('auth/performLogout', {router: this.$router});
            }
        },
        watch: {
            $route() {
                if (!this.userLoaded && !this.user) {
                    this.loadUser();
                }
            }
        }
    }
</script>
