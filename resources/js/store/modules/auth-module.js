import * as axios from "axios";
import {axiosSetJwt, axiosUnsetJwt} from "../../constants/authHelper";

class AuthModule {
    namespaced = true;

    state = {
        isLoading: false,
        hasError: false,
        user: null,
        userLoaded: false
    };

    getters = {};

    mutations = {
        startLoading(state) {
            state.isLoading = true;
        },
        stopLoading(state, successful) {
            state.isLoading = false;
            state.hasError = !successful;
        },
        setUser(state, user) {
            state.user = user;
            state.userLoaded = true;
        },
    };

    actions = {
        performLogin: ({commit, state}, {email, password, onFinished}) => {
            commit('startLoading');
            const promise = axios.post(`/auth`, {email, password});

            promise.then(response => {
                if (response.data && response.data.token) {
                    window.localStorage.setItem('jwt', response.data.token);
                    axiosSetJwt(response.data.token);
                    commit('stopLoading', true);
                    onFinished ? onFinished(true, response.data) : '';
                } else {
                    throw 'Unable to retrieve token.';
                }
            });

            promise.catch(error => {
                window.localStorage.removeItem('jwt');
                commit('stopLoading', false);
                onFinished ? onFinished(false, error) : '';
            });
        },
        getCurrentUser: ({commit, state}, {onFinished}) => {
            commit('startLoading');
            const promise = axios.get(`/me`);

            promise.then(response => {
                if (response.data && response.data.user) {
                    commit('stopLoading', true);
                    commit('setUser', response.data.user || null);
                    onFinished ? onFinished(true, response.data && response.data.user) : '';
                } else {
                    throw 'Unable to retrieve user data with current token.';
                }
            });

            promise.catch(error => {
                window.localStorage.removeItem('jwt');
                commit('stopLoading', false);
                onFinished ? onFinished(false, error) : '';
            });
        },
        performLogout: ({commit, state}, {router}) => {
            window.localStorage.removeItem('jwt');
            axiosUnsetJwt();
            commit('setUser', null);
            router.push('/login');
            window.toastr.success('User logged out');
        },
    };
}

export default AuthModule;
