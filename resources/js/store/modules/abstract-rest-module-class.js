import * as axios from "axios";

class AbstractRestModuleClass {
    modelUrl = null;

    constructor() {
        if (new.target === AbstractRestModuleClass) {
            throw new TypeError("Cannot construct Abstract instances directly");
        }
    }

    namespaced = true;

    state = {
        items: [],
        isLoading: false,
        applyingChanges: false,
        hasLoadingError: false,
        loaded: false
    };

    getters = {};

    mutations = {
        setItems(state, items) {
            state.items = items;
        },
        startLoading(state) {
            state.isLoading = true;
        },
        stopLoading(state, successful) {
            state.isLoading = false;
            state.hasLoadingError = !successful;

            if (successful) {
                state.loaded = successful;
            }
        },
        setApplyingChanges(state, isApplyingChanges) {
            state.applyingChanges = isApplyingChanges;
        },
    };

    actions = {
        retrieveItems: ({commit, state}, {params, onFinished}) => {
            if (!state.isLoading) {
                let queryStr = '?';

                if (typeof params === 'object') {
                    Object.keys(params).forEach(key => {
                        queryStr += `${key}=${params[key]}&`;
                    });
                }

                commit('startLoading');
                const promise = axios.get(`/api/${this.modelUrl}${queryStr}`);

                promise.then(response => {
                    commit('stopLoading', true);
                    commit('setItems', response.data || []);

                    onFinished ? onFinished(true) : '';
                });

                promise.catch(error => {
                    commit('stopLoading', false);
                    onFinished ? onFinished(false, error) : '';
                });
            }
        },
        findItem: ({commit, state}, {id, onFinished}) => {
            commit('startLoading');
            const promise = axios.get(`/api/${this.modelUrl}/${id}`);

            promise.then(response => {
                commit('stopLoading', true);
                onFinished ? onFinished(true, response.data) : '';
            });

            promise.catch(error => {
                commit('stopLoading', false);
                onFinished ? onFinished(false, error) : '';
            });
        },
        deleteItem: ({commit, state}, {id, onFinished}) => {
            commit('setApplyingChanges', true);
            const promise = axios.delete(`/api/${this.modelUrl}/${id}`);

            promise.then(() => {
                commit('setApplyingChanges', false);
                onFinished ? onFinished(true) : '';
            });

            promise.catch(() => {
                commit('setApplyingChanges', false);
                onFinished ? onFinished(false) : '';
            });
        },
        updateOrCreate: ({commit, state}, {data, id, onFinished}) => {
            commit('setApplyingChanges', true);
            const url = `/api/${this.modelUrl}${id ? `/${id}` : ''}`;
            const promise = axios({url, method: (id ? 'patch' : 'post'), data});

            promise.then((response) => {
                commit('setApplyingChanges', false);
                onFinished ? onFinished(true, response.data) : '';
            });

            promise.catch((error) => {
                console.error(error);
                commit('setApplyingChanges', false);
                onFinished ? onFinished(false) : '';
            });
        }
    };
}

export default AbstractRestModuleClass;
